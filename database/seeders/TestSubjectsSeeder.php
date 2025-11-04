<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;
use App\Models\Teacher;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class TestSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $levels = Level::all();
        $teachers = Teacher::all();

        if ($levels->isEmpty() || $teachers->isEmpty()) {
            $this->command->error('Levels or Teachers not present. Run LevelsSeeder and TeachersSeeder first.');
            return;
        }

        // find demo admin created by RolesAndAdminSeeder (phone '0987654321')
        $admin = User::where('phone', '0987654321')->first() ?? User::first();

        // create 12-20 subjects (adjust count if you want)
        $count = 15;
        for ($i = 0; $i < $count; $i++) {
            $level = $levels->random();
            $teacher = $teachers->random();

            // combine the level name for semantic relation to level
            $title = trim($level->name . ' — ' . ucfirst($faker->unique()->words(rand(1, 3), true)));

            Subject::create([
                'title' => $title,
                'description' => $faker->optional()->paragraph(),
                'created_by' => $admin->id,
                'level_id' => $level->id,
                'teacher_id' => $teacher->id,
            ]);
        }
    }
}
