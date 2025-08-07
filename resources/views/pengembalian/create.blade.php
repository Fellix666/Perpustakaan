@extends('layouts.app')
@section('title', 'Form Pengembalian Buku')
@section('page-title', 'Form Pengembalian Buku')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pengembalian.index') }}">Data Pengembalian</a></li>
    <li class="breadcrumb-item active">Pengembalian Buku</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-undo-alt me-2"></i>Form Pengembalian Buku</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pengembalian.store', $peminjaman->id) }}" method="POST" id="formPengembalian">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Anggota</label>
                        <input type="text" class="form-control" value="{{ $peminjaman->anggota->nama_lengkap }} ({{ $peminjaman->anggota->nomor_anggota }})" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Buku</label>
                        <input type="text" class="form-control" value="{{ $peminjaman->buku->judul }} ({{ $peminjaman->buku->kode_buku }})" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="text" class="form-control" value="{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kembali (Rencana)</label>
                        <input type="text" class="form-control" value="{{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kembali_aktual" class="form-label">Tanggal Kembali Aktual <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_kembali_aktual') is-invalid @enderror" id="tanggal_kembali_aktual" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual') }}" min="{{ $peminjaman->tanggal_kembali_rencana->format('Y-m-d') }}" required>
                        @error('tanggal_kembali_aktual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Tanggal tidak boleh lebih awal dari {{ $peminjaman->tanggal_kembali_rencana->format('d/m/Y') }}</small>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="2" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    <div>
                        <button type="submit" form="formPengembalian" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Pengembalian</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection