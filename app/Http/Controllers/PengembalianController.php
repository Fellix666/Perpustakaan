<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    /**
     * Menyimpan data pengembalian baru dan menghitung denda jika ada.
     */
    public function store(Request $request, $id)
    {
        $request->validate(['tanggal_kembali_aktual' => 'required|date']);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('buku')->findOrFail($id);

            if ($peminjaman->status === 'dikembalikan') {
                return redirect()->route('pengembalian.index')->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
            }

            // Perbarui status peminjaman
            $peminjaman->status = 'dikembalikan';
            $peminjaman->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
            $peminjaman->keterangan = $request->keterangan;
            
            $pesanSukses = 'Pengembalian berhasil diproses.';

            // Logika Pengecekan Denda
            $rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $aktual = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();

            if ($aktual->isAfter($rencana)) {
                // PERBAIKAN: Gunakan abs() untuk memastikan selisih hari selalu positif
                $selisihHari = abs($aktual->diffInDays($rencana));
                
                if ($selisihHari > 0) {
                    $dendaPerHari = 1000;
                    $totalDenda = $selisihHari * $dendaPerHari;

                    // Buat record baru di tabel dendas
                    Denda::create([
                        'peminjaman_id' => $peminjaman->id,
                        'hari_terlambat' => $selisihHari,
                        'denda_per_hari' => $dendaPerHari,
                        'total_denda' => $totalDenda,
                        'status_bayar' => 'belum-dibayar',
                    ]);

                    $pesanSukses = 'Pengembalian berhasil dengan denda keterlambatan sebesar Rp ' . number_format($totalDenda);
                }
            }
            
            $peminjaman->save();

            // Kembalikan stok buku
            $buku = $peminjaman->buku;
            if ($buku) {
                $buku->increment('stok_tersedia');
                if($buku->status === 'tidak-tersedia'){
                    $buku->status = 'tersedia';
                }
                $buku->save();
            }

            DB::commit();
            return redirect()->route('pengembalian.index')->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan daftar data yang sudah dikembalikan.
     */
    public function index(Request $request) 
    {
        $query = Peminjaman::query()->with(['anggota', 'buku', 'dendaRecord'])
                         ->where('status', 'dikembalikan');
        
        // Anda bisa menambahkan filter di sini jika perlu, contoh:
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('anggota', function($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%$q%");
            })->orWhereHas('buku', function($sub) use ($q) {
                $sub->where('judul', 'like', "%$q%");
            })->orWhere('kode_peminjaman', 'like', "%$q%");
        }

        $peminjamans = $query->orderBy('tanggal_kembali_aktual', 'desc')->paginate(10)->withQueryString();
        return view('pengembalian.index', compact('peminjamans'));
    }

    /**
     * Menampilkan form untuk proses pengembalian.
     */
    public function create($id) 
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        if ($peminjaman->status === 'dikembalikan') {
            return redirect()->route('peminjaman.show', $peminjaman->id)->with('error', 'Buku ini sudah dikembalikan.');
        }
        return view('pengembalian.create', compact('peminjaman'));
    }

    /**
     * Menampilkan detail data pengembalian.
     */
    public function show($id) 
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku', 'dendaRecord'])->findOrFail($id);
        return view('pengembalian.show', compact('peminjaman'));
    }

    /**
     * Menampilkan form untuk mengedit data pengembalian.
     */
    public function edit($id) 
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        if ($peminjaman->status !== 'dikembalikan') {
            return back()->with('error', 'Hanya data yang sudah dikembalikan yang bisa diedit.');
        }
        return view('pengembalian.edit', compact('peminjaman'));
    }

    /**
     * Memperbarui data pengembalian dan menghitung ulang denda.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Hapus denda lama jika ada, karena akan dihitung ulang
            if ($peminjaman->dendaRecord) {
                $peminjaman->dendaRecord()->delete();
            }

            $peminjaman->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
            $peminjaman->keterangan = $request->keterangan;
            $pesanSukses = 'Data pengembalian berhasil diperbarui.';

            // Hitung ulang keterlambatan dan denda
            $rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $aktual = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();

            if ($aktual->isAfter($rencana)) {
                $selisihHari = abs($aktual->diffInDays($rencana));
                if ($selisihHari > 0) {
                    $dendaPerHari = 1000;
                    $totalDenda = $selisihHari * $dendaPerHari;

                    // Buat denda baru
                    Denda::create([
                        'peminjaman_id' => $peminjaman->id,
                        'hari_terlambat' => $selisihHari,
                        'denda_per_hari' => $dendaPerHari,
                        'total_denda' => $totalDenda,
                        'status_bayar' => 'belum-dibayar',
                    ]);
                    $pesanSukses = 'Data pengembalian diperbarui dengan denda keterlambatan.';
                }
            }
            
            $peminjaman->save();

            DB::commit();
            return redirect()->route('pengembalian.index')->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus data pengembalian (peminjaman yang sudah selesai).
     */
    public function destroy($id) 
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'dikembalikan') {
            return back()->with('error', 'Hanya data yang sudah dikembalikan yang bisa dihapus.');
        }
        
        // Hapus denda terkait (jika ada) dan data peminjaman itu sendiri.
        // Relasi onDelete('cascade') di database akan menangani ini secara otomatis,
        // namun melakukannya secara eksplisit di sini juga baik.
        $peminjaman->dendaRecord()->delete();
        $peminjaman->delete();

        return redirect()->route('pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus.');
    }
}