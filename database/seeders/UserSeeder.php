<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::firstOrCreate(['name' => 'student']);

        // create a few students
        User::factory(20)->create()->each(function ($user) use ($studentRole) {
            $user->assignRole($studentRole);
        });
    }
}
