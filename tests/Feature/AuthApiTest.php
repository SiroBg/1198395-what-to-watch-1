<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});
describe('POST api/register (register)', function () {
    it('регистрирует пользователя', function () {
        expect(User::count())->toBe(0);

        $response = postJson('/api/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'file'                  => UploadedFile::fake()->image(
                'avatar.jpg'
            ),
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        expect($user)->not->toBeNull()
            ->and($user->roles()->where('name', 'user')->exists())->toBeTrue()
            ->and($user->tokens()->count())->toBe(1);
    });
});
describe('POST api/login (login)', function () {
    it('логинит пользователя', function () {
        $role = Role::create([
            'name' => 'user',
        ]);

        $user = User::factory()->create([
            'email'    => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->roles()->attach($role);

        $response = postJson('/api/login', [
            'email'    => 'john@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'token',
                ],
            ]);

        expect($user->fresh()->tokens()->count())->toBe(1);
    });

    it('возвращает ошибку 401 с неправильными данными', function () {
        User::factory()->create([
            'email'    => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        postJson('/api/login', [
            'email'    => 'john@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    });
});

describe('POST api/logout (logout)', function () {
    it('разлогинивает пользователя', function () {
        $user = User::factory()->create();

        $token = $user->createToken('auth-token');

        postJson('/api/logout', [], [
            'Authorization' => 'Bearer ' . $token->plainTextToken,
        ])
            ->assertOk();

        expect($user->fresh()->tokens()->count())->toBe(0);
    });

    it('возвращает ошибку 401 при попытке гостя разлогиниться',
        function () {
        postJson('/api/logout')
            ->assertUnauthorized();
    });
});
