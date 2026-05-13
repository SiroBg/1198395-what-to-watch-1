<?php

namespace App\Services;

use App\Repositories\FilmsRepositories\FilmsRepositoryInterface;

class MovieService
{
    public function __construct(
        private FilmsRepositoryInterface $repository,
    ) {
    }

    public function getMovie(string $imdbId): array
    {
        return $this->repository->getFilmByImdbId($imdbId);
    }
}
