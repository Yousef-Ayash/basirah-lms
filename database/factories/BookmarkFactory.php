<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Bookmark;
use App\Models\User;
use App\Models\SubjectMaterial;

class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'material_id' => SubjectMaterial::inRandomOrder()->first()?->id ?? SubjectMaterial::factory(),
        ];
    }
}
