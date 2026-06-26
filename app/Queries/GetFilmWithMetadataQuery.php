<?php

namespace App\Queries;

use App\Http\Resources\FilmResource;
use App\Models\Film;

class GetFilmWithMetadataQuery
{
    /**
     * Получить фильм со всеми агрегатами, связями и флагом "избранное" для пользователя.
     */
    public function execute(int $filmId, int|string|null $userId = null): FilmResource
    {
        $film = Film::query()
            ->withRating()
            ->withCount('comments as scores_count')
            ->with(['actors', 'directors', 'genres'])
            ->withIsFavorite($userId)
            ->whereKey($filmId)
            ->firstOrFail();

        return new FilmResource($film);
    }
}
