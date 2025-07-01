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

Route::get('/', function () { 
    return redirect('/login'); 
}); 

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); 
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); 
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register'); 
Route::post('/register', [AuthController::class, 'register']); 

Route::middleware(['auth:admin'])->group(function () { 
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 
    
    // Data Master Routes
    Route::resource('anggota', AnggotaController::class); 
    Route::resource('buku', BukuController::class); 
    Route::resource('kategori', KategoriController::class); 
    Route::resource('rak', RakController::class); 
    
    // Transaksi Routes
    Route::resource('peminjaman', PeminjamanController::class); 
    Route::get('peminjaman/{id}/pengembalian', [PeminjamanController::class, 'pengembalian'])->name('peminjaman.pengembalian'); 
    Route::post('peminjaman/{id}/pengembalian', [PeminjamanController::class, 'prosesPengembalian'])->name('peminjaman.proses-pengembalian'); 
    
    // Pengembalian Routes (Bisa menggunakan controller terpisah atau masih dengan PeminjamanController)
    Route::get('pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('pengembalian/{id}', [PengembalianController::class, 'show'])->name('pengembalian.show');
    Route::get('pengembalian/{id}/proses', [PengembalianController::class, 'create'])->name('pengembalian.create');
    Route::post('pengembalian/{id}/proses', [PengembalianController::class, 'store'])->name('pengembalian.store');
    
    // Denda Routes
    Route::get('denda', [DendaController::class, 'index'])->name('denda.index'); 
    Route::get('denda/{id}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar'); 
    Route::post('denda/{id}/bayar', [DendaController::class, 'prosesBayar'])->name('denda.proses-bayar'); 
    
    // Laporan Routes
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/data-master', [LaporanController::class, 'dataMaster'])->name('laporan.data-master');
    Route::get('laporan/transaksi', [LaporanController::class, 'transaksi'])->name('laporan.transaksi');
    Route::get('laporan/semua', [LaporanController::class, 'semuaData'])->name('laporan.semua');
    Route::get('laporan/print/data-master', [LaporanController::class, 'printDataMaster'])->name('laporan.print.data-master');
    Route::get('laporan/print/transaksi', [LaporanController::class, 'printTransaksi'])->name('laporan.print.transaksi');
    Route::get('laporan/print/semua', [LaporanController::class, 'printSemua'])->name('laporan.print.semua');
    
    // Routes lama untuk laporan (untuk backward compatibility)
    Route::get('laporan/peminjaman', [DashboardController::class, 'laporanPeminjaman'])->name('laporan.peminjaman'); 
    Route::get('laporan/denda', [DashboardController::class, 'laporanDenda'])->name('laporan.denda'); 
});