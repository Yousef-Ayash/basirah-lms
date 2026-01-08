<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;
use App\Models\User;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'description' => fake()->paragraph(),
            'order' => fake()->numberBetween(1, 100),
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
