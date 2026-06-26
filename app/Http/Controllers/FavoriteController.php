<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteFilmsRequest;
use App\Http\Resources\FilmPreviewResource;
use App\Http\Responses\Success;
use App\Models\Film;
use App\Queries\FetchFilmsQuery;
use Illuminate\Support\Facades\Auth;

/**
 * @psalm-api
 */
class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FavoriteFilmsRequest $request, FetchFilmsQuery $query)
    {
        $validated = $request->validated();

        $filters = array_merge([
            'order_by' => 'pivot_created_at',
            'order_to' => 'desc',
        ], $validated);

        $films = $query->execute($filters, $request->user()->favoriteFilms()->getQuery());

        $filmsResources = FilmPreviewResource::collection($films);

        return new Success($filmsResources);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Film $film)
    {
        $user = Auth::user();

        $alreadyFavorited = $user->favoriteFilms()->where('film_id', $film->id)->exists();

        if ($alreadyFavorited) {
            abort(422, 'Фильм уже добавлен в избранное');
        }

        $user->favoriteFilms()->attach($film);

        return new Success(['message' => 'Фильм добавлен']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Film $film)
    {
        $user = Auth::user();

        $alreadyFavorited = $user->favoriteFilms()->where('film_id', $film->id)->exists();

        if (!$alreadyFavorited) {
            abort(422, 'Фильм не находится в избранном');
        }

        $user->favoriteFilms()->detach($film->id);

        return new Success(['message' => 'Фильм убран из избранного']);
    }
}
