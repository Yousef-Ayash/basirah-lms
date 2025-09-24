<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\User;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();

        Subject::all()->each(function ($subject) use ($admin) {
            Exam::factory(rand(1,2))->create([
                'subject_id' => $subject->id,
                'created_by' => $admin ? $admin->id : User::factory(),
            ]);
        });
    }
}
