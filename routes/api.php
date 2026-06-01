<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'show']);
    Route::patch('/user', [UserController::class, 'update']);
});

Route::controller(FilmController::class)->group(function () {
    Route::get('/films', 'index');
    Route::get('/films/{id}', 'show');
    Route::get('/films/{id}/similar', 'similar');
    Route::patch('/films/{id}', 'update');
    Route::post('/films', 'store');
    Route::get('/promo', 'promo');
    Route::post('/promo/{id}', 'setPromo');
});

Route::controller(GenreController::class)->group(function () {
    Route::get('/genres', 'index');
    Route::patch('/genres/{genre}', 'update');
});

Route::controller(FavoriteController::class)->group(function () {
    Route::get('/favorite', 'index');
    Route::post('/films/{id}/favorite', 'store');
    Route::delete('/films/{id}/favorite', 'destroy');
});

Route::controller(CommentController::class)->group(function () {
    Route::get('/comments/{id}', 'show');
    Route::post('/comments/{id}', 'show');
    Route::patch('/comments/{comment}', 'update');
    Route::delete('/comments/{comment}', 'destroy');
});
