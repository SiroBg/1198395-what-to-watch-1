<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUserAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\Success;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request, RegisterUserAction $action): Success
    {
        $result = $action->execute($request->safe()->except('file'));

        return new Success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request): Success
    {
        if (!Auth::attempt($request->validated())) {
            abort(401, trans('auth.failed'));
        }

        $token = Auth::user()->createToken('auth-token');

        return new Success(['token' => $token->plainTextToken]);
    }

    public function logout(): Success
    {
        Auth::user()->currentAccessToken()?->delete();

        return new Success(['message' => 'Logged out']);
    }
}
