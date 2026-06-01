<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $films = Film::all();

        foreach ($films as $film) {
            $comments = Comment::factory(rand(3, 10))->create([
                'film_id' => $film->id,
                'user_id' => $users->random()->id,
            ]);

            foreach ($comments as $comment) {
                Comment::factory(rand(0, 3))->create([
                    'film_id' => $film->id,
                    'user_id' => $users->random()->id,
                    'comment_id' => $comment->id,
                    'rating' => null,
                ]);
            }
        }
    }
}
