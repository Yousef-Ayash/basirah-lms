<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\MarksReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarksReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure users and exams exist before trying to create marks
        if (User::count() === 0 || Exam::count() === 0) {
            $this->command->info('Cannot seed marks because no users or exams exist. Please run other seeders first.');
            return;
        }

        // Create 50 mark records using the factory
        MarksReport::factory()->count(50)->create();
    }
}