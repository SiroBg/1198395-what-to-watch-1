<?php

namespace App\Actions;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

final class DeleteCommentAction
{
    /**
     * Удаляет комментарий и ответы под ним.
     * @param Comment $comment Комментарий для удаления.
     *
     * @return void
     * @throws \Throwable
     */
    public function execute(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $comment->replies()->delete();
            $comment->delete();
        });
    }
}
