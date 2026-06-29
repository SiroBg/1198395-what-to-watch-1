<?php

namespace App\Jobs;

use App\Actions\SaveFilmAction;
use App\Enums\FilmStatus;
use App\Models\Film;
use App\Repositories\FilmsRepositories\FilmsRepositoryInterface;
use App\Services\OmdbDataConverter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class ProcessFilmImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Создаёт задачу на обработку фильма.
     *
     * @param  string  $imdbId  Imdb id фильма.
     */
    public function __construct(
        protected string $imdbId,
    ) {}

    /**
     * Обращается к репозиторию с фильмами и обновляет информацию о фильме.
     *
     * @param  FilmsRepositoryInterface  $filmRepository  Репозиторий для обращения за информацией о фильме.
     * @param  SaveFilmAction  $saveFilmAction  Действие сохранения фильма.
     * @param  OmdbDataConverter  $converter  Конвертер данных в нужный формат.
     *
     * @throws \Throwable
     */
    public function handle(
        FilmsRepositoryInterface $filmRepository,
        SaveFilmAction $saveFilmAction,
        OmdbDataConverter $converter
    ): void {
        $externalData = $filmRepository->getFilmByImdbId($this->imdbId);

        $convertedData = $converter->convert($externalData);

        $film = Film::firstOrNew(['imdb_id' => $this->imdbId]);
        $film->status = FilmStatus::MODERATION;

        $saveFilmAction->execute($film, $convertedData);

        Log::info(
            "Фильм с IMDb ID {$this->imdbId} успешно импортирован/обновлен в БД."
        );
    }
}
