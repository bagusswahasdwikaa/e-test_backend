<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NilaiPesertaController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\SoalController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::get('/nilai-peserta', [NilaiPesertaController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/{id}', [PesertaController::class, 'show'])->name('peserta.show');
    Route::get('/peserta/{id}/edit', [PesertaController::class, 'edit'])->name('peserta.edit');
    Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');
});


Route::apiResource('ujians', UjianController::class);
Route::get('/ujians', [UjianController::class, 'index']);

Route::post('/soals', [SoalController::class, 'store']);
Route::post('/soals/bulk', [SoalController::class, 'storeBulk']);
Route::get('/soals/by-ujian/{ujian_id}', [SoalController::class, 'getByUjianId']);