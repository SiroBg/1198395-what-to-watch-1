<?php

namespace Database\Factories;

use App\Models\Director;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Director>
 *
 * @psalm-api
 */
class DirectorFactory extends Factory
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
