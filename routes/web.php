<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SertifikatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sertifikat', function () {
    return view('sertifikat.template');
});

Route::get('/sertifikat', [SertifikatController::class, 'showTemplate']);
