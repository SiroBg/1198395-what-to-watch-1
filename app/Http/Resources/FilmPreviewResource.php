<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Film $film */
        $film = $this->resource;

        return [
            'id' => $film->id,
            'name' => $film->name,
            'preview_image' => $film->preview_image,
            'preview_video_link' => $film->preview_video_link,
        ];
    }
}
