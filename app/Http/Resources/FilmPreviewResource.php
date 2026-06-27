<?php

namespace App\Http\Resources;

use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FilmPreviewResource extends JsonResource
{
    /**
     * Возвращает правильный формат превью фильма.
     *
     * @param Request $request Запрос.
     *
     * @return array
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        /** @var Film $film */
        $film = $this->resource;

        return [
            'id'                 => $film->id,
            'name'               => $film->name,
            'preview_image'      => $film->preview_image,
            'preview_video_link' => $film->preview_video_link,
        ];
    }
}
