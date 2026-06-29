<?php

namespace App\Http\Resources;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FilmResource extends JsonResource
{
    /**
     * Возвращает правильный формат фильма.
     *
     * @param  Request  $request  Запрос.
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        /** @var Film $film */
        $film = $this->resource;

        return [
            'id' => $film->id,
            'name' => $film->name,
            'poster_image' => $film->poster_image,
            'preview_image' => $film->preview_image,
            'background_image' => $film->background_image,
            'background_color' => $film->background_color,
            'video_link' => $film->video_link,
            'preview_video_link' => $film->preview_video_link,
            'description' => $film->description,
            'rating' => round($film->rating ?? 0.0, 1),
            'scores_count' => $film->scores_count,
            'directors' => $film->directors->pluck('name')->toArray(),
            'starring' => $film->actors->pluck('name')->toArray(),
            'run_time' => $film->run_time,
            'genres' => $film->genres->pluck('name')->toArray(),
            'released' => $film->released,
            'is_favorite' => (bool) ($film->is_favorite ?? false),
        ];
    }
}
