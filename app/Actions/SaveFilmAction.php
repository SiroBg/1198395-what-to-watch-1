<?php

namespace App\Actions;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SaveFilmAction
{
    public function execute(Film $film, array $data): Film
    {
        return DB::transaction(function () use ($film, $data) {
            $actorIds = $this->createOrGetIds($data['starring'] ?? [], Actor::class);
            $directorIds = $this->createOrGetIds($data['directors'] ?? [], Director::class);
            $genreIds = $this->createOrGetIds($data['genre'] ?? [], Genre::class);

            $film->fill(Arr::except($data, ['starring', 'directors', 'genre']))->save();

            $film->actors()->sync($actorIds);
            $film->directors()->sync($directorIds);
            $film->genres()->sync($genreIds);

            return $film;
        });
    }

    private function createOrGetIds(array $arrayName, string $modelClass)
    {
        return collect($arrayName)
            ->map(
                fn ($value) =>
                $modelClass::firstOrCreate(['name' => $value])->id,
            );
    }
}
