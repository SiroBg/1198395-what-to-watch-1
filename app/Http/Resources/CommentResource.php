<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CommentResource extends JsonResource
{
    /**
     * Возвращает правильный формат комментария.
     *
     * @param  Request  $request  Запрос.
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        /** @var Comment $comment */
        $comment = $this->resource;

        return [
            'id' => $comment->id,
            'user_id' => $comment->user_id,
            'film_id' => $comment->film_id,
            'comment_id' => $comment->comment_id,
            'created_at' => $comment->created_at,
            'updated_at' => $comment->updated_at,
            'text' => $comment->text,
            'rating' => $comment->rating,
            'author' => $comment->author_name,
        ];
    }
}
