@extends('layouts.app')

@section('title', 'Laporan & Export')

@section('styles')
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }

    .page-title    { font-size:26px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

    /* Main card */
    .main-card { background:#fff; border-radius:16px; padding:28px 32px;
                 box-shadow:0 4px 20px rgba(0,0,0,0.08); margin-bottom:24px; }
    .card-section-title { font-size:16px; font-weight:700; color:#1E3A5F; margin-bottom:6px; }
    .card-section-sub   { font-size:13px; color:#64748B; margin-bottom:20px; }

    /* Tips box */
    .tips-box { display:flex; align-items:flex-start; gap:10px; background:#EFF6FF;
                border-left:4px solid #246BCE; border-radius:8px; padding:12px 16px;
                margin-bottom:24px; font-size:13px; color:#1E40AF; }
    .tips-box i { margin-top:1px; flex-shrink:0; }

    /* Filter grid */
    .filter-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:24px; }
    @media(max-width:768px){ .filter-grid{ grid-template-columns:1fr; } }
    .filter-group { display:flex; flex-direction:column; gap:6px; }
    .filter-label { font-size:11px; font-weight:700; color:#1E3A5F; text-transform:uppercase;
                    letter-spacing:0.5px; display:flex; align-items:center; gap:6px; }
    .filter-label i { color:#246BCE; }
    .filter-input { width:100%; padding:11px 14px; background:#F8FAFC; border:1px solid #E2E8F0;
                    border-radius:10px; font-size:14px; color:#1E3A5F; transition:all 0.2s;
                    box-sizing:border-box; }
    .filter-input:focus { outline:none; border-color:#246BCE; background:#fff;
                          box-shadow:0 0 0 3px rgba(36,107,206,0.1); }

    /* Format section */
    .format-section-title { font-size:14px; font-weight:600; color:#374151; margin-bottom:14px;
                            display:flex; align-items:center; gap:8px; }
    .format-section-title i { color:#246BCE; }
    .format-card { border:2px solid #10B981; border-radius:12px; padding:20px 24px;
                   display:flex; flex-direction:column; align-items:center; gap:8px;
                   background:linear-gradient(135deg,#F0FDF4,#ECFDF5); max-width:220px;
                   cursor:pointer; transition:all 0.2s; }
    .format-card:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(16,185,129,0.2); }
    .format-card .format-icon { font-size:36px; color:#10B981; }
    .format-card .format-name { font-size:15px; font-weight:700; color:#065F46; }
    .format-card .format-desc { font-size:12px; color:#6B7280; text-align:center; }

    /* Download button */
    .btn-download { display:flex; align-items:center; justify-content:center; gap:10px;
                    padding:14px 40px; background:#10B981; color:#fff; border:none;
                    border-radius:10px; font-size:15px; font-weight:700; cursor:pointer;
                    transition:all 0.2s; margin-top:24px; width:100%; max-width:320px;
                    margin-left:auto; margin-right:auto; }
    .btn-download:hover { background:#059669; transform:translateY(-2px);
                          box-shadow:0 6px 16px rgba(16,185,129,0.35); }
    .btn-download:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

    /* Riwayat export */
    .riwayat-card { background:#fff; border-radius:16px; padding:24px 28px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.08); }
    .riwayat-title { font-size:16px; font-weight:700; color:#1E3A5F; margin-bottom:16px;
                     display:flex; align-items:center; gap:8px; }
    .riwayat-title i { color:#246BCE; }
    .riwayat-item { display:flex; align-items:center; gap:14px; padding:14px 0;
                    border-bottom:1px solid #F1F5F9; }
    .riwayat-item:last-child { border-bottom:none; }
    .riwayat-info { flex:1; min-width:0; }
    .riwayat-name { font-size:14px; font-weight:700; color:#1E3A5F; }
    .riwayat-meta { font-size:12px; color:#64748B; margin-top:3px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .riwayat-meta i { color:#246BCE; }
    .riwayat-periode { font-size:12px; color:#9CA3AF; margin-top:2px; }
    .riwayat-actions { display:flex; gap:8px; flex-shrink:0; }

    /* Buttons */
    .btn { padding:8px 16px; border:none; border-radius:8px; font-size:13px; font-weight:600;
           cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn:hover { transform:translateY(-1px); }
    .btn-primary { background:#246BCE; color:#fff; } .btn-primary:hover { background:#1D58A8; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-outline { background:transparent; border:2px solid #246BCE; color:#246BCE; }
    .btn-outline:hover { background:#246BCE; color:#fff; }
    .btn-sm { padding:6px 12px; font-size:12px; }

    /* Meta badges */
    .meta-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 8px;
                  border-radius:12px; font-size:11px; font-weight:600; }
    .meta-excel { background:#D1FAE5; color:#065F46; }
    .meta-size  { background:#DBEAFE; color:#1E40AF; }

    /* Toast */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600;
                  box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease;
                  display:flex; align-items:center; gap:10px; min-width:260px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }

    .empty-riwayat { text-align:center; padding:30px; color:#9CA3AF; font-size:14px; }
</style>
@endsection

@section('content')
<div id="toast"></div>

{{-- Header --}}
<div>
    <h1 class="page-title">Laporan &amp; Export Data</h1>
    <p class="page-subtitle">Kelola dan ekspor laporan posyandu dalam format Excel</p>
</div>

{{-- Main Card --}}
<div class="main-card">
    <div class="card-section-title">Filter Data Export</div>
    <div class="card-section-sub">Pilih rentang waktu dan kategori data</div>

    {{-- Tips --}}
    <div class="tips-box">
        <i class="fas fa-lightbulb"></i>
        <div>
            <strong>Tips Cerdas</strong><br>
            Pilih rentang tanggal dan kategori yang ingin di-export. Data akan diproses secara otomatis dan siap diunduh dalam format Excel.
        </div>
    </div>

    {{-- Filter --}}
    <div class="filter-grid">
        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-calendar-alt"></i> Tanggal Mulai</label>
            <input type="date" id="start_date" class="filter-input">
        </div>
        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-calendar-alt"></i> Tanggal Akhir</label>
            <input type="date" id="end_date" class="filter-input">
        </div>
        <div class="filter-group">
            <label class="filter-label"><i class="fas fa-database"></i> Kategori Data</label>
            <select id="category" class="filter-input">
                <option value="anak">Data Anak Saja</option>
                <option value="all">Semua Data Anak</option>
                <option value="imunisasi">Data Imunisasi</option>
                <option value="pertumbuhan">Data Pertumbuhan</option>
                <option value="stunting">Data Stunting</option>
            </select>
        </div>
    </div>

    {{-- Format Export --}}
    <div class="format-section-title">
        <i class="fas fa-download"></i> Pilih Format Export
    </div>
    <div class="format-card">
        <div class="format-icon"><i class="fas fa-file-excel"></i></div>
        <div class="format-name">Excel (.xlsx)</div>
        <div class="format-desc">Format kompatibel dengan Excel &amp; Google Sheets</div>
    </div>

    {{-- Download Button --}}
    <button class="btn-download" id="btnUnduh" onclick="exportExcel()">
        <i class="fas fa-cloud-download-alt"></i> UNDUH EXCEL
    </button>
</div>

{{-- Riwayat Export --}}
<div class="riwayat-card">
    <div class="riwayat-title">
        <i class="fas fa-history"></i> Riwayat Export
    </div>
    <div id="riwayatList">
        <div class="empty-riwayat"><i class="fas fa-inbox"></i><br>Belum ada riwayat export</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ── Riwayat export (simpan di localStorage) ────────────────
const RIWAYAT_KEY = 'poscare_export_history';

function getRiwayat() {
    try { return JSON.parse(localStorage.getItem(RIWAYAT_KEY) || '[]'); }
    catch { return []; }
}

function saveRiwayat(item) {
    const list = getRiwayat();
    list.unshift(item);
    // Simpan max 10 riwayat
    localStorage.setItem(RIWAYAT_KEY, JSON.stringify(list.slice(0, 10)));
    renderRiwayat();
}

function deleteRiwayat(idx) {
    const list = getRiwayat();
    list.splice(idx, 1);
    localStorage.setItem(RIWAYAT_KEY, JSON.stringify(list));
    renderRiwayat();
}

function renderRiwayat() {
    const list = getRiwayat();
    const el   = document.getElementById('riwayatList');

    if (!list.length) {
        el.innerHTML = '<div class="empty-riwayat"><i class="fas fa-inbox"></i><br>Belum ada riwayat export</div>';
        return;
    }

    const catLabel = {
        all:'Semua Data', anak:'Data Anak', imunisasi:'Data Imunisasi',
        pertumbuhan:'Data Pertumbuhan', stunting:'Data Stunting'
    };

    el.innerHTML = list.map((item, idx) => `
        <div class="riwayat-item">
            <div class="riwayat-info">
                <div class="riwayat-name">${catLabel[item.category]||item.category} — ${item.total} records</div>
                <div class="riwayat-meta">
                    <span><i class="fas fa-calendar-alt"></i> ${item.datetime}</span>
                    <span class="meta-badge meta-excel"><i class="fas fa-file-excel"></i> Excel</span>
                    <span class="meta-badge meta-size"><i class="fas fa-weight"></i> ~${item.size}</span>
                </div>
                <div class="riwayat-periode">Periode: ${item.start_date} s/d ${item.end_date}</div>
            </div>
            <div class="riwayat-actions">
                <button class="btn btn-outline btn-sm" onclick="reExport(${idx})">
                    <i class="fas fa-redo"></i> Export Ulang
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteRiwayat(${idx})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`).join('');
}

function reExport(idx) {
    const item = getRiwayat()[idx];
    if (!item) return;
    document.getElementById('start_date').value = item.start_date;
    document.getElementById('end_date').value   = item.end_date;
    document.getElementById('category').value   = item.category;
    exportExcel();
}

// ── Toast ──────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const icons = { success:'check-circle', error:'times-circle', warning:'exclamation-triangle' };
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// ── Export Excel ───────────────────────────────────────────
async function exportExcel() {
    const start_date = document.getElementById('start_date').value;
    const end_date   = document.getElementById('end_date').value;
    const category   = document.getElementById('category').value;

    if (!start_date || !end_date) {
        toast('Pilih tanggal mulai dan akhir terlebih dahulu', 'warning');
        return;
    }

    const btn = document.getElementById('btnUnduh');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengekspor...';

    try {
        const res = await fetch('{{ route("laporan.export") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json, text/csv, */*',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ start_date, end_date, category }),
        });

        const contentType = res.headers.get('content-type') || '';

        if (!res.ok || (contentType.includes('application/json') && !contentType.includes('spreadsheet'))) {
            const err = await res.json().catch(() => ({ message: 'Gagal export' }));
            toast(err.message || 'Gagal export', 'error');
            return;
        }

        const blob = await res.blob();
        const url  = window.URL.createObjectURL(blob);
        const a    = document.createElement('a');
        const catMap = { all:'Semua_Data', anak:'Data_Anak', imunisasi:'Data_Imunisasi', pertumbuhan:'Data_Pertumbuhan', stunting:'Data_Stunting' };
        a.href     = url;
        a.download = `PosCare_${catMap[category]||'Laporan'}_${start_date}_${end_date}.xlsx`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);

        // Estimasi ukuran file
        const sizeKb = Math.round(blob.size / 1024);
        const sizeStr = sizeKb > 1024 ? (sizeKb / 1024).toFixed(1) + ' MB' : sizeKb + ' KB';

        // Hitung total records dari preview (atau estimasi dari blob)
        const totalEl = document.getElementById('totalPreview');
        const total   = totalEl ? parseInt(totalEl.textContent) || '?' : '?';

        // Simpan ke riwayat
        const now = new Date();
        saveRiwayat({
            category,
            start_date,
            end_date,
            total,
            size: sizeStr,
            datetime: now.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' }) +
                      ' ' + now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }),
        });

        toast('Export Excel berhasil diunduh!', 'success');

    } catch (e) {
        toast('Terjadi kesalahan saat export', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-cloud-download-alt"></i> UNDUH EXCEL';
    }
}

// ── Init ───────────────────────────────────────────────────
(function() {
    // Default: bulan ini
    const now  = new Date();
    const y    = now.getFullYear();
    const m    = String(now.getMonth() + 1).padStart(2, '0');
    const last = new Date(y, now.getMonth() + 1, 0).getDate();
    document.getElementById('start_date').value = `${y}-${m}-01`;
    document.getElementById('end_date').value   = `${y}-${m}-${String(last).padStart(2,'0')}`;

    renderRiwayat();

    // Handle query param ?action=ekspor dari dashboard
    const params = new URLSearchParams(window.location.search);
    if (params.get('action') === 'ekspor') {
        setTimeout(() => {
            document.getElementById('btnUnduh')?.scrollIntoView({ behavior:'smooth', block:'center' });
        }, 400);
    }
})();
</script>
@endsection
