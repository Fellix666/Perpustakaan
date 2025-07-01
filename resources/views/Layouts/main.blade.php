<!-- Desktop Sidebar -->
<div class="sidebar d-none d-lg-block">
    <div class="d-flex flex-column">
        <div class="p-3 text-center border-bottom">
            <h4 class="mb-0"><i class="fas fa-book-open me-2"></i>Logo SMP</h4>
            <small>Nama SMP</small>
        </div>
        <nav class="nav flex-column py-3">
            <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 small fw-bold text-muted">DATA MASTER</div>
            </div>
            <a class="nav-link @if(request()->routeIs('anggota.*')) active @endif" href="{{ route('anggota.index') }}">
                <i class="fas fa-users me-2"></i>Data Anggota
            </a>
            <a class="nav-link @if(request()->routeIs('buku.*')) active @endif" href="{{ route('buku.index') }}">
                <i class="fas fa-book me-2"></i>Data Buku
            </a>
            <a class="nav-link @if(request()->routeIs('kategori.*')) active @endif" href="{{ route('kategori.index') }}">
                <i class="fas fa-tags me-2"></i>Kategori Buku
            </a>
            <a class="nav-link @if(request()->routeIs('rak.*')) active @endif" href="{{ route('rak.index') }}">
                <i class="fas fa-archive me-2"></i>Data Rak
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 small fw-bold text-muted">TRANSAKSI</div>
            </div>
            <a class="nav-link @if(request()->routeIs('peminjaman.*')) active @endif" href="{{ route('peminjaman.index') }}">
                <i class="fas fa-exchange-alt me-2"></i>Peminjaman
            </a>
            <a class="nav-link @if(request()->routeIs('pengembalian.*')) active @endif" href="{{ route('pengembalian.index') }}">
                <i class="fas fa-undo me-2"></i>Pengembalian
            </a>
            <a class="nav-link @if(request()->routeIs('denda.*')) active @endif" href="{{ route('denda.index') }}">
                <i class="fas fa-money-bill-wave me-2"></i>Data Denda
            </a>
            
            <div class="nav-item">
                <div class="px-3 py-2 small fw-bold text-muted">LAPORAN</div>
            </div>
            <a class="nav-link @if(request()->routeIs('laporan.*')) active @endif" href="{{ route('laporan.index') }}">
                <i class="fas fa-chart-bar me-2"></i>Laporan
            </a>
        </nav>
    </div>
</div>

<!-- Mobile Sidebar -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title"><i class="fas fa-book-open me-2"></i>Perpustakaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="nav flex-column">
            <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            
            <div class="nav-item">
                <div class="text-muted px-3 py-2 small fw-bold">DATA MASTER</div>
            </div>
            <a class="nav-link @if(request()->routeIs('anggota.*')) active @endif" href="{{ route('anggota.index') }}">
                <i class="fas fa-users me-2"></i>Data Anggota
            </a>
            <a class="nav-link @if(request()->routeIs('buku.*')) active @endif" href="{{ route('buku.index') }}">
                <i class="fas fa-book me-2"></i>Data Buku
            </a>
            <a class="nav-link @if(request()->routeIs('kategori.*')) active @endif" href="{{ route('kategori.index') }}">
                <i class="fas fa-tags me-2"></i>Kategori Buku
            </a>
            <a class="nav-link @if(request()->routeIs('rak.*')) active @endif" href="{{ route('rak.index') }}">
                <i class="fas fa-archive me-2"></i>Data Rak
            </a>
            
            <div class="nav-item">
                <div class="text-muted px-3 py-2 small fw-bold">TRANSAKSI</div>
            </div>
            <a class="nav-link @if(request()->routeIs('peminjaman.*')) active @endif" href="{{ route('peminjaman.index') }}">
                <i class="fas fa-exchange-alt me-2"></i>Peminjaman
            </a>
            <a class="nav-link @if(request()->routeIs('pengembalian.*')) active @endif" href="{{ route('pengembalian.index') }}">
                <i class="fas fa-undo me-2"></i>Pengembalian
            </a>
            <a class="nav-link @if(request()->routeIs('denda.*')) active @endif" href="{{ route('denda.index') }}">
                <i class="fas fa-money-bill-wave me-2"></i>Data Denda
            </a>
            
            <div class="nav-item">
                <div class="text-muted px-3 py-2 small fw-bold">LAPORAN</div>
            </div>
            <a class="nav-link @if(request()->routeIs('laporan.*')) active @endif" href="{{ route('laporan.index') }}">
                <i class="fas fa-chart-bar me-2"></i>Laporan
            </a>
        </nav>
    </div>
</div>