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
                <div class="col-md-3" id="tahun_ajaran_container">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($availableYears ?? [] as $year)
                            <option value="{{ $year }}" {{ ($tahunAjaran ?? '') == $year ? 'selected' : '' }}>
                                {{ $year }}/{{ (int)$year + 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3" id="date_range_container">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3" id="end_date_container">
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
        <h6 class="mb-0">Summary Per Bulan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Sudah Dibayar</th>
                        <th>Belum Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summaryData as $bulanKey => $bulanData)
                    <tr>
                        <td>{{ $bulanData['bulan'] }}</td>
                        <td>
                            <span class="badge bg-success">{{ $bulanData['sudah_dibayar']['transaksi'] }} transaksi</span>
                            <br><small>Rp {{ number_format($bulanData['sudah_dibayar']['nominal']) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">{{ $bulanData['belum_dibayar']['transaksi'] }} transaksi</span>
                            <br><small>Rp {{ number_format($bulanData['belum_dibayar']['nominal']) }}</small>
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

<!-- Data Tabel -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            @if($jenisLaporan == 'pengumuman')
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Data Pengumuman Denda & Keterlambatan
            @else
                <i class="fas fa-chart-bar text-primary me-2"></i>Data Laporan Tahunan Denda
            @endif
        </h6>
        <a href="{{ route('laporan.print.denda', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            @if($jenisLaporan == 'pengumuman')
                <i class="fas fa-print me-2"></i>Cetak Pengumuman
            @else
                <i class="fas fa-print me-2"></i>Cetak Laporan
            @endif
        </a>
    </div>
    <div class="card-body p-0">
        @if($dendaBelumBayar->count() > 0 || $dendaSudahBayar->count() > 0 || $peminjamanTerlambat->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td><code>{{ $denda->peminjaman->kode_peminjaman ?? '-' }}</code></td>
                            <td>{{ $denda->peminjaman->anggota->nama_lengkap ?? '-' }} {{ $denda->peminjaman->anggota->kelas ?? '' }}</td>
                            <td>{{ Str::limit($denda->peminjaman->buku->judul ?? '-', 30) }}</td>
                            <td>{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $denda->peminjaman->tanggal_kembali_rencana->format('d/m/Y') ?? '-' }}</td>
                            <td><span class="badge bg-danger">Denda Belum Dibayar</span></td>
                            <td><span class="badge bg-warning">{{ $denda->hari_terlambat ?? 0 }} hari</span></td>
                            <td>Rp {{ number_format($denda->denda_per_hari ?? 1000) }}</td>
                            <td><strong>Rp {{ number_format($denda->total_denda) }}</strong></td>
                            <td>{{ $denda->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                        
                        {{-- Data Peminjaman Terlambat (hanya untuk pengumuman) --}}
                        @if($jenisLaporan == 'pengumuman')
                            @foreach($peminjamanTerlambat as $peminjaman)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td><code>{{ $peminjaman->kode_peminjaman ?? '-' }}</code></td>
                                <td>{{ $peminjaman->anggota->nama_lengkap ?? '-' }} {{ $peminjaman->anggota->kelas ?? '' }}</td>
                                <td>{{ Str::limit($peminjaman->buku->judul ?? '-', 30) }}</td>
                                <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') ?? '-' }}</td>
                                <td><span class="badge bg-warning">Terlambat Aktif</span></td>
                                @php
                                    $hariTerlambat = $this->hitungHariKerja($peminjaman->tanggal_kembali_rencana, Carbon::now());
                                    $dendaTerlambat = max(0, $hariTerlambat) * 1000;
                                @endphp
                                <td><span class="badge bg-danger">{{ $hariTerlambat }} hari</span></td>
                                <td>Rp 1,000</td>
                                <td><strong>Rp {{ number_format($dendaTerlambat) }}</strong></td>
                                <td>-</td>
                            </tr>
                            @endforeach
                        @endif
                        
                        {{-- Data Denda Sudah Dibayar (hanya untuk tahunan) --}}
                        @if($jenisLaporan == 'tahunan')
                            @foreach($dendaSudahBayar as $denda)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td><code>{{ $denda->peminjaman->kode_peminjaman ?? '-' }}</code></td>
                                <td>{{ $denda->peminjaman->anggota->nama_lengkap ?? '-' }} {{ $denda->peminjaman->anggota->kelas ?? '' }}</td>
                                <td>{{ Str::limit($denda->peminjaman->buku->judul ?? '-', 30) }}</td>
                                <td>{{ $denda->peminjaman->tanggal_pinjam->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $denda->peminjaman->tanggal_kembali_rencana->format('d/m/Y') ?? '-' }}</td>
                                <td><span class="badge bg-success">Denda Sudah Dibayar</span></td>
                                <td><span class="badge bg-warning">{{ $denda->hari_terlambat ?? 0 }} hari</span></td>
                                <td>Rp {{ number_format($denda->denda_per_hari ?? 1000) }}</td>
                                <td><strong>Rp {{ number_format($denda->total_denda) }}</strong></td>
                                <td>{{ $denda->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <h5 class="text-muted">Tidak ada data untuk periode yang dipilih.</h5>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisLaporan = document.getElementById('jenis_laporan');
    const tahunAjaranContainer = document.getElementById('tahun_ajaran_container');
    const dateRangeContainer = document.getElementById('date_range_container');
    const endDateContainer = document.getElementById('end_date_container');
    
    function toggleFilters() {
        const selectedValue = jenisLaporan.value;
        
        if (selectedValue === 'pengumuman') {
            // Untuk pengumuman, tampilkan semua filter
            tahunAjaranContainer.style.display = 'block';
            dateRangeContainer.style.display = 'block';
            endDateContainer.style.display = 'block';
        } else {
            // Untuk tahunan, sembunyikan filter tanggal
            tahunAjaranContainer.style.display = 'block';
            dateRangeContainer.style.display = 'none';
            endDateContainer.style.display = 'none';
        }
    }
    
    // Jalankan saat halaman dimuat
    toggleFilters();
    
    // Jalankan saat jenis laporan berubah
    jenisLaporan.addEventListener('change', toggleFilters);
});
</script>
@endsection 