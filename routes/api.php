<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;

// ========== AUTH ==========
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ========== NILAI ==========
Route::get('/nilai-peserta', [NilaiPesertaController::class, 'index']);

// ========== UJIAN ==========
Route::apiResource('ujians', UjianController::class); // includes GET, POST, PUT, DELETE
Route::put('/ujians/{id}', [UjianController::class, 'update']);
// ========== SOAL ==========
Route::apiResource('soals', SoalController::class)->except(['index']); // soals don't need index

// Tambahan custom routes soal
Route::post('/soals/bulk', [SoalController::class, 'storeBulk']);
Route::get('/soals/by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']);
Route::get('/ujians/{ujian_id}/soals', [SoalController::class, 'getByUjianId']); // alias route

// ========== PESERTA ==========
Route::post('/peserta', [PesertaController::class, 'store'])->name('peserta.store');
Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('peserta.show');
Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta.index');
});
