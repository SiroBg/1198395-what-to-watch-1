<?php

use App\Models\Film;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

dataset('film_access_matrix', [
    'неавторизованный гость получает 401' => [
        'user'   => fn () => null,
        'status' => 401
    ],
    'обычный пользователь получает 403' => [
        'user'   => fn () => User::factory()->create(),
        'status' => 403
    ],
    'модератор успешно проходит проверку' => [
        'user'   => function () {
            $moderator = User::factory()->create();
            $role = Role::firstOrCreate(['name' => 'moderator']);
            $moderator->roles()->attach($role);
            return $moderator;
        },
        'status' => null
    ],
]);

test('возвращает правильную структуру фильмов', function () {
    Film::factory()->count(10)->create();

    $response = $this->getJson('/api/films');

    expect($response)->assertOk()
        ->assertJsonStructure([
             'data' => [
                 '*' => ['id', 'name', 'preview_image', 'preview_video_link'],
             ],
        ]);
});

test('возвращает правильную структуру для одного фильма', function () {
    $film = Film::factory()->create();

    $response = $this->getJson('/api/films/' . $film->id);

    expect($response)->assertOk()
        ->assertJsonStructure([
             'data' => [
                'id', 'name', 'poster_image', 'preview_image', 'background_image',
                'background_color', 'video_link', 'preview_video_link', 'description',
                'rating', 'scores_count', 'directors', 'starring', 'run_time',
                'genres', 'released', 'is_favorite',
             ],
        ]);
});

test('проверка доступа к созданию фильма', function (Closure $user, ?int $status) {
    $imdbId = 'tt1234567';
    $resolvedUser = $user();

    $request = $resolvedUser ? $this->actingAs($resolvedUser) : $this;
    $response = $request->postJson('/api/films', ['imdb_id' => $imdbId]);

    if ($status) {
        expect($response)->assertStatus($status);
    } else {
        expect($response)->assertCreated();
        expect($response->json('data'))->imdb_id->toBe($imdbId);
    }
})->with('film_access_matrix');


test('проверка доступа к редактированию фильма', function (Closure $user, ?int $status) {
    $film = Film::factory()->create();
    $expectedData = [
        'imdb_id' => 'tt1234567',
        'name' => 'Titanic',
        'status' => 'ready',
    ];
    $resolvedUser = $user();

    $request = $resolvedUser ? $this->actingAs($resolvedUser) : $this;
    $response = $request->patchJson('/api/films/' . $film->id, $expectedData);

    if ($status) {
        expect($response)->assertStatus($status);
    } else {
        expect($response)->assertOk();
        expect($response->json('data'))
            ->imdb_id->toBe($expectedData['imdb_id'])
            ->name->toBe($expectedData['name'])
            ->status->toBe($expectedData['status']);
    }
})->with('film_access_matrix');
