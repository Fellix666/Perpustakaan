@extends('layouts.app')
@section('title', 'Detail Pengembalian')
@section('page-title', 'Detail Pengembalian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pengembalian.index') }}">Data Pengembalian</a></li>
    <li class="breadcrumb-item active">Detail Pengembalian</li>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-undo-alt fa-lg me-2"></i>
                <h5 class="mb-0">Detail Pengembalian</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <span class="fw-bold text-secondary">Kode Peminjaman</span><br>
                            <span class="fs-5"><code>{{ $peminjaman->kode_peminjaman }}</code></span>
                        </div>
                        <div class="mb-3">
                            <span class="fw-bold text-secondary">Tanggal Kembali (Aktual)</span><br>
                            <span>{{ $peminjaman->tanggal_kembali_aktual ? $peminjaman->tanggal_kembali_aktual->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="fw-bold text-secondary">Status</span><br>
                            @if($peminjaman->status == 'dikembalikan')
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Dikembalikan</span>
                            @elseif($peminjaman->status == 'terlambat')
                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i> Terlambat</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <span class="fw-bold text-secondary">Denda</span><br>
                            {{-- PERBAIKAN DI SINI --}}
                            @if($peminjaman->dendaRecord)
                                <span class="text-danger fw-bold fs-6">Rp {{ number_format($peminjaman->dendaRecord->total_denda, 0, ',', '.') }}</span><br>
                                <small class="badge bg-{{ $peminjaman->dendaRecord->status_bayar == 'belum-dibayar' ? 'danger' : 'success' }}">{{ $peminjaman->dendaRecord->status_bayar == 'belum-dibayar' ? 'Belum Lunas' : 'Lunas' }}</small>
                            @else
                                <span class="text-muted">Rp {{ number_format($peminjaman->denda, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4 p-3 rounded bg-light border">
                            <div class="mb-2 text-primary fw-bold"><i class="fas fa-user me-2"></i>Data Anggota</div>
                            <div class="mb-1"><span class="fw-bold">Nama:</span> {{ $peminjaman->anggota->nama_lengkap ?? '-' }}</div>
                            <div class="mb-1"><span class="fw-bold">No. Anggota:</span> {{ $peminjaman->anggota->nomor_anggota ?? '-' }}</div>
                            <div class="mb-1"><span class="fw-bold">Kelas:</span> {{ $peminjaman->anggota->kelas ?? '-' }}</div>
                        </div>
                        <div class="mb-4 p-3 rounded bg-light border">
                            <div class="mb-2 text-primary fw-bold"><i class="fas fa-book me-2"></i>Data Buku</div>
                            <div class="mb-1"><span class="fw-bold">Judul:</span> {{ $peminjaman->buku->judul ?? '-' }}</div>
                            <div class="mb-1"><span class="fw-bold">Kode Buku:</span> {{ $peminjaman->buku->kode_buku ?? '-' }}</div>
                            <div class="mb-1"><span class="fw-bold">Pengarang:</span> {{ $peminjaman->buku->pengarang ?? '-' }}</div>
                            <div class="mb-1"><span class="fw-bold">Penerbit:</span> {{ $peminjaman->buku->penerbit ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end bg-white border-0">
                <a href="{{ route('pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection