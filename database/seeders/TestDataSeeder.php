<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Levels
        $this->call(TestLevelsSeeder::class);

        // 2. Teachers
        $this->call(TestTeachersSeeder::class);

        // 3. Roles & admin
        $this->call(RolesAndAdminSeeder::class);

        // 4. Users
        $this->call(UserSeeder::class);

        // 5. Courses (New)
        $this->call(CourseSeeder::class);

        // 6. Subjects
        $this->call(TestSubjectsSeeder::class);
    }
}
