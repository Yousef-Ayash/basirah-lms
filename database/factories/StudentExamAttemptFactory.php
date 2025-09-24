<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StudentExamAttempt;
use App\Models\Exam;
use App\Models\User;

class StudentExamAttemptFactory extends Factory
{
    protected $model = StudentExamAttempt::class;

    public function definition()
    {
        $started = fake()->dateTimeBetween('-2 days', 'now');
        $submitted = (clone $started)->modify('+'.fake()->numberBetween(1,120).' minutes');

        return [
            'exam_id' => Exam::inRandomOrder()->first()?->id ?? Exam::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'started_at' => $started,
            'submitted_at' => $submitted,
            'score' => fake()->randomFloat(2, 0, 100),
            'scored_at' => (clone $submitted),
            'attempt_number' => 1,
            'metadata' => null,
        ];
    }
}
