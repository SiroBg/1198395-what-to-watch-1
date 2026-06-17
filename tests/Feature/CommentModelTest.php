<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_the_registered_user_name_as_author()
    {
        $user = User::factory()->create();
        $user->name = 'Иван Иванов';

        $comment = Comment::factory()->create();
        $comment->user_id = $user->id;

        $this->assertEquals('Иван Иванов', $comment->author_name);

        $guestComment = Comment::factory()->guest()->create();
        $this->assertEquals('Гость', $guestComment->author_name);
    }
}
