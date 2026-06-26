<?php

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('возвращает автора комментария в поле автор', function () {
    $user = User::factory(['name' => 'Иван Иванов'])->create();

    $comment = Comment::factory(['user_id' => $user->id])->create();

    expect($comment->author_name)->toBe('Иван Иванов');
});

test('возвращает гостя как автора комментария без user_id', function () {
    $guestComment = Comment::factory()->guest()->create();

    expect($guestComment->author_name)->toBe('Гость');
});
