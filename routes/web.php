<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\{
    FilmController,
};

Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/login', fn () => redirect()->route('home'))->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/register', [AuthenticatedSessionController::class, 'register'])->middleware('guest')->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::view('/admin/dashboard', 'admin.dashboard')
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');
