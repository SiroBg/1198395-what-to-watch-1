<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'poster_image' => $this->poster_image,
            'preview_image' => $this->preview_image,
            'background_image' => $this->background_image,
            'background_color' => $this->background_color,
            'video_link' => $this->video_link,
            'preview_video_link' => $this->preview_video_link,
            'description' => $this->description,
            'rating' => round($this->rating, 1),
            'scores_count' => $this->scores_count,
            'directors' => $this->directors->pluck('name')->toArray(),
            'starring' => $this->actors->pluck('name')->toArray(),
            'run_time' => $this->run_time,
            'genres' => $this->genres->pluck('name')->toArray(),
            'released' => $this->released,
            'is_favorite' => (bool) ($this->is_favorite ?? false),
        ];
    }
}
