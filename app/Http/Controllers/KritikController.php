<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Kritik;
use App\Http\Requests\StoreKritikRequest;
use App\Http\Requests\UpdateKritikRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KritikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreKritikRequest $request, Film $film): RedirectResponse
    {
        $request->user()->kritiks()->create([
            ...$request->validated(),
            'film_id' => $film->id,
        ]);

        return redirect()->route('movies.show', $film)->with('status', 'Komentar berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kritik $kritik)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kritik $kritik)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKritikRequest $request, Kritik $kritik): RedirectResponse
    {
        $kritik->update($request->validated());

        return redirect()->route('movies.show', $kritik->film)->with('status', 'Komentar berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Kritik $kritik): RedirectResponse
    {
        abort_unless(
            $request->user()->role?->name === 'admin' || $kritik->user_id === $request->user()->id,
            403
        );

        $film = $kritik->film;
        $kritik->delete();

        if ($request->routeIs('admin.comments.destroy')) {
            return redirect()->to(route('admin.dashboard').'#komentar')->with('status', 'Komentar berhasil dihapus.');
        }

        return redirect()->route('movies.show', $film)->with('status', 'Komentar berhasil dihapus.');
    }
}
