<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StudentExamAnswer;
use App\Models\StudentExamAttempt;
use App\Models\ExamQuestion;

class StudentExamAnswerFactory extends Factory
{
    protected $model = StudentExamAnswer::class;

    public function definition()
    {
        $question = ExamQuestion::inRandomOrder()->first();
        $options = $question ? $question->options : ['A','B','C'];
        $selected = $question ? fake()->numberBetween(1, count($options)) : null;

        return [
            'attempt_id' => StudentExamAttempt::inRandomOrder()->first()?->id ?? StudentExamAttempt::factory(),
            'question_id' => $question?->id ?? ExamQuestion::factory(),
            'selected_option' => $selected,
            'answer_text' => null,
            'is_correct' => $question ? ($selected === (int)$question->correct_answer) : null,
        ];
    }
}
