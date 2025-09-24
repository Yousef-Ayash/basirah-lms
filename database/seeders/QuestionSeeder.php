<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\ExamQuestion;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        Exam::all()->each(function ($exam) {
            ExamQuestion::factory(10)->create(['exam_id' => $exam->id]);
        });
    }
}
