<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MarksReport;
use App\Models\Exam;
use App\Models\User;
use App\Models\StudentExamAttempt;

class MarksReportFactory extends Factory
{
    protected $model = MarksReport::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'exam_id' => Exam::inRandomOrder()->first()?->id ?? Exam::factory(),
            'attempt_id' => StudentExamAttempt::inRandomOrder()->first()?->id ?? null,
            'marks' => fake()->randomFloat(2, 0, 100),
            'score' => fake()->randomFloat(2, 0, 100), // Percentage
            'notes' => fake()->sentence(),
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'updated_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
