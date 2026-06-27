<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /** {@inheritdoc} */
    #[\Override]
    public function definition(): array
    {
        return [
            'text'       => fake()->paragraph(),
            'rating'     => fake()->numberBetween(1, 10),
            'user_id'    => User::factory(),
            'film_id'    => Film::factory(),
            'comment_id' => null,
        ];
    }

    /**
     * @psalm-api
     */
    public function guest(): CommentFactory
    {
        return $this->state(fn(array $_attributes) => [
            'user_id' => null,
        ]);
    }
}
