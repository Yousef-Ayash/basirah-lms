<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ExamLog;
use App\Models\StudentExamAttempt;
use App\Models\User;

class ExamLogFactory extends Factory
{
    protected $model = ExamLog::class;

    public function definition()
    {
        return [
            'exam_attempt_id' => StudentExamAttempt::inRandomOrder()->first()?->id ?? StudentExamAttempt::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'action' => fake()->randomElement(['start', 'autosave', 'submit', 'view_answers']),
            'metadata' => ['note' => fake()->sentence()],
            'ip' => fake()->ipv4(),
            'user_agent' => 'TestingBot/1.0',
        ];
    }
}
