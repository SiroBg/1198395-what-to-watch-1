<?php

use App\Jobs\ProcessFilmImport;
use App\Models\Genre;
use App\Models\Film;
use App\Repositories\FilmsRepositories\FilmsRepositoryInterface;
use Illuminate\Support\Facades\Queue;

test('задача успешно парсит данные о фильме, сохраняет в бд и ставит задачу в очередь', function () {
    Queue::fake();

    $genre = Genre::factory()->create(['name' => 'Sci-Fi']);

    $imdbId = 'tt3896198';
    $mockApiResponse = [
        'Title' => 'Guardians of the Galaxy Vol. 2',
        'Year' => '2017',
        'Runtime' => '136 min',
        'imdbID' => $imdbId,
        'Response' => 'True'
    ];

    $repositoryMock = mock(FilmsRepositoryInterface::class);
    $repositoryMock->shouldReceive('getFilmByImdbId')
        ->once()
        ->with($imdbId)
        ->andReturn($mockApiResponse);

    $this->app->instance(FilmsRepositoryInterface::class, $repositoryMock);

    $job = new ProcessFilmImport($imdbId);
    app()->call([$job, 'handle']);

    $this->assertDatabaseHas('films', [
        'imdb_id' => $imdbId,
        'name' => 'Guardians of the Galaxy Vol. 2',
        'run_time' => 136,
    ]);
});
