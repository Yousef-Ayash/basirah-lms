<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Level;
use App\Models\User;
use App\Models\Teacher;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();
        $levels = Level::all();

        foreach ($levels as $level) {
            Subject::factory(5)->create([
                'level_id' => $level->id,
                'created_by' => $admin ? $admin->id : User::factory(),
                'teacher_id' => Teacher::inRandomOrder()->first()->id,
            ]);
        }
    }
}
