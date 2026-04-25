@extends('layouts.lansia')
@section('title', 'Dashboard Lansia')

@section('styles')
<style>
    body { background:linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
    @keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }

    .page-title { font-size:32px; font-weight:700; background:linear-gradient(135deg,#065F46,#10B981); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:6px; }
    .page-subtitle { font-size:15px; color:#64748B; margin-bottom:32px; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:36px; }
    @media(max-width:1200px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:768px){ .stats-grid{ grid-template-columns:1fr; } }

    .stat-card { background:rgba(255,255,255,0.85); border-radius:16px; padding:24px; box-shadow:0 4px 16px rgba(0,0,0,0.06); border:1px solid rgba(255,255,255,0.8); backdrop-filter:blur(10px); display:flex; align-items:center; gap:18px; animation:fadeInUp 0.5s ease backwards; transition:all 0.3s; }
    .stat-card:hover { transform:translateY(-6px); box-shadow:0 12px 32px rgba(0,0,0,0.1); }
    .stat-card:nth-child(1){ animation-delay:0.1s; } .stat-card:nth-child(2){ animation-delay:0.2s; }
    .stat-card:nth-child(3){ animation-delay:0.3s; } .stat-card:nth-child(4){ animation-delay:0.4s; }

    .stat-icon { width:60px; height:60px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0; }
    .si-green  { background:linear-gradient(135deg,rgba(16,185,129,0.15),rgba(5,150,105,0.1)); color:#10B981; }
    .si-blue   { background:linear-gradient(135deg,rgba(36,107,206,0.15),rgba(29,88,168,0.1)); color:#246BCE; }
    .si-pink   { background:linear-gradient(135deg,rgba(236,72,153,0.15),rgba(219,39,119,0.1)); color:#EC4899; }
    .si-orange { background:linear-gradient(135deg,rgba(245,158,11,0.15),rgba(217,119,6,0.1)); color:#F59E0B; }

    .stat-content { flex:1; text-align:right; }
    .stat-value { font-size:38px; font-weight:700; background:linear-gradient(135deg,#1E3A5F,#2C5E92); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1.1; }
    .stat-label { font-size:13px; font-weight:500; color:#64748B; text-transform:uppercase; letter-spacing:0.5px; }

    h2.card-title { font-size:20px; font-weight:700; color:#1E3A5F; margin-bottom:20px; padding-left:14px; position:relative; }
    h2.card-title::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:22px; background:linear-gradient(180deg,#10B981,#059669); border-radius:2px; }

    .quick-actions { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:36px; }
    @media(max-width:1200px){ .quick-actions{ grid-template-columns:repeat(2,1fr); } }
    .action-btn { background:rgba(255,255,255,0.95); border-radius:14px; padding:22px 16px; text-align:center; cursor:pointer; transition:all 0.3s; text-decoration:none; color:#065F46; box-shadow:0 2px 8px rgba(0,0,0,0.04); border:1px solid rgba(255,255,255,0.8); display:block; }
    .action-btn:hover { background:linear-gradient(135deg,rgba(16,185,129,0.08),rgba(5,150,105,0.05)); transform:translateY(-5px); box-shadow:0 10px 24px rgba(16,185,129,0.15); }
    .action-btn i { font-size:28px; color:#10B981; margin:0 auto 12px; display:block; }
    .action-btn span { display:block; font-weight:600; font-size:14px; color:#1E3A5F; }

    .info-card { background:rgba(255,255,255,0.95); border-radius:18px; padding:28px; box-shadow:0 6px 20px rgba(0,0,0,0.07); }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Selamat Datang, {{ ucfirst(Auth::user()->nama_lengkap ?? Auth::user()->username) }}!</h1>
    <p class="page-subtitle">Dashboard PosCare — Sistem Informasi Digital Posyandu Lansia</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon si-green"><i class="fas fa-user-friends"></i></div>
        <div class="stat-content">
            <div class="stat-value" id="totalLansia">0</div>
            <div class="stat-label">Total Lansia</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fas fa-mars"></i></div>
        <div class="stat-content">
            <div class="stat-value" id="totalLaki">0</div>
            <div class="stat-label">Laki-laki</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-pink"><i class="fas fa-venus"></i></div>
        <div class="stat-content">
            <div class="stat-value" id="totalPerempuan">0</div>
            <div class="stat-label">Perempuan</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-orange"><i class="fas fa-birthday-cake"></i></div>
        <div class="stat-content">
            <div class="stat-value" id="rataUsia">0</div>
            <div class="stat-label">Rata-rata Usia</div>
        </div>
    </div>
</div>

<h2 class="card-title">Aksi Cepat</h2>
<div class="quick-actions">
    <a href="{{ route('lansia.data.index') }}" class="action-btn">
        <i class="fas fa-user-plus"></i>
        <span>Tambah Data Lansia</span>
    </a>
    <a href="{{ route('lansia.jadwal.index') }}" class="action-btn">
        <i class="fas fa-calendar-plus"></i>
        <span>Buat Jadwal</span>
    </a>
    <a href="{{ route('lansia.pengobatan.index') }}" class="action-btn">
        <i class="fas fa-pills"></i>
        <span>Catat Pengobatan</span>
    </a>
    <a href="{{ route('lansia.laporan.index') }}" class="action-btn">
        <i class="fas fa-file-excel"></i>
        <span>Ekspor Laporan</span>
    </a>
</div>

<div class="info-card">
    <h2 class="card-title">Informasi Sistem</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div style="background:#F0FDF4;border-radius:12px;padding:20px;">
            <div style="font-size:13px;color:#065F46;font-weight:700;margin-bottom:8px;"><i class="fas fa-calendar-check"></i> Jadwal Bulan Ini</div>
            <div style="font-size:28px;font-weight:700;color:#10B981;" id="jadwalBulanIni">0</div>
            <div style="font-size:12px;color:#6B7280;">kegiatan posyandu lansia</div>
        </div>
        <div style="background:#EFF6FF;border-radius:12px;padding:20px;">
            <div style="font-size:13px;color:#1E40AF;font-weight:700;margin-bottom:8px;"><i class="fas fa-stethoscope"></i> Pemeriksaan Bulan Ini</div>
            <div style="font-size:28px;font-weight:700;color:#246BCE;" id="periksaBulanIni">0</div>
            <div style="font-size:12px;color:#6B7280;">pemeriksaan kesehatan</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function animateCounter(id, target) {
    const el = document.getElementById(id);
    if (!el) return;
    let cur = 0;
    const step = Math.max(1, Math.ceil(target / 30));
    const t = setInterval(() => {
        cur = Math.min(cur + step, target);
        el.textContent = Number.isInteger(target) ? cur : cur.toFixed(1);
        if (cur >= target) clearInterval(t);
    }, 30);
}

async function loadStats() {
    try {
        const res  = await fetch('{{ route("lansia.stats") }}', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (!data.success) return;
        const d = data.data;
        animateCounter('totalLansia',    d.total_lansia);
        animateCounter('totalLaki',      d.total_laki);
        animateCounter('totalPerempuan', d.total_perempuan);
        animateCounter('rataUsia',       d.rata_rata_usia);
        animateCounter('jadwalBulanIni', d.jadwal_bulan_ini);
        animateCounter('periksaBulanIni',d.periksa_bulan_ini);
    } catch (e) { console.error(e); }
}

document.addEventListener('DOMContentLoaded', loadStats);
</script>
@endsection
