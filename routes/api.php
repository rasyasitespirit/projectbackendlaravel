<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelangganDataController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\PenyewaanDetailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES (Tidak perlu JWT token)
// ============================================

// Auth - Login only
Route::post('/auth/login', [AuthController::class, 'login']);

// Fallback route untuk handle unauthenticated (Laravel redirect ke sini)
Route::any('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Unauthenticated. Silakan login melalui POST /api/auth/login',
        'data' => null
    ], 401);
})->name('login');

// ============================================
// PROTECTED ROUTES (Perlu JWT token - Admin Only)
// ============================================
Route::group(['middleware' => 'auth:api'], function () {
    
    // Auth Routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Pelanggan Routes (Semua perlu auth - data pribadi)
    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::post('/pelanggan', [PelangganController::class, 'store']);
    Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show']);
    Route::patch('/pelanggan/{pelanggan}', [PelangganController::class, 'update']);
    Route::delete('/pelanggan/{pelanggan}', [PelangganController::class, 'destroy']);

    // Pelanggan Data Routes (Semua perlu auth - data sensitif)
    Route::apiResource('pelanggan-data', PelangganDataController::class);

    // Admin Routes (Admin only)
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin', [AdminController::class, 'store']);
    Route::get('/admin/{admin}', [AdminController::class, 'show']);
    Route::patch('/admin/{admin}', [AdminController::class, 'update']);
    Route::delete('/admin/{admin}', [AdminController::class, 'destroy']);

    // Penyewaan Routes (Admin only)
    Route::apiResource('penyewaan', PenyewaanController::class);

    // Kategori Routes (Admin only)
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::get('/kategori/{kategori}', [KategoriController::class, 'show']);
    Route::patch('/kategori/{kategori}', [KategoriController::class, 'update']);
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy']);

    // Alat Routes (Admin only)
    Route::get('/alat', [AlatController::class, 'index']);
    Route::post('/alat', [AlatController::class, 'store']);
    Route::get('/alat/{alat}', [AlatController::class, 'show']);
    Route::patch('/alat/{alat}', [AlatController::class, 'update']);
    Route::delete('/alat/{alat}', [AlatController::class, 'destroy']);

    // Penyewaan Detail Routes (Admin only)
    Route::get('/penyewaan-detail', [PenyewaanDetailController::class, 'index']);
    Route::post('/penyewaan-detail', [PenyewaanDetailController::class, 'store']);
    Route::get('/penyewaan-detail/{penyewaan_detail}', [PenyewaanDetailController::class, 'show']);
    Route::patch('/penyewaan-detail/{penyewaan_detail}', [PenyewaanDetailController::class, 'update']);
    Route::delete('/penyewaan-detail/{penyewaan_detail}', [PenyewaanDetailController::class, 'destroy']);
});
