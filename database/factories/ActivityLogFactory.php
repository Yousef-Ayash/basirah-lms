<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition()
    {
        return [
            'log_name' => 'default',
            'description' => fake()->sentence(),
            'event' => fake()->randomElement(['created', 'updated', 'deleted']),
            'subject_type' => 'App\Models\Subject',
            'subject_id' => fake()->randomDigitNotNull(),
            'causer_type' => 'App\Models\User',
            'causer_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'properties' => ['ip' => fake()->ipv4()],
        ];
    }
}
