<?php

namespace Database\Factories;

use App\Models\Actor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Actor>
 *
 * @psalm-api
 */
class ActorFactory extends Factory
{
    /** {@inheritdoc} */
    #[\Override]
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
        ];
    }
}
