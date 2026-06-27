<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\Success;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @psalm-api
 */
class AuthController extends Controller
{
    /**
     * Регистрирует пользователя.
     *
     * @param  RegisterUserRequest  $request  Запрос из формы.
     * @param  RegisterUserAction  $action  Действие.
     * @return Success Формат ответа.
     *
     * @throws \Throwable
     */
    public function register(
        RegisterUserRequest $request,
        RegisterUserAction $action
    ): Success {
        $result = $action->execute($request->safe()->except('file'));

        return new Success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    /**
     * Логинит пользователя.
     *
     * @param  LoginRequest  $request  Запрос из формы.
     * @return Success Формат ответа.
     */
    public function login(LoginRequest $request): Success
    {
        if (! Auth::attempt($request->validated())) {
            abort(401, trans('auth.failed'));
        }

        $token = Auth::user()->createToken('auth-token');

        return new Success(['token' => $token->plainTextToken]);
    }

    /**
     * Разлогинивает пользователя.
     *
     * @return Success Формат ответа.
     */
    public function logout(): Success
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var PersonalAccessToken|null $token */
        $token = $user->currentAccessToken();

        $token?->delete();

        return new Success(['message' => 'Logged out']);
    }
}
