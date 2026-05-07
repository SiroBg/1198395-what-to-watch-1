<?php

namespace App\Service;

use App\Repository\RepositoryInterface;

class MovieService
{
    public function __construct(
        private RepositoryInterface $repository,
    ) {
    }

    public function getMovie(string $imdbId): array
    {
        return $this->repository->getMovieByImdbId($imdbId);
    }
}
