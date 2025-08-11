@extends('layouts.app')

@section('title', 'Manajemen Admin - Perpustakaan SMP Negeri 1 Sanggau Ledo')
@section('page-title', 'Manajemen Admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Manajemen Admin</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-users-cog me-2"></i>Daftar Admin
                </h5>
                <a href="{{ route('admin.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Tambah Admin
                </a>
            </div>
            <div class="card-body">
                @if($admins->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $index => $admin)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        @if($admin->role === 'admin')
                                            <span class="badge bg-primary">Administrator</span>
                                        @else
                                            <span class="badge bg-info">Kepala Perpustakaan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($admin->status === 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.show', $admin) }}" class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.edit', $admin) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($admin->id !== auth('admin')->id())
                                                <form action="{{ route('admin.reset-password', $admin) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-secondary btn-sm" title="Reset Password" 
                                                            onclick="return confirm('Reset password admin ini?')">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.toggle-status', $admin) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-{{ $admin->status === 'aktif' ? 'warning' : 'success' }} btn-sm" 
                                                            title="{{ $admin->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                            onclick="return confirm('{{ $admin->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }} admin ini?')">
                                                        <i class="fas fa-{{ $admin->status === 'aktif' ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.destroy', $admin) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                            onclick="return confirm('Hapus admin ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $admins->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data admin.</p>
                        <a href="{{ route('admin.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Tambah Admin Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
