<?php

namespace App\Http\Requests;

use App\Enums\FilmStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class FilmIndexRequest extends FormRequest
{
    /**
     * Проверяет авторизацию пользователя.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'genre' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('genres', 'id'),
            ],
            'status' => ['sometimes', Rule::enum(FilmStatus::class)],
            'order_by' => ['sometimes', Rule::in(['released', 'rating'])],
            'order_to' => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * Валидатор статуса фильма.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $status = $this->input('status');
            $user = auth('sanctum')->user();

            if (
                in_array(
                    $status,
                    [FilmStatus::PENDING->value, FilmStatus::MODERATION->value]
                )
                && (! $user || ! $user->hasRole('moderator'))
            ) {
                $validator->errors()->add(
                    'status',
                    'Недостаточно прав для просмотра данного статуса.',
                );
            }
        });
    }
}
