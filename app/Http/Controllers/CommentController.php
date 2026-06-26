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
    public function store(CreateCommentRequest $request, Film $film)
    {
        $comment = Comment::create([
            'film_id' => $film->id,
            'user_id' => $request->user()->id,
            'text' => $request->safe()->text,
            'rating' => $request->safe()->rating,
            'comment_id' => $request->safe()->comment_id,
        ]);

        return new Success(new CommentResource($comment), 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Film $film, GetFilmCommentsQuery $query): Success
    {
        $comments = $query->execute($film);

        return new Success(CommentResource::collection($comments));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): Success
    {
        $this->authorize('update', $comment);

        $comment->update([
            'text' => $request->safe()->text,
            'rating' => $comment->comment_id ? null : $request->safe()->rating,
        ]);

        return new Success(new CommentResource($comment), 201);
    }

    public function destroy(Comment $comment, DeleteCommentAction $action): Success
    {
        $this->authorize('delete', $comment);

        $action->execute($comment);

        return new Success(['message' => 'Комментарий удалён.']);
    }
}
