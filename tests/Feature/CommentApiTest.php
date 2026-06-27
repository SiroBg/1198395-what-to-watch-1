<?php

use App\Models\Comment;
use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

dataset('allowed users for deletion', [
    'автор комментария' => [
        fn ($comment) => User::find($comment->user_id),
    ],
    'модератор' => [
        fn () => tap(User::factory()->create(), function ($user) {
            $user->roles()->attach(Role::firstOrCreate(['name' => 'moderator']));
        }),
    ],
]);

describe('GET api/comments/{film} (show)', function () {
    test('возвращает правильную структуру комментариев', function () {
        $film = Film::factory()->create();
        Comment::factory()->count(3)->create(['film_id' => $film->id]);

        $response = $this->getJson('/api/comments/' . $film->id);

        expect($response)->assertOk()
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
                        'author'
                    ],
                ],
            ]);
    });
});

describe('POST api/comments/{film} (store)', function () {
    test('пользователи могут создавать комментарии', function () {
        $film = Film::factory()->create();
        $commentData = [
            'text' => str_repeat('X', 100),
            'rating' => 1,
        ];

        expect(
            $this->postJson('/api/comments/' . $film->id, $commentData)
        )->assertUnauthorized();

        $user = User::factory()->create();
        $authorizedResponse = $this->actingAs($user)->postJson(
            '/api/comments/' . $film->id,
            $commentData
        );

        expect($authorizedResponse)->assertCreated()
            ->and($authorizedResponse->json('data'))
            ->toBeArray()
            ->toHaveKey('user_id', $user->id)
            ->toHaveKey('film_id', $film->id)
            ->toHaveKey('text', $commentData['text'])
            ->toHaveKey('rating', $commentData['rating']);
    });
});

describe('PATCH api/comments/{comment} (update)', function () {
    test('автор комментария может редактировать свой комментарий',
    function () {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $expectedData = [
            'text' => str_repeat('X', 100),
            'rating' => 1,
        ];

        $response = $this->actingAs($user)->patchJson(
            '/api/comments/' . $comment->id,
            $expectedData
        );

        expect($response)->assertCreated()
            ->and($response->json('data'))
            ->id->toBe($comment->id)
            ->user_id->toBe($user->id)
            ->text->toBe($expectedData['text'])
            ->rating->toBe($expectedData['rating']);
    });

    test('модератор может редактировать чужие комментарии',
    function () {
        $moderator = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'moderator']);
        $moderator->roles()->attach($role);

        $comment = Comment::factory()->create();
        $expectedData = [
            'text' => str_repeat('X', 100),
            'rating' => 1,
        ];

        $patchResponse = $this->actingAs($moderator)->patchJson(
            '/api/comments/'.$comment->id, $expectedData
        );

        expect($patchResponse)->assertCreated()
            ->and($patchResponse->json('data'))
            ->id->toBe($comment->id)
            ->text->toBe($expectedData['text'])
            ->rating->toBe($expectedData['rating']);
    });
});

describe('DELETE api/comments/{comment} (destroy)', function () {
    test(
        'разрешенный пользователь может удалить комментарий',
        function (Closure $getUser) {
            $comment = Comment::factory()->create();
            $user = $getUser($comment);

            $response = $this->actingAs($user)->delete(
                '/api/comments/' . $comment->id
            );

            expect($response)->assertOk();
            $this->assertModelMissing($comment);
        }
    )->with('allowed users for deletion');
});

test('пользователь не может управлять чужим комментарием',
function (string $method, string $endpointSuffix) {
    $comment = Comment::factory()->create();
    $wrongUser = User::factory()->create();

    $url = '/api/comments/'.$comment->id.$endpointSuffix;
    $response = $this->actingAs($wrongUser)->json($method, $url, [
        'text' => str_repeat('X', 100),
        'rating' => 5,
    ]);

    expect($response)->assertForbidden();
})->with([
    'при попытке обновления' => ['PATCH', ''],
    'при попытке удаления' => ['DELETE', ''],
]);

