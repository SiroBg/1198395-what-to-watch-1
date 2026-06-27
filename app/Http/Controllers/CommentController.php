<?php

namespace App\Http\Controllers;

use App\Actions\DeleteCommentAction;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Responses\Success;
use App\Models\Comment;
use App\Models\Film;
use App\Queries\GetFilmCommentsQuery;

/**
 * @psalm-api
 */
class CommentController extends Controller
{
    /**
     * Создаёт отзыв о фильме или комментарий на существующий отзыв.
     *
     * @param CreateCommentRequest $request Запрос из формы.
     * @param Film                 $film Фильм.
     *
     * @return Success Формат ответа.
     */
    public function store(CreateCommentRequest $request, Film $film)
    {
        $comment = Comment::create([
            'film_id'    => $film->id,
            'user_id'    => $request->user()->id,
            'text'       => $request->safe()->text,
            'rating'     => $request->safe()->rating,
            'comment_id' => $request->safe()->comment_id,
        ]);

        return new Success(new CommentResource($comment), 201);
    }

    /**
     * Возвращает отзывы к фильму.
     *
     * @param Film                 $film Фильм.
     * @param GetFilmCommentsQuery $query Запрос.
     *
     * @return Success Формат ответа.
     */
    public function show(Film $film, GetFilmCommentsQuery $query): Success
    {
        $comments = $query->execute($film);

        return new Success(CommentResource::collection($comments));
    }

    /**
     * Обновляет комментарий или отзыв к фильму.
     *
     * @param UpdateCommentRequest $request Запрос из формы.
     * @param Comment              $comment Комментарий.
     *
     * @return Success Формат ответа.
     */
    public function update(
        UpdateCommentRequest $request,
        Comment $comment
    ): Success {
        $this->authorize('update', $comment);

        $comment->update([
            'text'   => $request->safe()->text,
            'rating' => $comment->comment_id ? null : $request->safe()->rating,
        ]);

        return new Success(new CommentResource($comment), 201);
    }

    /**
     * Удаляет комментарий или отзыв к фильму.
     *
     * @param Comment             $comment Комментарий.
     * @param DeleteCommentAction $action Действие.
     *
     * @return Success Формат ответа.
     * @throws \Throwable
     */
    public function destroy(
        Comment $comment,
        DeleteCommentAction $action
    ): Success {
        $this->authorize('delete', $comment);

        $action->execute($comment);

        return new Success(['message' => 'Комментарий удалён.']);
    }
}
