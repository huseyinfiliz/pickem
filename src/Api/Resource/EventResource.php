<?php

namespace HuseyinFiliz\Pickem\Api\Resource;

use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\Pickem\Event;

class EventResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'pickem-events';
    }

    public function model(): string
    {
        return Event::class;
    }

    public function fields(): array
    {
        return [
            Schema\Integer::make('weekId')->nullable(),
            Schema\Integer::make('homeTeamId'),
            Schema\Integer::make('awayTeamId'),
            Schema\DateTime::make('matchDate'),
            Schema\DateTime::make('cutoffDate'),
            Schema\Boolean::make('allowDraw'),
            Schema\Str::make('status'),
            Schema\Integer::make('homeScore')->nullable(),
            Schema\Integer::make('awayScore')->nullable(),
            Schema\Str::make('result')->nullable(),
            Schema\Boolean::make('canPick')->get(fn (Event $event) => $event->canPick()),
            Schema\DateTime::make('createdAt'),
            Schema\DateTime::make('updatedAt'),
            Schema\Relationship\ToOne::make('week')->type('pickem-weeks')->includable(),
            Schema\Relationship\ToOne::make('homeTeam')->type('pickem-teams')->includable(),
            Schema\Relationship\ToOne::make('awayTeam')->type('pickem-teams')->includable(),
        ];
    }
}
