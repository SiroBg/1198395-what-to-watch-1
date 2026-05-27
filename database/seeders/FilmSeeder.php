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
        Film::factory(50)->has(Actor::factory(20))->has(Genre::factory(5))->create();
    }
}
