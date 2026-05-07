<?php

namespace App\Repository;

interface RepositoryInterface
{
    public function getMovieByImdbId(string $movieId): array;
}
