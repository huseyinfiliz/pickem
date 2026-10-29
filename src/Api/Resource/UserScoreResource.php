<?php

namespace HuseyinFiliz\Pickem\Api\Resource;

use Flarum\Api\Resource\AbstractDatabaseResource;
use Flarum\Api\Schema;
use HuseyinFiliz\Pickem\UserScore;

class UserScoreResource extends AbstractDatabaseResource
{
    public function type(): string
    {
        return 'pickem-user-scores';
    }

    public function model(): string
    {
        return UserScore::class;
    }

    public function fields(): array
    {
        return [
            Schema\Integer::make('userId'),
            Schema\Integer::make('seasonId')->nullable(),
            Schema\Integer::make('totalPoints'),
            Schema\Integer::make('totalPicks'),
            Schema\Integer::make('correctPicks'),
            Schema\Number::make('accuracy')->get(fn (UserScore $score) => $score->getAccuracyPercentage()),
            Schema\Integer::make('rank')->get(fn (UserScore $score) => $score->calculatedRank ?? (int) $score->calculateRank()),
            Schema\DateTime::make('createdAt')->nullable(),
            Schema\DateTime::make('updatedAt')->nullable(),
            Schema\Relationship\ToOne::make('user')->type('users')->includable(),
            Schema\Relationship\ToOne::make('season')->type('pickem-seasons')->includable(),
        ];
    }
}
