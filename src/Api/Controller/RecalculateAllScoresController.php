<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Job\RecalculateUserScoreJob;
use HuseyinFiliz\Pickem\Pick;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RecalculateAllScoresController implements RequestHandlerInterface
{
    public function __construct(protected BusDispatcher $bus)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        // Chunking kullanarak belleği koruyoruz.
        // Her seferinde 100 kullanıcı ID'si çekip işliyoruz.
        $totalJobs = 0;

        Pick::query()
            ->distinct()
            ->select('user_id')
            ->orderBy('user_id')
            ->chunk(100, function ($picks) use (&$totalJobs) {
                foreach ($picks as $pick) {
                    $this->bus->dispatch(new RecalculateUserScoreJob($pick->user_id));
                    $totalJobs++;
                }
            });

        return new JsonResponse([
            'status' => 'dispatched',
            'jobCount' => $totalJobs,
            'message' => $totalJobs.' users queued for recalculation.'
        ]);
    }
}
