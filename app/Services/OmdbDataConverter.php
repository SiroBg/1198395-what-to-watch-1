<?php

namespace App\Services\Converters;

use Illuminate\Support\Str;

class OmdbDataConverter
{
    /**
     * Трансформирует формат OMDb под формат вашей БД.
     */
    public function convert(array $omdbData): array
    {
        return [
            'title' => $omdbData['Title'] ?? null,
            'description' => $omdbData['Plot'] ?? null,
            'releasedDate' => $this->parseReleaseYear($omdbData['Released'] ?? $omdbData['Year'] ?? null),
            'poster' => $omdbData['Poster'] ?? null,
            'rating' => $omdbData['imdbRating'] ?? null,

            // Превращаем строки через запятую в чистые массивы
            'starring' => $this->parseOmdbString($omdbData['Actors'] ?? ''),
            'directors' => $this->parseOmdbString($omdbData['Director'] ?? ''),
            'genre' => $this->parseOmdbString($omdbData['Genre'] ?? ''),
        ];
    }

    private function parseOmdbString(string $string): array
    {
        if (empty($string) || $string === 'N/A') {
            return [];
        }

        return Str::of($string)->explode(',')->map(fn ($item) => trim($item))->filter()->toArray();
    }

    private function parseReleaseYear(?string $date): ?int
    {
        if (!$date || $date === 'N/A') {
            return null;
        }

        try {
            return (int) \Carbon\Carbon::parse($date)->format('Y');
        } catch (\Exception $e) {
            if (preg_match('/\b\d{4}\b/', $date, $matches)) {
                return (int) $matches[0];
            }
            return null;
        }
    }
}
