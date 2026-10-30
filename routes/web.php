<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\KritikController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/login', fn () => redirect()->route('home'))->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/register', [AuthenticatedSessionController::class, 'register'])->middleware('guest')->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/admin/dashboard', DashboardController::class)
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');
Route::delete('/admin/comments/{kritik}', [KritikController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.comments.destroy');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])->middleware('auth')->name('comments.store');
Route::put('/comments/{kritik}', [KritikController::class, 'update'])->middleware('auth')->name('comments.update');
Route::delete('/comments/{kritik}', [KritikController::class, 'destroy'])->middleware('auth')->name('comments.destroy');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');
