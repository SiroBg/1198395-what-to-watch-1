<?php

namespace App\Http\Requests;

use App\Enums\FilmStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FilmIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'genre' => ['sometimes', 'nullable', 'integer', Rule::exists('genres', 'id')],
            'status' => ['sometimes', Rule::enum(FilmStatus::class)],
            'order_by' => ['sometimes', Rule::in(['released','rating'])],
            'order_to' => ['sometimes', Rule::in(['asc', 'desc'])] ,
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $status = $this->input('status');

            if (
                in_array($status, [FilmStatus::PENDING->value, FilmStatus::MODERATION->value])
                && (!$this->user() || !$this->user()->hasRole('moderator'))
            ) {
                $validator->errors()->add(
                    'status',
                    'Недостаточно прав для просмотра данного статуса.',
                );
            }
        });
    }
}
