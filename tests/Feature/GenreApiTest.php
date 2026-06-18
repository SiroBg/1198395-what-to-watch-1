<?php

namespace Tests\Feature;

use App\Models\Genre;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    public function test_returns_all_genres_with_200_response(): void
    {
        Genre::factory()->count(3)->create();

        $response = $this->getJson('/api/genres');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name'],
                     ],
                 ]);
    }

    public function test_patch_genre_changes_name(): void
    {
        $genre = Genre::factory()->create(['name' => 'Комедия']);

        $response = $this->patchJson('/api/genres/' . $genre->id, ['name' => 'Ужасы']);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id' => $genre->id,
                         'name' => 'Ужасы',
                     ],
                 ]);
    }
}
