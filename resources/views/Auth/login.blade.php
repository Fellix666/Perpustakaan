@extends('layouts.loginApp')

@section('title', 'Login - Perpustakaan SMP Negeri 1 Sanggau Ledo')

@section('content')
                        <div class="container">
                <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
                    <!-- Left Column - Logo Section with Background -->
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%); border-radius: 15px 0 0 15px; min-height: 450px;">
                        <div class="text-center text-white">
                            @if(file_exists(public_path('images/logo-smp.png')) && filesize(public_path('images/logo-smp.png')) > 1000)
                                <img src="{{ asset('images/logo-smp.png') }}" alt="Logo SMP Negeri 1 Sanggau Ledo" class="mb-3" style="max-height: 200px; max-width: 200px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));">
                            @else
                                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                                    <i class="fas fa-school fa-3x text-primary"></i>
                                </div>
                            @endif
                            <h4 class="fw-bold mb-2">Aplikasi Pengolahan Data Perpustakaan</h4>
                            <h6 class="mb-2">SMP Negeri 1 Sanggau Ledo</h6>
                            
                            <small class="text-white-75">Kelola Data Perpustakaan dengan mudah</small>
                        </div>
                    </div>
                    
                    <!-- Right Column - Login Form (WHITE BACKGROUND) -->
                    <div class="col-lg-6 d-flex align-items-center justify-content-center" style="border-radius: 0 15px 15px 0; min-height: 450px; background: white;">
                        <div class="w-100" style="max-width: 350px;">
                            <div class="card border-0" style="box-shadow: none;">
                                <div class="card-body p-3">
                        <!-- Mobile Logo (only visible on mobile) -->
                        <div class="text-center mb-4 d-lg-none">
                            @if(file_exists(public_path('images/logo-smp.png')) && filesize(public_path('images/logo-smp.png')) > 1000)
                                <img src="{{ asset('images/logo-smp.png') }}" alt="Logo SMP Negeri 1 Sanggau Ledo" class="mb-3" style="max-height: 80px; max-width: 80px;">
                            @else
                                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-school fa-2x text-white"></i>
                                </div>
                            @endif
                            <h4 class="text-primary fw-bold">Perpustakaan</h4>
                            <p class="text-muted mb-0">SMP Negeri 1 Sanggau Ledo</p>
                        </div>
                        
                                                                                                            <h5 class="text-center mb-3 fw-bold text-primary">Login</h5>
                                                <p class="text-center text-muted mb-3">Silakan login untuk melanjutkan</p>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                                                                    <div class="mb-3">
                                            <label for="email" class="form-label fw-semibold">
                                                <i class="fas fa-envelope me-2 text-primary"></i>Email
                                            </label>
                                                                                                    <input type="email" 
                                                            class="form-control @error('email') is-invalid @enderror" 
                                                            id="email" 
                                                            name="email" 
                                                            value="{{ old('email') }}" 
                                                            required 
                                                            autofocus
                                                            placeholder="Masukkan email Anda">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label fw-semibold">
                                                <i class="fas fa-lock me-2 text-primary"></i>Password
                                            </label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" 
                                                       name="password" 
                                                       required
                                                       placeholder="Masukkan password Anda">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                                                    <i class="fas fa-eye" id="iconPassword"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Ingat saya
                                            </label>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary w-100 mb-3" style="background: linear-gradient(135deg, #9fb0f1 0%, #4fa6fc 100%); border: none;">
                                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                                        </button>
                            
                                                                    @if (Route::has('password.request'))
                                            <div class="text-center">
                                                <a href="{{ route('password.request') }}" class="text-decoration-none text-primary">
                                                    <small>Lupa password?</small>
                                                </a>
                                            </div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('iconPassword');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});
</script>
@endsection