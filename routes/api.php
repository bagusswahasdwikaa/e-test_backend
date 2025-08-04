<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/nilai-peserta', [NilaiPesertaController::class, 'index']);

// Resource ujian
Route::apiResource('ujians', UjianController::class);
Route::post('/soals', [SoalController::class, 'store']);
Route::post('/soals/bulk', [SoalController::class, 'storeBulk']);
Route::get('/soals/by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']);

Route::post('/peserta', [PesertaController::class, 'store'])->name('peserta.store');
Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('peserta.show');
Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Daftar peserta
    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta.index');
   
});
