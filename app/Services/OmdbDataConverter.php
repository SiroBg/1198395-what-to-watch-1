<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class OmdbDataConverter
{
    public function convert(array $omdbData): array
    {
        return [
            'name' => $omdbData['Title'] ?? null,
            'description' => $omdbData['Plot'] ?? null,
            'released' => $this->parseReleaseYear($omdbData['Released'] ?? $omdbData['Year'] ?? null),
            'poster_image' => $omdbData['Poster'] ?? null,

            'starring' => $this->parseOmdbString($omdbData['Actors'] ?? ''),
            'directors' => $this->parseOmdbString($omdbData['Director'] ?? ''),
            'genres' => $this->parseOmdbString($omdbData['Genre'] ?? ''),
            'run_time' => $this->parseRuntime($omdbData['Runtime'] ?? null),
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
        if (! $date || $date === 'N/A') {
            return null;
        }

        try {
            return (int) Carbon::parse($date)->format('Y');
        } catch (\Exception $e) {
            if (preg_match('/\b\d{4}\b/', $date, $matches)) {
                return (int) $matches[0];
            }

            return null;
        }
    }

    private function parseRuntime(?string $runtime): ?int
    {
        return (int) Str::before($runtime, ' ');
    }
}
