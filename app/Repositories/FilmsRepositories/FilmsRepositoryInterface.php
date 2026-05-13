<?php

namespace App\Repositories\FilmsRepositories;

interface FilmsRepositoryInterface
{
    public function getFilmByImdbId(string $imdbId): array;
}
