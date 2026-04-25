@extends('layouts.lansia')
@section('title', 'Pengobatan Lansia')

@section('styles')
<style>
body { background:linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
@keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.page-title { font-size:28px; font-weight:700; background:linear-gradient(135deg,#065F46,#10B981); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:4px; }
.page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }
.toolbar { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.search-box { flex:1; min-width:200px; position:relative; }
.search-box input { width:100%; padding:10px 14px 10px 38px; border:1px solid #E2E8F0; border-radius:10px; font-size:14px; background:#fff; box-sizing:border-box; }
.search-box i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94A3B8; }
.btn { padding:10px 18px; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:7px; }
.btn:hover { transform:translateY(-1px); }
.btn-green  { background:#10B981; color:#fff; } .btn-green:hover  { background:#059669; }
.btn-yellow { background:#F59E0B; color:#fff; } .btn-yellow:hover { background:#D97706; }
.btn-red    { background:#EF4444; color:#fff; } .btn-red:hover    { background:#DC2626; }
.btn-sm { padding:6px 12px; font-size:12px; }
.table-card { background:#fff; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden; }
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; font-size:13px; }
thead tr { background:linear-gradient(135deg,#065F46,#10B981); }
thead th { padding:13px 14px; color:#fff; font-weight:600; text-align:left; white-space:nowrap; }
tbody tr { border-bottom:1px solid #F1F5F9; transition:background 0.15s; }
tbody tr:hover { background:#F0FDF4; }
tbody td { padding:11px 14px; color:#374151; vertical-align:middle; }
.badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-green { background:#D1FAE5; color:#065F46; } .badge-red { background:#FEE2E2; color:#991B1B; }
.pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid #F1F5F9; flex-wrap:wrap; gap:10px; }
.pagination-info { font-size:13px; color:#64748B; }
.pagination-btns { display:flex; gap:6px; }
.page-btn { padding:6px 12px; border:1px solid #E2E8F0; border-radius:8px; background:#fff; font-size:13px; cursor:pointer; transition:all 0.2s; }
.page-btn:hover,.page-btn.active { background:#10B981; color:#fff; border-color:#10B981; }
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff; border-radius:16px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:fadeInUp 0.3s ease; }
.modal-box.modal-sm { max-width:440px; }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid #F1F5F9; }
.modal-title { font-size:18px; font-weight:700; color:#065F46; }
.modal-close { background:none; border:none; font-size:20px; color:#94A3B8; cursor:pointer; padding:4px; }
.modal-close:hover { color:#EF4444; }
.modal-body { padding:24px; }
.modal-footer { padding:16px 24px; border-top:1px solid #F1F5F9; display:flex; justify-content:flex-end; gap:10px; }
.form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.form-label { font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:0.4px; }
.form-label .req { color:#EF4444; }
.form-input { padding:10px 13px; border:1px solid #E2E8F0; border-radius:9px; font-size:14px; color:#1E3A5F; transition:all 0.2s; width:100%; box-sizing:border-box; }
.form-input:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.12); }
.toggle-group { display:flex; gap:10px; }
.toggle-btn { flex:1; padding:10px; border:2px solid #E2E8F0; border-radius:9px; background:#fff; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; text-align:center; }
.toggle-btn.active-ya  { border-color:#EF4444; background:#FEF2F2; color:#991B1B; }
.toggle-btn.active-tidak { border-color:#10B981; background:#F0FDF4; color:#065F46; }
.checklist-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px; }
.check-item { display:flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid #E2E8F0; border-radius:8px; cursor:pointer; transition:all 0.2s; font-size:13px; }
.check-item:hover { border-color:#10B981; background:#F0FDF4; }
.check-item input[type=checkbox] { accent-color:#10B981; width:16px; height:16px; }
.check-item.checked { border-color:#10B981; background:#F0FDF4; }
.lansia-autocomplete { position:relative; }
.autocomplete-list { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #E2E8F0; border-radius:9px; box-shadow:0 8px 24px rgba(0,0,0,0.1); z-index:100; max-height:200px; overflow-y:auto; display:none; }
.autocomplete-item { padding:10px 14px; cursor:pointer; font-size:14px; border-bottom:1px solid #F1F5F9; }
.autocomplete-item:hover { background:#F0FDF4; color:#065F46; }
.empty-state { text-align:center; padding:40px; color:#94A3B8; }
.empty-state i { font-size:40px; margin-bottom:12px; display:block; }
#toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
.toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:260px; }
@keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
.toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }
.tags { display:flex; flex-wrap:wrap; gap:4px; }
.tag { background:#D1FAE5; color:#065F46; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; }
.tag-red { background:#FEE2E2; color:#991B1B; }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div>
    <h1 class="page-title">Pengobatan Lansia</h1>
    <p class="page-subtitle">Catat pengobatan dan pemberian obat/vitamin</p>
</div>

<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nama lansia..." oninput="debounceSearch()">
    </div>
    <button class="btn btn-green" onclick="openModalTambah()">
        <i class="fas fa-plus"></i> Catat Pengobatan
    </button>
</div>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lansia</th>
                    <th>Tanggal</th>
                    <th>Ada Keluhan</th>
                    <th>Obat Diberikan</th>
                    <th>Vitamin Diberikan</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr><td colspan="8" class="empty-state"><i class="fas fa-spinner fa-spin"></i><br>Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <div class="pagination-info" id="paginationInfo">Menampilkan 0 data</div>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

{{-- Modal Catat Pengobatan --}}
<div class="modal-overlay" id="modalForm">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="modalFormTitle">Catat Pengobatan</span>
            <button class="modal-close" onclick="closeModal('modalForm')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Pilih Lansia <span class="req">*</span></label>
                <div class="lansia-autocomplete">
                    <input type="text" id="lansiaSearch" class="form-input" placeholder="Ketik nama lansia..." autocomplete="off" oninput="searchLansia(this.value)">
                    <div class="autocomplete-list" id="autocompleteList"></div>
                </div>
                <input type="hidden" id="lansia_id">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal <span class="req">*</span></label>
                <input type="date" id="tanggal" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Ada Keluhan?</label>
                <div class="toggle-group">
                    <button type="button" class="toggle-btn" id="btnYa" onclick="setKeluhan(true)">Ya, Ada Keluhan</button>
                    <button type="button" class="toggle-btn active-tidak" id="btnTidak" onclick="setKeluhan(false)">Tidak Ada Keluhan</button>
                </div>
            </div>
            <div id="sectionKeluhan" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Keluhan</label>
                    <textarea id="keluhan" class="form-input" rows="2" placeholder="Deskripsi keluhan..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Obat Diberikan</label>
                    <div class="checklist-grid" id="checklistObat"></div>
                </div>
            </div>
            <div id="sectionVitamin">
                <div class="form-group">
                    <label class="form-label">Vitamin Diberikan</label>
                    <div class="checklist-grid" id="checklistVitamin"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Tambahan</label>
                <textarea id="catatan" class="form-input" rows="2" placeholder="Catatan tambahan..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-red" onclick="closeModal('modalForm')">Batal</button>
            <button class="btn btn-green" onclick="submitForm()" id="btnSimpan">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <span class="modal-title">Konfirmasi Hapus</span>
            <button class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
        </div>
        <div class="modal-body" style="text-align:center;">
            <i class="fas fa-exclamation-triangle" style="font-size:48px;color:#F59E0B;margin-bottom:16px;display:block;"></i>
            <p style="font-size:15px;color:#374151;">Hapus data pengobatan ini?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-yellow" onclick="closeModal('modalHapus')">Batal</button>
            <button class="btn btn-red" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Hapus</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const LIST_URL  = '{{ route("lansia.pengobatan.list") }}';
const STORE_URL = '{{ route("lansia.pengobatan.store") }}';
const API_BASE  = '/lansia/api/pengobatan';
const LANSIA_URL= '{{ route("lansia.data.list") }}';

const DAFTAR_OBAT = ['Paracetamol','Amlodipin','Metformin','Captopril','Simvastatin','Antasida','Vitamin B Complex','Asam Mefenamat','Glibenklamid','Furosemid'];
const DAFTAR_VITAMIN = ['Vitamin C','Vitamin D','Vitamin B12','Kalsium','Asam Folat','Zinc','Vitamin E','Omega-3'];

let allData = [], allLansia = [], currentPage = 1, perPage = 10;
let searchTimer = null, adaKeluhan = false;

// ── Toast ──────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const icons = { success:'check-circle', error:'times-circle', warning:'exclamation-triangle' };
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// ── Load Data ──────────────────────────────────────────────
async function loadData(q = '') {
    try {
        const url = LIST_URL + (q ? `?q=${encodeURIComponent(q)}` : '');
        const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Gagal memuat data');
        allData = json.data;
        currentPage = 1;
        renderTable();
    } catch (e) {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="8" class="empty-state"><i class="fas fa-exclamation-circle"></i><br>${e.message}</td></tr>`;
    }
}

async function loadLansia() {
    try {
        const res  = await fetch(LANSIA_URL, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        const json = await res.json();
        if (json.success) allLansia = json.data;
    } catch {}
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadData(document.getElementById('searchInput').value.trim()), 400);
}

// ── Render Table ───────────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById('tableBody');
    const start = (currentPage - 1) * perPage;
    const page  = allData.slice(start, start + perPage);

    if (!allData.length) {
        tbody.innerHTML = `<tr><td colspan="8" class="empty-state"><i class="fas fa-pills"></i><br>Belum ada data pengobatan</td></tr>`;
        updatePagination();
        return;
    }

    tbody.innerHTML = page.map((d, i) => {
        const obat    = (d.obat_diberikan || []).map(o => `<span class="tag tag-red">${o}</span>`).join('') || '-';
        const vitamin = (d.vitamin_diberikan || []).map(v => `<span class="tag">${v}</span>`).join('') || '-';
        return `<tr>
            <td>${start + i + 1}</td>
            <td><strong>${d.nama_lansia || '-'}</strong></td>
            <td>${d.tanggal ? formatDate(d.tanggal) : '-'}</td>
            <td><span class="badge ${d.ada_keluhan ? 'badge-red' : 'badge-green'}">${d.ada_keluhan ? 'Ya' : 'Tidak'}</span></td>
            <td><div class="tags">${obat}</div></td>
            <td><div class="tags">${vitamin}</div></td>
            <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${d.catatan||''}">${d.catatan || '-'}</td>
            <td style="white-space:nowrap;">
                <button class="btn btn-yellow btn-sm" onclick="openEdit(${d.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-red btn-sm" onclick="openHapus(${d.id})"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
    }).join('');

    updatePagination();
}

function updatePagination() {
    const total = allData.length;
    const pages = Math.ceil(total / perPage) || 1;
    const start = total ? (currentPage - 1) * perPage + 1 : 0;
    const end   = Math.min(currentPage * perPage, total);
    document.getElementById('paginationInfo').textContent = `Menampilkan ${start}–${end} dari ${total} data`;
    const btns = document.getElementById('paginationBtns');
    let html = `<button class="page-btn" onclick="changePage(${currentPage - 1})" ${currentPage <= 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>`;
    for (let p = Math.max(1, currentPage - 2); p <= Math.min(pages, currentPage + 2); p++) {
        html += `<button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="changePage(${p})">${p}</button>`;
    }
    html += `<button class="page-btn" onclick="changePage(${currentPage + 1})" ${currentPage >= pages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>`;
    btns.innerHTML = html;
}

function changePage(p) {
    const pages = Math.ceil(allData.length / perPage) || 1;
    if (p < 1 || p > pages) return;
    currentPage = p;
    renderTable();
}

// ── Modal helpers ──────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

// ── Checklist render ───────────────────────────────────────
function renderChecklist(containerId, items, selected = []) {
    document.getElementById(containerId).innerHTML = items.map(item => `
        <label class="check-item ${selected.includes(item) ? 'checked' : ''}">
            <input type="checkbox" value="${item}" ${selected.includes(item) ? 'checked' : ''}
                onchange="this.closest('.check-item').classList.toggle('checked', this.checked)">
            ${item}
        </label>`).join('');
}

function getChecked(containerId) {
    return [...document.querySelectorAll(`#${containerId} input[type=checkbox]:checked`)].map(el => el.value);
}

// ── Toggle keluhan ─────────────────────────────────────────
function setKeluhan(val) {
    adaKeluhan = val;
    document.getElementById('btnYa').className    = 'toggle-btn' + (val ? ' active-ya' : '');
    document.getElementById('btnTidak').className = 'toggle-btn' + (!val ? ' active-tidak' : '');
    document.getElementById('sectionKeluhan').style.display = val ? '' : 'none';
    document.getElementById('sectionVitamin').style.display = val ? 'none' : '';
}

// ── Autocomplete lansia ────────────────────────────────────
function searchLansia(q) {
    const list = document.getElementById('autocompleteList');
    if (!q.trim()) { list.style.display = 'none'; return; }
    const filtered = allLansia.filter(l => l.nama_lengkap.toLowerCase().includes(q.toLowerCase())).slice(0, 8);
    if (!filtered.length) { list.style.display = 'none'; return; }
    list.innerHTML = filtered.map(l => `<div class="autocomplete-item" onclick="selectLansia(${l.id}, '${l.nama_lengkap.replace(/'/g,"\\'")}')">
        ${l.nama_lengkap} <small style="color:#94A3B8;">${l.nik || ''}</small>
    </div>`).join('');
    list.style.display = 'block';
}

function selectLansia(id, nama) {
    document.getElementById('lansia_id').value = id;
    document.getElementById('lansiaSearch').value = nama;
    document.getElementById('autocompleteList').style.display = 'none';
}

document.addEventListener('click', e => {
    if (!e.target.closest('.lansia-autocomplete')) {
        document.getElementById('autocompleteList').style.display = 'none';
    }
});

// ── Tambah ─────────────────────────────────────────────────
function openModalTambah() {
    document.getElementById('editId') && (document.getElementById('editId').value = '');
    document.getElementById('modalFormTitle').textContent = 'Catat Pengobatan';
    document.getElementById('lansiaSearch').value = '';
    document.getElementById('lansia_id').value = '';
    document.getElementById('tanggal').value = today();
    document.getElementById('keluhan').value = '';
    document.getElementById('catatan').value = '';
    setKeluhan(false);
    renderChecklist('checklistObat', DAFTAR_OBAT);
    renderChecklist('checklistVitamin', DAFTAR_VITAMIN);
    openModal('modalForm');
}

// ── Edit ───────────────────────────────────────────────────
function openEdit(id) {
    const d = allData.find(x => x.id === id);
    if (!d) return;
    if (!document.getElementById('editId')) {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.id = 'editId';
        document.getElementById('modalForm').appendChild(inp);
    }
    document.getElementById('editId').value = id;
    document.getElementById('modalFormTitle').textContent = 'Edit Pengobatan';
    document.getElementById('lansiaSearch').value = d.nama_lansia || '';
    document.getElementById('lansia_id').value = d.lansia_id || '';
    document.getElementById('tanggal').value = d.tanggal || '';
    document.getElementById('catatan').value = d.catatan || '';
    setKeluhan(!!d.ada_keluhan);
    document.getElementById('keluhan').value = Array.isArray(d.keluhan) ? d.keluhan.join(', ') : (d.keluhan || '');
    renderChecklist('checklistObat', DAFTAR_OBAT, d.obat_diberikan || []);
    renderChecklist('checklistVitamin', DAFTAR_VITAMIN, d.vitamin_diberikan || []);
    openModal('modalForm');
}

// ── Submit ─────────────────────────────────────────────────
async function submitForm() {
    const editId = document.getElementById('editId')?.value;
    const lansiaId = document.getElementById('lansia_id').value;
    if (!lansiaId && !editId) { toast('Pilih lansia terlebih dahulu', 'warning'); return; }

    const body = {
        lansia_id:         parseInt(lansiaId) || undefined,
        tanggal:           document.getElementById('tanggal').value,
        ada_keluhan:       adaKeluhan,
        keluhan:           adaKeluhan ? [document.getElementById('keluhan').value.trim()].filter(Boolean) : [],
        obat_diberikan:    adaKeluhan ? getChecked('checklistObat') : [],
        vitamin_diberikan: !adaKeluhan ? getChecked('checklistVitamin') : [],
        catatan:           document.getElementById('catatan').value.trim() || null,
    };

    const btn = document.getElementById('btnSimpan');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const url    = editId ? `${API_BASE}/${editId}` : STORE_URL;
        const method = editId ? 'PUT' : 'POST';
        const res    = await fetch(url, {
            method, credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Gagal menyimpan');
        toast(json.message || 'Berhasil disimpan!');
        closeModal('modalForm');
        loadData(document.getElementById('searchInput').value.trim());
    } catch (err) {
        toast(err.message || 'Terjadi kesalahan', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    }
}

// ── Hapus ──────────────────────────────────────────────────
function openHapus(id) {
    document.getElementById('btnKonfirmasiHapus').onclick = () => hapus(id);
    openModal('modalHapus');
}

async function hapus(id) {
    try {
        const res  = await fetch(`${API_BASE}/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' } });
        const json = await res.json();
        if (!json.success) throw new Error(json.message);
        toast(json.message || 'Data berhasil dihapus!');
        closeModal('modalHapus');
        loadData(document.getElementById('searchInput').value.trim());
    } catch (err) {
        toast(err.message || 'Gagal menghapus', 'error');
    }
}

// ── Helpers ────────────────────────────────────────────────
function today() { return new Date().toISOString().split('T')[0]; }
function formatDate(s) {
    if (!s) return '-';
    return new Date(s).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}

// ── Init ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    loadLansia();
    renderChecklist('checklistObat', DAFTAR_OBAT);
    renderChecklist('checklistVitamin', DAFTAR_VITAMIN);
});
</script>
@endsection