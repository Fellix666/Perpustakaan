@extends('layouts.app')

@section('title', 'Dashboard - Perpustakaan Digital')
@section('page-title', 'Dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-muted small fw-bold">Total Anggota</div>
                        <div class="h2 mb-0 text-primary">{{ number_format($totalAnggota) }}</div>
                        <div class="small text-success">
                            <i class="fas fa-arrow-up me-1"></i>Aktif
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-muted small fw-bold">Total Buku</div>
                        <div class="h2 mb-0 text-success">{{ number_format($totalBuku) }}</div>
                        <div class="small text-muted">Tersedia: {{ number_format($bukuTersedia) }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-book fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-muted small fw-bold">Sedang Dipinjam</div>
                        <div class="h2 mb-0 text-warning">{{ number_format($bukuDipinjam) }}</div>
                        <div class="small text-warning">
                            <i class="fas fa-clock me-1"></i>Dalam sirkulasi
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-exchange-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-uppercase text-muted small fw-bold">Total Denda</div>
                        <div class="h2 mb-0 text-danger">Rp {{ number_format($totalDenda) }}</div>
                        <div class="small text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>Belum dibayar
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Trend Peminjaman 7 Hari Terakhir</h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="100" style="width: 100%; height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Status Peminjaman</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="100" style="width: 100%; height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2 text-info"></i>Peminjaman Terbaru</h5>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjamanTerbaru as $peminjaman)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            @if($peminjaman->anggota->foto)
                                                <img src="{{ asset('storage/anggota/' . $peminjaman->anggota->foto) }}" 
                                                     class="rounded-circle" width="32" height="32" alt="Foto"
                                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('fallback-icon'); this.nextElementSibling.style.display='flex';">
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center fallback-icon" 
                                                     style="width: 32px; height: 32px; display: none !important;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $peminjaman->anggota->nama_lengkap }}</div>
                                            <small class="text-muted">{{ $peminjaman->anggota->kelas }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold">{{ $peminjaman->buku->judul }}</div>
                                        <small class="text-muted">{{ $peminjaman->buku->kategori->nama_kategori ?? 'Tanpa Kategori' }}</small>
                                    </div>
                                </td>
                                <td>{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td>
                                    @if($peminjaman->status_realtime == 'dikembalikan')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @elseif($peminjaman->status_realtime == 'terlambat')
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>Belum ada data peminjaman</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0"><i class="fas fa-star me-2 text-warning"></i>Buku Terpopuler</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($bukuTerpopuler as $buku)
                    <div class="list-group-item border-0 d-flex align-items-center">
                        <div class="avatar-sm me-3">
                            @if($buku->cover)
                                <img src="{{ asset('storage/buku/' . $buku->cover) }}" 
                                     class="rounded" width="40" height="50" alt="Cover"
                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('fallback-icon'); this.nextElementSibling.style.display='flex';">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center fallback-icon" 
                                     style="width: 40px; height: 50px; display: none !important;">
                                    <i class="fas fa-book text-muted"></i>
                                </div>
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 50px;">
                                    <i class="fas fa-book text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $buku->judul }}</div>
                            <small class="text-muted">{{ $buku->total_peminjaman ?? 0 }} kali dipinjam</small>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item border-0 text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-book fa-2x mb-2"></i>
                            <p class="mb-0">Belum ada data buku terpopuler</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Data dari controller dengan validasi yang lebih ketat
const trendData = @json(isset($trendPeminjaman) ? $trendPeminjaman : []);
const statusData = @json(isset($statusPeminjaman) ? $statusPeminjaman : []);

console.log('Trend Data:', trendData);
console.log('Status Data:', statusData);

// Trend Chart
function initTrendChart() {
    try {
        const canvas = document.getElementById('trendChart');
        if (!canvas) {
            console.error('Canvas trendChart tidak ditemukan');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Context tidak dapat dibuat');
            return;
        }
        
        // Pastikan data ada
        const labels = trendData && trendData.length > 0 ? trendData.map(item => item.date) : ['01/01', '02/01', '03/01', '04/01', '05/01', '06/01', '07/01'];
        const data = trendData && trendData.length > 0 ? trendData.map(item => item.count) : [0, 0, 0, 0, 0, 0, 0];
        
        console.log('Membuat trend chart dengan labels:', labels, 'data:', data);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peminjaman',
                    data: data,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(75, 192, 192)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        console.log('Trend chart berhasil dibuat');
    } catch (error) {
        console.error('Error membuat trend chart:', error);
    }
}

// Status Chart
function initStatusChart() {
    try {
        const canvas = document.getElementById('statusChart');
        if (!canvas) {
            console.error('Canvas statusChart tidak ditemukan');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Context tidak dapat dibuat');
            return;
        }
        
        // Pastikan data ada dan konversi ke object jika perlu
        let statusDataObj = {};
        if (Array.isArray(statusData) && statusData.length > 0) {
            statusDataObj = statusData;
        } else if (typeof statusData === 'object' && statusData !== null) {
            statusDataObj = statusData;
        } else {
            statusDataObj = {
                'Dipinjam': 0,
                'Dikembalikan': 0,
                'Terlambat': 0
            };
        }
        
        const labels = Object.keys(statusDataObj).length > 0 ? Object.keys(statusDataObj) : ['Dipinjam', 'Dikembalikan', 'Terlambat'];
        const data = Object.keys(statusDataObj).length > 0 ? Object.values(statusDataObj) : [0, 0, 0];
        
        console.log('Membuat status chart dengan labels:', labels, 'data:', data);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        console.log('Status chart berhasil dibuat');
    } catch (error) {
        console.error('Error membuat status chart:', error);
    }
}

// Initialize charts when page loads
function initializeCharts() {
    console.log('Initializing charts...');
    initTrendChart();
    initStatusChart();
}

// Tunggu sampai DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing charts...');
        setTimeout(initializeCharts, 100);
    });
} else {
    console.log('DOM already loaded, initializing charts immediately...');
    setTimeout(initializeCharts, 100);
}

// Fallback untuk memastikan chart dibuat
setTimeout(initializeCharts, 500);
</script>
@endpush

@push('styles')
<style>
    .fallback-icon {
        display: none !important;
    }
    
    .avatar-sm img {
        object-fit: cover;
    }
</style>
@endpush