<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_user_without_moderator_role_cant_patch_genre(): void
    {
        $genre = Genre::factory()->create(['name' => 'Комедия']);

        $unauthorizedResponse = $this->patchJson('/api/genres/' . $genre->id, ['name' => 'Ужасы']);

        $unauthorizedResponse->assertStatus(401);

        $user = User::factory()->create();

        $notModeratorResponse = $this->actingAs($user)->patchJson('/api/genres/' . $genre->id, ['name' => 'Ужасы']);

        $notModeratorResponse->assertStatus(403);
    }

    public function test_moderator_can_patch_genres(): void
    {
        $genre = Genre::factory()->create(['name' => 'Комедия']);

        $user = User::factory()->create();
        $role = Role::create(['name' => 'moderator']);

        $user->roles()->attach($role);

        $response = $this->actingAs($user)->patchJson('/api/genres/' . $genre->id, ['name' => 'Ужасы']);

        $response->assertStatus(200)->assertJson([
                     'data' => [
                         'id' => $genre->id,
                         'name' => 'Ужасы',
                     ],
                 ]);
        ;
    }
}
