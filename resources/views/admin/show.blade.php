@extends('layouts.app')

@section('title', 'Detail Admin - Perpustakaan SMP Negeri 1 Sanggau Ledo')
@section('page-title', 'Detail Admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Manajemen Admin</a></li>
<li class="breadcrumb-item active">Detail Admin</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Detail Admin</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Nama Lengkap</strong></td>
                                <td width="5%">:</td>
                                <td>{{ $admin->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>:</td>
                                <td>{{ $admin->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Role</strong></td>
                                <td>:</td>
                                <td>
                                    @if($admin->role === 'admin')
                                        <span class="badge bg-primary">Administrator</span>
                                    @else
                                        <span class="badge bg-info">Kepala Perpustakaan</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>:</td>
                                <td>
                                    @if($admin->status === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Dibuat</strong></td>
                                <td>:</td>
                                <td>{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Terakhir Update</strong></td>
                                <td>:</td>
                                <td>{{ $admin->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                    <div>
                        <a href="{{ route('admin.edit', $admin) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @if($admin->id !== auth('admin')->id())
                            <form action="{{ route('admin.reset-password', $admin) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary" 
                                        onclick="return confirm('Reset password admin ini?')">
                                    <i class="fas fa-key me-1"></i>Reset Password
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
