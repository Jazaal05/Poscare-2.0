@extends('layouts.app')

@section('title', 'Master Vaksin')

@section('styles')
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
    .card { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:20px; }
    .header-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    table { width:100%; border-collapse:collapse; font-size:14px; }
    thead th { background:#F8FAFC; padding:12px 16px; text-align:left; font-weight:600; color:#4A6FA3; border-bottom:2px solid #E5E7EB; white-space:nowrap; }
    tbody td { padding:12px 16px; border-bottom:1px solid #F1F5F9; color:#374151; vertical-align:middle; }
    tbody tr:hover { background:#F8FAFC; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-info { background:#DBEAFE; color:#1E40AF; }
    .badge-warning { background:#FEF3C7; color:#92400E; }
    .badge-danger { background:#FEE2E2; color:#991B1B; }
    .btn { padding:8px 16px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#246BCE; color:#fff; } .btn-primary:hover { background:#1D58A8; }
    .btn-warning { background:#F59E0B; color:#fff; } .btn-warning:hover { background:#D97706; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-outline { background:transparent; border:2px solid #246BCE; color:#246BCE; } .btn-outline:hover { background:#246BCE; color:#fff; }
    .btn-sm { padding:5px 10px; font-size:12px; }
    .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select, .form-group textarea { padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; transition:all 0.2s; width:100%; box-sizing:border-box; }
    .form-group input:focus, .form-group select:focus { outline:none; border-color:#246BCE; box-shadow:0 0 0 3px rgba(36,107,206,0.1); }
    .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
    @media(max-width:768px){ .form-grid{ grid-template-columns:1fr; } }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:20px; padding:28px; width:90%; max-width:560px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid #E5E7EB; }
    .modal-title { font-size:20px; font-weight:700; color:#1E3A5F; }
    .modal-close { background:none; border:none; font-size:24px; color:#9CA3AF; cursor:pointer; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; transform:rotate(90deg); }
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:14px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:280px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }
    .loading-row td, .empty-row td { text-align:center; padding:40px; color:#9CA3AF; }
    .info-box { background:#EFF6FF; border:1px solid #BFDBFE; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-size:13px; color:#1E40AF; display:flex; align-items:flex-start; gap:10px; }
    .info-box i { margin-top:2px; flex-shrink:0; }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div class="header-row">
    <div>
        <h1 class="page-title"><i class="fas fa-vials" style="color:#246BCE;"></i> Master Vaksin</h1>
        <p class="page-subtitle">Kelola daftar jenis vaksin yang tersedia di posyandu</p>
    </div>
    <button class="btn btn-primary" onclick="openModalTambah()">
        <i class="fas fa-plus"></i> Tambah Vaksin
    </button>
</div>

<div class="info-box">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>Perhatian:</strong> Menghapus vaksin akan menghapus semua data imunisasi anak yang menggunakan vaksin tersebut.
        Pastikan data sudah dibackup sebelum menghapus.
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Vaksin</th>
                    <th>Usia Standar</th>
                    <th>Usia Min</th>
                    <th>Usia Maks</th>
                    <th>Keterangan</th>
                    <th>Jml Imunisasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="vaksinTableBody">
                <tr class="loading-row"><td colspan="8"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH / EDIT --}}
<div class="modal-overlay" id="modalForm">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="modalFormTitle"><i class="fas fa-plus-circle" style="color:#246BCE;"></i> Tambah Vaksin</h3>
            <button class="modal-close" onclick="closeModal('modalForm')">&times;</button>
        </div>
        <form id="formVaksin" onsubmit="submitVaksin(event)">
            <input type="hidden" id="vaksinId">
            <div class="form-group">
                <label>Nama Vaksin <span style="color:red">*</span></label>
                <input type="text" id="nama_vaksin" name="nama_vaksin" placeholder="Contoh: BCG, POLIO, DPT-HB-HIB" required>
                <small style="color:#9CA3AF;font-size:11px;">Nama akan otomatis diubah ke huruf kapital</small>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Usia Standar (bulan) <span style="color:red">*</span></label>
                    <input type="number" id="usia_standar_bulan" name="usia_standar_bulan" min="0" max="60" placeholder="Contoh: 2" required>
                </div>
                <div class="form-group">
                    <label>Usia Minimal (bulan)</label>
                    <input type="number" id="usia_minimal_bulan" name="usia_minimal_bulan" min="0" max="60" placeholder="Contoh: 0">
                </div>
                <div class="form-group">
                    <label>Usia Maksimal (bulan)</label>
                    <input type="number" id="usia_maksimal_bulan" name="usia_maksimal_bulan" min="0" max="60" placeholder="Contoh: 60">
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" placeholder="Keterangan singkat">
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" id="btnSubmitVaksin"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('modalForm')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box" style="max-width:460px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Vaksin</h3>
            <button class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
        </div>
        <p style="color:#374151;margin-bottom:8px;font-size:14px;">
            Apakah Anda yakin ingin menghapus vaksin <strong id="hapusNama"></strong>?
        </p>
        <p id="hapusWarning" style="color:#EF4444;font-size:13px;margin-bottom:20px;background:#FEE2E2;padding:10px 14px;border-radius:8px;"></p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-danger" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Ya, Hapus</button>
            <button class="btn btn-outline" onclick="closeModal('modalHapus')">Batal</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let hapusId = null;
let editMode = false;

function toast(msg, type = 'success') {
    const icons = { success:'check-circle', error:'times-circle', warning:'exclamation-triangle' };
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

// ── Load Data ──────────────────────────────────────────────
async function loadVaksin() {
    const tbody = document.getElementById('vaksinTableBody');
    tbody.innerHTML = '<tr class="loading-row"><td colspan="8"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>';

    try {
        const res  = await fetch('{{ route("vaksin.list") }}', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        const list = data.data || [];

        if (!list.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fas fa-inbox"></i> Belum ada data vaksin</td></tr>';
            return;
        }

        tbody.innerHTML = list.map((v, i) => `
            <tr>
                <td>${i + 1}</td>
                <td><strong>${v.nama_vaksin}</strong></td>
                <td><span class="badge badge-info">${v.usia_standar_bulan} bln</span></td>
                <td>${v.usia_minimal_bulan ?? '-'} bln</td>
                <td>${v.usia_maksimal_bulan ?? '-'} bln</td>
                <td style="color:#64748B;font-size:13px;">${v.keterangan || '-'}</td>
                <td>
                    <span class="badge ${v.jumlah_imunisasi > 0 ? 'badge-warning' : 'badge-info'}">
                        ${v.jumlah_imunisasi} data
                    </span>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick='openEdit(${JSON.stringify(v).replace(/'/g,"&#39;")})' title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="confirmHapus(${v.id}, '${v.nama_vaksin.replace(/'/g,"\\'")}', ${v.jumlah_imunisasi})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="8"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data</td></tr>';
        toast('Gagal memuat data vaksin', 'error');
    }
}

// ── Tambah ─────────────────────────────────────────────────
function openModalTambah() {
    editMode = false;
    document.getElementById('vaksinId').value = '';
    document.getElementById('formVaksin').reset();
    document.getElementById('modalFormTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:#246BCE;"></i> Tambah Vaksin Baru';
    document.getElementById('btnSubmitVaksin').innerHTML = '<i class="fas fa-save"></i> Simpan';
    openModal('modalForm');
}

// ── Edit ───────────────────────────────────────────────────
function openEdit(v) {
    editMode = true;
    document.getElementById('vaksinId').value            = v.id;
    document.getElementById('nama_vaksin').value         = v.nama_vaksin;
    document.getElementById('usia_standar_bulan').value  = v.usia_standar_bulan;
    document.getElementById('usia_minimal_bulan').value  = v.usia_minimal_bulan ?? '';
    document.getElementById('usia_maksimal_bulan').value = v.usia_maksimal_bulan ?? '';
    document.getElementById('keterangan').value          = v.keterangan ?? '';
    document.getElementById('modalFormTitle').innerHTML  = '<i class="fas fa-edit" style="color:#F59E0B;"></i> Edit Vaksin';
    document.getElementById('btnSubmitVaksin').innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
    openModal('modalForm');
}

// ── Submit Form ────────────────────────────────────────────
async function submitVaksin(e) {
    e.preventDefault();
    const id  = document.getElementById('vaksinId').value;
    const btn = document.getElementById('btnSubmitVaksin');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const body = {
        nama_vaksin:         document.getElementById('nama_vaksin').value,
        usia_standar_bulan:  parseInt(document.getElementById('usia_standar_bulan').value) || 0,
        usia_minimal_bulan:  parseInt(document.getElementById('usia_minimal_bulan').value) || 0,
        usia_maksimal_bulan: parseInt(document.getElementById('usia_maksimal_bulan').value) || 60,
        keterangan:          document.getElementById('keterangan').value,
    };

    try {
        const url    = id ? `/web/vaksin/${id}` : '{{ route("vaksin.store") }}';
        const method = id ? 'PUT' : 'POST';

        const res  = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(body),
        });
        const data = await res.json();

        if (data.success) {
            toast(data.message, 'success');
            closeModal('modalForm');
            loadVaksin();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = id ? '<i class="fas fa-save"></i> Simpan Perubahan' : '<i class="fas fa-save"></i> Simpan';
    }
}

// ── Hapus ──────────────────────────────────────────────────
function confirmHapus(id, nama, jumlahImunisasi) {
    hapusId = id;
    document.getElementById('hapusNama').textContent = nama;
    document.getElementById('hapusWarning').innerHTML = jumlahImunisasi > 0
        ? `⚠️ Vaksin ini memiliki <strong>${jumlahImunisasi} data imunisasi</strong> yang akan ikut terhapus!`
        : '✅ Vaksin ini belum memiliki data imunisasi, aman untuk dihapus.';
    document.getElementById('hapusWarning').style.background = jumlahImunisasi > 0 ? '#FEE2E2' : '#D1FAE5';
    document.getElementById('hapusWarning').style.color      = jumlahImunisasi > 0 ? '#991B1B' : '#065F46';
    openModal('modalHapus');
}

document.getElementById('btnKonfirmasiHapus').addEventListener('click', async () => {
    if (!hapusId) return;
    const btn = document.getElementById('btnKonfirmasiHapus');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

    try {
        const res  = await fetch(`/web/vaksin/${hapusId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        const data = await res.json();

        if (data.success) {
            let msg = data.message;
            if (data.deleted_imunisasi_count > 0) {
                msg += ` (${data.deleted_imunisasi_count} data imunisasi ikut dihapus)`;
            }
            toast(msg, 'success');
            closeModal('modalHapus');
            loadVaksin();
        } else {
            toast(data.message || 'Gagal menghapus', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus';
        hapusId = null;
    }
});

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});

document.addEventListener('DOMContentLoaded', loadVaksin);
</script>
@endsection
