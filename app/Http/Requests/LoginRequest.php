<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    /**
     * Проверяет авторизацию пользователя.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ];
    }
}
