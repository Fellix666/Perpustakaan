@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')
@section('page-title', 'Laporan & Statistik')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
<div class="row">
    {{-- Kolom Kiri - Navigasi Laporan --}}
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Navigasi Laporan</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('laporan.transaksi') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-exchange-alt me-2 text-success"></i> Laporan Peminjaman
                </a>
                <a href="{{ route('pengunjung.laporan') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-clock me-2 text-info"></i> Laporan Pengunjung
                </a>
                <a href="{{ route('laporan.denda') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-exclamation-triangle me-2 text-warning"></i> Laporan Denda & Keterlambatan
                </a>
                <a href="{{ route('laporan.analisis-peminjaman') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-chart-line me-2 text-success"></i> Analisis Peminjaman
                </a>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan - Statistik --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Keseluruhan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-muted">Total Anggota Aktif</h6>
                                <h3 class="fw-bold">{{ number_format($totalAnggota) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-muted">Total Judul Buku</h6>
                                <h3 class="fw-bold">{{ number_format($totalBuku) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-muted">Total Peminjaman</h6>
                                <h3 class="fw-bold">{{ number_format($totalPeminjaman) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title text-muted">Total Denda Belum Dibayar</h6>
                                <h3 class="fw-bold text-danger">Rp {{ number_format($dendaBelumBayar) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection