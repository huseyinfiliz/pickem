<?php

namespace HuseyinFiliz\Pickem;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeekFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'season_id' => null,
            'week_number' => null,
            'start_date' => null,
            'end_date' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ];
    }
}
