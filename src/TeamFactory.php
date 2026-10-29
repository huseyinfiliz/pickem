<?php

namespace HuseyinFiliz\Pickem;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'logo_path' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ];
    }
}
