<?php

namespace App\Actions;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class DeleteCommentAction
{
    public function execute(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $comment->replies()->delete();
            $comment->delete();
        });
    }
}
