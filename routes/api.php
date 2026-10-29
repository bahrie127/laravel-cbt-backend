<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\UjianController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
| API app peserta (flutter_cbt_app). Semua respons JSON; tanpa login → 401.
*/

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => UserResource::make($request->user()));
    Route::post('/logout', [AuthController::class, 'logout']);

    // Ujian
    Route::get('/ujian', [UjianController::class, 'aktif']);
    Route::post('/create-ujian', [UjianController::class, 'createUjian']);
    Route::get('/get-soal-ujian', [UjianController::class, 'getListSoalByKategori']);
    Route::post('/answers', [UjianController::class, 'jawabSoal']);
    Route::get('/get-nilai', [UjianController::class, 'hitungNilaiUjianByKategori']);
    Route::get('/hasil-ujian', [UjianController::class, 'hasil']);

    // Konten (Tentang Kami = id 1, Tips & Trik = id 2) & materi — hanya baca dari app.
    Route::get('/contents', [ContentController::class, 'index']);
    Route::get('/materis', [MateriController::class, 'index']);
});
