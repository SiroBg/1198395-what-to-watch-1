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
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/comments/{film}', [CommentController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentController::class, 'update']);
    Route::get('/favorite', [FavoriteController::class, 'index']);
    Route::post('/films/{film}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/films/{film}/favorite', [FavoriteController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'can:moderator'])->group(function () {
    Route::patch('/genres/{genre}', [GenreController::class, 'update']);
    Route::post('/promo/{film}', [FilmController::class, 'setPromo']);
    Route::post('/films', [FilmController::class, 'store']);
    Route::patch('/films/{film}', [FilmController::class, 'update']);
});

Route::controller(FilmController::class)->group(function () {
    Route::get('/films', 'index');
    Route::get('/films/{film}', 'show');
    Route::get('/films/{film}/similar', 'similar');
    Route::get('/promo', 'promo');
});

Route::get('/genres', [GenreController::class, 'index']);

Route::get('/comments/{film}', [CommentController::class, 'show']);
