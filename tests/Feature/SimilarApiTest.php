<?php

use App\Enums\FilmStatus;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

describe('GET api/films/{film}/similar (similar)', function () {
    it('возвращает до 4 фильмов', function () {
        $genre = Genre::factory()->create();

        $film = Film::factory()->create([
            'status' => FilmStatus::READY,
        ]);

        $film->genres()->attach($genre);

        $similarFilms = Film::factory()->count(4)->create([
            'status' => FilmStatus::READY,
        ]);

        foreach ($similarFilms as $similarFilm) {
            $similarFilm->genres()->attach($genre);
        }

        getJson("/api/films/{$film->id}/similar")
            ->assertOk()
            ->assertJsonCount(4, 'data');
    });

    it('возвращает фильм с похожим жанром', function () {
        $genre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();

        $film = Film::factory()->create([
            'status' => FilmStatus::READY,
        ]);

        $film->genres()->attach($genre);

        $expected = Film::factory()->count(4)->create([
            'status' => FilmStatus::READY,
        ]);

        foreach ($expected as $movie) {
            $movie->genres()->attach($genre);
        }

        $anotherFilm = Film::factory()->create([
            'status' => FilmStatus::READY,
        ]);

        $anotherFilm->genres()->attach($otherGenre);

        $response = getJson("/api/films/{$film->id}/similar")
            ->assertOk();

        $ids = collect($response->json('data'))
            ->pluck('id');

        expect($ids)->not->toContain($anotherFilm->id);
    });

    it('возвращает не больше 4 фильмов', function () {
        $genre = Genre::factory()->create();

        $film = Film::factory()->create([
            'status' => FilmStatus::READY,
        ]);

        $film->genres()->attach($genre);

        $films = Film::factory()->count(10)->create([
            'status' => FilmStatus::READY,
        ]);

        foreach ($films as $movie) {
            $movie->genres()->attach($genre);
        }

        getJson("/api/films/{$film->id}/similar")
            ->assertOk()
            ->assertJsonCount(4, 'data');
    });

    it('возвращает ошибку 404, если фильма не существует', function () {
        getJson('/api/films/999999/similar')
            ->assertNotFound();
    });
});
