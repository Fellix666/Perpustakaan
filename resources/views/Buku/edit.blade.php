@extends('layouts.app')

@section('title', 'Edit Buku - Nama Aplikasi')
@section('page-title', 'Edit Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Data Buku</a></li>
<li class="breadcrumb-item active">Edit Buku</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book-edit me-2"></i>Form Edit Buku</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('buku.update', $buku) }}" method="POST" id="formBuku" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="kode_buku" class="form-label">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_buku') is-invalid @enderror" id="kode_buku" name="kode_buku" value="{{ old('kode_buku', $buku->kode_buku) }}" placeholder="Contoh: BKU001">
                        @error('kode_buku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn', $buku->isbn) }}" placeholder="ISBN (opsional)">
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $buku->judul) }}" placeholder="Masukkan judul buku">
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pengarang" class="form-label">Pengarang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pengarang') is-invalid @enderror" id="pengarang" name="pengarang" value="{{ old('pengarang', $buku->pengarang) }}" placeholder="Masukkan nama pengarang">
                        @error('pengarang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="penerbit" class="form-label">Penerbit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('penerbit') is-invalid @enderror" id="penerbit" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" placeholder="Masukkan nama penerbit">
                        @error('penerbit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tahun_terbit" class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('tahun_terbit') is-invalid @enderror" id="tahun_terbit" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" placeholder="Contoh: 2024">
                        @error('tahun_terbit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_halaman" class="form-label">Jumlah Halaman</label>
                        <input type="number" class="form-control @error('jumlah_halaman') is-invalid @enderror" id="jumlah_halaman" name="jumlah_halaman" value="{{ old('jumlah_halaman', $buku->jumlah_halaman) }}" placeholder="Jumlah halaman (opsional)">
                        @error('jumlah_halaman')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi buku (opsional)">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="stok_total" class="form-label">Stok Total <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('stok_total') is-invalid @enderror" id="stok_total" name="stok_total" value="{{ old('stok_total', $buku->stok_total) }}" placeholder="Masukkan stok total buku">
                        @error('stok_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id', $buku->kategori_id) == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="rak_id" class="form-label">Rak <span class="text-danger">*</span></label>
                        <select class="form-select @error('rak_id') is-invalid @enderror" id="rak_id" name="rak_id">
                            <option value="">-- Pilih Rak --</option>
                            @foreach($raks as $rak)
                                <option value="{{ $rak->id }}" {{ old('rak_id', $buku->rak_id) == $rak->id ? 'selected' : '' }}>{{ $rak->nama_rak }}</option>
                            @endforeach
                        </select>
                        @error('rak_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="tersedia" {{ old('status', $buku->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="tidak-tersedia" {{ old('status', $buku->status) == 'tidak-tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="cover" class="form-label">Cover Buku (opsional)</label>
                        <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover" name="cover" accept="image/*">
                        @error('cover')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="previewCover" class="mt-2">
                            @if($buku->cover)
                                <img src="{{ asset('storage/buku/'.$buku->cover) }}" alt="Cover Lama" class="img-thumbnail" style="max-width:100px;">
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('buku.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button type="submit" form="formBuku" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Buku
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
document.getElementById('formBuku').addEventListener('submit', function(e) {
    const requiredFields = ['kode_buku', 'judul', 'pengarang', 'penerbit', 'tahun_terbit', 'stok_total', 'kategori_id', 'rak_id', 'status'];
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

document.getElementById('cover').addEventListener('change', function(e) {
    const preview = document.getElementById('previewCover');
    preview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview Cover" class="img-thumbnail" style="max-width:100px;">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endsection 