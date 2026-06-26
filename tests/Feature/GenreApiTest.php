<?php

use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

dataset('genre_access_matrix', [
    'неавторизованный гость получает 401' => [
        'user' => fn () => null,
        'status' => 401,
    ],
    'обычный пользователь получает 403' => [
        'user' => fn () => User::factory()->create(),
        'status' => 403,
    ],
    'модератор успешно обновляет жанр' => [
        'user' => function () {
            $moderator = User::factory()->create();
            $role = Role::firstOrCreate(['name' => 'moderator']);
            $moderator->roles()->attach($role);

            return $moderator;
        },
        'status' => null,
    ],
]);

test('возвращает правильную структуру данных', function () {
    Genre::factory()->count(3)->create();

    $response = $this->getJson('/api/genres');

    expect($response)
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name'],
            ],
        ]);
});

test('проверка доступа к редактированию жанра', function (Closure $user, ?int $status) {
    $genre = Genre::factory()->create(['name' => 'Комедия']);
    $resolvedUser = $user();

    $request = $resolvedUser ? $this->actingAs($resolvedUser) : $this;
    $response = $request->patchJson('/api/genres/'.$genre->id, ['name' => 'Ужасы']);

    if ($status) {
        expect($response)->assertStatus($status);
    } else {
        expect($response)->assertOk();
        expect($response->json('data'))
            ->id->toBe($genre->id)
            ->name->toBe('Ужасы');
    }
})->with('genre_access_matrix');
