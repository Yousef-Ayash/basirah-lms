<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Level;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition()
    {
        return [
            'name' => 'Level ' . fake()->unique()->numberBetween(1, 100),
            'order' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
