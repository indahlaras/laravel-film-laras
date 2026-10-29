<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Film;
use App\Models\Kritik;
use App\Interfaces\MovieRepositoryInterface;
use App\Classes\ApiResponseClass;
use App\Http\Resources\MovieResource;

class MovieController extends Controller
{
    private MovieRepositoryInterface $movieRepositoryInterface;

    public function __construct(MovieRepositoryInterface $movieRepositoryInterface)
    {
        $this->movieRepositoryInterface = $movieRepositoryInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = $this->movieRepositoryInterface->index();

        return ApiResponseClass::sendResponse(MovieResource::collection($data), '', 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'sinopsis' => ['required', 'string'],
            'year' => ['required', 'integer', 'between:1888,2100'],
            'poster' => ['required', 'string', 'max:2048'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
        ]);

        $film = Film::create($validated);

        return ApiResponseClass::sendResponse(MovieResource::make($film), 'Movie created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $film = Film::findOrFail($id);

        return ApiResponseClass::sendResponse(MovieResource::make($film), '', 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $film = Film::findOrFail($id);
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'sinopsis' => ['sometimes', 'required', 'string'],
            'year' => ['sometimes', 'required', 'integer', 'between:1888,2100'],
            'poster' => ['sometimes', 'required', 'string', 'max:2048'],
            'genre_id' => ['sometimes', 'required', 'integer', 'exists:genres,id'],
        ]);

        $film->update($validated);

        return ApiResponseClass::sendResponse(MovieResource::make($film->fresh()), 'Movie updated successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $film = Film::findOrFail($id);

        if ($film->perans()->exists() || Kritik::where('film_id', $film->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This movie cannot be deleted while cast credits or reviews are attached.',
            ], 409);
        }

        $film->delete();

        return ApiResponseClass::sendResponse(null, 'Movie deleted successfully.', 200);
    }
}
