<?php

namespace HuseyinFiliz\Pickem\Api\Resource;

use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\Pickem\Season;

class SeasonResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'pickem-seasons';
    }

    public function model(): string
    {
        return Season::class;
    }

    public function fields(): array
    {
        return [
            Schema\Str::make('name'),
            Schema\Str::make('slug'),
            Schema\DateTime::make('startDate')->nullable(),
            Schema\DateTime::make('endDate')->nullable(),
            Schema\DateTime::make('createdAt'),
            Schema\DateTime::make('updatedAt'),
            Schema\Relationship\ToMany::make('weeks')->type('pickem-weeks')->includable(),
        ];
    }
}
