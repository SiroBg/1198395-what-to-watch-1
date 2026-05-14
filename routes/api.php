<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', [UserController::class, 'show']);
Route::patch('/user', [UserController::class, 'update']);
Route::post('/logout', [UserController::class, 'logout']);

Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{id}', [FilmController::class, 'show']);
Route::get('/films/{id}/similar', [FilmController::class, 'similar']);
Route::patch('/films/{id}', [FilmController::class, 'update']);
Route::post('/films', [FilmController::class, 'store']);
Route::get('/promo', [FilmController::class, 'promo']);
Route::post('/promo/{id}', [FilmController::class, 'setPromo']);

Route::get('/genres', [GenreController::class, 'index']);
Route::patch('/genres/{genre}', [GenreController::class, 'update']);

Route::get('/favorite', [FavoriteController::class, 'index']);
Route::post('/films/{id}/favorite', [FavoriteController::class, 'store']);
Route::delete('/films/{id}/favorite', [FavoriteController::class, 'destroy']);

Route::get('/comments/{id}', [CommentController::class, 'show']);
Route::post('/comments/{id}', [CommentController::class, 'show']);
Route::patch('/comments/{comment}', [CommentController::class, 'update']);
Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
