<?php

namespace HuseyinFiliz\Pickem;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'start_date' => null,
            'end_date' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ];
    }
}
