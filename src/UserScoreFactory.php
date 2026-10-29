<?php

namespace HuseyinFiliz\Pickem;

use Carbon\Carbon;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'season_id' => null,
            'total_points' => 0,
            'total_picks' => 0,
            'correct_picks' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ];
    }
}
