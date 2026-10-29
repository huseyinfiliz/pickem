<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\WeekResource;
use HuseyinFiliz\Pickem\Validator\WeekValidator;
use HuseyinFiliz\Pickem\Week;
use Illuminate\Support\Arr; // EKLENDİ
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;

class CreateWeekController extends AbstractCreateController
{
    public $resource = WeekResource::class;

    public function __construct(protected WeekValidator $validator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        // --- YENİ MANTIK ---

        // 1. Convert camelCase keys (seasonId) to snake_case (season_id)
        $attributes = [];
        foreach ($data as $key => $value) {
            $attributes[Str::snake($key)] = $value;
        }

        // 2. Handle date transformations
        if ($startDate = Arr::get($attributes, 'start_date')) {
            $attributes['start_date'] = Carbon::parse($startDate);
        }
        if ($endDate = Arr::get($attributes, 'end_date')) {
            $attributes['end_date'] = Carbon::parse($endDate);
        }

        // 3. Validate the converted attributes
        $this->validator->assertValid($data);

        // 4. Create the model using the $fillable array in Week.php
        $week = Week::create($attributes);
        // --- YENİ MANTIK SONU ---

        return $week;
    }
}
