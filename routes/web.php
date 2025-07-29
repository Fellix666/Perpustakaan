<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Rute Autentikasi ---
Route::get('/', fn() => redirect('/login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// --- Grup Rute yang Membutuhkan Autentikasi Admin ---
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Manajemen Anggota ---
    Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('anggota/{anggota}', [AnggotaController::class, 'show'])->name('anggota.show');
    Route::get('anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    Route::get('anggota/{anggota}/card', [AnggotaController::class, 'card'])->name('anggota.card');
    Route::get('anggota/generate/nomor', [AnggotaController::class, 'generateNomorAnggota'])->name('anggota.generate-nomor');
    Route::get('anggota/search/ajax', [AnggotaController::class, 'search'])->name('anggota.search');
    Route::get('anggota/export/csv', [AnggotaController::class, 'export'])->name('anggota.export');
    Route::post('anggota/import', [AnggotaController::class, 'import'])->name('anggota.import');
    Route::post('anggota/upload-foto-zip', [AnggotaController::class, 'prosesUploadFotoZip'])->name('anggota.proses-upload-foto');

    // --- Manajemen Buku ---
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('buku/create', [BukuController::class, 'create'])->name('buku.create');
    Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
    Route::get('buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
    Route::get('buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    Route::get('buku/export/csv', [BukuController::class, 'export'])->name('buku.export');
    Route::post('buku/import', [BukuController::class, 'import'])->name('buku.import');

    // --- Manajemen Kategori ---
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // --- Manajemen Rak ---
    Route::get('rak', [RakController::class, 'index'])->name('rak.index');
    Route::get('rak/create', [RakController::class, 'create'])->name('rak.create');
    Route::post('rak', [RakController::class, 'store'])->name('rak.store');
    Route::get('rak/{rak}/edit', [RakController::class, 'edit'])->name('rak.edit');
    Route::put('rak/{rak}', [RakController::class, 'update'])->name('rak.update');
    Route::delete('rak/{rak}', [RakController::class, 'destroy'])->name('rak.destroy');
    
    // ======================================================================
    // RUTE BARU UNTUK PEMINJAMAN, PENGEMBALIAN, DAN DENDA
    // ======================================================================

    // --- Transaksi Peminjaman ---
    Route::get('peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::get('peminjaman/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
    Route::put('peminjaman/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::delete('peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

    // --- Transaksi Pengembalian ---
    Route::get('pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    // Rute untuk menampilkan form pengembalian berdasarkan ID peminjaman
    Route::get('pengembalian/{id}/create', [PengembalianController::class, 'create'])->name('pengembalian.create');
    // Rute untuk memproses data dari form pengembalian
    Route::post('pengembalian/{id}', [PengembalianController::class, 'store'])->name('pengembalian.store');
    Route::get('pengembalian/{id}', [PengembalianController::class, 'show'])->name('pengembalian.show');
    Route::get('pengembalian/{id}/edit', [PengembalianController::class, 'edit'])->name('pengembalian.edit');
    Route::put('pengembalian/{id}', [PengembalianController::class, 'update'])->name('pengembalian.update');
    Route::delete('pengembalian/{id}', [PengembalianController::class, 'destroy'])->name('pengembalian.destroy');
    
    // --- Transaksi Denda ---
    Route::get('denda', [DendaController::class, 'index'])->name('denda.index');
    // Rute untuk menampilkan form pembayaran denda
    Route::get('denda/{id}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');
    // Rute untuk memproses pembayaran denda
    Route::post('denda/{id}/bayar', [DendaController::class, 'prosesBayar'])->name('denda.proses-bayar');

    // --- Laporan ---
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/data-master', [LaporanController::class, 'dataMaster'])->name('laporan.data-master');
    Route::get('laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('laporan/semua', [LaporanController::class, 'semuaData'])->name('laporan.semua');
    Route::get('laporan/print/data-master', [LaporanController::class, 'printDataMaster'])->name('laporan.print.data-master');
    Route::get('laporan/print/transaksi', [LaporanController::class, 'printTransaksi'])->name('laporan.print.transaksi');
    Route::get('laporan/print/semua', [LaporanController::class, 'printSemua'])->name('laporan.print.semua');
    Route::get('laporan/peminjaman', [DashboardController::class, 'laporanPeminjaman'])->name('laporan.peminjaman');
    Route::get('laporan/denda', [DashboardController::class, 'laporanDenda'])->name('laporan.denda');

    // --- Profil ---
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});