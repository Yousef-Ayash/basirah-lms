<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Course;
use Faker\Factory as Faker;

class TestSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $levels = Level::all();
        $teachers = Teacher::all();
        $courses = Course::all();

        if ($levels->isEmpty() || $teachers->isEmpty()) {
            $this->command->error('Levels or Teachers not present. Run LevelsSeeder and TeachersSeeder first.');
            return;
        }

        $admin = User::where('phone', '0987654321')->first() ?? User::first();

        $count = 15;
        for ($i = 0; $i < $count; $i++) {
            $level = $levels->random();
            $teacher = $teachers->random();
            $course = $courses->isNotEmpty() ? $courses->random() : null;

            $title = trim($level->name . ' — ' . ucfirst($faker->unique()->words(rand(1, 3), true)));

            Subject::create([
                'title' => $title,
                'description' => $faker->optional()->paragraph(),
                'created_by' => $admin ? $admin->id : 1,
                'level_id' => $level->id,
                'teacher_id' => $teacher->id,
                'course_id' => $course ? $course->id : null,
            ]);
        }
    }
}
