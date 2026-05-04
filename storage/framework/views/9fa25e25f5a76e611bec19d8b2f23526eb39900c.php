

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    @keyframes shimmer { 0% { background-position:-1000px 0; } 100% { background-position:1000px 0; } }

    body {
        background: linear-gradient(135deg, #E8F4FF 0%, #D4E9FF 50%, #C5E2FF 100%) !important;
        font-family: 'Inter', sans-serif !important;
        min-height: 100vh;
    }
    body::before {
        content:''; position:fixed; top:0; left:0; width:100%; height:100%;
        background-image: radial-gradient(circle at 20% 50%, rgba(74,144,226,0.05) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(155,93,229,0.05) 0%, transparent 50%);
        pointer-events:none; z-index:0;
    }
    .container { position:relative; z-index:1; }
    .page-header { animation:fadeInUp 0.6s ease-out; margin-bottom:32px; }
    .page-title { font-size:32px !important; font-weight:700 !important; background:linear-gradient(135deg,#2C5E92 0%,#4A90E2 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; letter-spacing:-0.8px !important; margin-bottom:10px !important; }
    .page-subtitle { font-size:16px !important; font-weight:500 !important; color:#64748B !important; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; margin-bottom:40px; }
    @media(max-width:1200px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:768px){ .stats-grid{ grid-template-columns:1fr; } }

    .stat-card { background:rgba(255,255,255,0.75) !important; border-radius:16px !important; padding:28px !important; box-shadow:0 4px 16px rgba(0,0,0,0.06) !important; transition:all 0.4s cubic-bezier(0.4,0,0.2,1) !important; border:1px solid rgba(255,255,255,0.8) !important; backdrop-filter:blur(10px) !important; position:relative; overflow:hidden; animation:fadeInUp 0.6s ease-out backwards; min-height:140px; display:flex; align-items:center; }
    .stat-card:nth-child(1){ animation-delay:0.1s; } .stat-card:nth-child(2){ animation-delay:0.2s; } .stat-card:nth-child(3){ animation-delay:0.3s; } .stat-card:nth-child(4){ animation-delay:0.4s; }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#4A90E2,#9B5DE5); opacity:0; transition:opacity 0.3s ease; }
    .stat-card:hover::before { opacity:1; }
    .stat-card:hover { transform:translateY(-8px) scale(1.02) !important; box-shadow:0 20px 40px rgba(0,0,0,0.12) !important; }
    .stat-header { display:flex; justify-content:flex-start; align-items:center; flex-direction:row; gap:20px; width:100%; }
    .stat-content { flex:1; text-align:right; display:flex; flex-direction:column; align-items:flex-end; justify-content:center; }
    .stat-icon { width:64px !important; height:64px !important; border-radius:16px !important; display:flex; justify-content:center; align-items:center; font-size:28px !important; flex-shrink:0; transition:all 0.4s ease; }
    .stat-card:hover .stat-icon { transform:scale(1.1) rotate(5deg); }
    .stat-icon.blue { background:linear-gradient(135deg,rgba(74,144,226,0.15),rgba(59,130,246,0.1)) !important; color:#4A90E2 !important; box-shadow:0 4px 12px rgba(74,144,226,0.2); }
    .stat-icon.green { background:linear-gradient(135deg,rgba(39,174,96,0.15),rgba(16,185,129,0.1)) !important; color:#27AE60 !important; box-shadow:0 4px 12px rgba(39,174,96,0.2); }
    .stat-icon.orange { background:linear-gradient(135deg,rgba(245,166,35,0.15),rgba(251,146,60,0.1)) !important; color:#F5A623 !important; box-shadow:0 4px 12px rgba(245,166,35,0.2); }
    .stat-icon.purple { background:linear-gradient(135deg,rgba(155,93,229,0.15),rgba(139,92,246,0.1)) !important; color:#9B5DE5 !important; box-shadow:0 4px 12px rgba(155,93,229,0.2); }
    .stat-value { font-size:42px !important; font-weight:700 !important; background:linear-gradient(135deg,#1E3A5F 0%,#2C5E92 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1.1 !important; margin-bottom:8px !important; letter-spacing:-1px; }
    .stat-label { font-size:14px !important; font-weight:500 !important; color:#64748B !important; text-transform:uppercase; letter-spacing:0.5px; }

    h2.card-title { font-size:22px !important; font-weight:600 !important; color:#1E3A5F !important; margin-bottom:24px !important; position:relative; padding-left:16px; }
    h2.card-title::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:4px; height:24px; background:linear-gradient(180deg,#4A90E2,#9B5DE5); border-radius:2px; }

    .quick-actions { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:40px; }
    @media(max-width:1200px){ .quick-actions{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:768px){ .quick-actions{ grid-template-columns:1fr; } }

    .action-btn { background:rgba(255,255,255,0.95) !important; border-radius:16px !important; padding:24px 20px !important; text-align:center; cursor:pointer; transition:all 0.4s cubic-bezier(0.4,0,0.2,1); text-decoration:none; color:#2C5E92 !important; box-shadow:0 2px 8px rgba(0,0,0,0.04) !important; border:1px solid rgba(255,255,255,0.8) !important; backdrop-filter:blur(10px) !important; display:block; }
    .action-btn:hover { background:linear-gradient(135deg,rgba(74,144,226,0.08),rgba(155,93,229,0.05)) !important; transform:translateY(-6px) scale(1.02); box-shadow:0 12px 28px rgba(74,144,226,0.15) !important; }
    .action-btn i { font-size:32px !important; color:#4A90E2 !important; margin:0 auto 16px auto; display:block; transition:all 0.3s ease; }
    .action-btn:hover i { transform:scale(1.15); color:#9B5DE5 !important; }
    .action-btn span { display:block; font-weight:600; font-size:15px; color:#1E3A5F; }

    .dashboard-grid { display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-bottom:40px; }
    @media(max-width:992px){ .dashboard-grid{ grid-template-columns:1fr; } }

    .chart-container, .notification-card { background:rgba(255,255,255,0.95) !important; border-radius:20px !important; padding:32px !important; box-shadow:0 8px 24px rgba(0,0,0,0.08) !important; border:1px solid rgba(255,255,255,0.8) !important; backdrop-filter:blur(10px) !important; }
    #statusGiziChart { max-height:400px !important; width:100% !important; }

    .notification-item { padding:24px !important; border-left:5px solid #4A90E2 !important; background:linear-gradient(135deg,rgba(240,249,255,0.95),rgba(245,251,255,0.98)) !important; border-radius:16px !important; transition:all 0.3s ease; }
    .notification-item:hover { transform:translateX(6px); }
    .notification-item h4 { font-size:16px !important; font-weight:700 !important; color:#4A90E2 !important; margin-bottom:12px !important; display:flex; align-items:center; gap:10px; }
    .notification-item strong { font-size:15px !important; font-weight:700 !important; color:#1E3A5F !important; display:block; margin-bottom:6px; }
    .notification-item p { font-size:13px !important; color:#64748B !important; margin:0 !important; line-height:1.6; }

    .progress-bar-wrapper { background:rgba(226,232,240,0.5); border-radius:12px; height:24px; overflow:hidden; position:relative; margin-bottom:8px; }
    .progress-bar { height:100%; background:linear-gradient(90deg,#4A90E2,#5BA4F5); border-radius:12px; transition:width 1s cubic-bezier(0.4,0,0.2,1); display:flex; align-items:center; justify-content:center; }
    .progress-text { font-size:12px; font-weight:700; color:#fff; }
    .progress-label { font-size:11px; color:#64748B; font-weight:600; display:flex; justify-content:space-between; }

    .vaccine-list { list-style:none; padding:0; margin:12px 0 0 0; }
    .vaccine-item { display:flex; align-items:center; gap:10px; padding:8px 12px; background:rgba(255,255,255,0.6); border-radius:8px; margin-bottom:8px; transition:all 0.2s ease; }
    .vaccine-item:hover { background:rgba(255,255,255,0.9); transform:translateX(4px); }
    .vaccine-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; background:linear-gradient(135deg,rgba(74,144,226,0.15),rgba(74,144,226,0.1)); color:#4A90E2; }
    .vaccine-name { font-size:13px; font-weight:700; color:#1E3A5F; margin-bottom:2px; }
    .vaccine-count { font-size:11px; color:#64748B; font-weight:500; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1 class="page-title">Selamat Datang, <?php echo e(ucfirst(Auth::user()->nama_lengkap ?? Auth::user()->username)); ?>!</h1>
    <p class="page-subtitle">Beranda PosCare - Sistem Informasi Digital Posyandu</p>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon blue"><i class="fas fa-baby"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="totalAnak"><?php echo e($stats['total_anak']); ?></div>
                <div class="stat-label">Total Anak</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon green"><i class="fas fa-heart-pulse"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="giziBaik"><?php echo e($stats['gizi_baik']); ?></div>
                <div class="stat-label">Gizi Baik</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="stunting"><?php echo e($stats['risiko_stunting']); ?></div>
                <div class="stat-label">Resiko Stunting</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon purple"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-content">
                <div class="stat-value" id="jadwalBulanIni"><?php echo e($stats['jadwal_bulan_ini']); ?></div>
                <div class="stat-label">Total Jadwal</div>
            </div>
        </div>
    </div>
</div>


<h2 class="card-title" style="margin-bottom:20px;">Aksi Cepat</h2>
<div class="quick-actions">
    <a href="<?php echo e(route('anak.index')); ?>?tab=tambah" class="action-btn">
        <i class="fas fa-user-plus"></i>
        <span>Tambah Data Anak</span>
    </a>
    <a href="<?php echo e(route('jadwal.index')); ?>?action=buat" class="action-btn">
        <i class="fas fa-calendar-plus"></i>
        <span>Buat Jadwal</span>
    </a>
    <a href="<?php echo e(route('laporan.index')); ?>?action=ekspor" class="action-btn">
        <i class="fas fa-file-export"></i>
        <span>Ekspor Laporan</span>
    </a>
    <a href="<?php echo e(route('edukasi.index')); ?>?action=tambah" class="action-btn">
        <i class="fas fa-book-medical"></i>
        <span>Konten Edukasi</span>
    </a>
</div>


<div class="dashboard-grid">
    <div class="chart-container">
        <h2 class="card-title">Grafik Status Gizi</h2>
        <canvas id="statusGiziChart"></canvas>
    </div>
    <div class="notification-card">
        <h2 class="card-title">Notifikasi Penting</h2>
        <div class="notification-item" id="immunizationNotif">
            <h4><i class="fas fa-syringe"></i> Status Imunisasi</h4>
            <div class="progress-bar-wrapper">
                <div class="progress-bar" id="immunizationProgress" style="width:0%;">
                    <span class="progress-text" id="immunizationPercent">0%</span>
                </div>
            </div>
            <div class="progress-label">
                <span id="immunizationLabel">Memuat data...</span>
            </div>
            <ul class="vaccine-list" id="dynamicVaccineList">
                <li class="vaccine-item" style="justify-content:center;">
                    <div style="text-align:center;color:#64748B;">
                        <i class="fas fa-spinner fa-spin"></i> Memuat data vaksin...
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let statusGiziChart = null;

// Animasi counter angka
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

// Muat statistik dari API Laravel
async function loadDashboardStats() {
    try {
        const response = await fetch('<?php echo e(route("dashboard.stats")); ?>', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });

        if (!response.ok) return;
        const result = await response.json();
        if (!result.success) return;

        const data = result.data;

        // Update kartu statistik dengan animasi
        animateCounter('totalAnak',      data.stats.total_anak);
        animateCounter('giziBaik',       data.stats.gizi_baik);
        animateCounter('stunting',       data.stats.risiko_stunting);
        animateCounter('jadwalBulanIni', data.stats.jadwal_bulan_ini);

        // Update grafik
        renderChart(data.chart);

        // Update notifikasi imunisasi
        const imun = data.notifications.immunization;
        const progressEl = document.getElementById('immunizationProgress');
        const percentEl  = document.getElementById('immunizationPercent');
        const labelEl    = document.getElementById('immunizationLabel');

        if (progressEl) progressEl.style.width = imun.progress_percent + '%';
        if (percentEl)  percentEl.textContent   = imun.progress_percent + '%';
        if (labelEl) {
            // Tampilkan total anak yang sudah pernah imunisasi (bukan hanya bulan ini)
            labelEl.textContent = `${imun.anak_sudah_imunisasi} dari ${imun.total_anak} anak sudah pernah imunisasi`;
        }

        // Render daftar vaksin dinamis
        renderVaccineList(imun.vaccine_list);

    } catch (err) {
        console.error('Gagal memuat statistik:', err);
    }
}

function renderChart(chart) {
    const ctx = document.getElementById('statusGiziChart');
    if (!ctx) return;

    if (statusGiziChart) statusGiziChart.destroy();

    const categories = [
        { label: 'Stunting',             value: chart.stunting             || 0, color: '#EA580C' },
        { label: 'Resiko Stunting',       value: chart.risiko_stunting      || 0, color: '#FDBA74' },
        { label: 'Gizi Kurang',           value: chart.gizi_kurang          || 0, color: '#F87171' },
        { label: 'Beresiko Gizi Kurang',  value: chart.beresiko_gizi_kurang || 0, color: '#FCA5A5' },
        { label: 'Gizi Baik',             value: chart.gizi_baik            || 0, color: '#86EFAC' },
        { label: 'Beresiko Gizi Lebih',   value: chart.beresiko_gizi_lebih  || 0, color: '#FDE047' },
        { label: 'Gizi Lebih',            value: chart.gizi_lebih           || 0, color: '#FB923C' },
        { label: 'Obesitas',              value: chart.obesitas             || 0, color: '#C4B5FD' },
    ];

    const totalAnak = categories.reduce((sum, c) => sum + c.value, 0);

    statusGiziChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: categories.map(c => c.label),
            datasets: [{
                data:            categories.map(c => c.value),
                backgroundColor: categories.map(c => c.color),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 16,
                        font: { size: 12, family: 'Inter', weight: '500' },
                        usePointStyle: true,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => {
                                const value   = data.datasets[0].data[i];
                                const percent = totalAnak > 0 ? Math.round((value / totalAnak) * 100) : 0;
                                return {
                                    text:      `${label}: ${value} (${percent}%)`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    hidden:    false,
                                    index:     i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30,58,95,0.95)',
                    padding: 14,
                    callbacks: {
                        label: function(context) {
                            const value   = context.parsed || 0;
                            const percent = totalAnak > 0 ? Math.round((value / totalAnak) * 100) : 0;
                            return ` ${context.label}: ${value} anak (${percent}%)`;
                        }
                    }
                }
            },
            animation: { animateRotate: true, duration: 1200 }
        }
    });
}

function renderVaccineList(vaccineList) {
    const container = document.getElementById('dynamicVaccineList');
    if (!container || !vaccineList?.length) return;

    container.innerHTML = vaccineList.map(v => `
        <li class="vaccine-item">
            <div class="vaccine-icon"><i class="fas fa-syringe"></i></div>
            <div class="vaccine-info">
                <div class="vaccine-name">${v.label}</div>
                <div class="vaccine-count">${v.not_vaccinated} anak belum divaksin</div>
            </div>
        </li>
    `).join('');
}

// Jalankan saat halaman siap
document.addEventListener('DOMContentLoaded', loadDashboardStats);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\asus\VSCode\poscare-laravel\resources\views/dashboard/index.blade.php ENDPATH**/ ?>