<?php

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('GET api/favorite (index)', function () {

    it('блокирует доступ гостей (неавторизованных пользователей)', function () {
        $response = $this->getJson('api/favorite');

        $response->assertStatus(401);
    });

    it('возвращает пустой список, если у пользователя нет избранных фильмов', function () {
        $response = $this->actingAs($this->user)->getJson('api/favorite');

        $response->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(0, 'data');
    });

    it('успешно возвращает список избранных фильмов в правильном формате ресурса', function () {
        $films = Film::factory()->count(3)->create();
        $this->user->favoriteFilms()->attach($films);

        $response = $this->actingAs($this->user)->getJson('api/favorite');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'preview_image', 'preview_video_link'],
                ],
            ]);

        // Проверяем, что ID фильма совпадает с ID в ресурсе
        expect($response->json('data.0.id'))->toBe($films->last()->id);
    });

    it('корректно фильтрует избранные фильмы по жанру', function () {
        $genre = Genre::factory()->create();

        $filmWithGenre = Film::factory()->create();
        $filmWithGenre->genres()->attach($genre);

        $filmWithoutGenre = Film::factory()->create();

        $this->user->favoriteFilms()->attach([$filmWithGenre->id, $filmWithoutGenre->id]);

        // Отправляем запрос с фильтром по ID жанра
        $response = $this->actingAs($this->user)->getJson("api/favorite?genre={$genre->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $filmWithGenre->id);
    });

    it('возвращает ошибку валидации, если передан несуществующий жанр', function () {
        $response = $this->actingAs($this->user)->getJson('api/favorite?genre=99999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['genre']);
    });
});

describe('POST api/films/{film}/favorite (store)', function () {

    it('блокирует добавление в избранное для неавторизованных пользователей', function () {
        $film = Film::factory()->create();

        $response = $this->postJson("api/films/{$film->id}/favorite");

        $response->assertStatus(401);
        expect($this->user->favoriteFilms()->count())->toBe(0);
    });

    it('успешно добавляет фильм в избранное', function () {
        $film = Film::factory()->create();

        $response = $this->actingAs($this->user)->postJson("api/films/{$film->id}/favorite");

        $response->assertStatus(200)
            ->assertJsonPath('data.message', 'Фильм добавлен');

        $this->assertDatabaseHas('film_user', [
            'user_id' => $this->user->id,
            'film_id' => $film->id,
        ]);
    });

    it('возвращает ошибку 422, если фильм уже находится в избранном', function () {
        $film = Film::factory()->create();
        $this->user->favoriteFilms()->attach($film);

        $response = $this->actingAs($this->user)->postJson("api/films/{$film->id}/favorite");

        $response->assertStatus(422);
        expect($response->json('message'))->toBe('Фильм уже добавлен в избранное');
    });

    it('возвращает ошибку 404 при попытке добавить несуществующий фильм', function () {
        $response = $this->actingAs($this->user)->postJson('api/films/99999/favorite');

        $response->assertStatus(404);
    });
});

describe('DELETE api/films/{film}/favorite (destroy)', function () {

    it('блокирует удаление из избранного для неавторизованных пользователей', function () {
        $film = Film::factory()->create();

        $response = $this->deleteJson("api/films/{$film->id}/favorite");

        $response->assertStatus(401);
    });

    it('успешно удаляет фильм из избранного', function () {
        $film = Film::factory()->create();
        $this->user->favoriteFilms()->attach($film);

        $response = $this->actingAs($this->user)->deleteJson("api/films/{$film->id}/favorite");

        $response->assertStatus(200)
            ->assertJsonPath('data.message', 'Фильм убран из избранного');

        $this->assertDatabaseMissing('film_user', [
            'user_id' => $this->user->id,
            'film_id' => $film->id,
        ]);
    });

    it('возвращает ошибку 422, если пользователь пытается удалить фильм, которого нет в избранном', function () {
        $film = Film::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("api/films/{$film->id}/favorite");

        $response->assertStatus(422);
        expect($response->json('message'))->toBe('Фильм не находится в избранном');
    });
});
