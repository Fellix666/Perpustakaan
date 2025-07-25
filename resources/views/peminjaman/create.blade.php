@extends('layouts.app')

@section('title', 'Form Peminjaman Buku')
@section('page-title', 'Form Peminjaman Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Data Peminjaman</a></li>
<li class="breadcrumb-item active">Tambah Peminjaman</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book-reader me-2"></i>Form Peminjaman Buku</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('peminjaman.store') }}" method="POST" id="formPeminjaman">
                    @csrf
                    <div class="mb-3">
                        <label for="anggota_id" class="form-label">Anggota <span class="text-danger">*</span></label>
                        <select class="form-select @error('anggota_id') is-invalid @enderror" id="anggota_id" name="anggota_id" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($anggotas as $anggota)
                                <option value="{{ $anggota->id }}" {{ old('anggota_id') == $anggota->id ? 'selected' : '' }}>{{ $anggota->nama_lengkap }} ({{ $anggota->nomor_anggota }})</option>
                            @endforeach
                        </select>
                        @error('anggota_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="buku_id" class="form-label">Buku <span class="text-danger">*</span></label>
                        <select class="form-select @error('buku_id') is-invalid @enderror" id="buku_id" name="buku_id" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($bukus as $buku)
                                <option value="{{ $buku->id }}" {{ old('buku_id') == $buku->id ? 'selected' : '' }}>{{ $buku->judul }} ({{ $buku->kode_buku }})</option>
                            @endforeach
                        </select>
                        @error('buku_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_pinjam') is-invalid @enderror" id="tanggal_pinjam" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                        @error('tanggal_pinjam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kembali_rencana" class="form-label">Tanggal Kembali (Rencana) <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_kembali_rencana') is-invalid @enderror" id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}" required>
                        @error('tanggal_kembali_rencana')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button type="submit" form="formPeminjaman" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Peminjaman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 