<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    /**
     * Возвращает правильный формат информации о пользователе.
     *
     * @param Request $request Запрос.
     *
     * @return array
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'file'  => $user->file,
            'role'  => $user->roles->first()?->name,
        ];
    }
}
