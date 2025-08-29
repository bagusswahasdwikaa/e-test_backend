<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\UserUjianController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua endpoint untuk aplikasi (Auth, Ujian, Soal, Peserta, Profil, Email).
| Autentikasi menggunakan guard "api" (misal: Passport atau JWT).
*/

// =========================
// AUTHENTICATION
// =========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// =========================
// UJIAN - ADMIN (CRUD)
// =========================
Route::prefix('ujians')->group(function () {
    Route::get('/', [UjianController::class, 'index']);             // List semua ujian
    Route::post('/', [UjianController::class, 'store']);            // Tambah ujian baru
    Route::get('{id}', [UjianController::class, 'show']);           // Detail ujian
    Route::put('{id}', [UjianController::class, 'update']);         // Update ujian
    Route::delete('{id}', [UjianController::class, 'destroy']);     // Hapus ujian

    // Assign ujian ke user
    Route::post('{ujian}/assign', [UjianController::class, 'assignToUsers']);
    Route::get('{ujian}/users', [UjianController::class, 'assignedUsers']);

    // Ambil soal berdasarkan ujian
    Route::get('{ujian_id}/soals', [SoalController::class, 'getByUjianId']);
});

// Optional alias
Route::get('/daftar-ujian', [UjianController::class, 'index'])->name('ujians.list');

// =========================
// SOAL
// =========================
Route::prefix('soals')->group(function () {
    Route::post('bulk', [SoalController::class, 'storeBulk']);                  // Import soal massal
    Route::get('by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']); // Ambil soal by ujian
});
Route::apiResource('soals', SoalController::class)->except(['index']);

// =========================
// PESERTA
// =========================
Route::apiResource('peserta', PesertaController::class);
Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');

// =========================
// NILAI PESERTA
// =========================
Route::prefix('nilai-peserta')->group(function () {
    Route::get('/', [NilaiPesertaController::class, 'index']);
    Route::get('/export', [NilaiPesertaController::class, 'export']);
});

// =========================
// PROFILE (Auth: Required)
// =========================
Route::middleware('auth:api')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);

    // Ujian untuk user
    Route::prefix('user/ujians')->group(function () {
        Route::get('/', [UserUjianController::class, 'index']);                    // Daftar ujian user
        Route::get('{id}', [UserUjianController::class, 'show']);                 // Detail ujian
        Route::post('{id}/kerjakan', [UserUjianController::class, 'kerjakan']);   // Mulai ujian
        Route::post('{id}/jawaban', [UserUjianController::class, 'simpanJawaban']); // Kirim jawaban
    });

    // Admin kirim ujian lewat email
    Route::post('/admin/ujians/{idUjian}/kirim/{userId}', [UserUjianController::class, 'kirimEmail']);
});

// =========================
// EMAIL (opsional / manual)
// =========================
Route::post('/kirim-ujian-email', [EmailController::class, 'send']);
