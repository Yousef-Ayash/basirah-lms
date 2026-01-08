<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Course;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();
        $levels = Level::all();
        $courses = Course::all();

        if ($levels->isEmpty()) {
            return;
        }

        foreach ($levels as $level) {
            // Create 5 subjects per level
            Subject::factory(5)->create([
                'level_id' => $level->id,
                'created_by' => $admin ? $admin->id : User::factory(),
                'teacher_id' => Teacher::inRandomOrder()->first()?->id,
                // Assign to a random course if courses exist
                'course_id' => $courses->isNotEmpty() ? $courses->random()->id : null,
            ]);
        }
    }
}
