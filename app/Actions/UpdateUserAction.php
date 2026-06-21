<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateUserAction
{
    public function execute(User $user, array $data, ?UploadedFile $file = null): User
    {
        if ($file) {
            if ($user->file) {
                Storage::disk('public')->delete($user->file);
            }

            $data['file'] = $file->store('avatars', 'public');
        }

        $user->update($data);

        return $user;
    }
}
