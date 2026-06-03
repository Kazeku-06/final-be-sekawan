<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelangganDataController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PenyewaanDetailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — CV. Amanah Elektronik
|--------------------------------------------------------------------------
|
| Semua route selain login dilindungi oleh JWT middleware (jwt.auth).
|
*/

// ─── Public: Authentication ───────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// ─── Protected: JWT required ─────────────────────────────────────────────────
Route::middleware('jwt.auth')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('me',      [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh',[AuthController::class, 'refresh']);
    });

    // Kategori
    Route::apiResource('kategori', KategoriController::class)->parameters([
        'kategori' => 'id',
    ]);

    // Alat
    Route::apiResource('alat', AlatController::class)->parameters([
        'alat' => 'id',
    ]);

    // Pelanggan
    Route::apiResource('pelanggan', PelangganController::class)->parameters([
        'pelanggan' => 'id',
    ]);

    // Pelanggan Data (Upload Identitas)
    Route::get('pelanggan-data',       [PelangganDataController::class, 'index']);
    Route::post('pelanggan-data',      [PelangganDataController::class, 'store']);
    Route::get('pelanggan-data/{id}',  [PelangganDataController::class, 'show']);
    Route::delete('pelanggan-data/{id}', [PelangganDataController::class, 'destroy']);

    // Penyewaan (Transaksi)
    Route::apiResource('penyewaan', PenyewaanController::class)->parameters([
        'penyewaan' => 'id',
    ]);

    // Penyewaan Detail (read-only, dibuat otomatis saat transaksi)
    Route::get('penyewaan-detail',      [PenyewaanDetailController::class, 'index']);
    Route::get('penyewaan-detail/{id}', [PenyewaanDetailController::class, 'show']);
});
