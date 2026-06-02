<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
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

        return new Success([
            'name' => $user->name,
            'email' => $user->email,
            'file' => $user->file,
            'role' => $user->roles->first()?->name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request)
    {
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
    }
}
