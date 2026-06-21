<?php

namespace App\Http\Controllers;

use App\Actions\UpdateUserAction;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\Success;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user()->load('roles');

        return new Success(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, UpdateUserAction $action)
    {
        $user = Auth::user()->load('roles');

        $updatedUser = $action->execute(
            $user,
            $request->safe()->except('file'),
            $request->file('file'),
        );

        return new Success(new UserResource($updatedUser));
    }
}
