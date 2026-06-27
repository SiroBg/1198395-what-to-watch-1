<?php

namespace Database\Factories;

use App\Enums\FilmStatus;
use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Film>
 *
 * @psalm-api
 */
class FilmFactory extends Factory
{
    /** {@inheritdoc} */
    #[\Override]
    public function definition(): array
    {
        return [
            'name'               => fake()->word(),
            'poster_image'       => fake()->imageUrl(),
            'preview_image'      => fake()->imageUrl(),
            'background_image'   => fake()->imageUrl(),
            'background_color'   => fake()->hexColor(),
            'video_link'         => fake()->url(),
            'preview_video_link' => fake()->url(),
            'description'        => fake()->text(),
            'run_time'           => fake()->numberBetween(1, 1000),
            'released'           => fake()->numberBetween(1930, 2026),
            'imdb_id'            => fake()->regexify('tt[0-9]{7}'),
            'status'             => fake()->randomElement(FilmStatus::cases()),
        ];
    }
}
