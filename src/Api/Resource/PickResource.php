<?php

namespace HuseyinFiliz\Pickem\Api\Resource;

use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\Pickem\Pick;

class PickResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'pickem-picks';
    }

    public function model(): string
    {
        return Pick::class;
    }

    public function fields(): array
    {
        return [
            Schema\Integer::make('userId'),
            Schema\Integer::make('eventId'),
            Schema\Str::make('selectedOutcome'),
            Schema\Boolean::make('isCorrect')->nullable(),
            Schema\DateTime::make('createdAt'),
            Schema\DateTime::make('updatedAt'),
            Schema\Relationship\ToOne::make('user')->type('users')->includable(),
            Schema\Relationship\ToOne::make('event')->type('pickem-events')->includable(),
        ];
    }
}
