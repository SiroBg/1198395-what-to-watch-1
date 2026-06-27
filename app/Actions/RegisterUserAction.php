<?php

namespace App\Actions;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RegisterUserAction
{
    /**
     * Регистрирует пользователя, выдает роль и генерирует токен доступа.
     *
     * @return array ['user' => User, 'token' => string]
     * @throws \Throwable
     */
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $role = Role::firstOrCreate(['name' => 'user']);

            $user = User::create($data);

            $user->roles()->attach($role->id);

            $token = $user->createToken('auth-token')->plainTextToken;

            return [
                'user'  => $user,
                'token' => $token,
            ];
        });
    }
}
