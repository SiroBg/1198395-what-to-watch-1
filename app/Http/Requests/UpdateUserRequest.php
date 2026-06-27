<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    /**
     * Проверяет авторизацию пользователя.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Правила валидации.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'     => [
                'required',
                'string',
                'max:255',
            ],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(Auth::user()),
            ],
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'confirmed',
            ],
            'file'     => [
                'sometimes',
                'nullable',
                'image',
                'max:10240',
            ],
        ];
    }
}
