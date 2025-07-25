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

Route::get('/', fn() => redirect('/login'));

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes untuk Anggota
    Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('anggota/{anggota}', [AnggotaController::class, 'show'])->name('anggota.show');
    Route::get('anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    
    // Route untuk cetak kartu (perbaikan di sini)
    Route::get('anggota/{anggota}/card', [AnggotaController::class, 'card'])->name('anggota.card');
    
    // Routes lainnya untuk Anggota
    Route::get('anggota/generate/nomor', [AnggotaController::class, 'generateNomorAnggota'])->name('anggota.generate-nomor');
    Route::get('anggota/search/ajax', [AnggotaController::class, 'search'])->name('anggota.search');
    Route::get('anggota/export/csv', [AnggotaController::class, 'export'])->name('anggota.export');
    Route::post('anggota/import', [AnggotaController::class, 'import'])->name('anggota.import');
    
    // Routes untuk Buku (satu per satu)
    Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('buku/create', [BukuController::class, 'create'])->name('buku.create');
    Route::post('buku', [BukuController::class, 'store'])->name('buku.store');
    Route::get('buku/{buku}', [BukuController::class, 'show'])->name('buku.show');
    Route::get('buku/{buku}/edit', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('buku/{buku}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('buku/{buku}', [BukuController::class, 'destroy'])->name('buku.destroy');
    Route::get('buku/export/csv', [BukuController::class, 'export'])->name('buku.export');
    Route::post('buku/import', [BukuController::class, 'import'])->name('buku.import');
    
    // Routes untuk Kategori (satu per satu)
    Route::get('kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // Routes untuk Rak (satu per satu)
    Route::get('rak', [RakController::class, 'index'])->name('rak.index');
    Route::get('rak/create', [RakController::class, 'create'])->name('rak.create');
    Route::post('rak', [RakController::class, 'store'])->name('rak.store');
    Route::get('rak/{rak}/edit', [RakController::class, 'edit'])->name('rak.edit');
    Route::put('rak/{rak}', [RakController::class, 'update'])->name('rak.update');
    Route::delete('rak/{rak}', [RakController::class, 'destroy'])->name('rak.destroy');
    
    // Routes untuk Peminjaman (resourceful)
    Route::resource('peminjaman', PeminjamanController::class);
    Route::get('peminjaman/{peminjaman}/pengembalian', [PeminjamanController::class, 'pengembalian'])->name('peminjaman.pengembalian');
    Route::post('peminjaman/{peminjaman}/pengembalian', [PeminjamanController::class, 'prosesPengembalian'])->name('peminjaman.proses-pengembalian');

    // Routes untuk Pengembalian (resourceful)
    Route::resource('pengembalian', PengembalianController::class)->except(['create', 'store']);
    // Proses pengembalian tetap pakai route custom (untuk pengembalian dari peminjaman)
    Route::get('pengembalian/{peminjaman}/proses', [PengembalianController::class, 'create'])->name('pengembalian.create');
    Route::post('pengembalian/{peminjaman}/proses', [PengembalianController::class, 'store'])->name('pengembalian.store');
    
    Route::get('denda', [DendaController::class, 'index'])->name('denda.index');
    Route::get('denda/{id}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');
    Route::post('denda/{id}/bayar', [DendaController::class, 'prosesBayar'])->name('denda.proses-bayar');
    
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/data-master', [LaporanController::class, 'dataMaster'])->name('laporan.data-master');
    Route::get('laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('laporan/semua', [LaporanController::class, 'semuaData'])->name('laporan.semua');
    Route::get('laporan/print/data-master', [LaporanController::class, 'printDataMaster'])->name('laporan.print.data-master');
    Route::get('laporan/print/transaksi', [LaporanController::class, 'printTransaksi'])->name('laporan.print.transaksi');
    Route::get('laporan/print/semua', [LaporanController::class, 'printSemua'])->name('laporan.print.semua');
    Route::get('laporan/peminjaman', [DashboardController::class, 'laporanPeminjaman'])->name('laporan.peminjaman');
    Route::get('laporan/denda', [DashboardController::class, 'laporanDenda'])->name('laporan.denda');

    // Tambahkan route profil admin
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});