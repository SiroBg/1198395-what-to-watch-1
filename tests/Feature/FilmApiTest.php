<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_all_films_with_200_response(): void
    {
        Film::factory()->count(10)->create();

        $response = $this->getJson('/api/films');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                            'id',
                            'name',
                            'preview_image',
                            'preview_video_link',
                        ],
                     ],
                 ]);
    }

    public function test_returns_right_film_structure(): void
    {
        $film = Film::factory()->create();

        $response = $this->getJson('/api/films/' . $film->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                        'id',
                        'name',
                        'poster_image',
                        'preview_image',
                        'background_image',
                        'background_color',
                        'video_link',
                        'preview_video_link',
                        'description',
                        'rating',
                        'scores_count',
                        'directors',
                        'starring',
                        'run_time',
                        'genres',
                        'released',
                        'is_favorite',
                     ],
                 ]);
    }

    public function test_only_moderator_can_create_films(): void
    {
        $imdbId = 'tt1234567';

        $unauthorizedResponse = $this->postJson('/api/films', ['imdb_id' => $imdbId]);

        $unauthorizedResponse->assertStatus(401);

        $notModeratorUser = User::factory()->create();

        $notModeratorUserResponse = $this->actingAs($notModeratorUser)->postJson('/api/films', ['imdb_id' => $imdbId]);

        $notModeratorUserResponse->assertStatus(403);

        $moderator = User::factory()->create();
        $role = Role::create(['name' => 'moderator']);
        $moderator->roles()->attach($role);

        $moderatorResponse = $this->actingAs($moderator)->postJson('/api/films', ['imdb_id' => $imdbId]);

        $moderatorResponse->assertStatus(201)->assertJson([
            'data' => [
                'imdb_id' => $imdbId,
            ],
        ]);
    }

    public function test_only_moderator_can_patch_films(): void
    {
        $film = Film::factory()->create();

        $expectedData = [
            'imdb_id' => 'tt1234567',
            'name' => 'Titanic',
            'status' => 'ready',
        ];

        $unauthorizedResponse = $this->patchJson('/api/films/' . $film->id, $expectedData);

        $unauthorizedResponse->assertStatus(401);

        $notModeratorUser = User::factory()->create();

        $notModeratorUserResponse = $this->actingAs($notModeratorUser)->patchJson('/api/films/' . $film->id, $expectedData);

        $notModeratorUserResponse->assertStatus(403);

        $moderator = User::factory()->create();
        $role = Role::create(['name' => 'moderator']);
        $moderator->roles()->attach($role);

        $moderatorResponse = $this->actingAs($moderator)->patchJson('/api/films/' . $film->id, $expectedData);

        $moderatorResponse->assertStatus(201)->assertJson([
            'data' => $expectedData,
        ]);
    }
}
