<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\EventResource;
use HuseyinFiliz\Pickem\Event;
use HuseyinFiliz\Pickem\Validator\EventValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

class CreateEventController extends AbstractCreateController
{
    public $resource = EventResource::class;
    public $include = ['homeTeam', 'awayTeam', 'week'];

    public function __construct(protected EventValidator $validator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[Str::snake($key)] = $value;
        }

        if ($matchDate = Arr::get($attributes, 'match_date')) {
            $attributes['match_date'] = Carbon::parse($matchDate);
        }
        if ($cutoffDate = Arr::get($attributes, 'cutoff_date')) {
            $attributes['cutoff_date'] = Carbon::parse($cutoffDate);
        }

        $attributes['allow_draw'] = Arr::get($attributes, 'allow_draw', false);
        $attributes['status'] = Arr::get($attributes, 'status', 'scheduled');

        $this->validator->assertValid($data);

        return Event::create($attributes);
    }
}
