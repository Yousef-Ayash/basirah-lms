<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SubjectMaterial;
use App\Models\Subject;

class SubjectMaterialFactory extends Factory
{
    protected $model = SubjectMaterial::class;

    public function definition()
    {
        $types = ['pdf', 'picture', 'youtube'];
        $type = fake()->randomElement($types);

        return [
            'subject_id' => Subject::inRandomOrder()->first()?->id ?? Subject::factory(),
            'title' => fake()->sentence(4),
            'key_points' => fake()->sentence(10),
            'type' => $type,
            // 'file_path' => $type === 'youtube' ? null : 'materials/sample-' . fake()->numberBetween(1, 10) . '.pdf',
            'youtube_id' => $type === 'youtube' ? 'dQw4w9WgXcQ' : null,
            'order' => fake()->numberBetween(0, 20),
        ];
    }
}
