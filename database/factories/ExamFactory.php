<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition()
    {
        $open = fake()->dateTimeBetween('-1 month', '+1 month');
        $close = (clone $open)->modify('+7 days');

        return [
            'subject_id' => Subject::inRandomOrder()->first()?->id ?? Subject::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'time_limit_minutes' => fake()->numberBetween(10, 120),
            'open_at' => $open,
            'close_at' => $close,
            'max_attempts' => fake()->numberBetween(1, 3),
            'distance_between_attempts' => 0,
            'questions_to_display' => 0,
            'full_mark' => 100,
            'pass_threshold' => 50,
            'review_allowed' => fake()->boolean(80),
            'show_answers_after_close' => fake()->boolean(40),
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
