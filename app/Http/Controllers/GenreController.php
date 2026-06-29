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
     * Возвращает список всех жанров из БД.
     *
     * @return Success Формат ответа.
     */
    public function index(): Success
    {
        $genres = Genre::all();

        return new Success($genres->toArray());
    }

    /**
     * Обновляет информацию о жанре.
     *
     * @param  UpdateGenreRequest  $request  Запрос из формы.
     * @param  Genre  $genre  Жанр.
     * @return Success Формат ответа.
     */
    public function update(UpdateGenreRequest $request, Genre $genre): Success
    {
        $genre->update($request->validated());

        return new Success($genre->toArray());
    }
}
