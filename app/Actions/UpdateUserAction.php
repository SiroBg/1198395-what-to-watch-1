<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class UpdateUserAction
{
    /**
     * Обновляет информацию о пользователе.
     *
     * @param  User  $user  Пользователь.
     * @param  array  $data  Данные формы.
     * @param  UploadedFile|null  $file  Аватарка.
     */
    public function execute(
        User $user,
        array $data,
        ?UploadedFile $file = null
    ): User {
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
