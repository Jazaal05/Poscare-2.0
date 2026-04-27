@extends('layouts.lansia')
@section('title', 'Dashboard Lansia')

@section('styles')
<style>
    body { background:linear-gradient(135deg,#ECFDF5 0%,#D1FAE5 50%,#A7F3D0 100%) !important; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

    .welcome-section { margin-bottom:32px; animation:fadeUp 0.5s ease; }
    .welcome-title { font-size:28px; font-weight:800; color:#064E3B; margin-bottom:4px; }
    .welcome-sub { font-size:15px; color:#6B7280; }

    /* Stat cards */
    .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; margin-bottom:32px; }
    @media(max-width:900px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:600px){ .stats-grid{ grid-template-columns:1fr; } }

    .stat-card { background:#fff; border-radius:20px; padding:24px; display:flex; align-items:center;
                 gap:18px; box-shadow:0 4px 20px rgba(6,78,59,0.08); border:1px solid rgba(16,185,129,0.1);
                 animation:fadeUp 0.5s ease backwards; transition:all 0.3s; }
    .stat-card:hover { transform:translateY(-5px); box-shadow:0 12px 32px rgba(6,78,59,0.14); }
    .stat-card:nth-child(1){animation-delay:0.05s} .stat-card:nth-child(2){animation-delay:0.1s}
    .stat-card:nth-child(3){animation-delay:0.15s} .stat-card:nth-child(4){animation-delay:0.2s}
    .stat-card:nth-child(5){animation-delay:0.25s} .stat-card:nth-child(6){animation-delay:0.3s}

    .stat-icon { width:56px; height:56px; border-radius:16px; display:flex; align-items:center;
                 justify-content:center; font-size:24px; flex-shrink:0; }
    .si-teal   { background:linear-gradient(135deg,#D1FAE5,#A7F3D0); color:#065F46; }
    .si-blue   { background:linear-gradient(135deg,#DBEAFE,#BFDBFE); color:#1E40AF; }
    .si-pink   { background:linear-gradient(135deg,#FCE7F3,#FBCFE8); color:#9D174D; }
    .si-orange { background:linear-gradient(135deg,#FEF3C7,#FDE68A); color:#92400E; }
    .si-red    { background:linear-gradient(135deg,#FEE2E2,#FECACA); color:#991B1B; }
    .si-purple { background:linear-gradient(135deg,#EDE9FE,#DDD6FE); color:#5B21B6; }

    .stat-info { flex:1; }
    .stat-value { font-size:34px; font-weight:800; color:#064E3B; line-height:1; margin-bottom:4px; }
    .stat-label { font-size:13px; color:#6B7280; font-weight:500; }

    /* Section title */
    .section-title { font-size:18px; font-weight:700; color:#064E3B; margin-bottom:16px;
                     display:flex; align-items:center; gap:10px; }
    .section-title::before { content:''; width:4px; height:20px; background:linear-gradient(180deg,#10B981,#059669);
                              border-radius:2px; flex-shrink:0; }

    /* Quick actions */
    .actions-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:32px; }
    @media(max-width:900px){ .actions-grid{ grid-template-columns:repeat(2,1fr); } }

    .action-card { background:#fff; border-radius:18px; padding:24px 16px; text-align:center;
                   text-decoration:none; display:block; transition:all 0.3s;
                   box-shadow:0 2px 12px rgba(6,78,59,0.06); border:2px solid transparent; }
    .action-card:hover { transform:translateY(-6px); border-color:#10B981;
                         box-shadow:0 12px 28px rgba(16,185,129,0.18); }
    .action-card .ac-icon { width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg,#D1FAE5,#A7F3D0);
                            color:#065F46; display:flex; align-items:center; justify-content:center;
                            font-size:22px; margin:0 auto 14px; transition:all 0.3s; }
    .action-card:hover .ac-icon { background:linear-gradient(135deg,#10B981,#059669); color:#fff; }
    .action-card span { display:block; font-size:13px; font-weight:700; color:#064E3B; }

    /* Alert kondisi tidak normal */
    .alert-card { background:#fff; border-radius:18px; padding:20px 24px;
                  box-shadow:0 4px 20px rgba(6,78,59,0.08); border-left:5px solid #EF4444; }
    .alert-title { font-size:15px; font-weight:700; color:#991B1B; margin-bottom:4px;
                   display:flex; align-items:center; gap:8px; }
    .alert-sub { font-size:13px; color:#6B7280; }

    /* Toast */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600;
                  box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideIn 0.3s ease;
                  display:flex; align-items:center; gap:10px; min-width:260px; }
    @keyframes slideIn { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div class="welcome-section">
    <h1 class="welcome-title">Selamat Datang, {{ ucfirst(Auth::user()->nama_lengkap ?? Auth::user()->username) }}!</h1>
    <p class="welcome-sub">Dashboard PosCare Lansia — Sistem Informasi Digital Posyandu Lansia</p>
</div>

{{-- Statistik --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon si-teal"><i class="fas fa-user-friends"></i></div>
        <div class="stat-info">
            <div class="stat-value" id="totalLansia">—</div>
            <div class="stat-label">Total Lansia</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fas fa-mars"></i></div>
        <div class="stat-info">
            <div class="stat-value" id="totalLaki">—</div>
            <div class="stat-label">Laki-laki</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-pink"><i class="fas fa-venus"></i></div>
        <div class="stat-info">
            <div class="stat-value" id="totalPerempuan">—</div>
            <div class="stat-label">Perempuan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-orange"><i class="fas fa-birthday-cake"></i></div>
        <div class="stat-info">
            <div class="stat-value" id="rataUsia">—</div>
            <div class="stat-label">Rata-rata Usia</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-teal"><i class="fas fa-stethoscope"></i></div>
        <div class="stat-info">
            <div class="stat-value" id="kunjunganBulanIni">—</div>
            <div class="stat-label">Kunjungan Bulan Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <div class="stat-value" id="tidakNormal">—</div>
            <div class="stat-label">Kondisi Tidak Normal</div>
        </div>
    </div>
</div>

{{-- Aksi Cepat --}}
<div class="section-title">Aksi Cepat</div>
<div class="actions-grid">
    <a href="{{ route('lansia.kunjungan.index') }}" class="action-card">
        <div class="ac-icon"><i class="fas fa-stethoscope"></i></div>
        <span>Catat Kunjungan</span>
    </a>
    <a href="{{ route('lansia.laporan.index') }}" class="action-card">
        <div class="ac-icon"><i class="fas fa-file-excel"></i></div>
        <span>Ekspor Laporan</span>
    </a>
    <a href="{{ route('lansia.jadwal.index') }}" class="action-card">
        <div class="ac-icon"><i class="fas fa-calendar-plus"></i></div>
        <span>Buat Jadwal</span>
    </a>
    <a href="{{ route('lansia.edukasi.index') }}" class="action-card">
        <div class="ac-icon"><i class="fas fa-book-open"></i></div>
        <span>Konten Edukasi</span>
    </a>
</div>
@endsection

@section('scripts')
<script>
function animateCounter(id, target, suffix = '') {
    const el = document.getElementById(id);
    if (!el) return;
    let cur = 0;
    const step = Math.max(1, Math.ceil(target / 30));
    const t = setInterval(() => {
        cur = Math.min(cur + step, target);
        el.textContent = cur + suffix;
        if (cur >= target) clearInterval(t);
    }, 30);
}

async function loadStats() {
    try {
        const res  = await fetch('{{ route("lansia.stats") }}', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const json = await res.json();
        if (!json.success) return;
        const d = json.data;
        animateCounter('totalLansia',       d.total_lansia);
        animateCounter('totalLaki',         d.total_laki);
        animateCounter('totalPerempuan',    d.total_perempuan);
        document.getElementById('rataUsia').textContent = d.rata_rata_usia + ' th';
        animateCounter('kunjunganBulanIni', d.kunjungan_bulan_ini);
        animateCounter('tidakNormal',       d.tidak_normal);
    } catch (e) { console.error(e); }
}

document.addEventListener('DOMContentLoaded', loadStats);
</script>
@endsection
