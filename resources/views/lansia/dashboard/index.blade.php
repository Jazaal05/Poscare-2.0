@extends('layouts.lansia')
@section('title', 'Dashboard Lansia')

@section('styles')
<style>
    @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    
    body { background:linear-gradient(135deg,#D1FAE5 0%,#A7F3D0 50%,#6EE7B7 100%) !important; font-family:'Inter',sans-serif !important; min-height:100vh; }
    .container { max-width:1340px; margin:0 auto; padding:16px 20px; position:relative; z-index:1; }
    
    .page-header { animation:fadeInUp 0.6s ease-out; margin-bottom:32px; }
    .page-title { font-size:32px !important; font-weight:700 !important; background:linear-gradient(135deg,#065F46 0%,#10B981 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; letter-spacing:-0.8px !important; margin-bottom:10px !important; }
    .page-subtitle { font-size:16px !important; font-weight:500 !important; color:#64748B !important; }
    
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; margin-bottom:40px; }
    @media(max-width:1200px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:768px){ .stats-grid{ grid-template-columns:1fr; } }
    
    .stat-card { background:rgba(255,255,255,0.95) !important; border-radius:16px !important; padding:28px !important; box-shadow:0 4px 16px rgba(0,0,0,0.06) !important; transition:all 0.4s cubic-bezier(0.4,0,0.2,1) !important; border:1px solid rgba(255,255,255,0.8) !important; backdrop-filter:blur(10px) !important; position:relative; overflow:hidden; animation:fadeInUp 0.6s ease-out backwards; min-height:140px; display:flex; align-items:center; }
    .stat-card:nth-child(1){ animation-delay:0.1s; } .stat-card:nth-child(2){ animation-delay:0.2s; } .stat-card:nth-child(3){ animation-delay:0.3s; } .stat-card:nth-child(4){ animation-delay:0.4s; }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#10B981,#059669); opacity:0; transition:opacity 0.3s ease; }
    .stat-card:hover::before { opacity:1; }
    .stat-card:hover { transform:translateY(-8px) scale(1.02) !important; box-shadow:0 20px 40px rgba(0,0,0,0.12) !important; }
    
    .stat-header { display:flex; justify-content:flex-start; align-items:center; flex-direction:row; gap:20px; width:100%; }
    .stat-content { flex:1; text-align:right; display:flex; flex-direction:column; align-items:flex-end; justify-content:center; }
    .stat-icon { width:64px !important; height:64px !important; border-radius:16px !important; display:flex; justify-content:center; align-items:center; font-size:28px !important; flex-shrink:0; transition:all 0.4s ease; }
    .stat-card:hover .stat-icon { transform:scale(1.1) rotate(5deg); }
    
    .stat-icon.green { background:linear-gradient(135deg,rgba(16,185,129,0.15),rgba(5,150,105,0.1)) !important; color:#10B981 !important; box-shadow:0 4px 12px rgba(16,185,129,0.2); }
    .stat-icon.blue { background:linear-gradient(135deg,rgba(59,130,246,0.15),rgba(37,99,235,0.1)) !important; color:#3B82F6 !important; box-shadow:0 4px 12px rgba(59,130,246,0.2); }
    .stat-icon.orange { background:linear-gradient(135deg,rgba(251,146,60,0.15),rgba(249,115,22,0.1)) !important; color:#FB923C !important; box-shadow:0 4px 12px rgba(251,146,60,0.2); }
    .stat-icon.red { background:linear-gradient(135deg,rgba(239,68,68,0.15),rgba(220,38,38,0.1)) !important; color:#EF4444 !important; box-shadow:0 4px 12px rgba(239,68,68,0.2); }
    
    .stat-value { font-size:42px !important; font-weight:700 !important; background:linear-gradient(135deg,#065F46 0%,#10B981 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1.1 !important; margin-bottom:8px !important; letter-spacing:-1px; }
    .stat-label { font-size:14px !important; font-weight:500 !important; color:#64748B !important; text-transform:uppercase; letter-spacing:0.5px; }
    
    .card { background:rgba(255,255,255,0.95) !important; border-radius:20px !important; padding:32px !important; box-shadow:0 8px 24px rgba(0,0,0,0.08) !important; border:1px solid rgba(255,255,255,0.8) !important; backdrop-filter:blur(10px) !important; margin-bottom:24px; }
    h2.card-title { font-size:22px !important; font-weight:600 !important; color:#065F46 !important; margin-bottom:24px !important; position:relative; padding-left:16px; }
    h2.card-title::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:linear-gradient(180deg,#10B981,#059669); border-radius:2px; }
    
    .charts-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:24px; margin-bottom:24px; }
    @media(max-width:968px){ .charts-grid{ grid-template-columns:1fr; } }
    .chart-container { position:relative; height:300px; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard Lansia</h1>
    <p class="page-subtitle"><i class="fas fa-chart-line" style="color:#10B981;margin-right:6px;"></i> Statistik Kesehatan Lansia Terpadu</p>
</div>

{{-- Kartu Statistik --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon green"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="totalLansia">0</div>
                <div class="stat-label">Total Lansia</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="kunjunganBulanIni">0</div>
                <div class="stat-label">Kunjungan Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange"><i class="fas fa-birthday-cake"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="rataUsia">0</div>
                <div class="stat-label">Rata-rata Usia</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="tidakNormal">0</div>
                <div class="stat-label">Kondisi Tidak Normal</div>
            </div>
        </div>
    </div>
</div>

{{-- Grafik Statistik --}}
<div class="charts-grid">
    <div class="card">
        <h2 class="card-title">Distribusi Usia Lansia</h2>
        <div class="chart-container">
            <canvas id="chartDistribusiUsia"></canvas>
        </div>
    </div>
    <div class="card">
        <h2 class="card-title">Kondisi Kesehatan</h2>
        <div class="chart-container">
            <canvas id="chartKondisiKesehatan"></canvas>
        </div>
    </div>
</div>

<div class="card">
    <h2 class="card-title">Trend Kunjungan (6 Bulan Terakhir)</h2>
    <div class="chart-container">
        <canvas id="chartTrendKunjungan"></canvas>
    </div>
</div>

<div class="card">
    <h2 class="card-title">Informasi Dashboard</h2>
    <p style="color:#64748B;font-size:14px;line-height:1.8;">
        Dashboard ini menampilkan statistik kesehatan lansia secara real-time. Data diperbarui otomatis berdasarkan kunjungan dan pemeriksaan kesehatan terbaru. Grafik menampilkan distribusi usia, kondisi kesehatan terkini, dan trend kunjungan dalam 6 bulan terakhir.
    </p>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let chartDistribusiUsia = null;
let chartKondisiKesehatan = null;
let chartTrendKunjungan = null;

function animateCounter(id, target) {
    const el = document.getElementById(id);
    if (!el) return;
    let current = 0;
    const step = Math.ceil(target / 30);
    const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current;
        if (current >= target) clearInterval(timer);
    }, 30);
}

async function loadStats() {
    try {
        const res = await fetch('/lansia/web/dashboard/stats', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        
        if (!res.ok) {
            console.error('Response not OK:', res.status, res.statusText);
            return;
        }
        
        const result = await res.json();
        console.log('Stats data:', result);
        
        if (!result.success) {
            console.error('API returned success=false');
            return;
        }
        
        const data = result.data;
        animateCounter('totalLansia', data.total_lansia);
        animateCounter('kunjunganBulanIni', data.kunjungan_bulan_ini);
        animateCounter('tidakNormal', data.tidak_normal);
        document.getElementById('rataUsia').textContent = data.rata_rata_usia + ' th';
    } catch (err) {
        console.error('Gagal memuat statistik:', err);
    }
}

async function loadChartDistribusiUsia() {
    try {
        const res = await fetch('/lansia/web/dashboard/chart-distribusi-usia', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        
        if (!res.ok) {
            console.error('Chart Distribusi Usia - Response not OK:', res.status);
            return;
        }
        
        const result = await res.json();
        if (!result.success) {
            console.error('Chart Distribusi Usia - API returned success=false');
            return;
        }
        
        const ctx = document.getElementById('chartDistribusiUsia');
        if (!ctx) return;
        
        if (chartDistribusiUsia) {
            chartDistribusiUsia.destroy();
        }
        
        chartDistribusiUsia = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: result.data.labels,
                datasets: [{
                    label: 'Jumlah Lansia',
                    data: result.data.values,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 12 } },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (err) {
        console.error('Gagal memuat chart distribusi usia:', err);
    }
}

async function loadChartKondisiKesehatan() {
    try {
        const res = await fetch('/lansia/web/dashboard/chart-kondisi-kesehatan', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        
        if (!res.ok) {
            console.error('Chart Kondisi Kesehatan - Response not OK:', res.status);
            return;
        }
        
        const result = await res.json();
        if (!result.success) {
            console.error('Chart Kondisi Kesehatan - API returned success=false');
            return;
        }
        
        const ctx = document.getElementById('chartKondisiKesehatan');
        if (!ctx) return;
        
        if (chartKondisiKesehatan) {
            chartKondisiKesehatan.destroy();
        }
        
        chartKondisiKesehatan = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: result.data.labels,
                datasets: [{
                    data: result.data.values,
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',   // Normal - hijau
                        'rgba(239, 68, 68, 0.8)',    // Hipertensi - merah
                        'rgba(251, 146, 60, 0.8)',   // Diabetes - orange
                        'rgba(234, 179, 8, 0.8)',    // Kolesterol - kuning
                        'rgba(168, 85, 247, 0.8)',   // Asam Urat - ungu
                    ],
                    borderColor: '#fff',
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 },
                            usePointStyle: true,
                            pointStyle: 'circle',
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (err) {
        console.error('Gagal memuat chart kondisi kesehatan:', err);
    }
}

async function loadChartTrendKunjungan() {
    try {
        const res = await fetch('/lansia/web/dashboard/chart-trend-kunjungan', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        
        if (!res.ok) {
            console.error('Chart Trend Kunjungan - Response not OK:', res.status);
            return;
        }
        
        const result = await res.json();
        if (!result.success) {
            console.error('Chart Trend Kunjungan - API returned success=false');
            return;
        }
        
        const ctx = document.getElementById('chartTrendKunjungan');
        if (!ctx) return;
        
        if (chartTrendKunjungan) {
            chartTrendKunjungan.destroy();
        }
        
        chartTrendKunjungan = new Chart(ctx, {
            type: 'line',
            data: {
                labels: result.data.labels,
                datasets: [{
                    label: 'Jumlah Kunjungan',
                    data: result.data.values,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 12 } },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (err) {
        console.error('Gagal memuat chart trend kunjungan:', err);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadStats();
    loadChartDistribusiUsia();
    loadChartKondisiKesehatan();
    loadChartTrendKunjungan();
});
</script>
@endsection
