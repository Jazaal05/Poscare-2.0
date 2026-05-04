

<?php $__env->startSection('title', 'Imunisasi'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }

    .page-title    { font-size:26px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:20px; }

    /* Toolbar */
    .toolbar { display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:16px;
               background:#fff; border-radius:12px; padding:14px 18px;
               box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .search-wrapper { position:relative; min-width:220px; }
    .search-input { width:100%; padding:9px 14px 9px 38px; border:1px solid #E2E8F0;
                    border-radius:8px; font-size:14px; transition:all 0.2s; }
    .search-input:focus { outline:none; border-color:#246BCE; box-shadow:0 0 0 3px rgba(36,107,206,0.1); }
    .search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9CA3AF; font-size:13px; }
    .legend { display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:#374151; }
    .legend-label { color:#64748B; font-weight:500; margin-right:4px; }
    .badge-done    { background:#10B981; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:5px; }
    .badge-pending { background:#E5E7EB; color:#6B7280; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; gap:5px; border:1px solid #D1D5DB; }
    .toolbar-spacer { flex:1; }

    /* Buttons */
    .btn { padding:8px 16px; border:none; border-radius:8px; font-size:13px; font-weight:600;
           cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn:hover { transform:translateY(-1px); }
    .btn-primary { background:#246BCE; color:#fff; } .btn-primary:hover { background:#1D58A8; }
    .btn-warning { background:#F59E0B; color:#fff; } .btn-warning:hover { background:#D97706; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-success { background:#10B981; color:#fff; } .btn-success:hover { background:#059669; }
    .btn-outline { background:transparent; border:2px solid #246BCE; color:#246BCE; }
    .btn-outline:hover { background:#246BCE; color:#fff; }
    .btn-sm { padding:5px 10px; font-size:12px; }

    /* Table wrapper */
    .table-outer { background:#fff; border-radius:14px; box-shadow:0 4px 16px rgba(0,0,0,0.07);
                   overflow:hidden; }
    .table-scroll { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:13px; }

    /* Header */
    thead th { background:#246BCE; color:#fff; font-size:11px; font-weight:700;
               text-transform:uppercase; letter-spacing:0.05em; padding:13px 10px;
               text-align:center; white-space:nowrap; }
    thead th:first-child { text-align:left; padding-left:18px; min-width:160px; }
    .th-vaksin { position:relative; min-width:130px; }
    .th-del-btn { background:none; border:none; color:rgba(255,255,255,0.7); cursor:pointer;
                  font-size:12px; padding:2px 4px; border-radius:4px; transition:all 0.2s;
                  vertical-align:middle; margin-left:4px; }
    .th-del-btn:hover { color:#fff; background:rgba(239,68,68,0.5); }

    /* Body */
    tbody td { padding:10px 10px; border-bottom:1px solid #E7ECF3; text-align:center;
               vertical-align:middle; }
    tbody td:first-child { text-align:left; padding-left:18px; }
    tbody tr:nth-child(odd)  { background:#FCFDFE; }
    tbody tr:nth-child(even) { background:#fff; }
    tbody tr:hover { background:#EEF6FF; }

    .child-name { font-weight:700; color:#1E3A5F; font-size:14px; }
    .child-meta { font-size:11px; color:#9CA3AF; margin-top:1px; }

    /* Vaccine cell */
    .vax-cell { display:flex; flex-direction:column; align-items:center; gap:5px; }
    .vax-status { display:inline-flex; align-items:center; gap:5px; padding:4px 10px;
                  border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; }
    .vax-done    { background:#D1FAE5; color:#065F46; }
    .vax-pending { background:#F3F4F6; color:#9CA3AF; border:1px solid #E5E7EB; }
    .vax-pending:hover { background:#DBEAFE; color:#1E40AF; border-color:#93C5FD; cursor:pointer; }
    .vax-date    { font-size:11px; color:#64748B; }
    .vax-actions { display:flex; gap:4px; }

    /* Pagination */
    .pagination { display:flex; gap:5px; justify-content:center; align-items:center;
                  padding:14px; border-top:1px solid #E7ECF3; }
    .page-btn { padding:5px 11px; border:1px solid #E5E7EB; background:#fff; border-radius:6px;
                cursor:pointer; font-size:13px; transition:all 0.2s; }
    .page-btn:hover { background:#F3F4F6; border-color:#246BCE; }
    .page-btn.active { background:#246BCE; color:#fff; border-color:#246BCE; }
    .page-btn:disabled { opacity:0.4; cursor:not-allowed; }

    /* Modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
                     backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:16px; padding:28px; width:90%; max-width:460px;
                 box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; justify-content:space-between; align-items:center;
                    margin-bottom:20px; padding-bottom:14px; border-bottom:2px solid #E5E7EB; }
    .modal-title { font-size:17px; font-weight:700; color:#1E3A5F; display:flex; align-items:center; gap:8px; }
    .modal-close { background:none; border:none; font-size:22px; color:#9CA3AF; cursor:pointer;
                   width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; }
    .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select {
        padding:10px 14px; border:1px solid #E2E8F0; border-radius:8px;
        font-size:14px; transition:all 0.2s; width:100%; box-sizing:border-box; }
    .form-group input:focus { outline:none; border-color:#246BCE; box-shadow:0 0 0 3px rgba(36,107,206,0.1); }
    .form-group input[readonly] { background:#F8FAFC; color:#64748B; }
    .date-warn { font-size:12px; color:#F59E0B; display:none; margin-top:4px; }

    /* Toast */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600;
                  box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease;
                  display:flex; align-items:center; gap:10px; min-width:260px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }

    /* Tooltip */
    [data-tooltip] { position:relative; }
    [data-tooltip]:hover::after { content:attr(data-tooltip); position:absolute; top:calc(100% + 6px);
        left:50%; transform:translateX(-50%); background:rgba(30,58,95,0.92); color:#fff;
        padding:5px 10px; border-radius:6px; font-size:11px; white-space:nowrap; z-index:100; pointer-events:none; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="toast"></div>


<div>
    <h1 class="page-title"><i class="fas fa-syringe" style="color:#246BCE;"></i> Data Imunisasi Anak</h1>
    <p class="page-subtitle">Kelola dan pantau status imunisasi anak-anak di posyandu</p>
</div>


<div class="toolbar">
    <div class="search-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Cari nama anak..." oninput="debounceSearch()">
    </div>
    <span class="legend-label">Keterangan:</span>
    <span class="badge-done"><i class="fas fa-check-circle"></i> Sudah Vaksin</span>
    <span class="badge-pending"><i class="fas fa-circle"></i> Belum Vaksin</span>
    <div class="toolbar-spacer"></div>
    <?php if(Auth::user()->role === 'admin'): ?>
    <button class="btn btn-primary" onclick="openModalTambahVaksin()">
        <i class="fas fa-plus"></i> Tambah Vaksin
    </button>
    <?php endif; ?>
</div>


<div class="table-outer">
    <div class="table-scroll">
        <table>
            <thead id="imunisasiHead">
                <tr><th colspan="2" style="text-align:center;">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </th></tr>
            </thead>
            <tbody id="imunisasiBody"></tbody>
        </table>
    </div>
    <div class="pagination" id="pagination" style="display:none;"></div>
</div>


<div class="modal-overlay" id="modalTandai">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-syringe" style="color:#246BCE;"></i> Tandai Vaksin Diberikan</h3>
            <button class="modal-close" onclick="closeModal('modalTandai')">&times;</button>
        </div>
        <div class="form-group">
            <label>Nama Anak</label>
            <input type="text" id="tandaiNamaAnak" readonly>
        </div>
        <div class="form-group">
            <label>Jenis Vaksin</label>
            <input type="text" id="tandaiJenisVaksin" readonly>
        </div>
        <div class="form-group">
            <label>Tanggal Pemberian Vaksin</label>
            <input type="date" id="tanggalImunisasi" max="<?php echo e(date('Y-m-d')); ?>" value="<?php echo e(date('Y-m-d')); ?>"
                   oninput="checkTanggal()">
            <span class="date-warn" id="dateWarn">
                <i class="fas fa-exclamation-triangle"></i> Tanggal tidak boleh lebih dari hari ini
            </span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
            <button class="btn btn-outline" onclick="closeModal('modalTandai')">
                <i class="fas fa-times"></i> Batal
            </button>
            <button class="btn btn-success" id="btnKonfirmasiTandai">
                <i class="fas fa-check"></i> Simpan
            </button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="modalHapusImun">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Data Imunisasi</h3>
            <button class="modal-close" onclick="closeModal('modalHapusImun')">&times;</button>
        </div>
        <p id="hapusImunDesc" style="color:#374151;margin-bottom:20px;font-size:14px;"></p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeModal('modalHapusImun')">
                <i class="fas fa-times"></i> Batal
            </button>
            <button class="btn btn-danger" id="btnKonfirmasiHapusImun">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>


<?php if(Auth::user()->role === 'admin'): ?>
<div class="modal-overlay" id="modalTambahVaksin">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-plus-circle" style="color:#246BCE;"></i> Tambah Jenis Vaksin Baru</h3>
            <button class="modal-close" onclick="closeModal('modalTambahVaksin')">&times;</button>
        </div>
        <div class="form-group">
            <label>Nama Vaksin <span style="color:red">*</span></label>
            <input type="text" id="namaVaksinBaru" placeholder="Contoh: Hepatitis B, PCV, Rotavirus..."
                   oninput="this.value = this.value.toUpperCase()">
            <small style="color:#64748B;font-size:12px;">Nama akan ditampilkan di header tabel (minimal 2 huruf)</small>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
            <button class="btn btn-outline" onclick="closeModal('modalTambahVaksin')">
                <i class="fas fa-times"></i> Batal
            </button>
            <button class="btn btn-primary" id="btnSimpanVaksin" onclick="simpanVaksinBaru()">
                <i class="fas fa-check"></i> Simpan Vaksin
            </button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="modalHapusVaksin">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Vaksin</h3>
            <button class="modal-close" onclick="closeModal('modalHapusVaksin')">&times;</button>
        </div>
        <p id="hapusVaksinDesc" style="color:#374151;margin-bottom:8px;font-size:14px;"></p>
        <p id="hapusVaksinWarn" style="font-size:13px;color:#92400E;background:#FEF3C7;padding:10px 14px;border-radius:8px;margin-bottom:20px;"></p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-outline" onclick="closeModal('modalHapusVaksin')">Batal</button>
            <button class="btn btn-danger" id="btnKonfirmasiHapusVaksin">
                <i class="fas fa-trash"></i> Ya, Hapus
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
let allData = [], vaccines = [], currentPage = 1, perPage = 10, searchTimer = null;
let pendingTandai = null, pendingHapusImun = null, pendingHapusVaksin = null;

// ── Helpers ────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const icons = { success:'check-circle', error:'times-circle', warning:'exclamation-triangle' };
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

function checkTanggal() {
    const val  = document.getElementById('tanggalImunisasi').value;
    const warn = document.getElementById('dateWarn');
    const today = new Date().toISOString().split('T')[0];
    warn.style.display = (val > today) ? 'block' : 'none';
}

// ── Load data ──────────────────────────────────────────────
async function loadImunisasi() {
    const search = document.getElementById('searchInput').value.trim();
    document.getElementById('imunisasiHead').innerHTML =
        '<tr><th colspan="2" style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</th></tr>';
    document.getElementById('imunisasiBody').innerHTML = '';
    document.getElementById('pagination').style.display = 'none';

    try {
        const url = `<?php echo e(route('imunisasi.list')); ?>${search ? '?q=' + encodeURIComponent(search) : ''}`;
        const res  = await fetch(url, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (!data.success) { toast(data.message || 'Gagal memuat', 'error'); return; }

        allData  = data.data;
        vaccines = data.vaccines;
        currentPage = 1;
        renderHeader();
        renderTable();
    } catch (e) {
        document.getElementById('imunisasiHead').innerHTML =
            '<tr><th colspan="2" style="text-align:center;padding:20px;color:#EF4444;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data</th></tr>';
    }
}

// ── Render header ──────────────────────────────────────────
function renderHeader() {
    const isAdmin = <?php echo e(Auth::user()->role === 'admin' ? 'true' : 'false'); ?>;
    const vaxCols = vaccines.map(v => `
        <th class="th-vaksin">
            ${v.label}
            ${isAdmin ? `<button class="th-del-btn" onclick="confirmHapusVaksin(${v.id},'${v.label}')"
                data-tooltip="Hapus vaksin ${v.label} (dengan peringatan)">
                <i class="fas fa-trash-alt"></i>
            </button>` : ''}
        </th>`).join('');

    document.getElementById('imunisasiHead').innerHTML = `
        <tr>
            <th style="text-align:left;padding-left:18px;">NAMA ANAK</th>
            ${vaxCols}
        </tr>`;
}

// ── Render table body ──────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById('imunisasiBody');
    const start = (currentPage - 1) * perPage;
    const page  = allData.slice(start, start + perPage);

    if (!page.length) {
        tbody.innerHTML = `<tr><td colspan="${vaccines.length + 1}" style="text-align:center;padding:40px;color:#9CA3AF;">
            <i class="fas fa-inbox"></i><br>Tidak ada data
        </td></tr>`;
        document.getElementById('pagination').style.display = 'none';
        return;
    }

    tbody.innerHTML = page.map(child => {
        const vaxCells = vaccines.map(v => {
            const dateKey = v.key + '_date';
            const done    = !!child[dateKey];
            const dateStr = child[dateKey] ? formatDate(child[dateKey]) : '';

            if (done) {
                return `<td>
                    <div class="vax-cell">
                        <span class="vax-status vax-done"><i class="fas fa-check-circle"></i> Sudah Vaksin</span>
                        <span class="vax-date">${dateStr}</span>
                        <div class="vax-actions">
                            <button class="btn btn-warning btn-sm"
                                onclick="openEdit(${child.id},${v.id},'${v.label}','${child.nama_anak.replace(/'/g,"\\'")}','${child[dateKey]}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm"
                                onclick="confirmHapusImun(${child.id},${v.id},'${v.label}','${child.nama_anak.replace(/'/g,"\\'")}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </td>`;
            } else {
                return `<td>
                    <div class="vax-cell">
                        <span class="vax-status vax-pending" style="cursor:pointer;"
                            onclick="openTandai(${child.id},${v.id},'${v.label}','${child.nama_anak.replace(/'/g,"\\'")}')">
                            <i class="fas fa-circle"></i> Belum Vaksin
                        </span>
                        <div class="vax-actions" style="margin-top:4px;">
                            <button class="btn btn-warning btn-sm"
                                onclick="openTandai(${child.id},${v.id},'${v.label}','${child.nama_anak.replace(/'/g,"\\'")}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" disabled style="opacity:0.4;cursor:not-allowed;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </td>`;
            }
        }).join('');

        const gender = child.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        return `<tr>
            <td>
                <div class="child-name">${child.nama_anak}</div>
                <div class="child-meta">${gender} • ${child.umur_bulan} bulan</div>
            </td>
            ${vaxCells}
        </tr>`;
    }).join('');

    renderPagination();
}

function formatDate(d) {
    if (!d) return '';
    const dt = new Date(d);
    return dt.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}

// ── Pagination ─────────────────────────────────────────────
function renderPagination() {
    const total = Math.ceil(allData.length / perPage);
    const pg    = document.getElementById('pagination');
    if (total <= 1) { pg.style.display = 'none'; return; }
    pg.style.display = 'flex';

    let html = `<button class="page-btn" onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>`;
    for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || Math.abs(i - currentPage) <= 2) {
            html += `<button class="page-btn ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
        } else if (Math.abs(i - currentPage) === 3) {
            html += `<span style="padding:4px 6px;">...</span>`;
        }
    }
    html += `<button class="page-btn" onclick="goPage(${currentPage+1})" ${currentPage===total?'disabled':''}>›</button>`;
    pg.innerHTML = html;
}

function goPage(p) {
    const total = Math.ceil(allData.length / perPage);
    if (p < 1 || p > total) return;
    currentPage = p;
    renderTable();
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadImunisasi, 400);
}

// ── Tandai / Edit imunisasi ────────────────────────────────
function openTandai(anakId, vaksinId, vaksinLabel, anakNama) {
    pendingTandai = { anakId, vaksinId };
    document.getElementById('tandaiNamaAnak').value    = anakNama;
    document.getElementById('tandaiJenisVaksin').value = vaksinLabel;
    document.getElementById('tanggalImunisasi').value  = new Date().toISOString().split('T')[0];
    document.getElementById('dateWarn').style.display  = 'none';
    openModal('modalTandai');
}

function openEdit(anakId, vaksinId, vaksinLabel, anakNama, tanggal) {
    pendingTandai = { anakId, vaksinId };
    document.getElementById('tandaiNamaAnak').value    = anakNama;
    document.getElementById('tandaiJenisVaksin').value = vaksinLabel;
    document.getElementById('tanggalImunisasi').value  = tanggal;
    document.getElementById('dateWarn').style.display  = 'none';
    openModal('modalTandai');
}

document.getElementById('btnKonfirmasiTandai').addEventListener('click', async () => {
    if (!pendingTandai) return;
    const tanggal = document.getElementById('tanggalImunisasi').value;
    const today   = new Date().toISOString().split('T')[0];
    if (tanggal > today) { toast('Tanggal tidak boleh lebih dari hari ini', 'warning'); return; }

    const btn = document.getElementById('btnKonfirmasiTandai');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    try {
        const res  = await fetch('<?php echo e(route("imunisasi.tandai")); ?>', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ anak_id: pendingTandai.anakId, master_vaksin_id: pendingTandai.vaksinId, tanggal }),
        });
        const data = await res.json();
        if (data.success) { toast(data.message, 'success'); closeModal('modalTandai'); loadImunisasi(); }
        else toast(data.message || 'Gagal menyimpan', 'error');
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Simpan'; pendingTandai = null; }
});

// ── Hapus imunisasi (Delete) ───────────────────────────────
function confirmHapusImun(anakId, vaksinId, vaksinLabel, anakNama) {
    pendingHapusImun = { anakId, vaksinId };
    document.getElementById('hapusImunDesc').innerHTML =
        `Hapus data imunisasi <strong>${vaksinLabel}</strong> untuk anak <strong>${anakNama}</strong>?`;
    openModal('modalHapusImun');
}

document.getElementById('btnKonfirmasiHapusImun').addEventListener('click', async () => {
    if (!pendingHapusImun) return;
    const btn = document.getElementById('btnKonfirmasiHapusImun');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    try {
        const res  = await fetch('<?php echo e(route("imunisasi.undo")); ?>', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ anak_id: pendingHapusImun.anakId, master_vaksin_id: pendingHapusImun.vaksinId }),
        });
        const data = await res.json();
        if (data.success) { toast(data.message, 'success'); closeModal('modalHapusImun'); loadImunisasi(); }
        else toast(data.message || 'Gagal menghapus', 'error');
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash"></i> Hapus'; pendingHapusImun = null; }
});

<?php if(Auth::user()->role === 'admin'): ?>
// ── Tambah vaksin baru ─────────────────────────────────────
function openModalTambahVaksin() {
    document.getElementById('namaVaksinBaru').value = '';
    openModal('modalTambahVaksin');
    setTimeout(() => document.getElementById('namaVaksinBaru').focus(), 200);
}

async function simpanVaksinBaru() {
    const nama = document.getElementById('namaVaksinBaru').value.trim().toUpperCase();
    if (nama.length < 2) { toast('Nama vaksin minimal 2 huruf', 'warning'); return; }

    const btn = document.getElementById('btnSimpanVaksin');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    try {
        const res  = await fetch('<?php echo e(route("vaksin.store")); ?>', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ nama_vaksin: nama, usia_standar_bulan: 0, keterangan: 'Vaksin tambahan' }),
        });
        const data = await res.json();
        if (data.success) {
            toast(data.message, 'success');
            closeModal('modalTambahVaksin');
            loadImunisasi();
        } else toast(data.message || 'Gagal menyimpan', 'error');
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Simpan Vaksin'; }
}

// ── Hapus kolom vaksin ─────────────────────────────────────
function confirmHapusVaksin(vaksinId, vaksinLabel) {
    pendingHapusVaksin = vaksinId;
    document.getElementById('hapusVaksinDesc').innerHTML =
        `Hapus vaksin default <strong>${vaksinLabel}</strong> (dengan peringatan)?`;
    document.getElementById('hapusVaksinWarn').innerHTML =
        `<i class="fas fa-exclamation-triangle"></i> Semua data imunisasi ${vaksinLabel} untuk semua anak akan ikut terhapus!`;
    openModal('modalHapusVaksin');
}

document.getElementById('btnKonfirmasiHapusVaksin').addEventListener('click', async () => {
    if (!pendingHapusVaksin) return;
    const btn = document.getElementById('btnKonfirmasiHapusVaksin');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    try {
        const res  = await fetch(`<?php echo e(url('/api/vaksin')); ?>/${pendingHapusVaksin}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (data.success) { toast(data.message, 'success'); closeModal('modalHapusVaksin'); loadImunisasi(); }
        else toast(data.message || 'Gagal menghapus', 'error');
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus'; pendingHapusVaksin = null; }
});
<?php endif; ?>

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});

document.addEventListener('DOMContentLoaded', loadImunisasi);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\asus\VSCode\poscare-laravel\resources\views/imunisasi/index.blade.php ENDPATH**/ ?>