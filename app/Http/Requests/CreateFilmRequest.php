<?php

namespace App\Http\Requests;

use App\Models\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFilmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'imdb_id' => ['required', 'regex:/^tt\d+$/', Rule::unique(Film::class, 'imdb_id')],
        ];
    }
}
