<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BalaiAuthController;
use App\Http\Controllers\BalaiController;
use App\Http\Controllers\DashboardLaporanPelaksanaController;
use App\Http\Controllers\DashboardPICBalai;


Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::get('/balai/login', [BalaiAuthController::class, 'login'])->name('balai.login');
Route::post('/balai/login', [BalaiAuthController::class, 'authenticate'])->name('balai.login.authenticate');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/', [DashboardController::class, 'laporanMasukBencana'])->name('laporan.masuk-bencana');
        Route::get('/laporan/{laporan}', [DashboardController::class, 'show'])->name('laporan.show');
        Route::delete('/laporan/{laporan}', [DashboardController::class, 'destroyLaporan'])->name('laporan.destroy');
        Route::get('/laporan/{laporan}/edit', [DashboardController::class, 'edit'])->name('laporan.edit');
        Route::put('/laporan/{laporan}',[DashboardController::class, 'update'])->name('laporan.update');
        Route::get('/laporan/{id}/edit-lokasi', [DashboardController::class, 'editLokasi'])->name('laporan.edit-lokasi');
        Route::put('/laporan/{id}/update-lokasi', [DashboardController::class, 'updateLokasi'])->name('laporan.update-lokasi');
        Route::post('/laporan/{id}/toggle-verifikasi', [DashboardController::class, 'toggleVerifikasi'])->name('laporan.toggle-verifikasi');
        // Ajax routes for dynamic dropdowns
        Route::get('/ajax/kabupaten/{provinsi}', [DashboardController::class, 'getKabupaten']);
        Route::get('/ajax/kecamatan/{kabupaten}', [DashboardController::class, 'getKecamatan']);
        Route::get('/ajax/kelurahan/{kecamatan}', [DashboardController::class, 'getKelurahan']);
        Route::get('/ajax/balai/{provinsi_id}', [DashboardController::class, 'getBalaiByProvinsi']);


        Route::get('/laporan-penanganan-balai', [DashboardLaporanPelaksanaController::class, 'LPB'])->name('laporan-penanganan-balai');
        Route::get('/laporan-penanganan-balai/{laporan}', [DashboardLaporanPelaksanaController::class, 'LPBShow'])->name('laporan-penanganan-balai.show');

        Route::get('/data-pic-balai/create', [DashboardPICBalai::class, 'createBalai'])->name('balai.create');
        Route::get('/data-pic-balai', [DashboardPICBalai::class, 'databalai'])->name('data.pic-balai');
        Route::get('/data-pic-balai/{balai}', [DashboardPICBalai::class, 'balaiShow'])->name('data.pic-balai-show');
        Route::post('/data-pic-balai', [DashboardPICBalai::class, 'storeBalai'])->name('balai.store');
        Route::get('/data-pic-balai/{balai}/edit', [DashboardPICBalai::class, 'editBalai'])->name('balai.edit');
        Route::put('/data-pic-balai/{balai}', [DashboardPICBalai::class, 'updateBalai'])->name('balai.update');
        Route::delete('/data-pic-balai/{balai}', [DashboardPICBalai::class, 'destroyBalai'])->name('balai.destroy');

        // route blasting balai
        Route::post('/laporan/{laporan}/kirim-pic', [DashboardController::class, 'kirimPicNotifikasi'])->name('laporan.kirim-pic');
    });
});

Route::middleware('auth:balai')->group(function () {
    Route::post('/balai/logout', [BalaiAuthController::class, 'logout'])->name('balai.logout');
    Route::get('/balai/dashboard', [BalaiController::class, 'balaiDashboard'])->name('balai.dashboard');
    Route::get('/balai/laporan-penanganan-balai', [BalaiController::class, 'laporanPenanganan'])->name('balai.laporan-penanganan-balai');
    Route::get('/balai/laporan-penanganan-balai/create', [BalaiController::class, 'laporanPenangananCreate'])->name('balai.laporan-penanganan-balai.create');
    Route::get('/balai/laporan-penanganan-balai/{laporan}', [BalaiController::class, 'laporanPenangananShow'])->name('balai.laporan-penanganan-balai.show');
    Route::put('/balai/laporan-penanganan-balai/{laporan}/update-status', [BalaiController::class, 'updateStatus'])->name('balai.laporan-penanganan-balai.update-status');
    Route::delete('/balai/laporan-penanganan-balai/{laporan}', [BalaiController::class, 'laporanPenangananDestroy'])->name('balai.laporan-penanganan-balai.destroy');
    Route::get('/balai/data-pic-balai', [BalaiController::class, 'dataPicBalaiShow'])->name('balai.data-pic-balai.show');
    Route::get('/balai/data-pic-balai/edit', [BalaiController::class, 'editProfile'])->name('balai.data-pic-balai.edit');
    Route::put('/balai/data-pic-balai/update', [BalaiController::class, 'updateProfile'])->name('balai.data-pic-balai.update');
    
});