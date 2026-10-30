<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'filmCount' => Film::count(),
            'genreCount' => Genre::count(),
            'userCount' => User::count(),
            'recentFilms' => Film::with('genre')->latest()->take(5)->get(),
            'recentUsers' => User::with('role')->latest()->take(5)->get(),
        ]);
    }
}
