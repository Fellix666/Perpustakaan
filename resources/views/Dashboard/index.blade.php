@extends('layouts.app')

@section('title', 'Dashboard - Nama Aplikasi')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-white-50 small fw-bold">Total Anggota</div>
                        <div class="h2 mb-0 text-white">{{ number_format($totalAnggota) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card-success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-white-50 small fw-bold">Total Buku</div>
                        <div class="h2 mb-0 text-white">{{ number_format($totalBuku) }}</div>
                        <div class="small text-white-50">Tersedia: {{ number_format($bukuTersedia) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-book fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card-warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-white-50 small fw-bold">Sedang Dipinjam</div>
                        <div class="h2 mb-0 text-white">{{ number_format($bukuDipinjam) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exchange-alt fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card-info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-white-50 small fw-bold">Total Denda</div>
                        <div class="h2 mb-0 text-white">Rp {{ number_format($totalDenda) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Daily Activity -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Aktivitas Hari Ini</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <div class="h4 text-primary">{{ $peminjamanHariIni }}</div>
                            <div class="small text-muted">Peminjaman</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <div class="h4 text-success">{{ $pengembalianHariIni }}</div>
                            <div class="small text-muted">Pengembalian</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="h4 text-danger">{{ $terlambat }}</div>
                        <div class="small text-muted">Terlambat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Peminjaman Terbaru</h5>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($peminjamanTerbaru->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamanTerbaru as $peminjaman)
                            <tr>
                                <td>
                                    <code>{{ $peminjaman->kode_peminjaman }}</code>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $peminjaman->anggota->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $peminjaman->anggota->kelas }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ Str::limit($peminjaman->buku->judul, 30) }}</div>
                                    <small class="text-muted">{{ $peminjaman->buku->pengarang }}</small>
                                </td>
                                <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td>
                                    @if($peminjaman->status == 'dipinjam')
                                        <span class="badge bg-warning">Dipinjam</span>
                                    @elseif($peminjaman->status == 'dikembalikan')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @else
                                        <span class="badge bg-danger">Terlambat</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada peminjaman</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Popular Books -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Buku Terpopuler</h5>
            </div>
            <div class="card-body">
                @if($bukuTerpopuler->count() > 0)
                    @foreach($bukuTerpopuler as $index => $buku)
                    <div class="d-flex align-items-center mb-3 @if(!$loop->last) border-bottom pb-3 @endif">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 14px; font-weight: bold;">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="fw-bold">{{ Str::limit($buku->judul, 25) }}</div>
                            <small class="text-muted">{{ $buku->pengarang }}</small>
                            <div class="small text-success">
                                <i class="fas fa-chart-line me-1"></i>{{ $buku->peminjamans_count }} kali dipinjam
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center py-3">
                    <i class="fas fa-book fa-2x text-muted mb-2"></i>
                    <p class="text-muted small mb-0">Belum ada data</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto refresh dashboard setiap 5 menit
setTimeout(function() {
    location.reload();
}, 300000);
</script>
@endsection