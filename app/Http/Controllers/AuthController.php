<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Responses\Fail;
use App\Http\Responses\Success;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        try {
            $role = Role::where('name', 'user')->firstOrCreate();

            $params = $request->safe()->except('file');

            $user = User::create($params);

            $user->roles()->attach($role->id);

            $token = $user->createToken('auth-token');

            return new Success([
                'user' => $user,
                'token' => $token->plainTextToken,
            ], 201);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->validated())) {
                abort(401, trans('auth.failed'));
            }

            $token = Auth::user()->createToken('auth-token');

            return new Success(['token' => $token->plainTextToken]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    public function logout()
    {
        try {
            Auth::user()->tokens()->delete();

            return new Success(['message' => 'Logged out']);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }
}
