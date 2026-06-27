<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateGenreRequest extends FormRequest
{
    /**
     * Проверяет авторизацию пользователя.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
