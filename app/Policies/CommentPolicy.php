<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

/**
 * @psalm-api
 */
class CommentPolicy
{
    /**
     * Общая логика политики работы с комментариями.
     *
     * @param  User  $user  Пользователь.
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole('moderator')) {
            return true;
        }

        return null;
    }

    /**
     * Политика редактирования комментария/отзыва.
     *
     * @param  User  $user  Пользователь.
     * @param  Comment  $comment  Комментарий.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    /**
     * Политика удаления комментария/отзыва.
     *
     * @param  User  $user  Пользователь.
     * @param  Comment  $comment  Комментарий.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id
            && ! $comment->replies()->exists();
    }
}
