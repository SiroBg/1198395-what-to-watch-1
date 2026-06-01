<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Responses\Fail;
use App\Http\Responses\Success;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        try {
            $user = Auth::user()->load('roles');

            return new Success([
                'name' => $user->name,
                'email' => $user->email,
                'file' => $user->file,
                'role' => $user->roles->first()?->name,
            ]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request)
    {
        try {
            $user = Auth::user()->load('roles');

            $params = $request->safe()->except('file');

            if ($request->hasFile('file')) {
                $params['file'] = $request
                    ->file('file')
                    ->store('avatars', 'public');
            }

            $user->update($params);

            return new Success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'file' => $user->file,
                'role' => $user->roles->first()?->name,
            ]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }
}
