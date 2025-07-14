@extends('layouts.app')

@section('title', 'Edit Rak - Nama Aplikasi')
@section('page-title', 'Edit Rak')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('rak.index') }}">Data Rak</a></li>
<li class="breadcrumb-item active">Edit Rak</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-archive me-2"></i>Form Edit Rak</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rak.update', $rak) }}" method="POST" id="formRak">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="kode_rak" class="form-label">Kode Rak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_rak') is-invalid @enderror" id="kode_rak" name="kode_rak" value="{{ old('kode_rak', $rak->kode_rak) }}" placeholder="Contoh: RK001">
                        @error('kode_rak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $rak->kapasitas) }}" placeholder="Masukkan kapasitas rak">
                        @error('kapasitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_rak" class="form-label">Nama Rak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_rak') is-invalid @enderror" id="nama_rak" name="nama_rak" value="{{ old('nama_rak', $rak->nama_rak) }}" placeholder="Masukkan nama rak">
                        @error('nama_rak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi', $rak->lokasi) }}" placeholder="Lokasi rak (opsional)">
                        @error('lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi rak (opsional)">{{ old('deskripsi', $rak->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('rak.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button type="submit" form="formRak" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Rak
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
document.getElementById('formRak').addEventListener('submit', function(e) {
    const requiredFields = ['nama_rak'];
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