<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteFilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = Film::all();

        User::all()->each(function ($user) use ($films) {
            $user->films()->attach(
                $films->random(rand(2, 5))->pluck('id'),
            );
        });
    }
}
