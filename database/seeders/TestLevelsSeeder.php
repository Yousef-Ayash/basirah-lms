<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class TestLevelsSeeder extends Seeder
{
    public function run(): void
    {
        // create 4 levels: Level 1 .. Level 4 with order 1..4
        for ($i = 1; $i <= 4; $i++) {
            Level::firstOrCreate(
                ['order' => $i],
                ['name' => "Level {$i}"]
            );
        }
    }
}
