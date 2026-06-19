<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFilmRequest;
use App\Jobs\ProcessFilmImport;
use Illuminate\Http\JsonResponse;

class FilmImportController extends Controller
{
    public function __invoke(CreateFilmRequest $request): JsonResponse
    {
        $imdbId = $request->validated('imdb_id');

        ProcessFilmImport::dispatch($imdbId);

        return response()->json([
            'message' => 'Задача на импорт фильма успешно добавлена в очередь.',
        ], 202);
    }
}
