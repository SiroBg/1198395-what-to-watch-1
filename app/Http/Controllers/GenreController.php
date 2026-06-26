<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGenreRequest;
use App\Http\Responses\Success;
use App\Models\Genre;

/**
 * @psalm-api
 */
class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Genre::all();

        return new Success($genres->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return new Success($genre->toArray());
    }
}
