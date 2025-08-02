@extends('layouts.app')

@section('title', 'Laporan Denda')
@section('page-title', 'Laporan Denda & Pengumuman Keterlambatan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Denda</li>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan Denda & Keterlambatan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.denda') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="jenis_laporan" class="form-label">Jenis Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="form-select">
                        <option value="pengumuman" {{ $jenisLaporan == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                        <option value="tahunan" {{ $jenisLaporan == 'tahunan' ? 'selected' : '' }}>Laporan Tahunan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select">
                        @foreach($availableYears ?? [] as $year)
                            <option value="{{ $year }}" {{ ($tahunAjaran ?? '') == $year ? 'selected' : '' }}>
                                {{ $year }}/{{ (int)$year + 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Ringkasan -->
@if($jenisLaporan == 'pengumuman')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Denda Belum Dibayar</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalDendaBelumBayar) }}</h3>
                <small>{{ $dendaBelumBayar->count() }} transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Keterlambatan Aktif</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalDendaTerlambat) }}</h3>
                <small>{{ $peminjamanTerlambat->count() }} peminjaman terlambat</small>
            </div>
        </div>
    </div>
</div>
@else
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Denda Sudah Dibayar</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalDendaSudahBayar) }}</h3>
                <small>{{ $dendaSudahBayar->count() }} transaksi</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Denda Belum Dibayar</h6>
                <h3 class="fw-bold">Rp {{ number_format($totalDendaBelumBayar) }}</h3>
                <small>{{ $dendaBelumBayar->count() }} transaksi</small>
            </div>
        </div>
    </div>
</div>

<!-- Summary Per Bulan untuk Laporan Tahunan -->
@if(isset($summaryData) && $summaryData->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary Denda Per Bulan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Bulan</th>
                        <th class="text-center">Sudah Dibayar</th>
                        <th class="text-center">Belum Dibayar</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summaryData as $summary)
                    <tr>
                        <td class="fw-bold">{{ $summary['bulan'] }}</td>
                        <td class="text-center">
                            <div class="text-success fw-bold">Rp {{ number_format($summary['sudah_dibayar']['nominal']) }}</div>
                            <small class="text-muted">{{ $summary['sudah_dibayar']['transaksi'] }} transaksi</small>
                        </td>
                        <td class="text-center">
                            <div class="text-danger fw-bold">Rp {{ number_format($summary['belum_dibayar']['nominal']) }}</div>
                            <small class="text-muted">{{ $summary['belum_dibayar']['transaksi'] }} transaksi</small>
                        </td>
                        <td class="text-center">
                            @php
                                $totalBulan = $summary['sudah_dibayar']['nominal'] + $summary['belum_dibayar']['nominal'];
                                $totalTransaksi = $summary['sudah_dibayar']['transaksi'] + $summary['belum_dibayar']['transaksi'];
                            @endphp
                            <div class="fw-bold">Rp {{ number_format($totalBulan) }}</div>
                            <small class="text-muted">{{ $totalTransaksi }} transaksi</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endif

