@extends('layouts.lansia')
@section('title', 'Data Lansia')

@section('styles')
<style>
body { background:linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
@keyframes fadeInUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
.page-title { font-size:28px; font-weight:700; background:linear-gradient(135deg,#065F46,#10B981);
              -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:4px; }
.page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }
.toolbar { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.search-box { flex:1; min-width:200px; position:relative; }
.search-box input { width:100%; padding:10px 14px 10px 38px; border:1px solid #E2E8F0; border-radius:10px; font-size:14px; background:#fff; box-sizing:border-box; }
.search-box i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94A3B8; }
.btn { padding:10px 18px; border:none; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:7px; }
.btn:hover { transform:translateY(-1px); }
.btn-green { background:#10B981; color:#fff; } .btn-green:hover { background:#059669; }
.btn-blue  { background:#3B82F6; color:#fff; } .btn-blue:hover  { background:#2563EB; }
.btn-yellow{ background:#F59E0B; color:#fff; } .btn-yellow:hover{ background:#D97706; }
.btn-red   { background:#EF4444; color:#fff; } .btn-red:hover   { background:#DC2626; }
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
.badge-blue { background:#DBEAFE; color:#1D4ED8; } .badge-pink { background:#FCE7F3; color:#BE185D; }
.pagination { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid #F1F5F9; flex-wrap:wrap; gap:10px; }
.pagination-info { font-size:13px; color:#64748B; }
.pagination-btns { display:flex; gap:6px; }
.page-btn { padding:6px 12px; border:1px solid #E2E8F0; border-radius:8px; background:#fff; font-size:13px; cursor:pointer; transition:all 0.2s; }
.page-btn:hover,.page-btn.active { background:#10B981; color:#fff; border-color:#10B981; }
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff; border-radius:16px; width:100%; max-width:720px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:fadeInUp 0.3s ease; }
.modal-box.modal-sm { max-width:440px; }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid #F1F5F9; }
.modal-title { font-size:18px; font-weight:700; color:#065F46; }
.modal-close { background:none; border:none; font-size:20px; color:#94A3B8; cursor:pointer; padding:4px; }
.modal-close:hover { color:#EF4444; }
.modal-body { padding:24px; }
.modal-footer { padding:16px 24px; border-top:1px solid #F1F5F9; display:flex; justify-content:flex-end; gap:10px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:600px){ .form-grid{ grid-template-columns:1fr; } }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-group.full { grid-column:1/-1; }
.form-label { font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:0.4px; }
.form-label .req { color:#EF4444; }
.form-input { padding:10px 13px; border:1px solid #E2E8F0; border-radius:9px; font-size:14px; color:#1E3A5F; transition:all 0.2s; }
.form-input:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.12); }
.section-divider { grid-column:1/-1; margin:8px 0 4px; padding-bottom:8px; border-bottom:2px solid #D1FAE5; font-size:13px; font-weight:700; color:#065F46; display:flex; align-items:center; gap:7px; }
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.detail-item { background:#F8FAFC; border-radius:10px; padding:12px 14px; }
.detail-label { font-size:11px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:4px; }
.detail-value { font-size:14px; font-weight:600; color:#1E3A5F; }
.riwayat-table { width:100%; border-collapse:collapse; font-size:12px; margin-top:12px; }
.riwayat-table th { background:#065F46; color:#fff; padding:8px 10px; text-align:left; }
.riwayat-table td { padding:8px 10px; border-bottom:1px solid #F1F5F9; }
.empty-state { text-align:center; padding:40px; color:#94A3B8; }
.empty-state i { font-size:40px; margin-bottom:12px; display:block; }
#toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
.toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:260px; }
@keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
.toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }
</style>
@endsection
@section('content')
<div id="toast"></div>

<div>
    <h1 class="page-title">Data Lansia</h1>
    <p class="page-subtitle">Kelola data lansia posyandu</p>
</div>

<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nama, NIK, alamat..." oninput="debounceSearch()">
    </div>
    <button class="btn btn-green" onclick="openModalTambah()">
        <i class="fas fa-plus"></i> Tambah Lansia
    </button>
</div>

<div class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>JK</th>
                    <th>Umur</th>
                    <th>Alamat</th>
                    <th>BB (kg)</th>
                    <th>Tekanan Darah</th>
                    <th>Gula Darah</th>
                    <th>Tgl Periksa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr><td colspan="11" class="empty-state"><i class="fas fa-spinner fa-spin"></i><br>Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <div class="pagination-info" id="paginationInfo">Menampilkan 0 data</div>
        <div class="pagination-btns" id="paginationBtns"></div>
    </div>
</div>

{{-- Modal Tambah/Edit Lansia --}}
<div class="modal-overlay" id="modalForm">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="modalFormTitle">Tambah Lansia</span>
            <button class="modal-close" onclick="closeModal('modalForm')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formLansia" onsubmit="submitForm(event)">
                <input type="hidden" id="editId">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="nama_lengkap" class="form-input" placeholder="Nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIK (16 digit) <span class="req">*</span></label>
                        <input type="text" id="nik" class="form-input" placeholder="16 digit NIK" maxlength="16" pattern="\d{16}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                        <select id="jenis_kelamin" class="form-input" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span class="req">*</span></label>
                        <input type="date" id="tanggal_lahir" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" class="form-input" placeholder="Kota/kabupaten">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No HP</label>
                        <input type="text" id="no_hp" class="form-input" placeholder="08xx-xxxx-xxxx">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Alamat</label>
                        <textarea id="alamat" class="form-input" rows="2" placeholder="Alamat lengkap"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">RT/RW</label>
                        <input type="text" id="rt_rw" class="form-input" placeholder="001/002">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Wali</label>
                        <input type="text" id="nama_wali" class="form-input" placeholder="Nama wali/keluarga">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hubungan Wali</label>
                        <input type="text" id="hubungan_wali" class="form-input" placeholder="Anak, Suami, dll">
                    </div>

                    <div class="section-divider"><i class="fas fa-stethoscope"></i> Pemeriksaan Awal (Opsional)</div>

                    <div class="form-group">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" id="berat_badan" class="form-input" placeholder="60" step="0.1" min="20" max="200">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" id="tinggi_badan" class="form-input" placeholder="160" step="0.1" min="100" max="250">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tekanan Darah</label>
                        <input type="text" id="tekanan_darah" class="form-input" placeholder="120/80">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gula Darah (mg/dL)</label>
                        <input type="number" id="gula_darah" class="form-input" placeholder="100" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Asam Urat (mg/dL)</label>
                        <input type="number" id="asam_urat" class="form-input" placeholder="5.5" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kolesterol (mg/dL)</label>
                        <input type="number" id="kolesterol" class="form-input" placeholder="180" step="0.1">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Catatan</label>
                        <textarea id="catatan" class="form-input" rows="2" placeholder="Catatan pemeriksaan..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-red" onclick="closeModal('modalForm')">Batal</button>
            <button class="btn btn-green" onclick="submitForm(event)" id="btnSimpan">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>

{{-- Modal Detail Lansia --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Detail Lansia</span>
            <button class="modal-close" onclick="closeModal('modalDetail')">&times;</button>
        </div>
        <div class="modal-body" id="detailBody">
            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><br>Memuat...</div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-green" onclick="openModalPemeriksaan(currentDetailId)">
                <i class="fas fa-plus"></i> Tambah Pemeriksaan
            </button>
            <button class="btn btn-red" onclick="closeModal('modalDetail')">Tutup</button>
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
            <p style="font-size:15px;color:#374151;margin-bottom:6px;">Hapus data lansia ini?</p>
            <p style="font-size:13px;color:#94A3B8;" id="hapusNama"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-yellow" onclick="closeModal('modalHapus')">Batal</button>
            <button class="btn btn-red" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Hapus</button>
        </div>
    </div>
</div>

{{-- Modal Tambah Pemeriksaan --}}
<div class="modal-overlay" id="modalPemeriksaan">
    <div class="modal-box modal-sm" style="max-width:560px;">
        <div class="modal-header">
            <span class="modal-title">Tambah Pemeriksaan</span>
            <button class="modal-close" onclick="closeModal('modalPemeriksaan')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formPemeriksaan" onsubmit="submitPemeriksaan(event)">
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Tanggal Periksa <span class="req">*</span></label>
                        <input type="date" id="p_tanggal" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" id="p_bb" class="form-input" placeholder="60" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" id="p_tb" class="form-input" placeholder="160" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tekanan Darah</label>
                        <input type="text" id="p_td" class="form-input" placeholder="120/80">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gula Darah (mg/dL)</label>
                        <input type="number" id="p_gd" class="form-input" placeholder="100" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Asam Urat (mg/dL)</label>
                        <input type="number" id="p_au" class="form-input" placeholder="5.5" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kolesterol (mg/dL)</label>
                        <input type="number" id="p_kol" class="form-input" placeholder="180" step="0.1">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Catatan</label>
                        <textarea id="p_catatan" class="form-input" rows="2" placeholder="Catatan..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-red" onclick="closeModal('modalPemeriksaan')">Batal</button>
            <button class="btn btn-green" onclick="submitPemeriksaan(event)"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
const LIST_URL   = '{{ route("lansia.data.list") }}';
const STORE_URL  = '{{ route("lansia.data.store") }}';
const API_BASE   = '/lansia/api/data';

let allData = [], currentPage = 1, perPage = 10, currentDetailId = null;
let searchTimer = null;

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
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="11" class="empty-state"><i class="fas fa-exclamation-circle"></i><br>${e.message}</td></tr>`;
    }
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
        tbody.innerHTML = `<tr><td colspan="11" class="empty-state"><i class="fas fa-users"></i><br>Belum ada data lansia</td></tr>`;
        updatePagination();
        return;
    }

    tbody.innerHTML = page.map((d, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td><strong>${d.nama_lengkap}</strong></td>
            <td style="font-family:monospace;">${d.nik || '-'}</td>
            <td><span class="badge ${d.jenis_kelamin === 'L' ? 'badge-blue' : 'badge-pink'}">${d.jenis_kelamin === 'L' ? 'L' : 'P'}</span></td>
            <td>${d.umur ? d.umur + ' th' : '-'}</td>
            <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${d.alamat||''}">${d.alamat || '-'}</td>
            <td>${d.berat_badan ? d.berat_badan + ' kg' : '-'}</td>
            <td>${d.tekanan_darah || '-'}</td>
            <td>${d.gula_darah ? d.gula_darah + ' mg/dL' : '-'}</td>
            <td>${d.tanggal_periksa ? formatDate(d.tanggal_periksa) : '-'}</td>
            <td style="white-space:nowrap;">
                <button class="btn btn-blue btn-sm" onclick="openDetail(${d.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-yellow btn-sm" onclick="openEdit(${d.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-red btn-sm" onclick="openHapus(${d.id}, '${escHtml(d.nama_lengkap)}')"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`).join('');

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

// ── Tambah ─────────────────────────────────────────────────
function openModalTambah() {
    document.getElementById('editId').value = '';
    document.getElementById('modalFormTitle').textContent = 'Tambah Lansia';
    document.getElementById('formLansia').reset();
    document.getElementById('p_tanggal') && (document.getElementById('p_tanggal').value = today());
    openModal('modalForm');
}

// ── Edit ───────────────────────────────────────────────────
function openEdit(id) {
    const d = allData.find(x => x.id === id);
    if (!d) return;
    document.getElementById('editId').value = id;
    document.getElementById('modalFormTitle').textContent = 'Edit Data Lansia';
    document.getElementById('nama_lengkap').value  = d.nama_lengkap || '';
    document.getElementById('nik').value           = d.nik || '';
    document.getElementById('jenis_kelamin').value = d.jenis_kelamin || '';
    document.getElementById('tanggal_lahir').value = d.tanggal_lahir || '';
    document.getElementById('tempat_lahir').value  = d.tempat_lahir || '';
    document.getElementById('alamat').value        = d.alamat || '';
    document.getElementById('rt_rw').value         = d.rt_rw || '';
    document.getElementById('no_hp').value         = d.no_hp || '';
    document.getElementById('nama_wali').value     = d.nama_wali || '';
    document.getElementById('hubungan_wali').value = d.hubungan_wali || '';
    // Clear pemeriksaan fields on edit
    ['berat_badan','tinggi_badan','tekanan_darah','gula_darah','asam_urat','kolesterol','catatan'].forEach(f => {
        const el = document.getElementById(f); if (el) el.value = '';
    });
    openModal('modalForm');
}

// ── Submit Form ────────────────────────────────────────────
async function submitForm(e) {
    if (e && e.preventDefault) e.preventDefault();
    const id = document.getElementById('editId').value;
    const body = {
        nama_lengkap:  document.getElementById('nama_lengkap').value.trim(),
        nik:           document.getElementById('nik').value.trim(),
        jenis_kelamin: document.getElementById('jenis_kelamin').value,
        tanggal_lahir: document.getElementById('tanggal_lahir').value,
        tempat_lahir:  document.getElementById('tempat_lahir').value.trim(),
        alamat:        document.getElementById('alamat').value.trim(),
        rt_rw:         document.getElementById('rt_rw').value.trim(),
        no_hp:         document.getElementById('no_hp').value.trim(),
        nama_wali:     document.getElementById('nama_wali').value.trim(),
        hubungan_wali: document.getElementById('hubungan_wali').value.trim(),
    };
    if (!id) {
        Object.assign(body, {
            berat_badan:   document.getElementById('berat_badan').value || null,
            tinggi_badan:  document.getElementById('tinggi_badan').value || null,
            tekanan_darah: document.getElementById('tekanan_darah').value.trim() || null,
            gula_darah:    document.getElementById('gula_darah').value || null,
            asam_urat:     document.getElementById('asam_urat').value || null,
            kolesterol:    document.getElementById('kolesterol').value || null,
            catatan:       document.getElementById('catatan').value.trim() || null,
        });
    }

    const btn = document.getElementById('btnSimpan');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const url    = id ? `${API_BASE}/${id}` : STORE_URL;
        const method = id ? 'PUT' : 'POST';
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

// ── Detail ─────────────────────────────────────────────────
async function openDetail(id) {
    currentDetailId = id;
    document.getElementById('detailBody').innerHTML = '<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><br>Memuat...</div>';
    openModal('modalDetail');
    try {
        const res  = await fetch(`${API_BASE}/${id}`, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        const json = await res.json();
        if (!json.success) throw new Error(json.message);
        const d = json.data;
        const riwayat = (d.riwayat_periksa || []).map(p => `
            <tr>
                <td>${formatDate(p.tanggal_periksa)}</td>
                <td>${p.berat_badan ? p.berat_badan + ' kg' : '-'}</td>
                <td>${p.tinggi_badan ? p.tinggi_badan + ' cm' : '-'}</td>
                <td>${p.tekanan_darah || '-'}</td>
                <td>${p.gula_darah ? p.gula_darah + ' mg/dL' : '-'}</td>
                <td>${p.asam_urat ? p.asam_urat + ' mg/dL' : '-'}</td>
                <td>${p.kolesterol ? p.kolesterol + ' mg/dL' : '-'}</td>
                <td>${p.catatan || '-'}</td>
            </tr>`).join('') || '<tr><td colspan="8" style="text-align:center;color:#94A3B8;padding:12px;">Belum ada riwayat</td></tr>';

        document.getElementById('detailBody').innerHTML = `
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label">Nama Lengkap</div><div class="detail-value">${d.nama_lengkap}</div></div>
                <div class="detail-item"><div class="detail-label">NIK</div><div class="detail-value" style="font-family:monospace;">${d.nik || '-'}</div></div>
                <div class="detail-item"><div class="detail-label">Jenis Kelamin</div><div class="detail-value">${d.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</div></div>
                <div class="detail-item"><div class="detail-label">Tanggal Lahir</div><div class="detail-value">${d.tanggal_lahir ? formatDate(d.tanggal_lahir) : '-'}</div></div>
                <div class="detail-item"><div class="detail-label">Tempat Lahir</div><div class="detail-value">${d.tempat_lahir || '-'}</div></div>
                <div class="detail-item"><div class="detail-label">Umur</div><div class="detail-value">${d.umur ? d.umur + ' tahun' : '-'}</div></div>
                <div class="detail-item" style="grid-column:1/-1;"><div class="detail-label">Alamat</div><div class="detail-value">${d.alamat || '-'}${d.rt_rw ? ' RT/RW ' + d.rt_rw : ''}</div></div>
                <div class="detail-item"><div class="detail-label">No HP</div><div class="detail-value">${d.no_hp || '-'}</div></div>
                <div class="detail-item"><div class="detail-label">Nama Wali</div><div class="detail-value">${d.nama_wali || '-'}</div></div>
                <div class="detail-item"><div class="detail-label">Hubungan Wali</div><div class="detail-value">${d.hubungan_wali || '-'}</div></div>
            </div>
            <div style="margin-top:20px;">
                <div style="font-size:14px;font-weight:700;color:#065F46;margin-bottom:8px;"><i class="fas fa-history"></i> Riwayat Pemeriksaan</div>
                <div style="overflow-x:auto;">
                    <table class="riwayat-table">
                        <thead><tr><th>Tanggal</th><th>BB</th><th>TB</th><th>Tekanan Darah</th><th>Gula Darah</th><th>Asam Urat</th><th>Kolesterol</th><th>Catatan</th></tr></thead>
                        <tbody>${riwayat}</tbody>
                    </table>
                </div>
            </div>`;
    } catch (err) {
        document.getElementById('detailBody').innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><br>${err.message}</div>`;
    }
}

// ── Hapus ──────────────────────────────────────────────────
function openHapus(id, nama) {
    document.getElementById('hapusNama').textContent = nama;
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

// ── Pemeriksaan ────────────────────────────────────────────
function openModalPemeriksaan(id) {
    currentDetailId = id;
    document.getElementById('formPemeriksaan').reset();
    document.getElementById('p_tanggal').value = today();
    openModal('modalPemeriksaan');
}

async function submitPemeriksaan(e) {
    if (e && e.preventDefault) e.preventDefault();
    const body = {
        tanggal_periksa: document.getElementById('p_tanggal').value,
        berat_badan:     document.getElementById('p_bb').value || null,
        tinggi_badan:    document.getElementById('p_tb').value || null,
        tekanan_darah:   document.getElementById('p_td').value.trim() || null,
        gula_darah:      document.getElementById('p_gd').value || null,
        asam_urat:       document.getElementById('p_au').value || null,
        kolesterol:      document.getElementById('p_kol').value || null,
        catatan:         document.getElementById('p_catatan').value.trim() || null,
    };
    try {
        const res  = await fetch(`${API_BASE}/${currentDetailId}/pemeriksaan`, {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message);
        toast(json.message || 'Pemeriksaan berhasil dicatat!');
        closeModal('modalPemeriksaan');
        openDetail(currentDetailId);
        loadData(document.getElementById('searchInput').value.trim());
    } catch (err) {
        toast(err.message || 'Gagal menyimpan pemeriksaan', 'error');
    }
}

// ── Helpers ────────────────────────────────────────────────
function today() { return new Date().toISOString().split('T')[0]; }
function formatDate(s) {
    if (!s) return '-';
    const d = new Date(s);
    return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}
function escHtml(s) { return (s||'').replace(/'/g, "\\'"); }

// ── Init ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => loadData());
</script>
@endsection