<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteFilmsRequest;
use App\Http\Responses\Success;
use App\Models\Film;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FavoriteFilmsRequest $request)
    {
        $validated = $request->validated();

        $films = $request->user()
            ->favoriteFilms()
            ->genre($validated['genre'] ?? null)
            ->orderByPivot('created_at', 'desc')
            ->paginate(8);

        return new Success(['films' => $films]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Film $film)
    {
        $user = Auth::user()->with('films');

        if ($user->favoriteFilms()->where('id', $film->id)->exists()) {
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
        $user = Auth::user()->with('films');

        if (!$user->favoriteFilms()->where('id', $film->id)->exists()) {
            abort(422, 'Фильм не находится в избранном');
        }

        $user->favoriteFilms()->detach($film->id);

        return new Success(['message' => 'Фильм убран из избранного']);
    }
}
