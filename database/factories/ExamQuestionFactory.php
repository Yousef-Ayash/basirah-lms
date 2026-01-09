<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ExamQuestion;
use App\Models\Exam;

class ExamQuestionFactory extends Factory
{
    protected $model = ExamQuestion::class;

    public function definition()
    {
        $count = fake()->numberBetween(3, 5);
        $options = [];
        for ($i = 0; $i < $count; $i++) {
            $options[] = fake()->sentence(6);
        }
        $correct = fake()->numberBetween(1, $count); // 1-based

        return [
            'exam_id' => Exam::inRandomOrder()->first()?->id ?? Exam::factory(),
            'question_text' => fake()->sentence(12),
            'options' => $options, // cast to json automatically by model
            'correct_answer' => $correct,
            'mark' => fake()->randomFloat(2, 1, 10),
            'order' => fake()->numberBetween(0, 100),
        ];
    }
}
