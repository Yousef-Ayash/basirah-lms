<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\SubjectMaterial;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        Subject::all()->each(function ($subject) {
            SubjectMaterial::factory(3)->create(['subject_id' => $subject->id]);
        });
    }
}
