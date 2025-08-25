<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua endpoint yang digunakan oleh aplikasi.
| Terdiri dari autentikasi, ujian, soal, peserta, profil, dan email.
*/

// ==================== AUTH ====================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// ==================== NILAI PESERTA ====================
Route::get('/nilai-peserta', [NilaiPesertaController::class, 'index']);
Route::get('/nilai-peserta/export', [NilaiPesertaController::class, 'export']);

// ==================== UJIAN ====================
// CRUD Ujian (index, store, show, update, destroy)
Route::get('/ujians', [UjianController::class, 'index']);       // daftar semua ujian
Route::post('/ujians', [UjianController::class, 'store']);      // tambah ujian baru
Route::get('/ujians/{id}', [UjianController::class, 'show']);   // detail ujian by ID
Route::put('/ujians/{id}', [UjianController::class, 'update']); // update ujian
Route::delete('/ujians/{id}', [UjianController::class, 'destroy']); // hapus ujian

// Alias tambahan untuk endpoint daftar ujian
Route::get('/daftar-ujian', [UjianController::class, 'index'])->name('ujians.list');

// ==================== SOAL ====================
// CRUD Soal (tanpa index, karena ada route khusus by ujian)
Route::apiResource('soals', SoalController::class)->except(['index']);

// Custom routes soal berdasarkan ujian
Route::post('/soals/bulk', [SoalController::class, 'storeBulk']);            // Tambah soal banyak sekaligus
Route::get('/soals/by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']); // Ambil soal per ujian (alias 1)
Route::get('/ujians/{ujian_id}/soals', [SoalController::class, 'getByUjianId']);   // Ambil soal per ujian (alias 2, lebih natural)

// ==================== PESERTA ====================
Route::apiResource('peserta', PesertaController::class);
Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');

// ==================== PROFILE (Login Diperlukan) ====================
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);
});

// ==================== EMAIL ====================
Route::post('/kirim-ujian-email', [EmailController::class, 'send']);
