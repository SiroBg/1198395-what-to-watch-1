<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = Genre::all();

        $films = Film::factory(50)->has(Actor::factory(10))->create();

        foreach ($films as $film) {
            $film->genres()->attach(
                $genres->random()->pluck('id'),
            );
        }
    }
}
