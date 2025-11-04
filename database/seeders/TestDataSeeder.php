<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // order matters:
        // 1. Levels
        $this->call(TestLevelsSeeder::class);

        // 2. Teachers
        $this->call(TestTeachersSeeder::class);

        // 3. Roles & admin
        $this->call(RolesAndAdminSeeder::class);

        // 4. Users (students + factory)
        $this->call(UserSeeder::class);

        // 5. Subjects (depends on levels, teachers, admin user)
        $this->call(TestSubjectsSeeder::class);
    }
}
