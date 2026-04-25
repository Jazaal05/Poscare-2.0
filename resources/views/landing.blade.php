<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PosCare - Sistem Informasi Digital Posyandu</title>
    <link rel="icon" type="image/png" href="{{ asset('images/poscare-logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%); min-height:100vh; }

        /* Navbar */
        .navbar { background:rgba(255,255,255,0.25); backdrop-filter:blur(15px); border-bottom:1px solid rgba(255,255,255,0.3); padding:1rem 2rem; position:sticky; top:0; z-index:100; }
        .navbar-inner { max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; }
        .navbar-logo { display:flex; align-items:center; gap:10px; font-size:1.4rem; font-weight:700; color:#246BCE; text-decoration:none; }
        .navbar-logo img { width:36px; height:36px; object-fit:contain; }
        .navbar-actions { display:flex; gap:12px; }
        .btn-nav { padding:9px 22px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }
        .btn-nav-outline { border:2px solid #246BCE; color:#246BCE; background:transparent; }
        .btn-nav-outline:hover { background:#246BCE; color:#fff; }
        .btn-nav-solid { background:#246BCE; color:#fff; border:2px solid #246BCE; }
        .btn-nav-solid:hover { background:#1D58A8; }

        /* Hero */
        .hero { max-width:1200px; margin:0 auto; padding:80px 2rem 60px; text-align:center; }
        .hero-badge { display:inline-flex; align-items:center; gap:8px; background:rgba(36,107,206,0.1); color:#246BCE; padding:6px 16px; border-radius:20px; font-size:13px; font-weight:600; margin-bottom:24px; border:1px solid rgba(36,107,206,0.2); }
        .hero-title { font-size:clamp(2rem,5vw,3.5rem); font-weight:800; color:#1E3A5F; line-height:1.15; margin-bottom:20px; }
        .hero-title span { background:linear-gradient(135deg,#246BCE,#10B981); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .hero-subtitle { font-size:1.1rem; color:#64748B; max-width:600px; margin:0 auto 48px; line-height:1.7; }

        /* Layanan Cards */
        .layanan-section { max-width:1200px; margin:0 auto; padding:0 2rem 80px; }
        .layanan-title { text-align:center; font-size:1.5rem; font-weight:700; color:#1E3A5F; margin-bottom:8px; }
        .layanan-sub { text-align:center; color:#64748B; margin-bottom:40px; font-size:15px; }
        .layanan-grid { display:grid; grid-template-columns:1fr 1fr; gap:28px; max-width:800px; margin:0 auto; }
        @media(max-width:640px){ .layanan-grid{ grid-template-columns:1fr; } }

        .layanan-card { background:rgba(255,255,255,0.9); border-radius:24px; padding:40px 32px; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,0.08); border:2px solid transparent; transition:all 0.3s ease; cursor:pointer; text-decoration:none; display:block; }
        .layanan-card:hover { transform:translateY(-8px); box-shadow:0 20px 48px rgba(0,0,0,0.14); }
        .layanan-card.balita:hover { border-color:#246BCE; }
        .layanan-card.lansia:hover { border-color:#10B981; }

        .layanan-icon { width:80px; height:80px; border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:36px; margin:0 auto 20px; }
        .layanan-card.balita .layanan-icon { background:linear-gradient(135deg,rgba(36,107,206,0.15),rgba(59,130,246,0.1)); color:#246BCE; }
        .layanan-card.lansia .layanan-icon  { background:linear-gradient(135deg,rgba(16,185,129,0.15),rgba(5,150,105,0.1)); color:#10B981; }

        .layanan-name { font-size:1.3rem; font-weight:700; color:#1E3A5F; margin-bottom:10px; }
        .layanan-desc { font-size:14px; color:#64748B; line-height:1.6; margin-bottom:24px; }
        .layanan-btn { display:inline-flex; align-items:center; gap:8px; padding:12px 28px; border-radius:12px; font-size:14px; font-weight:700; transition:all 0.2s; }
        .layanan-card.balita .layanan-btn { background:#246BCE; color:#fff; }
        .layanan-card.balita:hover .layanan-btn { background:#1D58A8; }
        .layanan-card.lansia .layanan-btn  { background:#10B981; color:#fff; }
        .layanan-card.lansia:hover .layanan-btn  { background:#059669; }

        /* Features */
        .features { max-width:1200px; margin:0 auto; padding:0 2rem 80px; }
        .features-title { text-align:center; font-size:1.5rem; font-weight:700; color:#1E3A5F; margin-bottom:40px; }
        .features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
        @media(max-width:768px){ .features-grid{ grid-template-columns:1fr; } }
        .feature-item { background:rgba(255,255,255,0.7); border-radius:16px; padding:24px; display:flex; align-items:flex-start; gap:16px; }
        .feature-icon { width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,rgba(36,107,206,0.15),rgba(59,130,246,0.1)); color:#246BCE; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .feature-text h4 { font-size:15px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
        .feature-text p  { font-size:13px; color:#64748B; line-height:1.5; }

        /* Footer */
        footer { text-align:center; padding:24px; color:#94A3B8; font-size:13px; border-top:1px solid rgba(255,255,255,0.4); }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-inner">
        <a href="{{ route('landing') }}" class="navbar-logo">
            <img src="{{ asset('images/poscare-logo.png') }}" alt="PosCare">
            PosCare
        </a>
        <div class="navbar-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-nav btn-nav-outline">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav btn-nav-solid">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
            @endauth
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-badge">
        <i class="fas fa-hospital"></i> Sistem Informasi Digital Posyandu
    </div>
    <h1 class="hero-title">
        Kelola Posyandu<br>Lebih <span>Mudah & Efisien</span>
    </h1>
    <p class="hero-subtitle">
        PosCare hadir untuk membantu kader posyandu dalam mengelola data kesehatan balita dan lansia secara digital, akurat, dan terorganisir.
    </p>
</section>

<section class="layanan-section">
    <h2 class="layanan-title">Pilih Layanan</h2>
    <p class="layanan-sub">Tersedia dua layanan posyandu yang dapat diakses sesuai kebutuhan</p>

    <div class="layanan-grid">
        {{-- Layanan Balita --}}
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="layanan-card balita">
            <div class="layanan-icon"><i class="fas fa-baby"></i></div>
            <div class="layanan-name">Posyandu Balita</div>
            <div class="layanan-desc">
                Kelola data anak, pemantauan gizi, imunisasi, jadwal posyandu, dan laporan kesehatan balita.
            </div>
            <span class="layanan-btn">
                <i class="fas fa-arrow-right"></i> Masuk Layanan
            </span>
        </a>

        {{-- Layanan Lansia --}}
        <a href="{{ auth()->check() ? route('lansia.dashboard') : route('login') }}" class="layanan-card lansia">
            <div class="layanan-icon"><i class="fas fa-user-friends"></i></div>
            <div class="layanan-name">Posyandu Lansia</div>
            <div class="layanan-desc">
                Kelola data lansia, pemeriksaan kesehatan (tekanan darah, gula darah), pengobatan, dan laporan.
            </div>
            <span class="layanan-btn">
                <i class="fas fa-arrow-right"></i> Masuk Layanan
            </span>
        </a>
    </div>
</section>

<section class="features">
    <h2 class="features-title">Fitur Unggulan</h2>
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
            <div class="feature-text">
                <h4>Dashboard Informatif</h4>
                <p>Ringkasan data statistik kesehatan yang mudah dipahami</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-syringe"></i></div>
            <div class="feature-text">
                <h4>Tracking Imunisasi</h4>
                <p>Pantau status imunisasi setiap anak secara real-time</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-file-excel"></i></div>
            <div class="feature-text">
                <h4>Export Laporan Excel</h4>
                <p>Unduh laporan data dalam format Excel kapan saja</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="feature-text">
                <h4>Manajemen Jadwal</h4>
                <p>Buat dan kelola jadwal kegiatan posyandu dengan mudah</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-pills"></i></div>
            <div class="feature-text">
                <h4>Catatan Pengobatan</h4>
                <p>Rekam pemberian obat dan vitamin untuk lansia</p>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="fas fa-book-open"></i></div>
            <div class="feature-text">
                <h4>Konten Edukasi</h4>
                <p>Bagikan konten edukasi kesehatan dari berbagai platform</p>
            </div>
        </div>
    </div>
</section>

<footer>
    <p>&copy; {{ date('Y') }} PosCare — Sistem Informasi Digital Posyandu. Dibuat untuk Posyandu Nganjuk.</p>
</footer>

</body>
</html>
