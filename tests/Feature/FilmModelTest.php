<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_the_correct_average_rating_based_on_comments()
    {
        $film = Film::factory()->create();

        Comment::factory()->create(['film_id' => $film->id, 'rating' => 5]);
        Comment::factory()->create(['film_id' => $film->id, 'rating' => 4]);
        Comment::factory()->create(['film_id' => $film->id, 'rating' => 2]);

        $calculatedRating = $film->withRating()->first()->rating;

        $this->assertEquals(3.67, round($calculatedRating, 2));
    }

    public function test_returns_zero_rating_if_there_are_no_comments()
    {
        $film = Film::factory()->create();
        $calculatedRating = $film->withRating()->first()->rating;

        $this->assertEquals(0.0, $calculatedRating);
    }
}
