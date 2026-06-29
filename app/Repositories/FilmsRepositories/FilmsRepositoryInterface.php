<?php

namespace App\Repositories\FilmsRepositories;

interface FilmsRepositoryInterface
{
    /**
     * Получает информацию о фильме по imdb id.
     *
     * @param  string  $imdbId  Imdb id.
     */
    public function getFilmByImdbId(string $imdbId): array;
}
