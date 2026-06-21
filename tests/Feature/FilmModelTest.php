<?php

use App\Models\Comment;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('считает правильный рейтинг фильма', function () {
    $film = Film::factory()->create();

    Comment::factory()->create(['film_id' => $film->id, 'rating' => 5]);
    Comment::factory()->create(['film_id' => $film->id, 'rating' => 4]);
    Comment::factory()->create(['film_id' => $film->id, 'rating' => 2]);

    $calculatedRating = $film->withRating()->first()->rating;

    expect(round($calculatedRating, 2))->toBe(3.67);
});

test('возвращает нулевой рейтинг для фильма без отзывов', function () {
    $film = Film::factory()->create();

    $calculatedRating = $film->withRating()->first()->rating;

    expect((float) $calculatedRating)->toBe(0.0);
});
