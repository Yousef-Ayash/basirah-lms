<?php
namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('##########'),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'level_id' => Level::inRandomOrder()->first()?->id,
            'is_approved' => true,
        ];
    }
}