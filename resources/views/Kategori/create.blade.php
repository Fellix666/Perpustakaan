@extends('layouts.app')

@section('title', 'Tambah Kategori - Nama Aplikasi')
@section('page-title', 'Tambah Kategori')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Data Kategori</a></li>
<li class="breadcrumb-item active">Tambah Kategori</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tag me-2"></i>Form Tambah Kategori</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('kategori.store') }}" method="POST" id="formKategori">
                    @csrf
                    <div class="mb-3">
                        <label for="kode_kategori" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_kategori') is-invalid @enderror" id="kode_kategori" name="kode_kategori" value="{{ old('kode_kategori') }}" placeholder="Contoh: 000">
                        @error('kode_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_kategori') is-invalid @enderror" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" placeholder="Masukkan nama kategori">
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button type="submit" form="formKategori" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('formKategori').addEventListener('submit', function(e) {
    const requiredFields = ['nama_kategori'];
    let isValid = true;
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    if (!isValid) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Form Tidak Lengkap',
            text: 'Silakan lengkapi semua field yang wajib diisi!',
            timer: 3000,
            showConfirmButton: false
        });
    }
});
</script>
@endsection 