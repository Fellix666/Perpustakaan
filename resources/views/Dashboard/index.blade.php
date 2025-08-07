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
                        <div class="small text-muted">Dalam sirkulasi</div>
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
                        <div class="small text-danger">Belum dibayar</div>
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

<!-- Charts Row -->
<div class="row">
    <!-- Trend Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Trend Peminjaman 7 Hari Terakhir
                </h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-pie me-2"></i>Status Peminjaman
                </h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row">
    <!-- Activity Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Aktivitas Hari Ini
                </h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Kategori Buku Terpopuler
                </h6>
            </div>
            <div class="card-body">
                <div style="height: 300px; position: relative;">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-clock me-2"></i>Peminjaman Terbaru
                </h6>
            </div>
            <div class="card-body">
                @if($peminjamanTerbaru->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Anggota</th>
                                    <th>Buku</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($peminjamanTerbaru as $peminjaman)
                                <tr>
                                    <td>{{ $peminjaman->anggota->nama_lengkap }}</td>
                                    <td>{{ Str::limit($peminjaman->buku->judul, 30) }}</td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Belum ada data peminjaman.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-star me-2"></i>Buku Terpopuler
                </h6>
            </div>
            <div class="card-body">
                @if($bukuTerpopuler->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Total Pinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bukuTerpopuler as $buku)
                                <tr>
                                    <td>{{ Str::limit($buku->judul, 30) }}</td>
                                    <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $buku->total_peminjaman }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Belum ada data buku terpopuler.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Inline JavaScript -->
<script>
// Data dari controller
const trendData = @json($trendPeminjaman ?? []);
const statusData = @json($statusPeminjaman ?? []);
const kategoriData = @json($kategoriTerpopuler ?? []);
const pengunjungHariIni = {{ $pengunjungHariIni ?? 0 }};
const peminjamanHariIni = {{ $peminjamanHariIni ?? 0 }};
const pengembalianHariIni = {{ $pengembalianHariIni ?? 0 }};

// Create charts immediately
if (typeof Chart !== 'undefined') {
    createCharts();
}

function createCharts() {
    try {
        // Trend Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            const labels = trendData.length > 0 ? trendData.map(item => item.date) : ['01/01', '02/01', '03/01', '04/01', '05/01', '06/01', '07/01'];
            const data = trendData.length > 0 ? trendData.map(item => item.count) : [0, 0, 0, 0, 0, 0, 0];
            
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Peminjaman',
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderWidth: 3,
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
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgb(75, 192, 192)',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#666'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#666'
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: 'rgb(75, 192, 192)'
                        }
                    }
                }
            });
        }

        // Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const labels = Object.keys(statusData);
            const data = Object.values(statusData);
            
            const statusChart = new Chart(statusCtx, {
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
                        borderColor: [
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Activity Chart
        const activityCtx = document.getElementById('activityChart');
        if (activityCtx) {
            const activityChart = new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: ['Pengunjung', 'Peminjaman', 'Pengembalian'],
                    datasets: [{
                        label: 'Aktivitas Hari Ini',
                        data: [pengunjungHariIni, peminjamanHariIni, pengembalianHariIni],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#666'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666'
                            }
                        }
                    }
                }
            });
        }

        // Kategori Chart
        const kategoriCtx = document.getElementById('kategoriChart');
        if (kategoriCtx) {
            const labels = kategoriData.length > 0 ? kategoriData.map(item => item.nama_kategori) : ['Kategori 1', 'Kategori 2', 'Kategori 3'];
            const data = kategoriData.length > 0 ? kategoriData.map(item => item.total_peminjaman) : [0, 0, 0];
            
            const kategoriChart = new Chart(kategoriCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Peminjaman',
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                color: '#666'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#666',
                                maxRotation: 45
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error creating charts:', error);
    }
}
</script>
@endsection