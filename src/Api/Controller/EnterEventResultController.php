<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\EventResource;
use HuseyinFiliz\Pickem\Event;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class EnterEventResultController extends AbstractShowController
{
    public $resource = EventResource::class;
    public $include = ['homeTeam', 'awayTeam', 'week'];

    public function __construct(protected Translator $translator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $id = Arr::get($request->getQueryParams(), 'id');
        $event = Event::with(['homeTeam', 'awayTeam', 'week'])->findOrFail($id);

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        $homeScore = Arr::get($data, 'homeScore');
        $awayScore = Arr::get($data, 'awayScore');

        // 1. Skor Doğrulama
        if (null === $homeScore || null === $awayScore) {
            throw new ValidationException(['scores' => $this->translator->trans('huseyinfiliz-pickem.lib.messages.scores_required')]);
        }

        // 2. Skorları Ata
        $event->home_score = (int) $homeScore;
        $event->away_score = (int) $awayScore;

        // 3. Statü Kontrolü ve Güncelleme
        // Modeldeki 'booted' metodu result'ı otomatik hesaplayacak.
        // Ancak statüyü 'finished' olmaya zorlamak, özellikle 'closed' durumundaki maçlar için önemlidir.
        if (Event::STATUS_FINISHED !== $event->status) {
            $event->status = Event::STATUS_FINISHED;
        }

        $event->save();

        // The Event saved listener queues scoring and notifications.

        // Güncel veriyi döndür
        $event->load(['homeTeam', 'awayTeam', 'week']);

        return $event;
    }
}
