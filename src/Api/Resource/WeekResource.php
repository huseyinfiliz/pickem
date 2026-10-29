<?php

namespace HuseyinFiliz\Pickem\Api\Resource;

use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\Pickem\Week;

class WeekResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'pickem-weeks';
    }

    public function model(): string
    {
        return Week::class;
    }

    public function fields(): array
    {
        return [
            Schema\Str::make('name'),
            Schema\Integer::make('seasonId')->nullable(),
            Schema\Integer::make('weekNumber')->nullable(),
            Schema\DateTime::make('startDate')->nullable(),
            Schema\DateTime::make('endDate')->nullable(),
            Schema\DateTime::make('createdAt'),
            Schema\DateTime::make('updatedAt'),
            Schema\Relationship\ToOne::make('season')->type('pickem-seasons')->includable(),
            Schema\Relationship\ToMany::make('events')->type('pickem-events')->includable(),
        ];
    }
}
