<?php

namespace App\Jobs;

use App\Actions\SaveFilmAction;
use App\Models\Film;
use App\Repositories\FilmsRepositories\FilmsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessFilmImport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Количество попыток выполнения задачи при сбое (например, если API временно недоступно)
     */
    public $tries = 3;

    /**
     * Создать новый экземпляр задачи.
     */
    public function __construct(
        protected string $imdbId,
    ) {
    }

    /**
     * Выполнить задачу.
     */
    public function handle(FilmsRepositoryInterface $filmRepository, SaveFilmAction $saveFilmAction): void
    {
        $externalData = $filmRepository->getFilmByImdbId($this->imdbId);

        if (!$externalData) {
            Log::warning("Фильм с IMDb ID {$this->imdbId} не найден во внешнем сервисе.");
            return;
        }

        $film = Film::firstOrNew(['imdb_id' => $this->imdbId]);

        $saveFilmAction->execute($film, $externalData);

        Log::info("Фильм с IMDb ID {$this->imdbId} успешно импортирован/обновлен в БД.");
    }
}
