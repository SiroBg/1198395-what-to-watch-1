<?php

namespace App\Http\Controllers;

use App\Enums\FilmStatus;
use App\Http\Requests\CreateFilmRequest;
use App\Http\Requests\FilmIndexRequest;
use App\Http\Requests\UpdateFilmRequest;
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

        $films = Film::query()->select('id', 'name', 'preview_image', 'preview_video_link')
            ->genre($validated['genre'] ?? null)
            ->status($validated['status'] ?? FilmStatus::READY->value)
            ->sorting($validated['order_by'] ?? 'released', $validated['order_to'] ?? 'desc')
            ->paginate(8);

        return new Success($films->toArray());
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
        $film = $this->getFilmInfo($film->id);

        return new Success($film->toArray());
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

        $film->load(['actors', 'directors', 'genre']);

        return new Success($film->toArray());
    }

    public function similar(Film $film)
    {
        $films = Film::query()->select('id', 'name', 'preview_image', 'preview_video_link')
            ->genre($film->genres()->inRandomOrder()->value('id'))
            ->status(FilmStatus::READY->value)
            ->sorting('released', 'desc')
            ->limit(4);

        return new Success($films->toArray());
    }

    public function promo()
    {
        $promo = Promo::firstOrFail();

        $film = $this->getFilmInfo($promo->film_id);

        return new Success($film->toArray());
    }

    public function setPromo(Film $film)
    {
        Promo::updateOrCreate(
            ['id' => 1],
            ['film_id' => $film->id],
        );

        return new Success($this->getFilmInfo($film->id)->toArray(), 201);
    }

    private function createOrGetIds(array $arrayName, string $modelClass)
    {
        return collect($arrayName)
            ->map(
                fn ($value) =>
                $modelClass::firstOrCreate(['name' => $value])->id,
            );
    }

    private function getFilmInfo(int $id)
    {
        return Film::query()
            ->withAggregate('comments as rating', 'avg', 'rating')
            ->withCount('comments as scores_count')
            ->with(['actors', 'directors', 'genres'])
            ->whereKey($id)
            ->firstOrFail();
    }
}
