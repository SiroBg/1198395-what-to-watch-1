<?php

namespace App\Http\Requests;

use App\Actions\SaveFilmAction;
use App\Enums\FilmStatus;
use App\Models\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateFilmRequest extends FormRequest
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
        /** @var Film|null $film */
        $film = $this->route('film');

        return [
            'name'               => ['required', 'string', 'max:255'],
            'poster_image'       => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'preview_image'      => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'background_image'   => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'background_color'   => [
                'sometimes',
                'nullable',
                'regex:/^#[0-9a-fA-F]{6}$/'
            ],
            'video_link'         => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'preview_video_link' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],
            'description'        => [
                'sometimes',
                'nullable',
                'string',
                'max:1000'
            ],
            'directors'          => ['sometimes', 'array'],
            'directors.*'        => ['string', 'max:255'],
            'starring'           => ['sometimes', 'array'],
            'starring.*'         => ['string', 'max:255'],
            'genre'              => ['sometimes', 'array'],
            'genre.*'            => ['string', 'max:255'],
            'run_time'           => ['sometimes', 'nullable', 'integer'],
            'released'           => ['sometimes', 'nullable', 'integer'],
            'imdb_id'            => [
                'required',
                'regex:/^tt\d+$/',
                Rule::unique(Film::class, 'imdb_id')
                    ->ignore($film->id)
            ],
            'status'             => ['required', Rule::enum(FilmStatus::class)],
        ];
    }

    /**
     * Обновляет информацию о фильме.
     *
     * @param Film           $film Фильм.
     * @param SaveFilmAction $saveFilmAction Действие.
     *
     * @return Film
     * @throws \Throwable
     */
    public function save(Film $film, SaveFilmAction $saveFilmAction): Film
    {
        return $saveFilmAction->execute($film, $this->validated());
    }
}
