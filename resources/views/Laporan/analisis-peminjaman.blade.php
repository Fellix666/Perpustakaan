@extends('layouts.app')

@section('title', 'Analisis Peminjaman')
@section('page-title', 'Analisis Peminjaman')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
<li class="breadcrumb-item active">Analisis Peminjaman</li>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Analisis Peminjaman</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.analisis-peminjaman') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select">
                        @if($availableYears->count() > 0)
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $tahunAjaran == $year ? 'selected' : '' }}>
                                    {{ $year }}/{{ (int)$year + 1 }}
                                </option>
                            @endforeach
                        @else
                            <option value="{{ date('Y') }}">{{ date('Y') }}/{{ date('Y') + 1 }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-search me-2"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Analisis Peminjaman Tahun Ajaran {{ $tahunAjaran }}/{{ (int)$tahunAjaran + 1 }}</h5>
        <a href="{{ route('laporan.print.analisis-peminjaman', request()->all()) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print me-2"></i>Cetak
        </a>
    </div>
    <div class="card-body">
        <!-- Statistik Utama -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ number_format($statistikData['totalPeminjaman']) }}</h4>
                        <small>Total Peminjaman</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ number_format($statistikData['rataRataPerBulan']) }}</h4>
                        <small>Rata-rata per Bulan</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ $statistikData['kelasTertinggi']->kelas ?? 'N/A' }}</h4>
                        <small>Kelas Tertinggi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ $statistikData['bulanTertinggi'] ? \Carbon\Carbon::createFromDate($statistikData['bulanTertinggi']->tahun, $statistikData['bulanTertinggi']->bulan, 1)->format('M Y') : 'N/A' }}</h4>
                        <small>Bulan Tertinggi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Statistik -->
        <div class="row mb-4">
            <!-- Persentase per Tingkat -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Persentase per Tingkat</h6>
                    </div>
                    <div class="card-body">
                        @foreach($statistikData['persentaseTingkat'] as $tingkat => $data)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">{{ $tingkat }}</span>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 100px; height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $data['persentase'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $data['persentase'] }}% ({{ $data['total'] }})</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kategori Buku Favorit -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-book me-2"></i>Kategori Buku Favorit</h6>
                    </div>
                    <div class="card-body">
                        @forelse($statistikData['kategoriFavorit'] as $index => $kategori)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $index + 1 }}. {{ $kategori->nama_kategori }}</span>
                            <span class="badge bg-primary">{{ $kategori->total }}</span>
                        </div>
                        @empty
                        <p class="text-muted mb-0">Tidak ada data kategori</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Peminjam -->
        <div class="row mb-4">
            <!-- Top 10 Overall -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top 10 Peminjam Terbanyak</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topPeminjamData['topPeminjam'] as $index => $anggota)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <span class="badge bg-warning">🥇</span>
                                            @elseif($index == 1)
                                                <span class="badge bg-secondary">🥈</span>
                                            @elseif($index == 2)
                                                <span class="badge bg-warning">🥉</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $anggota->nama_lengkap }}</td>
                                        <td>{{ $anggota->kelas }}</td>
                                        <td class="text-center fw-bold">{{ $anggota->total_peminjaman }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 3 per Kelas -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-medal me-2 text-info"></i>Top 3 per Kelas</h6>
                    </div>
                    <div class="card-body">
                        @forelse($topPeminjamData['topPerKelas'] as $kelas => $data)
                        <div class="mb-3">
                            <h6 class="text-primary">{{ $kelas }}</h6>
                            @foreach($data as $index => $anggota)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span>{{ $index + 1 }}. {{ $anggota->nama_lengkap }}</span>
                                <span class="badge bg-primary">{{ $anggota->total_peminjaman }}</span>
                            </div>
                            @endforeach
                        </div>
                        @empty
                        <p class="text-muted mb-0">Tidak ada data per kelas</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 per Bulan -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-success"></i>Top 5 Peminjam per Bulan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($topPeminjamData['topPerBulan'] as $bulanKey => $bulanData)
                    <div class="col-md-4 mb-3">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">{{ $bulanData['bulan'] }}</h6>
                            </div>
                            <div class="card-body">
                                @foreach($bulanData['data'] as $index => $anggota)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small>{{ $index + 1 }}. {{ $anggota->nama_lengkap }}</small>
                                    <small class="badge bg-primary">{{ $anggota->total_peminjaman }}</small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <p class="text-muted text-center mb-0">Tidak ada data per bulan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 