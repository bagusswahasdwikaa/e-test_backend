<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailController;

// ========== AUTH ==========
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ========== NILAI ==========
Route::get('/nilai-peserta', [NilaiPesertaController::class, 'index']);
Route::get('/nilai-peserta/export', [NilaiPesertaController::class, 'export']);

// ========== UJIAN ==========
Route::apiResource('ujians', UjianController::class); // includes GET, POST, PUT, DELETE
Route::put('/ujians/{id}', [UjianController::class, 'update']);
// ========== SOAL ==========
Route::apiResource('soals', SoalController::class)->except(['index']); // soals don't need index

// Tambahan custom routes soal
Route::apiResource('soals', SoalController::class)->except(['index']);
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
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);
});

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::apiResource('peserta', PesertaController::class);
Route::post('/kirim-ujian-email', [EmailController::class, 'send']);