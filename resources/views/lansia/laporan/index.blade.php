@extends('layouts.lansia')
@section('title', 'Laporan Lansia')

@section('styles')
<style>
    body { background:linear-gradient(135deg,#D1FAE5 0%,#A7F3D0 50%,#6EE7B7 100%) !important; }
    .container { max-width:900px; margin:0 auto; padding:16px 20px; }
    .card { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#065F46; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

    .btn { padding:12px 28px; border:none; border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:10px; }
    .btn-export { background:linear-gradient(135deg,#10B981,#059669); color:#fff; box-shadow:0 4px 16px rgba(16,185,129,0.3); }
    .btn-export:hover { background:linear-gradient(135deg,#059669,#047857); transform:translateY(-2px); box-shadow:0 6px 20px rgba(16,185,129,0.4); }
    .btn-export:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
    @media(max-width:900px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    .stat-box { background:linear-gradient(135deg,#F0FDF4,#DCFCE7); padding:20px; border-radius:12px; border:2px solid #BBF7D0; text-align:center; }
    .stat-value { font-size:36px; font-weight:700; color:#065F46; }
    .stat-label { font-size:13px; color:#16A34A; font-weight:600; margin-top:4px; }

    .export-box { background:linear-gradient(135deg,#ECFDF5,#D1FAE5); border:2px solid #A7F3D0; border-radius:14px; padding:28px; text-align:center; }
    .export-icon { font-size:56px; color:#10B981; margin-bottom:16px; }
    .export-title { font-size:22px; font-weight:700; color:#065F46; margin-bottom:8px; }
    .export-desc { font-size:14px; color:#059669; margin-bottom:24px; line-height:1.6; }

    .year-select { padding:10px 16px; border:2px solid #A7F3D0; border-radius:8px; font-size:15px; font-weight:600; color:#065F46; background:#fff; cursor:pointer; margin-right:12px; }
    .year-select:focus { outline:none; border-color:#10B981; }

    .sheet-preview { display:grid; grid-template-columns:repeat(6,1fr); gap:8px; margin:20px 0; }
    @media(max-width:600px){ .sheet-preview{ grid-template-columns:repeat(3,1fr); } }
    .sheet-item { background:#fff; border:2px solid #D1FAE5; border-radius:8px; padding:10px 6px; text-align:center; font-size:12px; font-weight:600; color:#065F46; }
    .sheet-item i { display:block; font-size:18px; color:#10B981; margin-bottom:4px; }

    .info-list { text-align:left; background:#fff; border-radius:10px; padding:16px 20px; margin-top:20px; border:1px solid #D1FAE5; }
    .info-list li { font-size:13px; color:#374151; margin-bottom:6px; line-height:1.5; }
    .info-list li::marker { color:#10B981; }

    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:14px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:280px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; }
    .toast-error   { background:#EF4444; }
    .toast-info    { background:#3B82F6; }

    .loading-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:9998; align-items:center; justify-content:center; }
    .loading-overlay.active { display:flex; }
    .loading-box { background:#fff; padding:40px 48px; border-radius:16px; text-align:center; }
    @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
@endsection

@section('content')
<div id="toast"></div>
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-box">
        <i class="fas fa-spinner fa-spin" style="font-size:48px;color:#10B981;display:block;margin-bottom:16px;"></i>
        <p style="font-size:16px;font-weight:700;color:#065F46;">Sedang membuat file Excel...</p>
        <p style="font-size:13px;color:#64748B;margin-top:6px;">Mohon tunggu, proses ini mungkin memakan beberapa detik</p>
    </div>
</div>

<div class="page-header">
    <h1 class="page-title">Laporan Lansia</h1>
    <p class="page-subtitle"><i class="fas fa-file-excel" style="color:#10B981;margin-right:6px;"></i> Export Laporan Kunjungan Lansia ke Excel</p>
</div>

{{-- STATISTIK --}}
<div class="card">
    <h3 style="font-size:17px;font-weight:700;color:#065F46;margin-bottom:16px;"><i class="fas fa-chart-bar"></i> Statistik Data Lansia</h3>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value" id="totalLansia">–</div>
            <div class="stat-label">Total Lansia</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" id="totalLakiLaki">–</div>
            <div class="stat-label">Laki-laki</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" id="totalPerempuan">–</div>
            <div class="stat-label">Perempuan</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" id="rataUsia">–</div>
            <div class="stat-label">Rata-rata Usia</div>
        </div>
    </div>
</div>

{{-- EXPORT --}}
<div class="card">
    <div class="export-box">
        <div class="export-icon"><i class="fas fa-file-excel"></i></div>
        <div class="export-title">Export Laporan Kunjungan Lansia</div>
        <p class="export-desc">
            File Excel berisi <strong>12 sheet</strong> (Januari – Desember).<br>
            Setiap sheet menampilkan data kunjungan pada bulan tersebut.<br>
            Bulan yang tidak ada kunjungan akan tampil kosong secara otomatis.
        </p>

        {{-- Preview 12 sheet --}}
        <div class="sheet-preview">
            @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $b)
            <div class="sheet-item">
                <i class="fas fa-table"></i>
                {{ $b }}
            </div>
            @endforeach
        </div>

        {{-- Pilih tahun + tombol export --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap;margin-top:8px;">
            <div>
                <label style="font-size:13px;font-weight:600;color:#065F46;margin-right:8px;">Tahun:</label>
                <select id="selectTahun" class="year-select">
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button class="btn btn-export" id="btnExport" onclick="exportExcel()">
                <i class="fas fa-download"></i> Download Excel
            </button>
        </div>

        {{-- Info kolom --}}
        <ul class="info-list">
            <li><strong>Data per baris:</strong> Nama, NIK, Jenis Kelamin, Usia, Tanggal Kunjungan</li>
            <li><strong>Pengukuran:</strong> Berat Badan, Tinggi Badan, Tekanan Darah, Gula Darah, Kolesterol, Asam Urat</li>
            <li><strong>Status:</strong> Status Tensi, Status Gula, Status Kolesterol, Status Asam Urat</li>
            <li><strong>Catatan:</strong> Keluhan, Obat Diberikan, Vitamin Diberikan, Catatan Bidan</li>
            <li><strong>Nama file:</strong> Laporan_Kunjungan_Lansia_[tahun].xlsx</li>
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    const icons = { success:'check-circle', error:'times-circle', info:'info-circle' };
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 5000);
}

async function loadStats() {
    try {
        const res = await fetch('/lansia/api/laporan/stats', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('totalLansia').textContent    = data.data.total_lansia;
            document.getElementById('totalLakiLaki').textContent  = data.data.total_laki;
            document.getElementById('totalPerempuan').textContent = data.data.total_perempuan;
            document.getElementById('rataUsia').textContent       = data.data.rata_usia + ' th';
        }
    } catch (e) { console.error(e); }
}

async function exportExcel() {
    const tahun = document.getElementById('selectTahun').value;
    const btn   = document.getElementById('btnExport');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    document.getElementById('loadingOverlay').classList.add('active');
    toast('Membuat file Excel, mohon tunggu...', 'info');

    try {
        const res = await fetch(`/lansia/api/laporan/export?tahun=${tahun}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN },
            credentials: 'same-origin'
        });

        if (!res.ok) throw new Error('Server error ' + res.status);

        const blob = await res.blob();
        const url  = window.URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `Laporan_Kunjungan_Lansia_${tahun}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        toast('File Excel berhasil diunduh!', 'success');
    } catch (err) {
        toast('Gagal export: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-download"></i> Download Excel';
        document.getElementById('loadingOverlay').classList.remove('active');
    }
}

document.addEventListener('DOMContentLoaded', loadStats);
</script>
@endsection
