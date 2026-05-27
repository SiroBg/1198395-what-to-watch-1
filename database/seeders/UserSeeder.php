<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();

        $users = User::factory(10)->create();

        foreach ($users as $user) {
            $user->roles()->attach(
                $roles->random()->pluck('id'),
            );
        }
    }
}
