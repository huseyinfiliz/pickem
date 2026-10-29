<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use HuseyinFiliz\Pickem\Api\Resource\PickResource;
use HuseyinFiliz\Pickem\Pick;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

class ListPicksController extends AbstractListController
{
    public $resource = PickResource::class;
    public $include = ['event', 'event.homeTeam', 'event.awayTeam', 'event.week', 'user'];

    public $limit = 20;
    public $sort = ['createdAt' => 'desc'];
    public $sortFields = ['createdAt', 'updatedAt'];

    public function __construct(protected UrlGenerator $url)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.makePicks');

        $query = Pick::query();

        if ($userId = Arr::get($request->getQueryParams(), 'filter.user')) {
            if ($actor->hasPermission('pickem.manage') || $actor->id == $userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('user_id', $actor->id);
            }
        } else {
            $query->where('user_id', $actor->id);
        }

        // GÜNCELLENDİ: Virgülle ayrılmış çoklu ID desteği eklendi (1,2,3)
        if ($eventId = Arr::get($request->getQueryParams(), 'filter.event')) {
            $eventIds = explode(',', $eventId);
            $query->whereIn('event_id', $eventIds);
        }

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $sort = $this->extractSort($request);

        foreach ($sort as $field => $order) {
            $query->orderBy(Str::snake($field), $order);
        }

        $total = $query->count();

        $results = $query->with($this->include)
                         ->limit($limit)
                         ->offset($offset)
                         ->get();

        $document->addPaginationLinks(
            $this->url->to('api')->route('pickem.picks.index'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $total
        );

        $document->setMeta(['total' => $total]);

        return $results;
    }
}
