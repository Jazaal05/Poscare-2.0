<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/poscare-logo.png') }}">
    <title>@yield('title', 'PosCare') - PosCare</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    @yield('styles')
</head>
<body>

{{-- NAVBAR - sama persis dengan project lama --}}
<nav class="navbar">
    <div class="navbar-container">
        <span class="navbar-logo">
            <img src="{{ asset('images/poscare-logo.png') }}" alt="PosCare Logo" style="width:32px;height:32px;object-fit:contain;"> PosCare
        </span>
        <ul class="navbar-menu">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>Beranda
            </a></li>
            <li><a href="{{ route('anak.index') }}" class="{{ request()->routeIs('anak*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>Data Anak
            </a></li>
            <li><a href="{{ route('jadwal.index') }}" class="{{ request()->routeIs('jadwal*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>Jadwal
            </a></li>
            <li><a href="{{ route('edukasi.index') }}" class="{{ request()->routeIs('edukasi*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>Edukasi
            </a></li>
            <li><a href="{{ route('imunisasi.index') }}" class="{{ request()->routeIs('imunisasi*') ? 'active' : '' }}">
                <i class="fas fa-syringe"></i>Imunisasi
            </a></li>
            <li><a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>Laporan
            </a></li>
            <li><a href="{{ route('pengaturan.index') }}" class="{{ request()->routeIs('pengaturan*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>Pengaturan
            </a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm" style="display:inline;">
                    @csrf
                    <a href="#" onclick="event.preventDefault();document.getElementById('logoutForm').submit();"
                       style="color:#ef4444 !important;">
                        <i class="fas fa-sign-out-alt"></i>Keluar
                    </a>
                </form>
            </li>
        </ul>
    </div>
</nav>

{{-- KONTEN HALAMAN --}}
<div class="container">
    @yield('content')
</div>

<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
</script>
<script src="{{ asset('js/input-validator.js') }}"></script>

@yield('scripts')

</body>
</html>
