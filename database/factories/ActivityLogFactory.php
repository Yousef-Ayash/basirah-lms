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
            'causer_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'action' => fake()->randomElement(['created_subject','deleted_material','approved_user']),
            'subject_type' => null,
            'subject_id' => null,
            'properties' => ['note' => fake()->sentence()],
        ];
    }
}
