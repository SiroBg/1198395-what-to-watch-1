<?php

namespace App\Http\Controllers;

use App\Enums\FilmStatus;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\FilmIndexRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Http\Resources\FilmPreviewResource;
use App\Http\Resources\FilmResource;
use App\Http\Responses\Success;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Promo;
use Illuminate\Support\Arr;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilmIndexRequest $request)
    {
        $validated = $request->validated();

        $films = Film::query()
            ->genre($validated['genre'] ?? null)
            ->status($validated['status'] ?? null)
            ->sorting($validated['order_by'] ?? 'released', $validated['order_to'] ?? 'desc')
            ->paginate(8);

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

        return new Success($film->toArray(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Film $film)
    {
        $filmResource = $this->getFilmResource($film->id);

        return new Success($filmResource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilmRequest $request, Film $film)
    {
        $validated = $request->validated();

        $actorIds = $this->createOrGetIds($validated['starring'] ?? [], Actor::class);
        $directorIds = $this->createOrGetIds($validated['directors'] ?? [], Director::class);
        $genreIds = $this->createOrGetIds($validated['genre'] ?? [], Genre::class);

        $film->update(Arr::except($validated, ['starring', 'directors', 'genre']));

        $film->actors()->sync($actorIds);
        $film->directors()->sync($directorIds);
        $film->genres()->sync($genreIds);

        $film->load(['actors', 'directors', 'genres']);

        return new Success($film->toArray());
    }

    public function similar(Film $film)
    {
        $filmRandomGenreId = $film->genres()->inRandomOrder()->first()->id;

        $films = Film::query()
            ->genre($filmRandomGenreId)
            ->status(FilmStatus::READY->value)
            ->sorting('released', 'desc')
            ->limit(4)->get();

        $filmsResources = FilmPreviewResource::collection($films)->resolve();

        return new Success($filmsResources);
    }

    public function promo()
    {
        $promo = Promo::firstOrFail();

        $filmResource = $this->getFilmResource($promo->film_id);

        return new Success($filmResource);
    }

    public function setPromo(Film $film)
    {
        Promo::truncate();

        Promo::create(
            ['film_id' => $film->id],
        );

        $filmResource = $this->getFilmResource($film->id);

        return new Success($filmResource, 201);
    }

    private function createOrGetIds(array $arrayName, string $modelClass)
    {
        return collect($arrayName)
            ->map(
                fn ($value) =>
                $modelClass::firstOrCreate(['name' => $value])->id,
            );
    }

    private function getFilmResource(int $filmId)
    {
        $userId = auth('sanctum')->id();

        $film = Film::query()
            ->withRating()
            ->withCount('comments as scores_count')
            ->with(['actors', 'directors', 'genres'])
            ->whereKey($filmId)
            ->withIsFavorite($userId)
            ->firstOrFail();

        return new FilmResource($film);
    }
}
