<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Denda & Keterlambatan</title>
    <style>
        body { 
            font-family: sans-serif; 
            margin: 20px; 
            font-size: 12px;
        }
        h1, h2, h3, h4, h5 { 
            text-align: center; 
            margin-bottom: 5px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: left; 
            font-size: 11px; 
        }
        thead { 
            background-color: #f2f2f2; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .table-danger { background-color: #f8d7da; }
        .table-warning { background-color: #fff3cd; }
        .alert { 
            border: 1px solid #ccc; 
            padding: 10px; 
            margin: 10px 0; 
            border-radius: 5px;
        }
        .alert-warning { 
            background-color: #fff3cd; 
            border-color: #ffeaa7;
        }
        .alert-danger { 
            background-color: #f8d7da; 
            border-color: #f5c6cb;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .card-body {
            padding: 10px;
        }
        @media print {
            .no-print { display: none; }
        }
        .btn { 
            padding: 8px 12px; 
            background-color: #0d6efd; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn">Cetak Laporan</button>
    </div>

    @if($jenisLaporan == 'pengumuman')
        <h3>LAPORAN PENGUMUMAN DENDA & KETERLAMBATAN</h3>
    @else
        <h3>LAPORAN DATA DENDA TAHUNAN</h3>
    @endif
    <h4>PERPUSTAKAAN SMP NEGERI 1 SANGGAU LEDO</h4>
    @if($jenisLaporan == 'pengumuman')
        @if($startDate && $endDate)
            <p style="text-align: center; margin-top:0;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @else
            <p style="text-align: center; margin-top:0;">Periode: Semua Data</p>
        @endif
    @else
        <p style="text-align: center; margin-top:0;">Tahun Ajaran: {{ $tahunAjaran }}/{{ $tahunAjaran ? (int)$tahunAjaran + 1 : '' }}</p>
        @if($startDate && $endDate)
            <p style="text-align: center; margin-top:0;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @else
            <p style="text-align: center; margin-top:0;">Periode: Semua Data</p>
        @endif
    @endif

    <!-- Ringkasan -->
    @if($jenisLaporan == 'pengumuman')
    <div class="card">
        <div class="card-header">Ringkasan Pengumuman</div>
        <div class="card-body">
            <div style="display: flex; justify-content: space-between;">
                <div style="text-align: center; flex: 1;">
                    <h5 style="color: #dc3545;">Total Denda Belum Dibayar</h5>
                    <h3 style="color: #dc3545; font-weight: bold;">Rp {{ number_format($totalDendaBelumBayar) }}</h3>
                    <small>{{ $dendaBelumBayar->count() }} transaksi</small>
                </div>
                <div style="text-align: center; flex: 1;">
                    <h5 style="color: #ffc107;">Total Denda Keterlambatan</h5>
                    <h3 style="color: #ffc107; font-weight: bold;">Rp {{ number_format($totalDendaTerlambat) }}</h3>
                    <small>{{ $peminjamanTerlambat->count() }} peminjaman terlambat</small>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-header">Ringkasan Denda Tahunan</div>
        <div class="card-body">
            <div style="display: flex; justify-content: space-between;">
                <div style="text-align: center; flex: 1;">
                    <h5 style="color: #28a745;">Total Denda Sudah Dibayar</h5>
                    <h3 style="color: #28a745; font-weight: bold;">Rp {{ number_format($totalDendaSudahBayar) }}</h3>
                    <small>{{ $dendaSudahBayar->count() }} transaksi</small>
                </div>
                <div style="text-align: center; flex: 1;">
                    <h5 style="color: #dc3545;">Total Denda Belum Dibayar</h5>
                    <h3 style="color: #dc3545; font-weight: bold;">Rp {{ number_format($totalDendaBelumBayar) }}</h3>
                    <small>{{ $dendaBelumBayar->count() }} transaksi</small>
                </div>
            </div>
        </div>
    </div>
    

    @endif

    <!-- Tabel Data -->
    @if($jenisLaporan == 'pengumuman' && ($dendaBelumBayar->count() > 0 || $peminjamanTerlambat->count() > 0))
    <div class="card">
        <div class="card-header">Data Pengumuman Denda & Keterlambatan</div>
        <div class="card-body">
            <table>
                <thead class="table-danger">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th class="text-center">Tgl Pinjam</th>
                        <th class="text-center">Hari Terlambat</th>
                        <th class="text-right">Total Denda</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    
                    {{-- Data Denda Belum Dibayar --}}
                    @foreach($dendaBelumBayar as $denda)
                    <tr class="table-danger">
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            {{ $denda->peminjaman->anggota->nama_lengkap }}<br>
                            <small>{{ $denda->peminjaman->anggota->kelas }}</small>
                        </td>
                        <td>{{ $denda->peminjaman->buku->judul }}</td>
                        <td class="text-center">{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $denda->hari_terlambat }} hari</td>
                        <td class="text-right fw-bold">Rp {{ number_format($denda->total_denda) }}</td>
                        <td class="text-center">Belum Dibayar</td>
                        <td class="text-center">-</td>
                    </tr>
                    @endforeach
                    
                    {{-- Data Peminjaman Terlambat Aktif --}}
                    @foreach($peminjamanTerlambat as $peminjaman)
                                         @php
                         // Perbaikan perhitungan hari terlambat
                         $tanggalSekarang = Carbon\Carbon::now()->startOfDay();
                         $tanggalKembali = $peminjaman->tanggal_kembali_rencana->startOfDay();
                         $hariTerlambat = max(0, $tanggalKembali->diffInDays($tanggalSekarang, false));
                         $dendaAkanDikenakan = $hariTerlambat * 1000;
                     @endphp
                    <tr class="table-warning">
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            {{ $peminjaman->anggota->nama_lengkap }}<br>
                            <small>{{ $peminjaman->anggota->kelas }}</small>
                        </td>
                        <td>{{ $peminjaman->buku->judul }}</td>
                        <td class="text-center">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $hariTerlambat }} hari</td>
                        <td class="text-right fw-bold">Rp {{ number_format($dendaAkanDikenakan) }}</td>
                        <td class="text-center">Terlambat Aktif</td>
                        <td class="text-center">-</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @elseif($jenisLaporan == 'tahunan' && ($dendaSudahBayar->count() > 0 || $dendaBelumBayar->count() > 0))
    <div class="card">
        <div class="card-header">Data Denda Tahun Ajaran {{ $tahunAjaran }}/{{ $tahunAjaran ? (int)$tahunAjaran + 1 : '' }}</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th class="text-center">Tgl Pinjam</th>
                        <th class="text-center">Hari Terlambat</th>
                        <th class="text-right">Total Denda</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Tgl Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    
                    {{-- Data Denda Sudah Dibayar --}}
                    @foreach($dendaSudahBayar as $denda)
                    <tr class="table-success">
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            {{ $denda->peminjaman->anggota->nama_lengkap }}<br>
                            <small>{{ $denda->peminjaman->anggota->kelas }}</small>
                        </td>
                        <td>{{ $denda->peminjaman->buku->judul }}</td>
                        <td class="text-center">{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $denda->hari_terlambat }} hari</td>
                        <td class="text-right fw-bold">Rp {{ number_format($denda->total_denda) }}</td>
                        <td class="text-center">Sudah Dibayar</td>
                        <td class="text-center">{{ $denda->tanggal_bayar ? $denda->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Data Denda Belum Dibayar --}}
                    @foreach($dendaBelumBayar as $denda)
                    <tr class="table-danger">
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            {{ $denda->peminjaman->anggota->nama_lengkap }}<br>
                            <small>{{ $denda->peminjaman->anggota->kelas }}</small>
                        </td>
                        <td>{{ $denda->peminjaman->buku->judul }}</td>
                        <td class="text-center">{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $denda->hari_terlambat }} hari</td>
                        <td class="text-right fw-bold">Rp {{ number_format($denda->total_denda) }}</td>
                        <td class="text-center">Belum Dibayar</td>
                        <td class="text-center">-</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Pengumuman -->
    @if($jenisLaporan == 'pengumuman' && ($peminjamanTerlambat->count() > 0 || $dendaBelumBayar->count() > 0))
    <div class="alert alert-warning">
        <h5 style="margin: 0 0 10px 0;">
            <strong>PERHATIAN!</strong>
        </h5>
        <p style="margin: 0 0 10px 0;">Kepada seluruh anggota perpustakaan yang terlambat mengembalikan buku:</p>
        <ul style="margin: 0 0 10px 0;">
            <li>Denda keterlambatan sebesar <strong>Rp 1.000 per hari</strong></li>
            <li>Segera kembalikan buku yang dipinjam untuk menghindari penumpukan denda</li>
            <li>Denda yang belum dibayar akan mempengaruhi status keanggotaan</li>
        </ul>
        <hr style="margin: 10px 0;">
        <p style="margin: 0;">
            <strong>Total denda keterlambatan: Rp {{ number_format($totalDendaTerlambat) }}</strong>
        </p>
    </div>
    @elseif($jenisLaporan == 'denda' && $dendaBelumBayar->count() > 0)
    <div class="alert alert-danger">
        <h5 style="margin: 0 0 10px 0;">
            <strong>INFORMASI DENDA!</strong>
        </h5>
        <p style="margin: 0 0 10px 0;">Daftar denda yang belum dibayar oleh anggota perpustakaan:</p>
        <ul style="margin: 0 0 10px 0;">
            <li>Total denda belum dibayar: <strong>Rp {{ number_format($totalDendaBelumBayar) }}</strong></li>
            <li>Total denda sudah dibayar: <strong>Rp {{ number_format($totalDendaSudahBayar) }}</strong></li>
            <li>Segera lakukan pembayaran untuk menghindari sanksi</li>
        </ul>
    </div>
    @endif

    @if(($jenisLaporan == 'denda' && $dendaBelumBayar->count() == 0 && $dendaSudahBayar->count() == 0) || 
        ($jenisLaporan == 'keterlambatan' && $peminjamanTerlambat->count() == 0))
    <div class="alert alert-warning">
        <h5 style="text-align: center; margin: 0;">
            @if($jenisLaporan == 'denda')
                Tidak ada data denda untuk periode yang dipilih.
            @else
                Tidak ada data keterlambatan untuk periode yang dipilih.
            @endif
        </h5>
        <p style="text-align: center; margin: 10px 0 0 0;">
            @if($jenisLaporan == 'denda')
                Semua denda sudah dibayar atau tidak ada denda untuk periode ini.
            @else
                Semua peminjaman sudah dikembalikan tepat waktu.
            @endif
        </p>
    </div>
    @endif



</body>
</html> 