<?php

namespace App\Http\Controllers;

use App\Http\Responses\Fail;
use App\Http\Responses\Success;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }
}
