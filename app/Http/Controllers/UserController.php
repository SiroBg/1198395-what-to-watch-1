<?php

namespace App\Http\Controllers;

use App\Actions\UpdateUserAction;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\Success;
use Illuminate\Support\Facades\Auth;

/**
 * @psalm-api
 */
class UserController extends Controller
{
    /**
     * Возвращает информацию об авторизованном пользователе.
     *
     * @return Success Формат ответа.
     */
    public function show(): Success
    {
        $user = Auth::user()->load('roles');

        return new Success(new UserResource($user));
    }

    /**
     * Обновляет информацию об авторизованном пользователе.
     *
     * @param UpdateUserRequest $request Запрос из формы.
     * @param UpdateUserAction  $action  Действие.
     *
     * @return Success Формат ответа.
     */
    public function update(
        UpdateUserRequest $request,
        UpdateUserAction $action
    ): Success {
        $user = Auth::user()->load('roles');

        $updatedUser = $action->execute(
            $user,
            $request->safe()->except('file'),
            $request->file('file'),
        );

        return new Success(new UserResource($updatedUser));
    }
}
