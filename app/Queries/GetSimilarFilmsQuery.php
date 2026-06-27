<?php

namespace App\Queries;

use App\Enums\FilmStatus;
use App\Http\Resources\FilmPreviewResource;
use App\Models\Film;

final class GetSimilarFilmsQuery
{
    /**
     * Возвращает информацию о похожих фильмах по переданному.
     *
     * @param Film $film Фильм.
     *
     * @return array
     */
    public function execute(Film $film): array
    {
        $filmRandomGenreId = $film->genres()->inRandomOrder()->first()->id;

        $films = Film::query()
            ->genre($filmRandomGenreId)
            ->status(FilmStatus::READY->value)
            ->sorting('released', 'desc')
            ->limit(4)->get();

        return FilmPreviewResource::collection($films)->resolve();
    }
}
