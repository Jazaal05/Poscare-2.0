<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/poscare-logo.png') }}">
    <title>@yield('title', 'PosCare Lansia') - PosCare</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Override warna navbar untuk lansia — hijau teal */
        .navbar-logo { color:#10B981 !important; }
        .navbar-menu li a.active { color:#10B981 !important; }
        .navbar-menu li a.active::after { background:#10B981 !important; }
        .navbar-menu li a:hover { color:#10B981 !important; }
        .navbar-menu li a:hover::after { transform:translateX(-50%) scaleX(1); background:linear-gradient(90deg,#10B981,#34D399) !important; }
        .navbar-menu li a::after { background:linear-gradient(90deg,#10B981,#34D399) !important; }
        .lansia-badge { background:#10B981; color:#fff; font-size:10px; font-weight:700;
                        padding:2px 7px; border-radius:10px; margin-left:6px; vertical-align:middle; }
    </style>
    @yield('styles')
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <span class="navbar-logo">
            <img src="{{ asset('images/poscare-logo.png') }}" alt="PosCare Logo" style="width:32px;height:32px;object-fit:contain;">
            PosCare <span class="lansia-badge">LANSIA</span>
        </span>
        <ul class="navbar-menu">
            <li><a href="{{ route('lansia.dashboard') }}" class="{{ request()->routeIs('lansia.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>Beranda
            </a></li>
            <li><a href="{{ route('lansia.kunjungan.index') }}" class="{{ request()->routeIs('lansia.kunjungan*') ? 'active' : '' }}">
                <i class="fas fa-stethoscope"></i>Kunjungan
            </a></li>
            <li><a href="{{ route('lansia.jadwal.index') }}" class="{{ request()->routeIs('lansia.jadwal*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>Jadwal
            </a></li>
            <li><a href="{{ route('lansia.edukasi.index') }}" class="{{ request()->routeIs('lansia.edukasi*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>Edukasi
            </a></li>
            <li><a href="{{ route('lansia.laporan.index') }}" class="{{ request()->routeIs('lansia.laporan*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>Laporan
            </a></li>
            <li><a href="{{ route('lansia.pengaturan.index') }}" class="{{ request()->routeIs('lansia.pengaturan*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>Pengaturan
            </a></li>
        </ul>
        <div class="navbar-actions">
            <a href="{{ route('dashboard') }}" style="color:#246BCE !important;text-decoration:none;font-weight:500;font-size:0.95rem;padding:0.5rem 1rem;display:inline-flex;align-items:center;gap:0.5rem;" title="Ke Balita">
                <i class="fas fa-exchange-alt"></i>Balita
            </a>
            <form method="POST" action="{{ route('logout') }}" id="logoutFormLansia" style="display:inline;">
                @csrf
                <a href="#" onclick="event.preventDefault();document.getElementById('logoutFormLansia').submit();"
                   style="color:#ef4444 !important;text-decoration:none;font-weight:500;font-size:0.95rem;padding:0.5rem 1rem;display:inline-flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-sign-out-alt"></i>Keluar
                </a>
            </form>
        </div>
    </div>
</nav>

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
