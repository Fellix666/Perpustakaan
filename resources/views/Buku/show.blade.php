@extends('layouts.app')

@section('title', 'Detail Buku - Nama Aplikasi')
@section('page-title', 'Detail Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Data Buku</a></li>
<li class="breadcrumb-item active">Detail Buku</li>
@endsection

@section('page-actions')
<div class="d-flex gap-2">
    @if(auth('admin')->user()->role === 'admin')
    <a href="{{ route('buku.edit', $buku) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i>Edit
    </a>
    <form action="{{ route('buku.destroy', $buku) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="confirmDelete(event)">
            <i class="fas fa-trash me-2"></i>Hapus
        </button>
    </form>
    @endif
</div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Informasi Buku</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="35%"><strong>Kode Buku</strong></td>
                        <td>:</td>
                        <td><code class="fs-6">{{ $buku->kode_buku }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>ISBN</strong></td>
                        <td>:</td>
                        <td>{{ $buku->isbn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Judul</strong></td>
                        <td>:</td>
                        <td>{{ $buku->judul }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pengarang</strong></td>
                        <td>:</td>
                        <td>{{ $buku->pengarang }}</td>
                    </tr>
                    <tr>
                        <td><strong>Penerbit</strong></td>
                        <td>:</td>
                        <td>{{ $buku->penerbit }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tahun Terbit</strong></td>
                        <td>:</td>
                        <td>{{ $buku->tahun_terbit }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Halaman</strong></td>
                        <td>:</td>
                        <td>{{ $buku->jumlah_halaman ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Deskripsi</strong></td>
                        <td>:</td>
                        <td>{{ $buku->deskripsi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Stok Total</strong></td>
                        <td>:</td>
                        <td>{{ $buku->stok_total }}</td>
                    </tr>
                    <tr>
                        <td><strong>Stok Tersedia</strong></td>
                        <td>:</td>
                        <td>{{ $buku->stok_tersedia }}</td>
                    </tr>
                    <tr>
                        <td><strong>Kategori</strong></td>
                        <td>:</td>
                        <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Rak</strong></td>
                        <td>:</td>
                        <td>{{ $buku->rak->nama_rak ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>:</td>
                        <td>
                            @if($buku->status == 'tersedia')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Tidak Tersedia</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Cover Buku</h5>
            </div>
            <div class="card-body text-center">
                @if($buku->cover)
                    <img src="{{ asset('storage/buku/'.$buku->cover) }}" alt="Cover Buku" class="img-thumbnail" style="max-width:120px;max-height:160px;object-fit:cover;">
                @else
                    <div class="bg-light border rounded d-inline-flex align-items-center justify-content-center" style="width:120px;height:160px;">
                        <i class="fas fa-book fa-4x text-muted"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(event) {
    event.preventDefault();
    if (confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
        event.target.closest('form').submit();
    }
    return false;
}
</script>
@endsection 