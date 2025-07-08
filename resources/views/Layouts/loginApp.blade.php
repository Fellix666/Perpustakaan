<!DOCTYPE html>
<html lang="id">
@include('layouts/head')
<body>
    {{-- @include('layouts/navbar') --}}
    <main>
        @auth
            @if(!request()->routeIs('login'))
                @include('layouts/main')
            @endif
        @endauth
        @include('layouts/wrapper')
    </main>
    {{-- @include('layouts/footer') --}}
    @include('layouts/scripts')
    @stack('scripts')
    @yield('scripts')
</body>
</html>