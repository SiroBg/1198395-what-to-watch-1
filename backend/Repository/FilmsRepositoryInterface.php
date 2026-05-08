<?php

namespace App\FilmsRepository;

interface FilmsRepositoryInterface
{
    public function getFilmByImdbId(string $imdbId): array;
}
