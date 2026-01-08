<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('admin')->first();

        // Create 3 main courses
        $courses = [
            ['title' => 'المقرر التأسيسي', 'order' => 1],
            ['title' => 'المقرر المتوسط', 'order' => 2],
            ['title' => 'المقرر المتقدم', 'order' => 3],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['title' => $courseData['title']],
                [
                    'description' => 'وصف تجريبي للمقرر التدريبي...',
                    'order' => $courseData['order'],
                    'created_by' => $admin ? $admin->id : null,
                ]
            );
        }

        // Create a few random courses
        Course::factory(2)->create([
            'created_by' => $admin ? $admin->id : null,
        ]);
    }
}
