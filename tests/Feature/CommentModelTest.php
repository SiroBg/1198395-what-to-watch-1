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
        $user = User::factory(['name' => 'Иван Иванов'])->create();

        $comment = Comment::factory(['user_id' => $user->id])->create();

        $this->assertEquals('Иван Иванов', $comment->author_name);
    }

    public function test_returns_guest_if_comment_has_no_user_id()
    {
        $guestComment = Comment::factory()->guest()->create();
        $this->assertEquals('Гость', $guestComment->author_name);
    }
}
