<?php

namespace App\Http\Controllers;

use App\Http\Responses\Fail;
use App\Http\Responses\Success;
use Illuminate\Http\Request;

class FavoriteController extends Controller
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }
}
