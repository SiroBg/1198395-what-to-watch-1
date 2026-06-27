<?php

namespace App\Queries;

use App\Models\Film;
use Illuminate\Database\Eloquent\Collection;

final class GetFilmCommentsQuery
{
    /**
     * Возвращает отзывы к фильму.
     *
     * @param  Film  $film  Фильм.
     */
    public function execute(Film $film): Collection
    {
        return $film->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
