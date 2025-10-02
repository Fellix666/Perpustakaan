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
                    <div class="mb-3">
                        <label for="buku_id" class="form-label">Buku <span class="text-danger">*</span></label>
                        <select class="form-select select2-buku @error('buku_id') is-invalid @enderror" id="buku_id" name="buku_id" required>
                            <option value="">-- Pilih Buku --</option>
                        </select>
                        @error('buku_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-search me-1"></i>Ketik untuk mencari buku
                        </div>
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

@section('scripts')
<script>
$(document).ready(function() {
    $('.select2-anggota').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Anggota',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("peminjaman.search-anggota") }}',
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

        $.ajax({
            url: '{{ route("peminjaman.search-anggota") }}',
            dataType: 'json',
            data: {
                search: '{{ $selectedAnggotaId }}',
                exact_id: '{{ $selectedAnggotaId }}'
            },
            success: function(data) {
                if (data.length > 0) {
                    const anggota = data[0];
                    const option = new Option(
                        anggota.nomor_anggota + ' - ' + anggota.nama_lengkap + ' (' + anggota.kelas + ')',
                        anggota.id,
                        true,
                        true
                    );
                    $('#anggota_id').append(option).trigger('change');
                }
            }
        });
    @endif

    $('.select2-buku').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Buku',
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("peminjaman.search-buku") }}',
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
                            text: item.judul + ' (' + item.kode_buku + ') - Stok: ' + item.stok_tersedia,
                            data: item
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        language: {
            noResults: function() {
                return "Tidak ada buku ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    $('#buku_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const stok = selectedOption.data('stok');
        const kode = selectedOption.data('kode');
        
        if (stok !== undefined && kode !== undefined) {

            $('.stok-info').remove();

            const infoHtml = `
                <div class="alert alert-info stok-info mt-2">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Kode:</strong> ${kode} | <strong>Stok Tersedia:</strong> ${stok}
                </div>
            `;
            $(this).parent().append(infoHtml);
        } else {
            $('.stok-info').remove();
        }
    });

    $('#tanggal_pinjam').on('change', function() {
        const loanDate = new Date($(this).val());
        if (loanDate) {
            const returnDate = new Date(loanDate);
            returnDate.setDate(returnDate.getDate() + 7);
            
            const returnDateStr = returnDate.toISOString().split('T')[0];
            $('#tanggal_kembali_rencana').val(returnDateStr);
        }
    });

    $('#formPeminjaman').on('submit', function(e) {
        const anggotaId = $('#anggota_id').val();
        const bukuId = $('#buku_id').val();
        const tanggalPinjam = $('#tanggal_pinjam').val();
        const tanggalKembali = $('#tanggal_kembali_rencana').val();

        if (!anggotaId || !bukuId || !tanggalPinjam || !tanggalKembali) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Form Tidak Lengkap',
                text: 'Silakan lengkapi semua field yang wajib diisi!',
                timer: 3000,
                showConfirmButton: false
            });
            return false;
        }

        if (tanggalKembali <= tanggalPinjam) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal kembali harus setelah tanggal pinjam!',
                timer: 3000,
                showConfirmButton: false
            });
            return false;
        }
    });
});
</script>
@endsection 