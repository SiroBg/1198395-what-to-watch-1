<?php

namespace App\Http\Controllers;

use App\Http\Responses\Fail;
use App\Http\Responses\Success;
use Illuminate\Http\Request;

class CommentController extends Controller
{
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
