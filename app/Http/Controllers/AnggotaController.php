<?php
namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->paginate(10);
        return view('anggota.index', compact('anggotas'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota',
            'nama_lengkap' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|max:20',
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date'
        ]);

        $data = $request->all();
        $data['status'] = 'aktif';
        Anggota::create($data);

        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil ditambahkan');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load(['peminjamans.buku', 'peminjamans.denda']);
        return view('anggota.show', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $request->validate([
            'nomor_anggota' => 'required|unique:anggotas,nomor_anggota,' . $anggota->id,
            'nama_lengkap' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas' => 'required|max:20',
            'alamat' => 'required|max:255',
            'telepon' => 'nullable|max:15',
            'tanggal_daftar' => 'required|date',
            'status' => 'required|in:aktif,non-aktif'
        ]);

        $anggota->update($request->all());
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil diperbarui');
    }

    public function destroy(Anggota $anggota)
    {
        if ($anggota->peminjamanAktif()->count() > 0) {
            return redirect()->route('anggota.index')->with('error', 'Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif');
        }

        $anggota->delete();
        return redirect()->route('anggota.index')->with('success', 'Anggota berhasil dihapus');
    }

    // Perbaikan fungsi card - mengarahkan ke view card yang sudah ada
    public function card(Anggota $anggota)
    {
        return view('anggota.card', compact('anggota'));
    }

    // Tambahan fungsi untuk cetak kartu dalam format print-friendly
    public function printCard(Anggota $anggota)
    {
        return view('anggota.card', compact('anggota'));
    }

    public function generateNomorAnggota()
    {
        $currentYear = date('Y');
        $lastAnggota = Anggota::where('nomor_anggota', 'like', "AGT{$currentYear}%")
            ->orderBy('nomor_anggota', 'desc')
            ->first();

        if ($lastAnggota) {
            $lastNumber = (int) substr($lastAnggota->nomor_anggota, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return response()->json(['nomor_anggota' => "AGT{$currentYear}{$newNumber}"]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $anggotas = Anggota::where('nama_lengkap', 'like', "%{$query}%")
            ->orWhere('nomor_anggota', 'like', "%{$query}%")
            ->orWhere('kelas', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($anggotas);
    }

    public function export()
    {
        $anggotas = Anggota::orderBy('nama_lengkap')->get();
        $filename = 'data_anggota_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ];

        return response()->stream(function() use ($anggotas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Anggota','Nama Lengkap','Jenis Kelamin','Kelas','Alamat','Telepon','Tanggal Daftar','Status']);
            
            foreach ($anggotas as $anggota) {
                fputcsv($file, [
                    $anggota->nomor_anggota,
                    $anggota->nama_lengkap,
                    $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $anggota->kelas,
                    $anggota->alamat,
                    $anggota->telepon,
                    $anggota->tanggal_daftar->format('d/m/Y'),
                    $anggota->status
                ]);
            }
            fclose($file);
        }, 200, $headers);
    }
}