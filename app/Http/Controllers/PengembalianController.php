<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
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
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('buku')->findOrFail($id);

            // Validasi tanggal kembali aktual tidak boleh lebih awal dari tanggal pinjam
            $tanggalPinjam = Carbon::parse($peminjaman->tanggal_pinjam)->startOfDay();
            $tanggalAktual = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();
            
            if ($tanggalAktual->lt($tanggalPinjam)) {
                return redirect()->back()
                    ->withErrors(['tanggal_kembali_aktual' => 'Tanggal kembali aktual tidak boleh lebih awal dari tanggal pinjam (' . $peminjaman->tanggal_pinjam->format('d/m/Y') . ')'])
                    ->withInput();
            }

            if ($peminjaman->status === 'dikembalikan') {
                return redirect()->route('pengembalian.index')->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
            }

            // Perbarui status peminjaman
            $peminjaman->status = 'dikembalikan';
            $peminjaman->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
            $peminjaman->keterangan = $request->keterangan;
            
            $pesanSukses = 'Pengembalian berhasil diproses.';

            // Logika Pengecekan Denda dengan perhitungan hari kerja
            $rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $aktual = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();

            if ($aktual->isAfter($rencana)) {
                // PERBAIKAN: Hitung hari kalender biasa (termasuk Sabtu-Minggu)
                $selisihHariKalender = $rencana->diffInDays($aktual, false);
                
                if ($selisihHariKalender > 0) {
                    $dendaPerHari = 1000;
                    $totalDenda = $selisihHariKalender * $dendaPerHari;

                    // Buat record baru di tabel dendas
                    Denda::create([
                        'peminjaman_id' => $peminjaman->id,
                        'hari_terlambat' => $selisihHariKalender,
                        'denda_per_hari' => $dendaPerHari,
                        'total_denda' => $totalDenda,
                        'status_bayar' => 'belum-dibayar',
                    ]);

                    $pesanSukses = 'Pengembalian berhasil dengan denda keterlambatan sebesar Rp ' . number_format($totalDenda) . ' (' . $selisihHariKalender . ' hari)';
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
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        if ($peminjaman->status === 'dikembalikan') {
            return redirect()->route('peminjaman.show', $peminjaman->id)->with('error', 'Buku ini sudah dikembalikan.');
        }
        return view('pengembalian.create', compact('peminjaman'));
    }

    /**
     * Menampilkan detail pengembalian.
     */
    public function show($id) 
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku', 'dendaRecord'])->findOrFail($id);
        return view('pengembalian.show', compact('peminjaman'));
    }

    /**
     * Menampilkan form edit pengembalian.
     */
    public function edit($id) 
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $peminjaman = Peminjaman::with(['anggota', 'buku'])->findOrFail($id);
        if ($peminjaman->status !== 'dikembalikan') {
            return redirect()->route('peminjaman.show', $peminjaman->id)->with('error', 'Buku ini belum dikembalikan.');
        }
        return view('pengembalian.edit', compact('peminjaman'));
    }

    /**
     * Update data pengembalian dan recalculate denda jika tanggal berubah.
     */
    public function update(Request $request, $id)
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('buku')->findOrFail($id);
            
            if ($peminjaman->status !== 'dikembalikan') {
                return redirect()->route('peminjaman.show', $peminjaman->id)->with('error', 'Buku ini belum dikembalikan.');
            }

            // Validasi tanggal kembali aktual tidak boleh lebih awal dari tanggal kembali rencana
            $tanggalRencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
            $tanggalAktual = Carbon::parse($request->tanggal_kembali_aktual)->startOfDay();
            
            if ($tanggalAktual->lt($tanggalRencana)) {
                return redirect()->back()
                    ->withErrors(['tanggal_kembali_aktual' => 'Tanggal kembali aktual tidak boleh lebih awal dari tanggal kembali rencana (' . $peminjaman->tanggal_kembali_rencana->format('d/m/Y') . ')'])
                    ->withInput();
            }

            $tanggalLama = $peminjaman->tanggal_kembali_aktual;
            $tanggalBaru = Carbon::parse($request->tanggal_kembali_aktual);

            // Update data peminjaman
            $peminjaman->tanggal_kembali_aktual = $request->tanggal_kembali_aktual;
            $peminjaman->keterangan = $request->keterangan;
            
            $pesanSukses = 'Data pengembalian berhasil diperbarui.';

            // Recalculate denda jika tanggal berubah
            if ($tanggalLama != $tanggalBaru) {
                // Hapus denda lama jika ada
                if ($peminjaman->dendaRecord) {
                    $peminjaman->dendaRecord->delete();
                }

                // Hitung denda baru dengan hari kalender biasa
                $rencana = Carbon::parse($peminjaman->tanggal_kembali_rencana)->startOfDay();
                $aktual = $tanggalBaru->startOfDay();

                if ($aktual->isAfter($rencana)) {
                    $selisihHariKalender = $rencana->diffInDays($aktual, false);
                    
                    if ($selisihHariKalender > 0) {
                        $dendaPerHari = 1000;
                        $totalDenda = $selisihHariKalender * $dendaPerHari;

                        // Buat record denda baru
                        Denda::create([
                            'peminjaman_id' => $peminjaman->id,
                            'hari_terlambat' => $selisihHariKalender,
                            'denda_per_hari' => $dendaPerHari,
                            'total_denda' => $totalDenda,
                            'status_bayar' => 'belum-dibayar',
                        ]);

                        $pesanSukses = 'Data pengembalian berhasil diperbarui dengan denda keterlambatan sebesar Rp ' . number_format($totalDenda) . ' (' . $selisihHariKalender . ' hari)';
                    }
                }
            }
            
            $peminjaman->save();

            DB::commit();
            return redirect()->route('pengembalian.index')->with('success', $pesanSukses);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus data pengembalian (menghapus data peminjaman secara permanen).
     */
    public function destroy($id) 
    {
        if (auth('admin')->user()->role === 'kepala_perpus') {
            abort(403, 'Akses hanya untuk admin');
        }
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('buku')->findOrFail($id);
            
            if ($peminjaman->status !== 'dikembalikan') {
                return redirect()->route('peminjaman.show', $peminjaman->id)->with('error', 'Buku ini belum dikembalikan.');
            }

            // Hapus denda jika ada
            if ($peminjaman->dendaRecord) {
                $peminjaman->dendaRecord->delete();
            }

            // Hapus data peminjaman secara permanen
            // Catatan: Stok buku tidak perlu dikembalikan karena sudah dikembalikan saat proses pengembalian
            $peminjaman->delete();

            DB::commit();
            return redirect()->route('pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus secara permanen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}