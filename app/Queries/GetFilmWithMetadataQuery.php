<?php

namespace App\Queries;

use App\Http\Resources\FilmResource;
use App\Models\Film;

final class GetFilmWithMetadataQuery
{
    /**
     * Возвращает информацию о фильме со всеми необходимыми данными.
     *
     * @param int             $filmId Id фильма.
     * @param int|string|null $userId Id пользователя.
     *
     * @return FilmResource
     */
    public function execute(
        int $filmId,
        int|string|null $userId = null
    ): FilmResource {
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