<!-- Tabel Gabungan Denda & Keterlambatan -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @if($jenisLaporan == 'pengumuman')
                Data Pengumuman Denda & Keterlambatan
            @else
                Data Denda Tahun Ajaran {{ $tahunAjaran ?? '' }}/{{ ($tahunAjaran ? (int)$tahunAjaran + 1 : '') }}
            @endif
        </h5>
        <a href="{{ route('laporan.print.denda', request()->all()) }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="fas fa-print me-2"></i>
            @if($jenisLaporan == 'pengumuman')
                Cetak Pengumuman
            @else
                Cetak Laporan Tahunan
            @endif
        </a>
    </div>
    <div class="card-body p-0">
        @if($jenisLaporan == 'pengumuman' && ($dendaBelumBayar->count() > 0 || $peminjamanTerlambat->count() > 0))
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-danger">
                        <tr>
                            <th>No</th>
                            <th>Kode Peminjaman</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali (Rencana)</th>
                            <th>Status</th>
                            <th>Hari Terlambat</th>
                            <th>Denda per Hari</th>
                            <th>Total Denda</th>
                            <th>Tanggal Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        
                        {{-- Data Denda Belum Dibayar --}}
                        @foreach($dendaBelumBayar as $denda)
                        <tr class="table-danger">
                            <td>{{ $no++ }}</td>
                            <td><code>{{ $denda->peminjaman->kode_peminjaman }}</code></td>
                            <td>
                                <div class="fw-bold">{{ $denda->peminjaman->anggota->nama_lengkap }}</div>
                                <small class="text-muted">{{ $denda->peminjaman->anggota->kelas }}</small>
                            </td>
                            <td>{{ $denda->peminjaman->buku->judul }}</td>
                            <td>{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td>{{ $denda->peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">Denda Belum Dibayar</span></td>
                            <td><span class="badge bg-danger">{{ $denda->hari_terlambat }} hari</span></td>
                            <td>Rp {{ number_format($denda->denda_per_hari) }}</td>
                            <td class="fw-bold text-danger">Rp {{ number_format($denda->total_denda) }}</td>
                            <td>{{ $denda->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                        
                        {{-- Data Denda Sudah Dibayar --}}
                        @foreach($dendaSudahBayar as $denda)
                        <tr class="table-success">
                            <td>{{ $no++ }}</td>
                            <td><code>{{ $denda->peminjaman->kode_peminjaman }}</code></td>
                            <td>
                                <div class="fw-bold">{{ $denda->peminjaman->anggota->nama_lengkap }}</div>
                                <small class="text-muted">{{ $denda->peminjaman->anggota->kelas }}</small>
                            </td>
                            <td>{{ $denda->peminjaman->buku->judul }}</td>
                            <td>{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td>{{ $denda->peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td><span class="badge bg-success">Denda Sudah Dibayar</span></td>
                            <td><span class="badge bg-success">{{ $denda->hari_terlambat }} hari</span></td>
                            <td>Rp {{ number_format($denda->denda_per_hari) }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($denda->total_denda) }}</td>
                            <td>{{ $denda->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                        
                        {{-- Data Keterlambatan Aktif --}}
                        @foreach($peminjamanTerlambat as $peminjaman)
                        @php
                            $hariTerlambat = Carbon\Carbon::now()->startOfDay()->diffInDays($peminjaman->tanggal_kembali_rencana->startOfDay(), false);
                            $dendaAkanDikenakan = max(0, $hariTerlambat) * 1000; // Rp 1.000 per hari, minimal 0
                        @endphp
                        <tr class="table-warning">
                            <td>{{ $no++ }}</td>
                            <td><code>{{ $peminjaman->kode_peminjaman }}</code></td>
                            <td>
                                <div class="fw-bold">{{ $peminjaman->anggota->nama_lengkap }}</div>
                                <small class="text-muted">{{ $peminjaman->anggota->kelas }}</small>
                            </td>
                            <td>{{ $peminjaman->buku->judul }}</td>
                            <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td>{{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td><span class="badge bg-warning text-dark">Terlambat</span></td>
                            <td><span class="badge bg-warning text-dark">{{ $hariTerlambat }} hari</span></td>
                            <td>Rp 1,000</td>
                            <td class="fw-bold text-warning">Rp {{ number_format($dendaAkanDikenakan) }}</td>
                            <td>-</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($jenisLaporan == 'tahunan' && ($dendaSudahBayar->count() > 0 || $dendaBelumBayar->count() > 0))
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Kode Peminjaman</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali (Rencana)</th>
                            <th>Status</th>
                            <th>Hari Terlambat</th>
                            <th>Denda per Hari</th>
                            <th>Total Denda</th>
                            <th>Tanggal Denda</th>
                            <th>Tanggal Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        
                        {{-- Data Denda Sudah Dibayar --}}
                        @foreach($dendaSudahBayar as $denda)
                        <tr class="table-success">
                            <td>{{ $no++ }}</td>
                            <td><code>{{ $denda->peminjaman->kode_peminjaman }}</code></td>
                            <td>
                                <div class="fw-bold">{{ $denda->peminjaman->anggota->nama_lengkap }}</div>
                                <small class="text-muted">{{ $denda->peminjaman->anggota->kelas }}</small>
                            </td>
                            <td>{{ $denda->peminjaman->buku->judul }}</td>
                            <td>{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td>{{ $denda->peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td><span class="badge bg-success">Sudah Dibayar</span></td>
                            <td><span class="badge bg-success">{{ $denda->hari_terlambat }} hari</span></td>
                            <td>Rp {{ number_format($denda->denda_per_hari) }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($denda->total_denda) }}</td>
                            <td>{{ $denda->created_at->format('d/m/Y') }}</td>
                            <td>{{ $denda->tanggal_bayar ? $denda->tanggal_bayar->format('d/m/Y') : '-' }}</td>
                        </tr>
                        @endforeach
                        
                        {{-- Data Denda Belum Dibayar --}}
                        @foreach($dendaBelumBayar as $denda)
                        <tr class="table-danger">
                            <td>{{ $no++ }}</td>
                            <td><code>{{ $denda->peminjaman->kode_peminjaman }}</code></td>
                            <td>
                                <div class="fw-bold">{{ $denda->peminjaman->anggota->nama_lengkap }}</div>
                                <small class="text-muted">{{ $denda->peminjaman->anggota->kelas }}</small>
                            </td>
                            <td>{{ $denda->peminjaman->buku->judul }}</td>
                            <td>{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                            <td>{{ $denda->peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</td>
                            <td><span class="badge bg-danger">Belum Dibayar</span></td>
                            <td><span class="badge bg-danger">{{ $denda->hari_terlambat }} hari</span></td>
                            <td>Rp {{ number_format($denda->denda_per_hari) }}</td>
                            <td class="fw-bold text-danger">Rp {{ number_format($denda->total_denda) }}</td>
                            <td>{{ $denda->created_at->format('d/m/Y') }}</td>
                            <td>-</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">Tidak ada data sesuai filter</h5>
                <p class="text-muted">
                    @if($jenisLaporan == 'pengumuman')
                        Tidak ada data pengumuman untuk kriteria yang dipilih.
                    @else
                        Tidak ada data denda tahunan untuk kriteria yang dipilih.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Pengumuman Keterlambatan -->
@if($jenisLaporan == 'pengumuman' && ($peminjamanTerlambat->count() > 0 || $dendaBelumBayar->count() > 0))
<div class="card shadow-sm mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-bullhorn me-2"></i>Pengumuman Keterlambatan
        </h5>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>PERHATIAN!
            </h6>
            <p class="mb-2">Kepada seluruh anggota perpustakaan yang terlambat mengembalikan buku:</p>
            <ul class="mb-2">
                <li>Denda keterlambatan sebesar <strong>Rp 1.000 per hari</strong></li>
                <li>Segera kembalikan buku yang dipinjam untuk menghindari penumpukan denda</li>
                <li>Denda yang belum dibayar akan mempengaruhi status keanggotaan</li>
            </ul>
            <hr>
            <p class="mb-0">
                <strong>Total denda yang belum dibayar: Rp {{ number_format($totalDendaBelumBayar + $totalDendaTerlambat) }}</strong>
            </p>
        </div>
    </div>
</div>
@endif
@endsection 