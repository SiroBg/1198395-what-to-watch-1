<?php

namespace App\Http\Controllers;

use App\Http\Resources\FilmPreviewResource;
use App\Http\Responses\Success;
use App\Models\Film;
use Illuminate\Support\Facades\Auth;

/**
 * @psalm-api
 */
class FavoriteController extends Controller
{
    /**
     * Возвращает список избранных фильмов.
     *
     * @return Success Формат ответа.
     */
    public function index(): Success
    {
        $user = Auth::user();

        $films = $user
            ->favoriteFilms()
            ->orderByPivot('created_at', 'desc')
            ->paginate(8);

        $filmsResources = FilmPreviewResource::collection($films);

        return new Success($filmsResources);
    }

    /**
     * Добавляет фильм в избранное.
     *
     * @param  Film  $film  Фильм.
     * @return Success Формат ответа.
     */
    public function store(Film $film): Success
    {
        $user = Auth::user();

        $alreadyFavorited = $user->favoriteFilms()
            ->where('film_id', $film->id)
            ->exists();

        if ($alreadyFavorited) {
            abort(422, 'Фильм уже добавлен в избранное');
        }

        $user->favoriteFilms()->attach($film);

        return new Success(['message' => 'Фильм добавлен']);
    }

    /**
     * Удаляет фильм из избранного.
     *
     * @param  Film  $film  Фильм.
     * @return Success Формат ответа.
     */
    public function destroy(Film $film): Success
    {
        $user = Auth::user();

        $alreadyFavorited = $user->favoriteFilms()
            ->where('film_id', $film->id)
            ->exists();

        if (! $alreadyFavorited) {
            abort(422, 'Фильм не находится в избранном');
        }

        $user->favoriteFilms()->detach($film->id);

        return new Success(['message' => 'Фильм убран из избранного']);
    }
}
