@extends('layouts.app')

@section('title', 'Pembayaran Denda')
@section('page-title', 'Pembayaran Denda')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('denda.index') }}">Data Denda</a></li>
<li class="breadcrumb-item active">Pembayaran Denda</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-money-bill-wave fa-lg me-2"></i>
                <h5 class="mb-0">Pembayaran Denda</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="fw-bold text-secondary">Kode Peminjaman:</span> <code>{{ $denda->peminjaman->kode_peminjaman ?? '-' }}</code><br>
                    <span class="fw-bold text-secondary">Anggota:</span> {{ $denda->peminjaman->anggota->nama_lengkap ?? '-' }}<br>
                    <span class="fw-bold text-secondary">Buku:</span> {{ $denda->peminjaman->buku->judul ?? '-' }}<br>
                </div>
                <div class="mb-3">
                    <span class="fw-bold text-secondary">Hari Terlambat:</span> {{ $denda->hari_terlambat }}<br>
                    <span class="fw-bold text-secondary">Total Denda:</span> <span class="text-danger fw-bold">Rp {{ number_format($denda->total_denda, 0, ',', '.') }}</span><br>
                    <span class="fw-bold text-secondary">Status Bayar:</span>
                    @if($denda->status_bayar == 'belum-dibayar')
                        <span class="badge bg-danger">Belum Lunas</span>
                    @else
                        <span class="badge bg-success">Lunas</span>
                    @endif
                </div>
                @if($denda->status_bayar == 'belum-dibayar')
                <form action="{{ route('denda.proses-bayar', $denda->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="tanggal_bayar" class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_bayar') is-invalid @enderror" id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                        @error('tanggal_bayar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Bayar & Lunas
                        </button>
                        <a href="{{ route('denda.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </form>
                @else
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle me-2"></i>Denda sudah lunas.
                </div>
                <div class="text-end">
                    <a href="{{ route('denda.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 