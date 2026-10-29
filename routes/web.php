<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HasilUjianController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Admin\MateriController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
| Panel pengelola (admin & staf). Login lewat Fortify (/login), peserta memakai app.
*/

Route::get('/', fn () => auth()->check() ? redirect()->route('home') : redirect()->route('login'));

Route::middleware(['auth', 'pengelola'])->group(function () {
    Route::get('home', DashboardController::class)->name('home');

    Route::resource('soal', SoalController::class)->except('show');
    Route::resource('materi', MateriController::class)->except('show');
    Route::resource('konten', KontenController::class)->only(['index', 'edit', 'update']);
    Route::resource('hasil', HasilUjianController::class)->only(['index', 'show'])->parameters(['hasil' => 'ujian']);

    Route::resource('user', UserController::class)->except('show')->middleware('can:kelola-pengguna');
});
