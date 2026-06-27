<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

describe('GET api/user (show)', function () {
    it(
        'возвращает информацию об авторизированном пользователе',
        function () {
            $role = Role::create([
                'name' => 'user',
            ]);

            $user = User::factory()->create();

            $user->roles()->attach($role);

            Sanctum::actingAs($user);

            getJson('/api/user')
                ->assertOk()
                ->assertJsonPath('data.email', $user->email)
                ->assertJsonPath('data.name', $user->name);
        }
    );

    it('возвращает ошибку 404 для гостя', function () {
        getJson('/api/user')
            ->assertUnauthorized();
    });
});

describe('PATCH api/user (update)', function () {
    it('обновляет информацию пользователя', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        patchJson('/api/user', [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.email', 'new@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    });

    it('обновляет пароль пользователя', function () {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        Sanctum::actingAs($user);

        patchJson('/api/user', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertOk();

        expect(
            Hash::check('newpassword123', $user->fresh()->password)
        )->toBeTrue();
    });

    it('обновляет аватар пользователя', function () {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        patchJson('/api/user', [
            'name' => $user->name,
            'email' => $user->email,
            'file' => $file,
        ])
            ->assertOk();

        expect($user->fresh()->file)->not->toBeNull();

        Storage::disk('public')->assertExists(
            $user->fresh()->file
        );
    });

    it('удаляет предыдущий аватар при загрузке нового', function () {
        Storage::disk('public')->put('avatars/old.jpg', 'old');

        $user = User::factory()->create([
            'file' => 'avatars/old.jpg',
        ]);

        Sanctum::actingAs($user);

        patchJson('/api/user', [
            'name' => $user->name,
            'email' => $user->email,
            'file' => UploadedFile::fake()->image('new.jpg'),
        ])
            ->assertOk();

        Storage::disk('public')->assertMissing('avatars/old.jpg');

        Storage::disk('public')->assertExists(
            $user->fresh()->file
        );
    });

    it('возвращает ошибку валидации, если email существует', function () {
        User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        patchJson('/api/user', [
            'name' => $user->name,
            'email' => 'taken@example.com',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    });

    it('возвращает ошибку 401 для гостя', function () {
        patchJson('/api/user', [
            'name' => 'John',
            'email' => 'john@example.com',
        ])
            ->assertUnauthorized();
    });
});
