<?php

namespace App\Queries;

use App\Models\Film;

class GetFilmWithMetadataQuery
{
    /**
     * Получить фильм со всеми агрегатами, связями и флагом "избранное" для пользователя.
     */
    public function execute(int $filmId, ?int $userId = null): Film
    {
        return Film::query()
            ->withRating()
            ->withCount('comments as scores_count')
            ->with(['actors', 'directors', 'genres'])
            ->withIsFavorite($userId)
            ->whereKey($filmId)
            ->firstOrFail();
    }
}
