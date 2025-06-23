<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DendaController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Optional: Registration routes (uncomment if needed for testing)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    
    // Data Master - Anggota
    Route::resource('anggota', AnggotaController::class);
    
    // Data Master - Buku
    Route::resource('buku', BukuController::class);
    
    // Data Master - Kategori
    Route::resource('kategori', KategoriController::class);
    
    // Data Master - Rak
    Route::resource('rak', RakController::class);
    
    // Transaksi - Peminjaman
    Route::resource('peminjaman', PeminjamanController::class);
    Route::get('peminjaman/{id}/pengembalian', [PeminjamanController::class, 'pengembalian'])->name('peminjaman.pengembalian');
    Route::post('peminjaman/{id}/pengembalian', [PeminjamanController::class, 'prosesPengembalian'])->name('peminjaman.proses-pengembalian');
    
    // Transaksi - Denda
    Route::get('denda', [DendaController::class, 'index'])->name('denda.index');
    Route::get('denda/{id}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');
    Route::post('denda/{id}/bayar', [DendaController::class, 'prosesBayar'])->name('denda.proses-bayar');
    
    // Laporan
    Route::get('laporan/peminjaman', [DashboardController::class, 'laporanPeminjaman'])->name('laporan.peminjaman');
    Route::get('laporan/denda', [DashboardController::class, 'laporanDenda'])->name('laporan.denda');
    
});