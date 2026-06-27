<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

final class OmdbDataConverter
{
    /**
     * Создаёт конвертер данных от omdb api.
     *
     * @param array $omdbData Данные.
     *
     * @return array
     */
    public function convert(array $omdbData): array
    {
        return [
            'name'         => $omdbData['Title'] ?? null,
            'description'  => $omdbData['Plot'] ?? null,
            'released'     => $this->parseReleaseYear(
                $omdbData['Released'] ?? $omdbData['Year'] ?? null
            ),
            'poster_image' => $omdbData['Poster'] ?? null,

            'starring'  => $this->parseOmdbString($omdbData['Actors'] ?? ''),
            'directors' => $this->parseOmdbString($omdbData['Director'] ?? ''),
            'genres'    => $this->parseOmdbString($omdbData['Genre'] ?? ''),
            'run_time'  => $this->parseRuntime($omdbData['Runtime'] ?? null),
        ];
    }

    /**
     * Парсит строку в массив.
     *
     * @param string $string Строка.
     *
     * @return array
     */
    private function parseOmdbString(string $string): array
    {
        if (empty($string) || $string === 'N/A') {
            return [];
        }

        $items = Str::of($string)->explode(',')
            ->map(fn($item) => trim($item))
            ->filter()->toArray();

        return array_values($items);
    }

    /**
     * Парсит год выпуска в int.
     *
     * @param string|null $date Дата.
     *
     * @return int|null
     */
    private function parseReleaseYear(?string $date): ?int
    {
        if (!$date || $date === 'N/A') {
            return null;
        }

        try {
            return (int)Carbon::parse($date)->format('Y');
        } catch (\Exception $e) {
            if (preg_match('/\b\d{4}\b/', $date, $matches)) {
                return (int)$matches[0];
            }

            return null;
        }
    }

    /**
     * Парсит длительность фильма в int.
     *
     * @param string|null $runtime
     *
     * @return int|null
     */
    private function parseRuntime(?string $runtime): ?int
    {
        if (!$runtime) {
            return null;
        }

        return (int)Str::before($runtime, ' ');
    }
}
