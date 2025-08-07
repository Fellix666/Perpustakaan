@extends('layouts.app')

@section('title', 'Tambah Anggota - Nama Aplikasi')
@section('page-title', 'Tambah Anggota')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('anggota.index') }}">Data Anggota</a></li>
<li class="breadcrumb-item active">Tambah Anggota</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Form Tambah Anggota Baru
                </h5>
            </div>
            <div class="card-body">
                {{-- ====================================================== --}}
                {{-- <<<--- PERBAIKAN: Tambahkan enctype di sini ---<<< --}}
                {{-- ====================================================== --}}
                <form action="{{ route('anggota.store') }}" method="POST" id="formAnggota" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- ... Sisa isi form Anda tetap sama ... --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nomor_anggota" class="form-label">
                                    Nomor Anggota <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nomor_anggota') is-invalid @enderror" 
                                       id="nomor_anggota" 
                                       name="nomor_anggota" 
                                       value="{{ old('nomor_anggota') }}"
                                       placeholder="Contoh: AGT001">
                                @error('nomor_anggota')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nomor anggota harus unik
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_daftar" class="form-label">
                                    Tanggal Daftar <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('tanggal_daftar') is-invalid @enderror" 
                                       id="tanggal_daftar" 
                                       name="tanggal_daftar" 
                                       value="{{ old('tanggal_daftar', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                                @error('tanggal_daftar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nama_lengkap') is-invalid @enderror" 
                               id="nama_lengkap" 
                               name="nama_lengkap" 
                               value="{{ old('nama_lengkap') }}"
                               placeholder="Masukkan nama lengkap anggota">
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Jakarta">
                                @error('tempat_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}">
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                        id="jenis_kelamin" 
                                        name="jenis_kelamin">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                        Perempuan
                                    </option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelas" class="form-label">
                                    Kelas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('kelas') is-invalid @enderror" 
                                        id="kelas" 
                                        name="kelas">
                                    <option value="">Pilih Kelas</option>
                                    <optgroup label="Kelas VII">
                                        <option value="VII A" {{ old('kelas') == 'VII A' ? 'selected' : '' }}>VII A</option>
                                        <option value="VII B" {{ old('kelas') == 'VII B' ? 'selected' : '' }}>VII B</option>
                                        <option value="VII C" {{ old('kelas') == 'VII C' ? 'selected' : '' }}>VII C</option>
                                        <option value="VII D" {{ old('kelas') == 'VII D' ? 'selected' : '' }}>VII D</option>
                                        <option value="VII E" {{ old('kelas') == 'VII E' ? 'selected' : '' }}>VII E</option>
                                    </optgroup>
                                    <optgroup label="Kelas VIII">
                                        <option value="VIII A" {{ old('kelas') == 'VIII A' ? 'selected' : '' }}>VIII A</option>
                                        <option value="VIII B" {{ old('kelas') == 'VIII B' ? 'selected' : '' }}>VIII B</option>
                                        <option value="VIII C" {{ old('kelas') == 'VIII C' ? 'selected' : '' }}>VIII C</option>
                                        <option value="VIII D" {{ old('kelas') == 'VIII D' ? 'selected' : '' }}>VIII D</option>
                                        <option value="VIII E" {{ old('kelas') == 'VIII E' ? 'selected' : '' }}>VIII E</option>
                                    </optgroup>
                                    <optgroup label="Kelas IX">
                                        <option value="IX A" {{ old('kelas') == 'IX A' ? 'selected' : '' }}>IX A</option>
                                        <option value="IX B" {{ old('kelas') == 'IX B' ? 'selected' : '' }}>IX B</option>
                                        <option value="IX C" {{ old('kelas') == 'IX C' ? 'selected' : '' }}>IX C</option>
                                        <option value="IX D" {{ old('kelas') == 'IX D' ? 'selected' : '' }}>IX D</option>
                                        <option value="IX E" {{ old('kelas') == 'IX E' ? 'selected' : '' }}>IX E</option>
                                    </optgroup>
                                </select>
                                @error('kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tahun_ajaran_masuk" class="form-label">
                            Tahun Ajaran Masuk <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('tahun_ajaran_masuk') is-invalid @enderror" 
                                id="tahun_ajaran_masuk" 
                                name="tahun_ajaran_masuk">
                            <option value="">Pilih Tahun Ajaran</option>
                            @php
                                $currentYear = date('Y');
                                for ($i = 4; $i >= 0; $i--) {
                                    $year = $currentYear - $i;
                                    $academicYear = $year . '/' . ($year + 1);
                                    $selected = old('tahun_ajaran_masuk') == $academicYear ? 'selected' : '';
                                    echo "<option value=\"{$academicYear}\" {$selected}>{$academicYear}</option>";
                                }
                            @endphp
                        </select>
                        @error('tahun_ajaran_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Tahun ajaran ketika siswa pertama kali masuk sekolah
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">
                            Alamat <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                  id="alamat" 
                                  name="alamat" 
                                  rows="3" 
                                  placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="telepon" class="form-label">
                            Nomor Telepon
                        </label>
                        <input type="text" 
                               class="form-control @error('telepon') is-invalid @enderror" 
                               id="telepon" 
                               name="telepon" 
                               value="{{ old('telepon') }}"
                               placeholder="Contoh: 081234567890">
                        @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Opsional - dapat diisi nomor telepon siswa atau orang tua
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto Anggota (opsional)</label>
                        <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="previewFoto" class="mt-2"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> Status anggota akan otomatis diset sebagai "Aktif" setelah pendaftaran berhasil.
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('anggota.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <div>
                        <button type="button" class="btn btn-outline-primary me-2" onclick="resetForm()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <button type="submit" form="formAnggota" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Anggota
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
// Auto generate nomor anggota
document.addEventListener('DOMContentLoaded', function() {
    generateNomorAnggota();
});

function generateNomorAnggota() {
    const nomorInput = document.getElementById('nomor_anggota');
    if (!nomorInput.value) {
        // Generate nomor anggota otomatis
        const currentYear = new Date().getFullYear();
        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        nomorInput.value = `AGT${currentYear}${randomNum}`;
    }
}

function resetForm() {
    document.getElementById('formAnggota').reset();
    generateNomorAnggota();
    // Reset validation classes
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
}

// Validasi form sebelum submit
document.getElementById('formAnggota').addEventListener('submit', function(e) {
    const requiredFields = ['nomor_anggota', 'nama_lengkap', 'jenis_kelamin', 'kelas', 'alamat', 'tanggal_daftar'];
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

// Format nomor telepon
document.getElementById('telepon').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Hapus semua non-digit
    
    // Batasi maksimal 15 digit
    if (value.length > 15) {
        value = value.substring(0, 15);
    }
    
    e.target.value = value;
});

// Validasi real-time
document.querySelectorAll('input, select, textarea').forEach(input => {
    input.addEventListener('blur', function() {
        if (this.hasAttribute('required') || this.value.trim()) {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.hasAttribute('required')) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            }
        }
    });
});

// Konfirmasi sebelum meninggalkan halaman jika ada perubahan
let formChanged = false;
document.getElementById('formAnggota').addEventListener('change', function() {
    formChanged = true;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.getElementById('foto').addEventListener('change', function(e) {
    const preview = document.getElementById('previewFoto');
    preview.innerHTML = '';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview Foto" class="img-thumbnail" style="max-width:100px;">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endsection