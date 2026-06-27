<?php

namespace App\Http\Requests;

use App\Models\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateFilmRequest extends FormRequest
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
            'imdb_id' => [
                'required',
                'regex:/^tt\d+$/',
                Rule::unique(Film::class, 'imdb_id')
            ],
        ];
    }
}
