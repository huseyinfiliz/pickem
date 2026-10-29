<?php

namespace HuseyinFiliz\Pickem;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'week_id' => null,
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'match_date' => Carbon::now(),
            'cutoff_date' => Carbon::now(),
            'allow_draw' => false,
            'status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'result' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ];
    }
}
