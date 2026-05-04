<?php $__env->startSection('title', 'Laporan Lansia'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    body { background:linear-gradient(135deg,#D1FAE5 0%,#A7F3D0 50%,#6EE7B7 100%) !important; }
    .container { max-width:1340px; margin:0 auto; padding:16px 20px; }
    .card { background:#fff; border-radius:16px; padding:20px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#065F46; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

    .btn { padding:10px 20px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; }
    .btn-primary { background:#10B981; color:#fff; } .btn-primary:hover { background:#059669; }
    .btn-success { background:#059669; color:#fff; } .btn-success:hover { background:#047857; }
    .btn-outline { background:transparent; border:2px solid #10B981; color:#10B981; } .btn-outline:hover { background:#10B981; color:#fff; }

    .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select { padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; transition:all 0.2s; }
    .form-group input:focus, .form-group select:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.1); }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:30px; }
    @media(max-width:1200px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:768px){ .stats-grid{ grid-template-columns:1fr; } }

    .stat-box { background:linear-gradient(135deg,#F0FDF4,#DCFCE7); padding:20px; border-radius:12px; border:2px solid #BBF7D0; }
    .stat-value { font-size:36px; font-weight:700; color:#065F46; margin-bottom:4px; }
    .stat-label { font-size:14px; color:#16A34A; font-weight:600; }

    .export-card { background:linear-gradient(135deg,#ECFDF5,#D1FAE5); padding:24px; border-radius:12px; border:2px solid #A7F3D0; margin-bottom:20px; }
    .export-title { font-size:18px; font-weight:700; color:#065F46; margin-bottom:12px; display:flex; align-items:center; gap:10px; }
    .export-desc { font-size:14px; color:#059669; margin-bottom:16px; }

    .filter-section { background:#F9FAFB; padding:20px; border-radius:12px; border:2px solid #E5E7EB; margin-bottom:20px; }
    .filter-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
    @media(max-width:768px){ .filter-grid{ grid-template-columns:1fr; } }

    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:14px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:280px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; }
    .toast-error   { background:#EF4444; }
    .toast-info    { background:#3B82F6; }

    .info-box { background:#EFF6FF; border:2px solid #BFDBFE; border-radius:8px; padding:16px; margin-top:20px; }
    .info-box-title { font-weight:700; color:#1E40AF; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
    .info-box-text { font-size:13px; color:#1E40AF; line-height:1.6; }

    .loading-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:9998; align-items:center; justify-content:center; }
    .loading-overlay.active { display:flex; }
    .loading-box { background:#fff; padding:40px; border-radius:16px; text-align:center; }
    .loading-spinner { font-size:48px; color:#10B981; margin-bottom:16px; animation:spin 1s linear infinite; }
    @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="toast"></div>
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-box">
        <div class="loading-spinner"><i class="fas fa-spinner"></i></div>
        <p style="font-size:16px;font-weight:600;color:#065F46;">Sedang memproses export...</p>
        <p style="font-size:13px;color:#64748B;margin-top:8px;">Mohon tunggu sebentar</p>
    </div>
</div>

<div class="page-header">
    <h1 class="page-title">Laporan Lansia</h1>
    <p class="page-subtitle"><i class="fas fa-file-excel" style="color:#10B981;margin-right:6px;"></i> Export dan Laporan Data Lansia</p>
</div>


<div class="card">
    <h3 style="font-size:18px;font-weight:700;color:#065F46;margin-bottom:20px;"><i class="fas fa-chart-bar"></i> Statistik Data Lansia</h3>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value" id="totalLansia">0</div>
            <div class="stat-label">Total Lansia</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" id="totalLakiLaki">0</div>
            <div class="stat-label">Laki-laki</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" id="totalPerempuan">0</div>
            <div class="stat-label">Perempuan</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" id="rataUsia">0</div>
            <div class="stat-label">Rata-rata Usia</div>
        </div>
    </div>
</div>


<div class="card">
    <div class="export-card">
        <div class="export-title">
            <i class="fas fa-file-excel" style="font-size:24px;"></i>
            Export Data Lansia ke Excel
        </div>
        <p class="export-desc">
            Export semua data lansia termasuk data pribadi, data kesehatan, dan riwayat kunjungan ke format Excel (.xlsx)
        </p>

        <div class="filter-section">
            <h4 style="font-size:15px;font-weight:700;color:#065F46;margin-bottom:16px;"><i class="fas fa-filter"></i> Filter Data Export</h4>
            <div class="filter-grid">
                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="filterTanggalMulai" max="<?php echo e(date('Y-m-d')); ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" id="filterTanggalAkhir" max="<?php echo e(date('Y-m-d')); ?>">
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select id="filterJenisKelamin">
                        <option value="">Semua</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:16px;">
                <button class="btn btn-outline" onclick="resetFilter()"><i class="fas fa-undo"></i> Reset Filter</button>
            </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-success" onclick="exportExcel()">
                <i class="fas fa-file-excel"></i> Export ke Excel
            </button>
            <button class="btn btn-primary" onclick="exportExcelSemua()">
                <i class="fas fa-download"></i> Export Semua Data (Tanpa Filter)
            </button>
        </div>

        <div class="info-box">
            <div class="info-box-title">
                <i class="fas fa-info-circle"></i>
                Informasi Export
            </div>
            <div class="info-box-text">
                <strong>Data yang akan di-export:</strong>
                <ul style="margin:8px 0 0 20px;">
                    <li>Data Pribadi: Nama, NIK, Jenis Kelamin, Tanggal Lahir, Tempat Lahir, Usia, Alamat</li>
                    <li>Data Keluarga: Nama Wali, NIK Wali, No HP Wali, Nama KK, RT/RW</li>
                    <li>Data Kesehatan: Berat Badan, Tinggi Badan, BMI, Tekanan Darah, Gula Darah, Kolesterol, Asam Urat</li>
                    <li>Status Kesehatan: Kategori kondisi kesehatan lansia</li>
                    <li>Tanggal Pemeriksaan Terakhir</li>
                </ul>
                <p style="margin-top:8px;"><strong>Format:</strong> File Excel (.xlsx) dengan nama "Laporan_Lansia_[tanggal].xlsx"</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    const icons = { success:'check-circle', error:'times-circle', info:'info-circle' };
    el.innerHTML = `<i class="fas fa-${icons[type]}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.add('active');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.remove('active');
}

async function loadStats() {
    try {
        const res = await fetch('/lansia/api/laporan/stats', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        
        if (data.success) {
            document.getElementById('totalLansia').textContent = data.data.total_lansia;
            document.getElementById('totalLakiLaki').textContent = data.data.total_laki;
            document.getElementById('totalPerempuan').textContent = data.data.total_perempuan;
            document.getElementById('rataUsia').textContent = data.data.rata_usia + ' th';
        }
    } catch (e) {
        console.error('Gagal memuat statistik:', e);
    }
}

async function exportExcel() {
    const tanggalMulai = document.getElementById('filterTanggalMulai').value;
    const tanggalAkhir = document.getElementById('filterTanggalAkhir').value;
    const jenisKelamin = document.getElementById('filterJenisKelamin').value;

    if (tanggalMulai && tanggalAkhir && tanggalMulai > tanggalAkhir) {
        toast('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 'error');
        return;
    }

    showLoading();
    toast('Memproses export data...', 'info');

    try {
        const params = new URLSearchParams();
        if (tanggalMulai) params.append('tanggal_mulai', tanggalMulai);
        if (tanggalAkhir) params.append('tanggal_akhir', tanggalAkhir);
        if (jenisKelamin) params.append('jenis_kelamin', jenisKelamin);

        const res = await fetch(`/lansia/api/laporan/export?${params.toString()}`, {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            credentials: 'same-origin'
        });

        if (!res.ok) {
            throw new Error('Export gagal');
        }

        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Laporan_Lansia_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        hideLoading();
        toast('Export berhasil! File sedang diunduh...', 'success');
    } catch (err) {
        hideLoading();
        toast('Gagal export data: ' + err.message, 'error');
    }
}

async function exportExcelSemua() {
    if (!confirm('Export semua data lansia tanpa filter? Proses ini mungkin memakan waktu.')) {
        return;
    }

    showLoading();
    toast('Memproses export semua data...', 'info');

    try {
        const res = await fetch('/lansia/api/laporan/export', {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            credentials: 'same-origin'
        });

        if (!res.ok) {
            throw new Error('Export gagal');
        }

        const blob = await res.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Laporan_Lansia_Lengkap_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        hideLoading();
        toast('Export berhasil! File sedang diunduh...', 'success');
    } catch (err) {
        hideLoading();
        toast('Gagal export data: ' + err.message, 'error');
    }
}

function resetFilter() {
    document.getElementById('filterTanggalMulai').value = '';
    document.getElementById('filterTanggalAkhir').value = '';
    document.getElementById('filterJenisKelamin').value = '';
    toast('Filter direset', 'info');
}

document.addEventListener('DOMContentLoaded', loadStats);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lansia', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\asus\VSCode\poscare-laravel\resources\views/lansia/laporan/index.blade.php ENDPATH**/ ?>