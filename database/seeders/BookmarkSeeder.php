<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bookmark;
use App\Models\User;
use App\Models\SubjectMaterial;

class BookmarkSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role('student')->get();
        $materials = SubjectMaterial::all();

        foreach ($users as $user) {
            $materials->random(min(3, $materials->count()))->each(function ($mat) use ($user) {
                Bookmark::firstOrCreate([
                    'user_id' => $user->id,
                    'material_id' => $mat->id,
                ]);
            });
        }
    }
}
