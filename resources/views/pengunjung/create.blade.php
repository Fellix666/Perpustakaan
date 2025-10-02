@extends('layouts.app')

@section('title', 'Tambah Kunjungan')
@section('page-title', 'Tambah Data Kunjungan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('pengunjung.index') }}">Data Pengunjung</a></li>
<li class="breadcrumb-item active">Tambah Kunjungan</li>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Verifikasi Anggota & Tambah Kunjungan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('pengunjung.store') }}" method="POST">
            @csrf
            
            <!-- Verifikasi Anggota -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Langkah 1: Verifikasi Anggota</h6>
                        <p class="mb-0">Cari dan pilih anggota yang akan berkunjung ke perpustakaan.</p>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-5">
                    <label for="anggota_id" class="form-label">Pilih Anggota <span class="text-danger">*</span></label>
                    <select class="form-select select2-anggota @error('anggota_id') is-invalid @enderror" id="anggota_id" name="anggota_id" required>
                        <option value="">-- Pilih Anggota --</option>
                    </select>
                    @error('anggota_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="fas fa-search me-1"></i>Ketik untuk mencari anggota
                    </div>
                </div>
                <div class="col-md-5">
                    <label for="tujuan_kunjungan" class="form-label">Tujuan Kunjungan <span class="text-danger">*</span></label>
                    <select class="form-select @error('tujuan_kunjungan') is-invalid @enderror" id="tujuan_kunjungan" name="tujuan_kunjungan" required>
                        <option value="">Pilih tujuan kunjungan...</option>
                        <option value="pinjam">Pinjam Buku</option>
                        <option value="baca">Baca di Tempat</option>
                    </select>
                    @error('tujuan_kunjungan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-secondary w-100" onclick="resetSearch()">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-12">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btn_submit" disabled>
                            <i class="fas fa-save me-2"></i>Catat Kunjungan
                        </button>
                        <a href="{{ route('pengunjung.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for Anggota
    $('.select2-anggota').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Anggota',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("pengunjung.search-anggota") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.nomor_anggota + ' - ' + item.nama_lengkap + ' (' + item.kelas + ')'
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        language: {
            noResults: function() {
                return "Tidak ada anggota ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    @if(isset($selectedAnggotaId) && $selectedAnggotaId)
        $('#anggota_id').val('{{ $selectedAnggotaId }}').trigger('change');
    @endif

    $('#anggota_id').on('select2:select', function(e) {
        checkFormValidity();
    });

    $('#anggota_id').on('select2:clear', function(e) {
        checkFormValidity();
    });

    $('#tujuan_kunjungan').on('change', function() {
        checkFormValidity();
    });

    function checkFormValidity() {
        var anggotaSelected = $('#anggota_id').val() !== '' && $('#anggota_id').val() !== null;
        var tujuanSelected = $('#tujuan_kunjungan').val() !== '';
        
        if (anggotaSelected && tujuanSelected) {
            $('#btn_submit').prop('disabled', false);
        } else {
            $('#btn_submit').prop('disabled', true);
        }
    }

    checkFormValidity();
});

function resetSearch() {
    $('#anggota_id').val(null).trigger('change');
    $('#tujuan_kunjungan').val('');
    $('#keterangan').val('');
    $('#btn_submit').prop('disabled', true);
}
</script>
@endsection 