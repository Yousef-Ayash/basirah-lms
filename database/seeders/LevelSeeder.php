<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Level 1', 'order' => 1],
            ['name' => 'Level 2', 'order' => 2],
            ['name' => 'Level 3', 'order' => 3],
            ['name' => 'Level 4', 'order' => 4],
        ];

        foreach ($levels as $l) {
            Level::firstOrCreate(['name' => $l['name']], $l);
        }
    }
}
