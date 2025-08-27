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

// ==================== AUTH ====================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// ==================== NILAI PESERTA ====================
Route::get('/nilai-peserta', [NilaiPesertaController::class, 'index']);
Route::get('/nilai-peserta/export', [NilaiPesertaController::class, 'export']);

// ==================== UJIAN (Admin CRUD) ====================
Route::get('/ujians', [UjianController::class, 'index']);             // daftar semua ujian
Route::post('/ujians', [UjianController::class, 'store']);            // tambah ujian baru
Route::get('/ujians/{id}', [UjianController::class, 'show']);         // detail ujian by ID
Route::put('/ujians/{id}', [UjianController::class, 'update']);       // update ujian
Route::delete('/ujians/{id}', [UjianController::class, 'destroy']);   // hapus ujian

// Alias tambahan (opsional)
Route::get('/daftar-ujian', [UjianController::class, 'index'])->name('ujians.list');

// ==================== SOAL ====================
Route::apiResource('soals', SoalController::class)->except(['index']);
Route::post('/soals/bulk', [SoalController::class, 'storeBulk']);
Route::get('/soals/by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']);
Route::get('/ujians/{ujian_id}/soals', [SoalController::class, 'getByUjianId']);

// ==================== PESERTA ====================
Route::apiResource('peserta', PesertaController::class);
Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');

// ==================== PROFILE + PROTECTED ROUTES (login diperlukan) ====================
Route::middleware('auth:api')->group(function () {
    // ---- Auth Logout ----
    Route::post('/logout', [AuthController::class, 'logout']);

    // ---- Profile ----
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);

    // ---- USER UJIAN (peserta) ----
    Route::get('/user/ujians', [UserUjianController::class, 'index']);              // daftar ujian yang dikirim ke user
    Route::get('/user/ujians/{id}', [UserUjianController::class, 'show']);          // detail ujian user
    Route::post('/user/ujians/{id}/kerjakan', [UserUjianController::class, 'kerjakan']); // submit jawaban ujian

    // ---- ADMIN: kirim ujian ke user lewat email ----
    Route::post('/admin/ujians/{idUjian}/kirim/{userId}', [UserUjianController::class, 'kirimEmail']);
});

// ==================== EMAIL (opsional lama/manual) ====================
Route::post('/kirim-ujian-email', [EmailController::class, 'send']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user/ujians', [UjianController::class, 'userIndex']);
});