@extends('layouts.lansia')
@section('title', 'Kunjungan Lansia')

@section('styles')
<style>
    body { background:linear-gradient(135deg,#ECFDF5 0%,#D1FAE5 50%,#A7F3D0 100%) !important; }

    /* ── Header ── */
    .page-header { margin-bottom:28px; }
    .page-title  { font-size:26px; font-weight:800; color:#064E3B; margin-bottom:4px; }
    .page-sub    { font-size:14px; color:#6B7280; }

    /* ── Toolbar ── */
    .toolbar { display:flex; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
    .search-box { flex:1; min-width:220px; position:relative; }
    .search-box input { width:100%; padding:11px 14px 11px 40px; border:1px solid #A7F3D0;
                        border-radius:12px; font-size:14px; background:#fff; box-sizing:border-box;
                        transition:all 0.2s; }
    .search-box input:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.12); }
    .search-box i { position:absolute; left:13px; top:50%; transform:translateY(-50%); color:#6B7280; }

    /* ── Buttons ── */
    .btn { padding:10px 20px; border:none; border-radius:10px; font-size:14px; font-weight:600;
           cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:7px; }
    .btn:hover { transform:translateY(-1px); }
    .btn-teal   { background:#10B981; color:#fff; } .btn-teal:hover   { background:#059669; }
    .btn-yellow { background:#F59E0B; color:#fff; } .btn-yellow:hover { background:#D97706; }
    .btn-red    { background:#EF4444; color:#fff; } .btn-red:hover    { background:#DC2626; }
    .btn-outline{ background:transparent; border:2px solid #10B981; color:#10B981; }
    .btn-outline:hover { background:#10B981; color:#fff; }
    .btn-sm { padding:6px 12px; font-size:12px; }

    /* ── Table card ── */
    .table-card { background:#fff; border-radius:20px; box-shadow:0 4px 20px rgba(6,78,59,0.08);
                  overflow:hidden; border:1px solid rgba(16,185,129,0.1); }
    .table-scroll { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    thead tr { background:linear-gradient(135deg,#064E3B,#065F46); }
    thead th { padding:14px 14px; color:#fff; font-weight:600; text-align:left; white-space:nowrap;
               font-size:12px; text-transform:uppercase; letter-spacing:0.04em; }
    tbody tr { border-bottom:1px solid #ECFDF5; transition:background 0.15s; }
    tbody tr:hover { background:#F0FDF4; }
    tbody td { padding:12px 14px; color:#374151; vertical-align:middle; }

    /* ── Status badges ── */
    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px;
                    border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .s-normal    { background:#D1FAE5; color:#065F46; }
    .s-warning   { background:#FEF3C7; color:#92400E; }
    .s-danger    { background:#FEE2E2; color:#991B1B; }
    .s-info      { background:#DBEAFE; color:#1E40AF; }
    .s-unknown   { background:#F3F4F6; color:#6B7280; }

    /* ── Masalah indicator ── */
    .ada-masalah { display:inline-flex; align-items:center; gap:5px; padding:4px 10px;
                   border-radius:20px; font-size:11px; font-weight:700; }
    .masalah-ya  { background:#FEE2E2; color:#991B1B; }
    .masalah-tidak { background:#D1FAE5; color:#065F46; }

    /* ── Pagination ── */
    .pagination { display:flex; align-items:center; justify-content:space-between;
                  padding:14px 20px; border-top:1px solid #ECFDF5; flex-wrap:wrap; gap:10px; }
    .pagination-info { font-size:13px; color:#6B7280; }
    .pagination-btns { display:flex; gap:6px; }
    .page-btn { padding:6px 12px; border:1px solid #A7F3D0; background:#fff; border-radius:8px;
                font-size:13px; cursor:pointer; transition:all 0.2s; color:#065F46; }
    .page-btn:hover, .page-btn.active { background:#10B981; color:#fff; border-color:#10B981; }
    .page-btn:disabled { opacity:0.4; cursor:not-allowed; }

    /* ── Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
                     backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:16px; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:20px; width:100%; max-width:680px; max-height:92vh;
                 overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease; }
    .modal-box.modal-sm { max-width:460px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; align-items:center; justify-content:space-between;
                    padding:20px 24px; border-bottom:2px solid #ECFDF5; position:sticky; top:0; background:#fff; z-index:1; }
    .modal-title { font-size:18px; font-weight:800; color:#064E3B; }
    .modal-close { background:none; border:none; font-size:22px; color:#9CA3AF; cursor:pointer;
                   width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; }
    .modal-body { padding:24px; }
    .modal-footer { padding:16px 24px; border-top:1px solid #ECFDF5; display:flex; justify-content:flex-end; gap:10px; }

    /* ── Form ── */
    .form-section { margin-bottom:24px; }
    .form-section-title { font-size:14px; font-weight:700; color:#065F46; margin-bottom:14px;
                          padding-bottom:8px; border-bottom:2px solid #D1FAE5;
                          display:flex; align-items:center; gap:8px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    @media(max-width:600px){ .form-grid{ grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:5px; }
    .form-group.full { grid-column:1/-1; }
    .form-label { font-size:12px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:0.4px; }
    .form-label .req { color:#EF4444; }
    .form-input { padding:10px 13px; border:1px solid #D1FAE5; border-radius:10px; font-size:14px;
                  color:#1E3A5F; transition:all 0.2s; background:#fff; }
    .form-input:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.12); }
    .form-hint { font-size:11px; color:#6B7280; margin-top:3px; }

    /* ── Autocomplete ── */
    .autocomplete-wrap { position:relative; }
    .autocomplete-list { position:absolute; top:100%; left:0; right:0; background:#fff;
                         border:1px solid #D1FAE5; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.1);
                         z-index:100; max-height:200px; overflow-y:auto; display:none; }
    .autocomplete-item { padding:10px 14px; cursor:pointer; font-size:14px; border-bottom:1px solid #ECFDF5; }
    .autocomplete-item:hover { background:#F0FDF4; color:#065F46; }

    /* ── Toggle keluhan ── */
    .toggle-row { display:flex; gap:10px; }
    .toggle-btn { flex:1; padding:10px; border:2px solid #D1FAE5; border-radius:10px; background:#fff;
                  font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; text-align:center; }
    .toggle-btn.active-ya    { border-color:#EF4444; background:#FEF2F2; color:#991B1B; }
    .toggle-btn.active-tidak { border-color:#10B981; background:#F0FDF4; color:#065F46; }

    /* ── Checklist ── */
    .checklist-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px; }
    .check-item { display:flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid #D1FAE5;
                  border-radius:8px; cursor:pointer; transition:all 0.2s; font-size:13px; }
    .check-item:hover { border-color:#10B981; background:#F0FDF4; }
    .check-item input[type=checkbox] { accent-color:#10B981; width:15px; height:15px; }
    .check-item.checked { border-color:#10B981; background:#F0FDF4; }

    /* ── Riwayat kunjungan ── */
    .riwayat-item { background:#F0FDF4; border-radius:12px; padding:16px; margin-bottom:12px;
                    border-left:4px solid #10B981; }
    .riwayat-item.ada-masalah-item { border-left-color:#EF4444; background:#FEF2F2; }
    .riwayat-date { font-size:13px; font-weight:700; color:#064E3B; margin-bottom:10px; }
    .riwayat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
    @media(max-width:600px){ .riwayat-grid{ grid-template-columns:repeat(2,1fr); } }
    .riwayat-field { background:#fff; border-radius:8px; padding:8px 10px; }
    .riwayat-field-label { font-size:10px; color:#6B7280; font-weight:700; text-transform:uppercase; margin-bottom:3px; }
    .riwayat-field-value { font-size:13px; font-weight:700; color:#064E3B; }

    /* ── Empty state ── */
    .empty-state { text-align:center; padding:50px 20px; color:#9CA3AF; }
    .empty-state i { font-size:3rem; margin-bottom:12px; display:block; color:#A7F3D0; }
    .empty-state p { font-size:15px; }

    /* ── Toast ── */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600;
                  box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideIn 0.3s ease;
                  display:flex; align-items:center; gap:10px; min-width:260px; }
    @keyframes slideIn { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-stethoscope" style="color:#10B981;"></i> Kunjungan Posyandu Lansia</h1>
    <p class="page-sub">Catat dan pantau hasil pemeriksaan serta pengobatan setiap kunjungan</p>
</div>

<div class="toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari nama lansia..." oninput="debounceSearch()">
    </div>
    <button class="btn btn-teal" onclick="openModalTambah()">
        <i class="fas fa-plus"></i> Catat Kunjungan
    </button>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lansia</th>
                    <th>Tgl Kunjungan</th>
                    <th>BB (kg)</th>
                    <th>Tensi</th>
                    <th>Gula Darah</th>
                    <th>Kolesterol</th>
                    <th>Asam Urat</th>
                    <th>Keluhan</th>
                    <th>Kondisi</th>
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

{{-- ── Modal Catat Kunjungan ── --}}
<div class="modal-overlay" id="modalForm">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="modalFormTitle">
                <i class="fas fa-stethoscope" style="color:#10B981;"></i> Catat Kunjungan
            </span>
            <button class="modal-close" onclick="closeModal('modalForm')">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="editId">

            {{-- Step 1: Pilih Lansia --}}
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user"></i> Identitas & Tanggal</div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Pilih Lansia <span class="req">*</span></label>
                        <div class="autocomplete-wrap">
                            <input type="text" id="lansiaSearch" class="form-input"
                                   placeholder="Ketik nama lansia..." autocomplete="off"
                                   oninput="searchLansia(this.value)">
                            <div class="autocomplete-list" id="autocompleteList"></div>
                        </div>
                        <input type="hidden" id="lansia_id">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Kunjungan <span class="req">*</span></label>
                        <input type="date" id="tanggal_kunjungan" class="form-input" required>
                        <small style="color:#6B7280; margin-top:4px; display:block;">Hanya bisa memilih hari ini atau tanggal yang akan datang</small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" id="berat_badan" class="form-input" placeholder="60" step="0.1" min="20" max="200">
                    </div>
                </div>
            </div>

            {{-- Step 2: Tekanan Darah --}}
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-heartbeat"></i> Tekanan Darah</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Tekanan Darah</label>
                        <input type="text" id="tekanan_darah" class="form-input" placeholder="120/80"
                               oninput="previewStatusTensi(this.value)">
                        <div class="form-hint">Format: sistolik/diastolik (contoh: 130/85)</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Tensi</label>
                        <div id="previewTensi" style="padding:10px 13px;border-radius:10px;background:#F3F4F6;font-size:13px;font-weight:600;color:#6B7280;">
                            — Isi tekanan darah
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Cek Darah --}}
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-tint"></i> Cek Darah</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Gula Darah (mg/dL)</label>
                        <input type="number" id="gula_darah" class="form-input" placeholder="100" step="0.1"
                               oninput="previewStatusGula(this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Gula</label>
                        <div id="previewGula" style="padding:10px 13px;border-radius:10px;background:#F3F4F6;font-size:13px;font-weight:600;color:#6B7280;">— Isi gula darah</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kolesterol (mg/dL)</label>
                        <input type="number" id="kolesterol" class="form-input" placeholder="180" step="0.1"
                               oninput="previewStatusKol(this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Kolesterol</label>
                        <div id="previewKol" style="padding:10px 13px;border-radius:10px;background:#F3F4F6;font-size:13px;font-weight:600;color:#6B7280;">— Isi kolesterol</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Asam Urat (mg/dL)</label>
                        <input type="number" id="asam_urat" class="form-input" placeholder="5.5" step="0.1"
                               oninput="previewStatusAU(this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Asam Urat</label>
                        <div id="previewAU" style="padding:10px 13px;border-radius:10px;background:#F3F4F6;font-size:13px;font-weight:600;color:#6B7280;">— Isi asam urat</div>
                    </div>
                </div>
            </div>

            {{-- Step 4: Pengobatan --}}
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-pills"></i> Pengobatan</div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">Ada Keluhan?</label>
                    <div class="toggle-row">
                        <button type="button" class="toggle-btn" id="btnYa" onclick="setKeluhan(true)">
                            <i class="fas fa-exclamation-circle"></i> Ya, Ada Keluhan
                        </button>
                        <button type="button" class="toggle-btn active-tidak" id="btnTidak" onclick="setKeluhan(false)">
                            <i class="fas fa-check-circle"></i> Tidak Ada Keluhan
                        </button>
                    </div>
                </div>
                <div id="sectionKeluhan" style="display:none;">
                    <div class="form-group" style="margin-bottom:14px;">
                        <label class="form-label">Deskripsi Keluhan</label>
                        <textarea id="keluhan" class="form-input" rows="2" placeholder="Deskripsi keluhan lansia..."></textarea>
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
            </div>

            {{-- Step 5: Catatan Bidan --}}
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-notes-medical"></i> Catatan Bidan</div>
                <div class="form-group">
                    <textarea id="catatan_bidan" class="form-input" rows="3"
                              placeholder="Catatan atau hasil penyuluhan dari bidan (opsional)..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalForm')">Batal</button>
            <button class="btn btn-teal" id="btnSimpan" onclick="submitForm()">
                <i class="fas fa-save"></i> Simpan Kunjungan
            </button>
        </div>
    </div>
</div>

{{-- ── Modal Riwayat Kunjungan ── --}}
<div class="modal-overlay" id="modalRiwayat">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" id="riwayatTitle">Riwayat Kunjungan</span>
            <button class="modal-close" onclick="closeModal('modalRiwayat')">&times;</button>
        </div>
        <div class="modal-body" id="riwayatBody">
            <div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat...</p></div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalRiwayat')">Tutup</button>
        </div>
    </div>
</div>

{{-- ── Modal Konfirmasi Hapus ── --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <span class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Kunjungan</span>
            <button class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
        </div>
        <div class="modal-body" style="text-align:center;">
            <i class="fas fa-exclamation-triangle" style="font-size:48px;color:#F59E0B;margin-bottom:16px;display:block;"></i>
            <p style="font-size:15px;color:#374151;">Hapus data kunjungan ini?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalHapus')">Batal</button>
            <button class="btn btn-red" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Hapus</button>
        </div>
    </div>
</div>

{{-- ── Modal: Daftarkan Lansia Baru ── --}}
<div class="modal-overlay" id="modalDaftarLansia">
    <div class="modal-box modal-sm" style="max-width:520px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fas fa-user-plus" style="color:#10B981;"></i> Daftarkan Lansia Baru</span>
            <button class="modal-close" onclick="closeModal('modalDaftarLansia')">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:#6B7280;margin-bottom:16px;background:#F0FDF4;padding:10px 14px;border-radius:8px;">
                <i class="fas fa-info-circle" style="color:#10B981;"></i>
                Lansia belum terdaftar. Isi data dasar untuk mendaftarkan terlebih dahulu, lalu lanjutkan catat kunjungan.
            </p>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" id="daftarNamaLengkap" class="form-input" placeholder="Nama lengkap lansia">
                </div>
                <div class="form-group">
                    <label class="form-label">NIK (16 digit) <span class="req">*</span></label>
                    <input type="text" id="daftarNik" class="form-input" placeholder="16 digit NIK" maxlength="16">
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                    <select id="daftarJK" class="form-input">
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span class="req">*</span></label>
                    <input type="date" id="daftarTglLahir" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">No HP</label>
                    <input type="text" id="daftarNoHp" class="form-input" placeholder="08xx-xxxx-xxxx">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modalDaftarLansia')">Batal</button>
            <button class="btn btn-teal" id="btnSimpanDaftar" onclick="submitDaftarLansia()">
                <i class="fas fa-save"></i> Simpan & Pilih
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const LIST_URL   = '{{ route("lansia.kunjungan.list") }}';
const STORE_URL  = '{{ route("lansia.kunjungan.store") }}';
const API_BASE   = '/lansia/api/kunjungan';
const LANSIA_URL = '{{ route("lansia.lansia.list") }}';

const DAFTAR_OBAT    = ['Paracetamol','Amlodipin','Metformin','Captopril','Simvastatin','Antasida','Vitamin B Complex','Asam Mefenamat','Glibenklamid','Furosemid','Amlodipine','Lisinopril'];
const DAFTAR_VITAMIN = ['Vitamin C','Vitamin D','Vitamin B12','Kalsium','Asam Folat','Zinc','Vitamin E','Omega-3','Multivitamin'];

let allData = [], allLansia = [], currentPage = 1, perPage = 10;
let searchTimer = null, adaKeluhan = false;

// ── Toast ──────────────────────────────────────────────────
function toast(msg, type='success') {
    const icons={success:'check-circle',error:'times-circle',warning:'exclamation-triangle'};
    const el=document.createElement('div'); el.className=`toast-item toast-${type}`;
    el.innerHTML=`<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el); setTimeout(()=>el.remove(),4000);
}
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}

// ── Status helpers ─────────────────────────────────────────
const STATUS_MAP = {
    tensi: {
        normal:        {label:'Normal',        cls:'s-normal'},
        prehipertensi: {label:'Prehipertensi', cls:'s-warning'},
        hipertensi1:   {label:'Hipertensi I',  cls:'s-danger'},
        hipertensi2:   {label:'Hipertensi II', cls:'s-danger'},
    },
    gula: {
        rendah:       {label:'Rendah',        cls:'s-info'},
        normal:       {label:'Normal',        cls:'s-normal'},
        tinggi:       {label:'Tinggi',        cls:'s-warning'},
        sangat_tinggi:{label:'Sangat Tinggi', cls:'s-danger'},
    },
    kol: {
        normal:{label:'Normal',cls:'s-normal'},
        batas: {label:'Batas', cls:'s-warning'},
        tinggi:{label:'Tinggi',cls:'s-danger'},
    },
    au: {
        normal:{label:'Normal',cls:'s-normal'},
        tinggi:{label:'Tinggi',cls:'s-danger'},
    },
};

function statusBadge(type, val) {
    if (!val) return '<span class="status-badge s-unknown">—</span>';
    const s = STATUS_MAP[type]?.[val];
    if (!s) return `<span class="status-badge s-unknown">${val}</span>`;
    return `<span class="status-badge ${s.cls}">${s.label}</span>`;
}

// ── Preview status real-time ───────────────────────────────
function hitungTensi(td) {
    if (!td) return null;
    const p = td.split('/'); if (p.length < 2) return null;
    const s = parseInt(p[0]), d = parseInt(p[1]);
    if (s >= 160 || d >= 100) return 'hipertensi2';
    if (s >= 140 || d >= 90)  return 'hipertensi1';
    if (s >= 120 || d >= 80)  return 'prehipertensi';
    return 'normal';
}
function hitungGula(v) {
    if (!v) return null; v = parseFloat(v);
    if (v < 70) return 'rendah'; if (v < 140) return 'normal';
    if (v < 200) return 'tinggi'; return 'sangat_tinggi';
}
function hitungKol(v) {
    if (!v) return null; v = parseFloat(v);
    if (v < 200) return 'normal'; if (v < 240) return 'batas'; return 'tinggi';
}
function hitungAU(v) {
    if (!v) return null; return parseFloat(v) <= 7.0 ? 'normal' : 'tinggi';
}

function setPreview(elId, type, status) {
    const el = document.getElementById(elId);
    if (!el) return;
    if (!status) { el.style.background='#F3F4F6'; el.style.color='#6B7280'; el.textContent='—'; return; }
    const s = STATUS_MAP[type]?.[status];
    const colors = {
        's-normal':  {bg:'#D1FAE5',color:'#065F46'},
        's-warning': {bg:'#FEF3C7',color:'#92400E'},
        's-danger':  {bg:'#FEE2E2',color:'#991B1B'},
        's-info':    {bg:'#DBEAFE',color:'#1E40AF'},
    };
    const c = colors[s?.cls] || {bg:'#F3F4F6',color:'#6B7280'};
    el.style.background = c.bg; el.style.color = c.color;
    el.textContent = s?.label || status;
}

function previewStatusTensi(v) { setPreview('previewTensi','tensi', hitungTensi(v)); }
function previewStatusGula(v)  { setPreview('previewGula', 'gula',  hitungGula(v)); }
function previewStatusKol(v)   { setPreview('previewKol',  'kol',   hitungKol(v)); }
function previewStatusAU(v)    { setPreview('previewAU',   'au',    hitungAU(v)); }

// ── Load data ──────────────────────────────────────────────
async function loadData(q='') {
    try {
        const url = LIST_URL + (q ? `?q=${encodeURIComponent(q)}` : '');
        const res = await fetch(url, {headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin'});
        const json = await res.json();
        if (!json.success) throw new Error(json.message);
        allData = json.data;
        currentPage = 1;
        renderTable();
    } catch(e) {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="11" class="empty-state"><i class="fas fa-exclamation-circle"></i><br>${e.message}</td></tr>`;
    }
}

async function loadLansia() {
    try {
        const res = await fetch(LANSIA_URL, {headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin'});
        const json = await res.json();
        if (json.success) {
            allLansia = json.data;
            console.log('Lansia loaded:', allLansia.length, 'items');
        } else {
            console.error('Failed to load lansia:', json.message);
        }
    } catch(e) {
        console.error('Error loading lansia:', e);
    }
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => loadData(document.getElementById('searchInput').value.trim()), 400);
}

// ── Render table ───────────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById('tableBody');
    const start = (currentPage-1)*perPage;
    const page  = allData.slice(start, start+perPage);

    if (!allData.length) {
        tbody.innerHTML = `<tr><td colspan="11" class="empty-state"><i class="fas fa-stethoscope"></i><br>Belum ada data kunjungan</td></tr>`;
        updatePagination(); return;
    }

    tbody.innerHTML = page.map((d,i) => `
        <tr>
            <td>${start+i+1}</td>
            <td><strong>${d.nama_lansia||'-'}</strong><br><small style="color:#6B7280;">${d.jk_lansia==='L'?'Laki-laki':'Perempuan'}, ${d.umur_lansia||'-'} th</small></td>
            <td>${d.tanggal_kunjungan ? formatDate(d.tanggal_kunjungan) : '-'}</td>
            <td>${d.berat_badan ? d.berat_badan+' kg' : '-'}</td>
            <td>${d.tekanan_darah||'-'}<br>${statusBadge('tensi',d.status_tensi)}</td>
            <td>${d.gula_darah ? d.gula_darah+' mg/dL' : '-'}<br>${statusBadge('gula',d.status_gula)}</td>
            <td>${d.kolesterol ? d.kolesterol+' mg/dL' : '-'}<br>${statusBadge('kol',d.status_kolesterol)}</td>
            <td>${d.asam_urat ? d.asam_urat+' mg/dL' : '-'}<br>${statusBadge('au',d.status_asam_urat)}</td>
            <td><span class="status-badge ${d.ada_keluhan?'s-danger':'s-normal'}">${d.ada_keluhan?'Ada':'Tidak'}</span></td>
            <td><span class="ada-masalah ${d.ada_masalah?'masalah-ya':'masalah-tidak'}">
                <i class="fas fa-${d.ada_masalah?'exclamation-triangle':'check-circle'}"></i>
                ${d.ada_masalah?'Perlu Perhatian':'Normal'}
            </span></td>
            <td style="white-space:nowrap;">
                <button class="btn btn-outline btn-sm" onclick="openRiwayat(${d.lansia_id},'${(d.nama_lansia||'').replace(/'/g,"\\'")}')">
                    <i class="fas fa-history"></i>
                </button>
                <button class="btn btn-yellow btn-sm" onclick="openEdit(${d.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-red btn-sm" onclick="openHapus(${d.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`).join('');

    updatePagination();
}

function updatePagination() {
    const total = allData.length, pages = Math.ceil(total/perPage)||1;
    const start = total ? (currentPage-1)*perPage+1 : 0;
    const end   = Math.min(currentPage*perPage, total);
    document.getElementById('paginationInfo').textContent = `Menampilkan ${start}–${end} dari ${total} data`;
    const btns = document.getElementById('paginationBtns');
    let html = `<button class="page-btn" onclick="changePage(${currentPage-1})" ${currentPage<=1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
    for (let p=Math.max(1,currentPage-2); p<=Math.min(pages,currentPage+2); p++) {
        html += `<button class="page-btn ${p===currentPage?'active':''}" onclick="changePage(${p})">${p}</button>`;
    }
    html += `<button class="page-btn" onclick="changePage(${currentPage+1})" ${currentPage>=pages?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
    btns.innerHTML = html;
}

function changePage(p) {
    const pages = Math.ceil(allData.length/perPage)||1;
    if (p<1||p>pages) return; currentPage=p; renderTable();
}

// ── Checklist ──────────────────────────────────────────────
function renderChecklist(id, items, selected=[]) {
    document.getElementById(id).innerHTML = items.map(item => `
        <label class="check-item ${selected.includes(item)?'checked':''}">
            <input type="checkbox" value="${item}" ${selected.includes(item)?'checked':''}
                onchange="this.closest('.check-item').classList.toggle('checked',this.checked)">
            ${item}
        </label>`).join('');
}
function getChecked(id) {
    return [...document.querySelectorAll(`#${id} input[type=checkbox]:checked`)].map(el=>el.value);
}

// ── Toggle keluhan ─────────────────────────────────────────
function setKeluhan(val) {
    adaKeluhan = val;
    document.getElementById('btnYa').className    = 'toggle-btn'+(val?' active-ya':'');
    document.getElementById('btnTidak').className = 'toggle-btn'+(!val?' active-tidak':'');
    document.getElementById('sectionKeluhan').style.display = val?'':'none';
    document.getElementById('sectionVitamin').style.display = val?'none':'';
}

// ── Autocomplete lansia ────────────────────────────────────
function searchLansia(q) {
    const list = document.getElementById('autocompleteList');
    if (!q.trim()) { list.style.display='none'; return; }
    const filtered = allLansia.filter(l=>l.nama_lengkap.toLowerCase().includes(q.toLowerCase())).slice(0,8);
    if (!filtered.length) {
        list.innerHTML = `
            <div style="padding:10px 14px;font-size:13px;color:#9CA3AF;">Tidak ada hasil untuk "${q}"</div>
            <div class="autocomplete-item" style="color:#10B981;font-weight:700;border-top:1px solid #ECFDF5;"
                 onclick="openModalDaftarLansia('${q.replace(/'/g,"\\'")}')">
                <i class="fas fa-user-plus"></i> Daftarkan "${q}" sebagai lansia baru
            </div>`;
        list.style.display = 'block';
        return;
    }
    list.innerHTML = filtered.map(l=>`
        <div class="autocomplete-item" onclick="selectLansia(${l.id},'${l.nama_lengkap.replace(/'/g,"\\'")}')">
            ${l.nama_lengkap} <small style="color:#9CA3AF;">${l.nik||''} • ${l.umur||'-'} th</small>
        </div>`).join('');
    list.style.display = 'block';
}
function selectLansia(id, nama) {
    document.getElementById('lansia_id').value = id;
    document.getElementById('lansiaSearch').value = nama;
    document.getElementById('autocompleteList').style.display = 'none';
    console.log('Lansia selected:', id, nama);
}
document.addEventListener('click', e => {
    if (!e.target.closest('.autocomplete-wrap')) document.getElementById('autocompleteList').style.display='none';
});

// ── Tambah ─────────────────────────────────────────────────
function openModalTambah() {
    document.getElementById('editId').value = '';
    document.getElementById('modalFormTitle').innerHTML = '<i class="fas fa-stethoscope" style="color:#10B981;"></i> Catat Kunjungan';
    document.getElementById('lansiaSearch').value = '';
    document.getElementById('lansia_id').value = '';
    setMinTanggalKunjungan();
    ['berat_badan','tekanan_darah','gula_darah','kolesterol','asam_urat','keluhan','catatan_bidan'].forEach(id=>{
        const el=document.getElementById(id); if(el) el.value='';
    });
    ['previewTensi','previewGula','previewKol','previewAU'].forEach(id=>setPreview(id,'tensi',null));
    setKeluhan(false);
    renderChecklist('checklistObat', DAFTAR_OBAT);
    renderChecklist('checklistVitamin', DAFTAR_VITAMIN);
    openModal('modalForm');
}

// ── Edit ───────────────────────────────────────────────────
function openEdit(id) {
    const d = allData.find(x=>x.id===id); if (!d) return;
    document.getElementById('editId').value = id;
    document.getElementById('modalFormTitle').innerHTML = '<i class="fas fa-edit" style="color:#F59E0B;"></i> Edit Kunjungan';
    document.getElementById('lansiaSearch').value = d.nama_lansia||'';
    document.getElementById('lansia_id').value = d.lansia_id||'';
    document.getElementById('tanggal_kunjungan').value = d.tanggal_kunjungan||'';
    // Saat edit, hapus batasan minimum tanggal agar bisa edit tanggal lama
    document.getElementById('tanggal_kunjungan').min = '';
    document.getElementById('berat_badan').value = d.berat_badan||'';
    document.getElementById('tekanan_darah').value = d.tekanan_darah||'';
    document.getElementById('gula_darah').value = d.gula_darah||'';
    document.getElementById('kolesterol').value = d.kolesterol||'';
    document.getElementById('asam_urat').value = d.asam_urat||'';
    document.getElementById('keluhan').value = d.keluhan||'';
    document.getElementById('catatan_bidan').value = d.catatan_bidan||'';
    previewStatusTensi(d.tekanan_darah||'');
    previewStatusGula(d.gula_darah||'');
    previewStatusKol(d.kolesterol||'');
    previewStatusAU(d.asam_urat||'');
    setKeluhan(!!d.ada_keluhan);
    renderChecklist('checklistObat', DAFTAR_OBAT, d.obat_diberikan||[]);
    renderChecklist('checklistVitamin', DAFTAR_VITAMIN, d.vitamin_diberikan||[]);
    openModal('modalForm');
}

// ── Submit ─────────────────────────────────────────────────
async function submitForm() {
    const editId = document.getElementById('editId').value;
    const lansiaId = document.getElementById('lansia_id').value;
    const lansiaSearch = document.getElementById('lansiaSearch').value.trim();
    const tanggalKunjungan = document.getElementById('tanggal_kunjungan').value;
    
    if (!lansiaId && !editId) { 
        if (!lansiaSearch) {
            toast('Pilih lansia dari daftar atau ketik nama lansia','warning');
        } else {
            toast('Pilih lansia dari hasil pencarian','warning');
        }
        return; 
    }

    // Validasi tanggal tidak boleh di masa lalu
    if (tanggalKunjungan && !editId) {
        const selectedDate = new Date(tanggalKunjungan);
        const todayDate = new Date(today());
        if (selectedDate < todayDate) {
            toast('Tanggal kunjungan tidak boleh di masa lalu. Pilih hari ini atau tanggal yang akan datang','warning');
            return;
        }
    }

    const body = {
        lansia_id:         parseInt(lansiaId)||undefined,
        tanggal_kunjungan: document.getElementById('tanggal_kunjungan').value,
        berat_badan:       document.getElementById('berat_badan').value||null,
        tekanan_darah:     document.getElementById('tekanan_darah').value.trim()||null,
        gula_darah:        document.getElementById('gula_darah').value||null,
        kolesterol:        document.getElementById('kolesterol').value||null,
        asam_urat:         document.getElementById('asam_urat').value||null,
        ada_keluhan:       adaKeluhan,
        keluhan:           adaKeluhan ? document.getElementById('keluhan').value.trim()||null : null,
        obat_diberikan:    adaKeluhan ? getChecked('checklistObat') : [],
        vitamin_diberikan: !adaKeluhan ? getChecked('checklistVitamin') : [],
        catatan_bidan:     document.getElementById('catatan_bidan').value.trim()||null,
    };

    const btn = document.getElementById('btnSimpan');
    btn.disabled=true; btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const url    = editId ? `${API_BASE}/${editId}` : STORE_URL;
        const method = editId ? 'PUT' : 'POST';
        const res    = await fetch(url, {
            method, credentials:'same-origin',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body: JSON.stringify(body),
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message||'Gagal menyimpan');
        toast(json.message||'Kunjungan berhasil dicatat!');
        closeModal('modalForm');
        loadData(document.getElementById('searchInput').value.trim());
    } catch(err) { toast(err.message||'Terjadi kesalahan','error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Simpan Kunjungan'; }
}

// ── Riwayat ────────────────────────────────────────────────
async function openRiwayat(lansiaId, nama) {
    document.getElementById('riwayatTitle').textContent = `Riwayat Kunjungan — ${nama}`;
    document.getElementById('riwayatBody').innerHTML = '<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat...</p></div>';
    openModal('modalRiwayat');
    try {
        const res  = await fetch(`${API_BASE}/${lansiaId}/riwayat`, {
            headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}, credentials:'same-origin'
        });
        const json = await res.json();
        if (!json.success) throw new Error(json.message);
        const list = json.data;
        if (!list.length) {
            document.getElementById('riwayatBody').innerHTML = '<div class="empty-state"><i class="fas fa-history"></i><p>Belum ada riwayat kunjungan</p></div>';
            return;
        }
        document.getElementById('riwayatBody').innerHTML = list.map(k => `
            <div class="riwayat-item ${k.ada_masalah?'ada-masalah-item':''}">
                <div class="riwayat-date">
                    <i class="fas fa-calendar-check"></i> ${formatDate(k.tanggal_kunjungan)}
                    ${k.ada_masalah ? '<span class="ada-masalah masalah-ya" style="margin-left:8px;"><i class="fas fa-exclamation-triangle"></i> Perlu Perhatian</span>' : '<span class="ada-masalah masalah-tidak" style="margin-left:8px;"><i class="fas fa-check-circle"></i> Normal</span>'}
                </div>
                <div class="riwayat-grid">
                    <div class="riwayat-field">
                        <div class="riwayat-field-label">BB</div>
                        <div class="riwayat-field-value">${k.berat_badan ? k.berat_badan+' kg' : '—'}</div>
                    </div>
                    <div class="riwayat-field">
                        <div class="riwayat-field-label">Tensi</div>
                        <div class="riwayat-field-value">${k.tekanan_darah||'—'}</div>
                        ${statusBadge('tensi',k.status_tensi)}
                    </div>
                    <div class="riwayat-field">
                        <div class="riwayat-field-label">Gula Darah</div>
                        <div class="riwayat-field-value">${k.gula_darah ? k.gula_darah+' mg/dL' : '—'}</div>
                        ${statusBadge('gula',k.status_gula)}
                    </div>
                    <div class="riwayat-field">
                        <div class="riwayat-field-label">Kolesterol</div>
                        <div class="riwayat-field-value">${k.kolesterol ? k.kolesterol+' mg/dL' : '—'}</div>
                        ${statusBadge('kol',k.status_kolesterol)}
                    </div>
                    <div class="riwayat-field">
                        <div class="riwayat-field-label">Asam Urat</div>
                        <div class="riwayat-field-value">${k.asam_urat ? k.asam_urat+' mg/dL' : '—'}</div>
                        ${statusBadge('au',k.status_asam_urat)}
                    </div>
                    <div class="riwayat-field">
                        <div class="riwayat-field-label">Keluhan</div>
                        <div class="riwayat-field-value">${k.ada_keluhan ? 'Ada' : 'Tidak'}</div>
                    </div>
                </div>
                ${k.obat_diberikan?.length ? `<div style="margin-top:10px;font-size:12px;color:#065F46;"><strong>Obat:</strong> ${k.obat_diberikan.join(', ')}</div>` : ''}
                ${k.vitamin_diberikan?.length ? `<div style="margin-top:4px;font-size:12px;color:#065F46;"><strong>Vitamin:</strong> ${k.vitamin_diberikan.join(', ')}</div>` : ''}
                ${k.catatan_bidan ? `<div style="margin-top:8px;font-size:12px;color:#6B7280;background:#fff;padding:8px 10px;border-radius:8px;"><strong>Catatan Bidan:</strong> ${k.catatan_bidan}</div>` : ''}
            </div>`).join('');
    } catch(err) {
        document.getElementById('riwayatBody').innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>${err.message}</p></div>`;
    }
}

// ── Hapus ──────────────────────────────────────────────────
function openHapus(id) {
    document.getElementById('btnKonfirmasiHapus').onclick = () => hapus(id);
    openModal('modalHapus');
}
async function hapus(id) {
    try {
        const res  = await fetch(`${API_BASE}/${id}`, {method:'DELETE',credentials:'same-origin',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}});
        const json = await res.json();
        if (!json.success) throw new Error(json.message);
        toast(json.message||'Data berhasil dihapus!');
        closeModal('modalHapus');
        loadData(document.getElementById('searchInput').value.trim());
    } catch(err) { toast(err.message||'Gagal menghapus','error'); }
}

// ── Helpers ────────────────────────────────────────────────
function today() { return new Date().toISOString().split('T')[0]; }

function setMinTanggalKunjungan() {
    const input = document.getElementById('tanggal_kunjungan');
    if (input) {
        input.min = today();
        input.value = today();
    }
}

function formatDate(s) {
    if (!s) return '-';
    return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});
}

// ── Daftarkan Lansia Baru (dari dalam form kunjungan) ──────
function openModalDaftarLansia(namaAwal = '') {
    document.getElementById('autocompleteList').style.display = 'none';
    document.getElementById('daftarNamaLengkap').value = namaAwal;
    document.getElementById('daftarNik').value = '';
    document.getElementById('daftarJK').value = '';
    document.getElementById('daftarTglLahir').value = '';
    document.getElementById('daftarNoHp').value = '';
    openModal('modalDaftarLansia');
    setTimeout(() => document.getElementById('daftarNamaLengkap').focus(), 200);
}

async function submitDaftarLansia() {
    const nama = document.getElementById('daftarNamaLengkap').value.trim();
    const nik  = document.getElementById('daftarNik').value.trim();
    const jk   = document.getElementById('daftarJK').value;
    const tgl  = document.getElementById('daftarTglLahir').value;
    const hp   = document.getElementById('daftarNoHp').value.trim();

    if (!nama || !nik || !jk || !tgl) { toast('Nama, NIK, JK, dan Tanggal Lahir wajib diisi','warning'); return; }
    if (nik.length !== 16) { toast('NIK harus 16 digit','warning'); return; }

    const btn = document.getElementById('btnSimpanDaftar');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res = await fetch('/lansia/api/lansia', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ nik, nama_lengkap:nama, jenis_kelamin:jk, tanggal_lahir:tgl, no_hp:hp||null }),
        });
        const data = await res.json();
        if (data.success) {
            toast(`${nama} berhasil didaftarkan!`, 'success');
            closeModal('modalDaftarLansia');
            await loadLansia();
            // Pilih otomatis lansia yang baru didaftarkan
            const newId = data.data?.id;
            if (newId) {
                selectLansia(newId, nama);
            } else {
                document.getElementById('lansiaSearch').value = nama;
            }
        } else {
            toast(data.message || 'Gagal mendaftarkan lansia', 'error');
        }
    } catch(e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="fas fa-save"></i> Simpan & Pilih'; }
}

document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');}));

document.addEventListener('DOMContentLoaded', () => {
    loadData();
    loadLansia();
    renderChecklist('checklistObat', DAFTAR_OBAT);
    renderChecklist('checklistVitamin', DAFTAR_VITAMIN);
    
    // Set minimum tanggal kunjungan
    const tanggalInput = document.getElementById('tanggal_kunjungan');
    if (tanggalInput) {
        tanggalInput.min = today();
        // Validasi real-time saat user mengubah tanggal
        tanggalInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const todayDate = new Date(today());
            if (selectedDate < todayDate) {
                toast('Tanggal tidak boleh di masa lalu','warning');
                this.value = today();
            }
        });
    }
});
</script>
@endsection
