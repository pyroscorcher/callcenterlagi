<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalaiAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'laporanMasukBencana'])->name('laporan.masuk-bencana');
        Route::get('/laporan/{laporan}', [DashboardController::class, 'show'])->name('laporan.show');
        Route::delete('/laporan/{laporan}', [DashboardController::class, 'destroyLaporan'])->name('laporan.destroy');
        Route::get('/laporan/{laporan}/edit', [DashboardController::class, 'edit'])->name('laporan.edit');
        Route::put('/laporan/{laporan}',[DashboardController::class, 'update'])->name('laporan.update');
        Route::get('/laporan/{id}/edit-lokasi', [DashboardController::class, 'editLokasi'])->name('laporan.edit-lokasi');
        Route::put('/laporan/{id}/update-lokasi', [DashboardController::class, 'updateLokasi'])->name('laporan.update-lokasi');


        Route::get('/laporan-penanganan-balai', [DashboardController::class, 'LPB'])->name('laporan-penanganan-balai');
        Route::get('/laporan-penanganan-balai/{laporan}', [DashboardController::class, 'LPBShow'])->name('laporan-penanganan-balai.show');

        Route::get('/data-pic-balai/create', [DashboardController::class, 'createBalai'])->name('balai.create');
        Route::get('/data-pic-balai', [DashboardController::class, 'databalai'])->name('data.pic-balai');
        Route::get('/data-pic-balai/{balai}', [DashboardController::class, 'balaiShow'])->name('data.pic-balai-show');
        Route::post('/data-pic-balai', [DashboardController::class, 'storeBalai'])->name('balai.store');
        Route::get('/data-pic-balai/{balai}/edit', [DashboardController::class, 'editBalai'])->name('balai.edit');
        Route::put('/data-pic-balai/{balai}', [DashboardController::class, 'updateBalai'])->name('balai.update');
        Route::delete('/data-pic-balai/{balai}', [DashboardController::class, 'destroyBalai'])->name('balai.destroy');
    });
});

Route::get('/balai/login', [BalaiAuthController::class, 'login'])->name('balai.login');
Route::post('/balai/login', [BalaiAuthController::class, 'authenticate'])->name('balai.login.authenticate');

Route::middleware('auth:balai')->group(function () {
    Route::post('/balai/logout', [BalaiAuthController::class, 'logout'])->name('balai.logout');
    Route::get('/balai/dashboard', [DashboardController::class, 'balaiDashboard'])->name('balai.dashboard');
});