<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservasiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Admin Auth
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/admin/register', [AuthController::class, 'adminRegister']);

// Admin Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/profile', [AuthController::class, 'adminProfile']);
    Route::put('/admin/profile', [AuthController::class, 'adminUpdateProfile']);
    Route::post('/admin/logout', [AuthController::class, 'adminLogout']);
    
    // Admin data routes
    Route::get('/admin/reservasi', [ReservasiController::class, 'allReservations']);
    Route::get('/admin/pelanggan', [ReservasiController::class, 'allCustomers']);
    Route::get('/admin/dashboard', [ReservasiController::class, 'dashboardStats']);
    Route::put('/admin/reservasi/{id}/status', [ReservasiController::class, 'updateReservationStatus']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/slots/{tanggal}', [ReservasiController::class, 'slots']);

Route::post('/reservasi', [ReservasiController::class, 'store']);
Route::post('/reservasi/upload-bukti', [ReservasiController::class, 'uploadBukti']);
Route::put('/reservasi/{id}/konfirmasi-pembayaran', [ReservasiController::class, 'confirmPayment']);
Route::put('/reservasi/{id}/batal', [ReservasiController::class, 'cancel']);
Route::get('/reservasi/pelanggan', [ReservasiController::class, 'myReservations']);
Route::get('/pelanggan/profile', [ReservasiController::class, 'profile']);
Route::put('/pelanggan/{id}', [ReservasiController::class, 'updateProfile']);

// Di dalam routes/api.php, tambahkan:
Route::apiResource('layanan', LayananController::class);
// Atau manual:
// Route::get('/layanan', [LayananController::class, 'index']);
// Route::get('/layanan/{id}', [LayananController::class, 'show']);
// Route::post('/layanan', [LayananController::class, 'store']);
// Route::put('/layanan/{id}', [LayananController::class, 'update']);
// Route::delete('/layanan/{id}', [LayananController::class, 'destroy']);