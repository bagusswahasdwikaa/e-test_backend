<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ListPesertaController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserUjianController;
use App\Http\Controllers\HasilUjianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\RankController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua endpoint untuk aplikasi e-test.
| Otentikasi via guard "api" (JWT / Passport / Sanctum).
*/

// ======================================================
// ===============  PUBLIC ROUTES  =======================
// ======================================================

// AUTH
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/dashboard', [DashboardController::class, 'index']);

// ======================================================
// ===============  PROTECTED ROUTES  ===================
// ======================================================
Route::middleware('auth:api')->group(function () {

    // === AUTH ===
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // === PROFILE ===
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'update']);
        Route::delete('/photo', [ProfileController::class, 'deletePhoto']);
    });

    // === UJIAN (ADMIN CRUD + PESERTA AKSES) ===
    Route::prefix('ujians')->group(function () {
        Route::get('/', [UjianController::class, 'index']);             // List semua ujian
        Route::post('/', [UjianController::class, 'store']);            // Tambah ujian
        Route::get('{id}', [UjianController::class, 'show']);           // Detail ujian
        Route::put('{id}', [UjianController::class, 'update']);         // Update ujian
        Route::delete('{id}', [UjianController::class, 'destroy']);     // Hapus ujian

        // Assign ujian ke user
        Route::post('{ujian}/assign', [UjianController::class, 'assignToUsers']);
        Route::get('{ujian}/users', [UjianController::class, 'assignedUsers']);

        // Ambil soal berdasarkan ujian
        Route::get('{ujian_id}/soals', [SoalController::class, 'getByUjianId']);

        // Clone ujian
        Route::post('{id}/clone', [UjianController::class, 'clone']);

        // Ranking peserta (aman, tidak public)
        Route::get('/ranking/{ujianId}', [RankController::class, 'getRankByUjian']);
    });

    // === SOAL ===
    Route::prefix('soals')->group(function () {
        Route::post('bulk', [SoalController::class, 'storeBulk']);                    // Import soal massal
        Route::get('by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']);   // Ambil soal by ujian
    });
    Route::apiResource('soals', SoalController::class)->except(['index']);

    // === PESERTA ===
    Route::apiResource('peserta', PesertaController::class);
    Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
    Route::apiResource('list-peserta', ListPesertaController::class);
    Route::post('/peserta/import', [PesertaController::class, 'importExcel'])->name('peserta.import');

    // === NILAI PESERTA ===
    Route::prefix('nilai-peserta')->group(function () {
        Route::get('/', [NilaiPesertaController::class, 'index']);
        Route::get('/peserta/{id}', [NilaiPesertaController::class, 'riwayatByUser']);
        Route::get('/export', [NilaiPesertaController::class, 'export']);
    });

    // === HASIL UJIAN ===
    Route::get('/hasil-ujian', [HasilUjianController::class, 'index'])->name('hasil-ujian.index');

    // === USER UJIAN (Peserta) ===
    Route::prefix('user/ujians')->group(function () {
        Route::get('/', [UserUjianController::class, 'index']);                         
        Route::get('{id}', [UserUjianController::class, 'show']);                       
        Route::post('{id}/kerjakan', [UserUjianController::class, 'kerjakan']);         
        Route::get('{id}/soal', [UserUjianController::class, 'getSoalUjian']);          
        Route::post('{id}/jawaban', [UserUjianController::class, 'simpanJawaban']);     
        Route::post('{id}/submit', [UserUjianController::class, 'submitUjian']);        
        Route::post('{id}/ulang', [UserUjianController::class, 'ulangUjian']);          
        Route::get('{id}/hasil', [UserUjianController::class, 'hasilUjian']);           
        Route::get('{id}/riwayat', [UserUjianController::class, 'getRiwayatUjian']);    
    });

    // === ADMIN - Kirim email ke peserta ===
    Route::post('/admin/ujians/{idUjian}/kirim/{userId}', [UserUjianController::class, 'kirimEmail']);

    // === SERTIFIKAT ===
    Route::prefix('sertifikat')->group(function () {
        Route::get('/', [SertifikatController::class, 'index']);           
        Route::get('/{id}', [SertifikatController::class, 'show']);        
        Route::get('/{id}/download', [SertifikatController::class, 'download']); 
    });
});

// === ALIAS OPSIONAL (public list ujian untuk front jika perlu)
Route::get('/daftar-ujian', [UjianController::class, 'index'])->name('ujians.list');
