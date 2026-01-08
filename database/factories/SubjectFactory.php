<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subject;
use App\Models\User;
use App\Models\Level;
use App\Models\Teacher;
use App\Models\Course;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'description' => fake()->paragraph(),
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'level_id' => Level::inRandomOrder()->first()?->id ?? Level::factory(),
            'teacher_id' => Teacher::inRandomOrder()->first()?->id ?? Teacher::factory(),
            'course_id' => Course::inRandomOrder()->first()?->id ?? null,
        ];
    }
}
