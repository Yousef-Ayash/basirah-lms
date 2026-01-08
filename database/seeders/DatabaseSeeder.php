<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndAdminSeeder::class,
            LevelSeeder::class,
            TeacherSeeder::class,
            UserSeeder::class,
            CourseSeeder::class, // Added before SubjectSeeder
            SubjectSeeder::class,
            MaterialSeeder::class,
            ExamSeeder::class,
            QuestionSeeder::class,
            BookmarkSeeder::class,
            MarksReportSeeder::class
        ]);
    }
}
