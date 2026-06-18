<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_all_comments_with_200_response(): void
    {
        $film = Film::factory()->create();

        Comment::factory()->create(['film_id' => $film->id]);
        Comment::factory()->create(['film_id' => $film->id]);
        Comment::factory()->create(['film_id' => $film->id]);

        $response = $this->getJson('/api/comments/' . $film->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                            'id',
                            'user_id',
                            'film_id',
                            'comment_id',
                            'created_at',
                            'updated_at',
                            'text',
                            'rating',
                            'author',
                        ],
                     ],
                 ]);
    }

    public function test_user_can_create_comments(): void
    {
        $film = Film::factory()->create();

        $commentData = [
            'text' => str_repeat('X', 100),
            'rating' => 1,
        ];

        $unauthorizedResponse = $this->postJson('/api/comments/' . $film->id, $commentData);

        $unauthorizedResponse->assertStatus(401);

        $user = User::factory()->create();

        $authorizedResponse = $this->actingAs($user)->postJson('/api/comments/' . $film->id, $commentData);

        $authorizedResponse->assertStatus(201)->assertJson([
            'data' => [
                'user_id' => $user->id,
                'film_id' => $film->id,
                'comment_id' => null,
                'text' => $commentData['text'],
                'rating' => $commentData['rating'],
            ],
        ]);
    }

    public function test_user_can_update_his_comments(): void
    {
        $userWithComment = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $userWithComment->id]);

        $userWithoutComment = User::factory()->create();

        $expectedData = [
            'text' => str_repeat('X', 100),
            'rating' => 1,
        ];

        $rightUserResponse = $this->actingAs($userWithComment)->patchJson('/api/comments/' . $comment->id, [
            'text' => $expectedData['text'],
            'rating' => $expectedData['rating'],
        ]);

        $rightUserResponse->assertStatus(201)->assertJson([
            'data' => [
                'id' => $comment->id,
                'user_id' => $userWithComment->id,
                'text' => $expectedData['text'],
                'rating' => $expectedData['rating'],
            ],
        ]);

        $wrongUserResponse = $this->actingAs($userWithoutComment)->patchJson('/api/comments/' . $comment->id, [
            'text' => $expectedData['text'],
            'rating' => $expectedData['rating'],
        ]);

        $wrongUserResponse->assertStatus(403);
    }

    public function test_user_can_delete_his_comments(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $wrongUser = User::factory()->create();

        $wrongUserResponse = $this->actingAs($wrongUser)->delete('/api/comments/' . $comment->id);

        $wrongUserResponse->assertStatus(403);

        $response = $this->actingAs($user)->delete('/api/comments/' . $comment->id);

        $response->assertStatus(200);

        $this->assertModelMissing($comment);
    }

    public function test_moderator_can_patch_and_delete_others_comments(): void
    {
        $moderator = User::factory()->create();
        $role = Role::create(['name' => 'moderator']);
        $moderator->roles()->attach($role);

        $comment = Comment::factory()->create();

        $expectedData = [
            'text' => str_repeat('X', 100),
            'rating' => 1,
        ];

        $patchResponse = $this->actingAs($moderator)->patchJson('/api/comments/' . $comment->id, [
            'text' => $expectedData['text'],
            'rating' => $expectedData['rating'],
        ]);

        $patchResponse->assertStatus(201)->assertJson([
            'data' => [
                'id' => $comment->id,
                'text' => $expectedData['text'],
                'rating' => $expectedData['rating'],
            ],
        ]);

        $deleteResponse = $this->actingAs($moderator)->delete('/api/comments/' . $comment->id);

        $deleteResponse->assertStatus(200);

        $this->assertModelMissing($comment);
    }
}
