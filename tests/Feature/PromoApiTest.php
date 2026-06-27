<?php

use App\Models\Film;
use App\Models\Promo;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->moderatorRole = Role::firstOrCreate([
        'name' => 'moderator',
    ]);

    $this->userRole = Role::firstOrCreate([
        'name' => 'user',
    ]);
});

function moderator(): User
{
    $user = User::factory()->create();

    $user->roles()->attach(test()->moderatorRole);

    return $user;
}

function regularUser(): User
{
    $user = User::factory()->create();

    $user->roles()->attach(test()->userRole);

    return $user;
}

describe('GET api/promo (promo)', function () {
    it('возвращает промо фильм', function () {
        $film = Film::factory()->create();

        Promo::create([
            'film_id' => $film->id,
        ]);

        getJson('/api/promo')
            ->assertOk()
            ->assertJsonPath('data.id', $film->id);
    });

    it('возвращает 404, если промо фильма нету', function () {
        getJson('/api/promo')
            ->assertNotFound();
    });
});

describe('POST api/promo/{film} (setPromo)', function () {
    it('модератор может установить промо фильм', function () {
        $film = Film::factory()->create();

        Sanctum::actingAs(moderator());

        postJson("/api/promo/{$film->id}")
            ->assertCreated()
            ->assertJsonPath('data.film_id', $film->id);

        $this->assertDatabaseHas('promos', [
            'film_id' => $film->id,
        ]);

        expect(Promo::count())->toBe(1);
    });

    it('меняет промо фильм', function () {
        $oldFilm = Film::factory()->create();
        $newFilm = Film::factory()->create();

        Promo::create([
            'film_id' => $oldFilm->id,
        ]);

        Sanctum::actingAs(moderator());

        postJson("/api/promo/{$newFilm->id}")
            ->assertCreated();

        expect(Promo::count())->toBe(1);

        $this->assertDatabaseHas('promos', [
            'film_id' => $newFilm->id,
        ]);

        $this->assertDatabaseMissing('promos', [
            'film_id' => $oldFilm->id,
        ]);
    });

    it('обычный пользователь не может менять промо фильм', function () {
        $film = Film::factory()->create();

        Sanctum::actingAs(regularUser());

        postJson("/api/promo/{$film->id}")
            ->assertForbidden();

        expect(Promo::count())->toBe(0);
    });

    it('возвращает ошибку 401, если гость меняет промо фильм',
    function () {
        $film = Film::factory()->create();

        postJson("/api/promo/{$film->id}")
            ->assertUnauthorized();

        expect(Promo::count())->toBe(0);
    });
});
