<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <button class="navbar-toggler me-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand mx-auto d-flex align-items-center" href="{{ route('dashboard') }}">
            @if(file_exists(public_path('images/logo-smp.png')) && filesize(public_path('images/logo-smp.png')) > 1000)
                <img src="{{ asset('images/logo-smp.png') }}" alt="Logo SMP Negeri 1 Sanggau Ledo" class="me-2" style="max-height: 45px; max-width: 45px;">
            @else
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                    <i class="fas fa-school text-white"></i>
                </div>
            @endif
            <div class="d-flex flex-column">
                <span class="fw-bold text-primary">Perpustakaan</span>
                <small class="text-muted">SMP Negeri 1 Sanggau Ledo</small>
            </div>
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name ?? 'Admin' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>