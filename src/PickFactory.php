<?php

namespace HuseyinFiliz\Pickem;

use Carbon\Carbon;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PickFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'selected_outcome' => 'home',
            'is_correct' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ];
    }
}
