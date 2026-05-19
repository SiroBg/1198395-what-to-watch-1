<?php

namespace App\Http\Controllers;

use App\Http\Responses\Fail;
use App\Http\Responses\Success;
use Illuminate\Http\Request;

class FilmController extends Controller
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
     * Display the specified resource.
     */
    public function show(string $id)
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

    public function similar(string $id)
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    public function promo()
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }

    public function setPromo(string $id)
    {
        try {
            return new Success([]);
        } catch (\Throwable $e) {
            return new Fail($e);
        }
    }
}
