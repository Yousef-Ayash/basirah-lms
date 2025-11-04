<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use Faker\Factory as Faker;

class TestTeachersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // create 5 teachers with unique order 1..5
        for ($i = 1; $i <= 5; $i++) {
            Teacher::firstOrCreate(
                ['order' => $i],
                [
                    'name' => $faker->name(),
                    'bio' => $faker->optional()->sentence(8),
                ]
            );
        }
    }
}
