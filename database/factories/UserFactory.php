<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Пароль, используемый фабрикой.
     */
    protected static ?string $password;

    /** {@inheritdoc} */
    #[\Override]
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'file'              => fake()->imageUrl(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Уточняет, что почта пользователя не должна быть верифицированной.
     *
     * @psalm-api
     */
    public function unverified(): static
    {
        return $this->state(fn(array $_attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
