<?php

namespace HuseyinFiliz\Pickem\Api\Resource;

use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\Pickem\Team;

class TeamResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'pickem-teams';
    }

    public function model(): string
    {
        return Team::class;
    }

    public function fields(): array
    {
        return [
            Schema\Str::make('name'),
            Schema\Str::make('slug'),
            Schema\Str::make('logoPath')->nullable(),
            Schema\Str::make('logoUrl')->nullable(),
            Schema\DateTime::make('createdAt'),
            Schema\DateTime::make('updatedAt'),
        ];
    }
}
