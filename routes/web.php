<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalaiAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes — must stay outside auth/role middleware,
// otherwise a logged-out user could never reach /login.
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Laporan Masuk Bencana
        Route::get('/dashboard', [DashboardController::class, 'laporanMasukBencana'])->name('laporan.masuk-bencana');
        Route::get('/laporan/{laporan}', [DashboardController::class, 'show'])->name('laporan.show');
        Route::delete('/laporan/{laporan}', [DashboardController::class, 'destroy'])->name('laporan.destroy');

        // Laporan Penanganan Balai
        Route::get('/laporan-penanganan-balai', [DashboardController::class, 'laporanPenangananBalai'])->name('laporan-penanganan-balai');
        Route::get('/laporan-penanganan-balai/{laporan}', [DashboardController::class, 'lpbshow'])->name('laporan-penanganan-balai.show');

        // Data PIC Balai
        Route::get('/data-pic-balai', [DashboardController::class, 'dataPicBalai'])->name('data.pic-balai');
    });

Route::get('/balai/login', [BalaiAuthController::class, 'login'])->name('balai.login');
Route::post('/balai/login', [BalaiAuthController::class, 'authenticate'])->name('balai.login.authenticate');
 
Route::middleware('auth:balai')->group(function () {
        Route::post('/balai/logout', [BalaiAuthController::class, 'logout'])->name('balai.logout');
        Route::get('/balai/dashboard', [BalaiAuthController::class, 'balaiDashboard'])->name('balai.dashboard');
    });
});