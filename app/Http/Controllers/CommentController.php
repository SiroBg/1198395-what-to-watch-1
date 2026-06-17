<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Responses\Success;
use App\Models\Comment;
use App\Models\Film;

class CommentController extends Controller
{
    public function store(CreateCommentRequest $request, Film $film)
    {
        $comment = Comment::create([
            'film_id' => $film->id,
            'user_id' => $request->user()->id,
            'text' => $request->text,
            'rating' => $request->rating,
            'comment_id' => $request->comment_id,
        ]);

        return new Success($comment->toArray(), 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(Film $film)
    {
        $commentResource = CommentResource::collection(
            $film->comments()
                ->orderBy('created_at', 'desc')
                ->get(),
        );

        return new Success($commentResource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update([
            'text' => $request->text,

            'rating' => $comment->comment_id
                ? null
                : $request->rating,
        ]);

        return new Success($comment->toArray(), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->replies()->delete();

        $comment->delete();

        return new Success(['message' => 'Комментарий удалён.']);
    }
}
