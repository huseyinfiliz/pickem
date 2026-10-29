<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use Flarum\Http\UrlGenerator;
use HuseyinFiliz\Pickem\Api\Resource\EventResource;
use HuseyinFiliz\Pickem\Event;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class ListEventsController extends AbstractListController
{
    public $resource = EventResource::class;
    public $include = ['homeTeam', 'awayTeam', 'week'];

    public $sortFields = ['matchDate'];

    public function __construct(protected UrlGenerator $url)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.view');

        $query = Event::query();
        $query->with($this->include);

        $filters = $this->extractFilter($request);

        if ($weekIdString = Arr::get($filters, 'week')) {
            $weekIds = explode(',', $weekIdString);

            $query->where(function ($q) use ($weekIds) {
                $ids = array_diff($weekIds, ['null']);

                if (! empty($ids)) {
                    $q->whereIn('week_id', $ids);
                } elseif (! in_array('null', $weekIds)) {
                    $q->whereRaw('1=0');
                }

                if (in_array('null', $weekIds)) {
                    $q->orWhereNull('week_id');
                }
            });
        }

        if ($status = Arr::get($filters, 'status')) {
            $query->where('status', $status);
        }

        if ($teamId = Arr::get($filters, 'team')) {
            $query->where(function ($query) use ($teamId) {
                $query->where('home_team_id', $teamId)
                      ->orWhere('away_team_id', $teamId);
            });
        }

        $sort = $this->extractSort($request);
        $sortOrder = 'desc';

        if (is_array($sort) && ! empty($sort)) {
            $sortField = ltrim(key($sort), '-');
            if ('matchDate' === $sortField) {
                $sortOrder = $sort[key($sort)];
            }
        }

        $query->orderBy('match_date', $sortOrder);

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);

        $total = $query->count();

        $results = $query->limit($limit)->offset($offset)->get();

        $document->addPaginationLinks(
            $this->url->to('api')->route('pickem.events.index'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $total
        );

        $document->setMeta(['total' => $total]);

        return $results;
    }
}
