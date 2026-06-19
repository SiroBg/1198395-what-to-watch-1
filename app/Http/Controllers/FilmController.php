<?php

namespace App\Http\Controllers;

use App\Actions\SaveFilmAction;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\FilmIndexRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Http\Resources\FilmPreviewResource;
use App\Http\Responses\Success;
use App\Jobs\ProcessFilmImport;
use App\Models\Film;
use App\Models\Promo;
use App\Queries\FetchFilmsQuery;
use App\Queries\GetFilmWithMetadataQuery;
use App\Queries\GetSimilarFilmsQuery;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilmIndexRequest $request, FetchFilmsQuery $query)
    {
        $films = $query->execute($request->validated());

        return new Success(FilmPreviewResource::collection($films));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateFilmRequest $request)
    {
        $film = Film::create([
            'imdb_id' => $request['imdb_id'],
        ]);

        ProcessFilmImport::dispatch($request['imdb_id']);

        return new Success($film->toArray(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film, GetFilmWithMetadataQuery $query)
    {
        $userId = auth('sanctum')->id();

        $filmResource = $query->execute($film->id, $userId);

        return new Success($filmResource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilmRequest $request, Film $film, SaveFilmAction $action)
    {
        $film = $request->save($film, $action);

        $film->load(['actors', 'directors', 'genres']);

        return new Success($film->toArray());
    }

    public function similar(Film $film, GetSimilarFilmsQuery $query)
    {
        $films = $query->execute($film);

        return new Success($films);
    }

    public function promo(GetFilmWithMetadataQuery $query)
    {
        $promo = Promo::firstOrFail();
        $userId = auth('sanctum')->id();

        $filmResource = $query->execute($promo->film_id, $userId);

        return new Success($filmResource);
    }

    public function setPromo(Film $film)
    {
        Promo::truncate();

        $promo = Promo::create(
            ['film_id' => $film->id],
        );

        return new Success($promo, 201);
    }
}
