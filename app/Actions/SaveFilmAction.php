<?php

namespace App\Actions;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Film;
use App\Models\Genre;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class SaveFilmAction
{
    /**
     * Сохраняет фильм в БД.
     *
     * @param Film $film Модель фильма.
     * @param array $data Данные о фильме из формы.
     *
     * @return Film
     * @throws \Throwable
     */
    public function execute(Film $film, array $data): Film
    {
        return DB::transaction(function () use ($film, $data) {
            $actorIds = $this->createOrGetIds(
                $data['starring'] ?? [],
                Actor::class
            );
            $directorIds = $this->createOrGetIds(
                $data['directors'] ?? [],
                Director::class
            );
            $genreIds = $this->createOrGetIds(
                $data['genre'] ?? [],
                Genre::class
            );

            $film->fill(Arr::except($data, ['starring', 'directors', 'genre']))
                ->save();

            $film->actors()->sync($actorIds);
            $film->directors()->sync($directorIds);
            $film->genres()->sync($genreIds);

            return $film;
        });
    }

    /**
     * Создаёт или получает id моделей в БД и возвращает коллекцию этих моделей.
     *
     * @param array  $arrayName Массив значений.
     * @param string $modelClass Имя класса.
     *
     * @return \Illuminate\Support\Collection
     */
    private function createOrGetIds(
        array $arrayName,
        string $modelClass
    ): \Illuminate\Support\Collection {
        return collect($arrayName)
            ->map(
                fn($value) => $modelClass::firstOrCreate(['name' => $value]
                )->id,
            );
    }
}
