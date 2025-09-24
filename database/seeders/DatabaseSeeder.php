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
            UserSeeder::class,
            SubjectSeeder::class,
            MaterialSeeder::class,
            ExamSeeder::class,
            QuestionSeeder::class,
            BookmarkSeeder::class,
            MarksReportSeeder::class
        ]);
    }
}
