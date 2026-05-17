@extends('layouts.lansia')
@section('title', 'Pengaturan Lansia')

@section('styles')
<style>
    body { background: linear-gradient(135deg,#D1FAE5 0%,#A7F3D0 50%,#6EE7B7 100%) !important; }
    .card { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#065F46; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

    /* Info posyandu */
    .posyandu-header { background:linear-gradient(135deg,#10B981,#059669); border-radius:16px; padding:28px; margin-bottom:24px; color:#fff; text-align:center; }
    .posyandu-header h2 { font-size:22px; font-weight:700; margin:0 0 6px; }
    .posyandu-header p  { font-size:14px; opacity:0.9; margin:0; }

    /* Grid */
    .cards-row { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
    @media(max-width:768px){ .cards-row{ grid-template-columns:1fr; } }
    .card-title { font-size:18px; font-weight:700; color:#065F46; margin-bottom:20px; padding-bottom:12px; border-bottom:2px solid #E5E7EB; display:flex; align-items:center; gap:10px; }

    /* Form */
    .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input { padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; transition:all 0.2s; width:100%; box-sizing:border-box; }
    .form-group input:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.1); }
    .form-group input[readonly] { background:#F8FAFC; color:#94A3B8; cursor:not-allowed; }
    .otp-input { text-align:center; font-size:24px; letter-spacing:8px; font-weight:700; }

    /* Buttons */
    .btn { padding:10px 20px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#10B981; color:#fff; width:100%; justify-content:center; } .btn-primary:hover { background:#059669; }
    .btn-secondary { background:#64748B; color:#fff; width:100%; justify-content:center; margin-top:8px; } .btn-secondary:hover { background:#475569; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-outline { background:transparent; border:2px solid #10B981; color:#10B981; } .btn-outline:hover { background:#10B981; color:#fff; }
    .btn-sm { padding:5px 10px; font-size:12px; }

    /* Badges */
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-info    { background:#DBEAFE; color:#1E40AF; }
    .badge-warning { background:#FEF3C7; color:#92400E; }
    .badge-success { background:#D1FAE5; color:#065F46; }
    .badge-danger  { background:#FEE2E2; color:#991B1B; }

    /* Table */
    .table-wrapper { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:14px; }
    thead th { background:#F8FAFC; padding:12px 16px; text-align:left; font-weight:600; color:#065F46; border-bottom:2px solid #E5E7EB; white-space:nowrap; }
    tbody td { padding:12px 16px; border-bottom:1px solid #F1F5F9; color:#374151; vertical-align:middle; }
    tbody tr:hover { background:#F8FAFC; }

    /* Modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:20px; padding:28px; width:90%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid #E5E7EB; }
    .modal-title { font-size:18px; font-weight:700; color:#065F46; }
    .modal-close { background:none; border:none; font-size:24px; color:#9CA3AF; cursor:pointer; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; }

    /* Info box */
    .info-box { background:#ECFDF5; border:1px solid #A7F3D0; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#065F46; display:flex; align-items:flex-start; gap:8px; }
    .access-denied { background:#FEF2F2; border:2px solid #EF4444; border-radius:12px; padding:24px; text-align:center; }

    /* Toast */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:14px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:280px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }
</style>
@endsection

@section('content')
<div id="toast"></div>

{{-- INFO POSYANDU --}}
<div class="posyandu-header">
    <h2><i class="fas fa-hospital"></i> {{ $posyanduNama }}</h2>
    <p><i class="fas fa-map-marker-alt"></i> {{ $posyanduAlamat }}</p>
</div>

<div style="margin-bottom:20px;">
    <h1 class="page-title"><i class="fas fa-cog" style="color:#10B981;"></i> Pengaturan Lansia</h1>
    <p class="page-subtitle">Kelola profil dan keamanan akun Anda</p>
</div>

<div class="cards-row">

    {{-- CARD 1: PROFIL --}}
    <div class="card">
        <div class="card-title"><i class="fas fa-user-circle" style="color:#10B981;"></i> Profil Saya</div>
        <form id="formProfil" onsubmit="submitProfil(event)">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Nama lengkap Anda">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" name="email" placeholder="email@contoh.com">
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" id="no_telp" name="no_telp" placeholder="08xxxxxxxxxx">
            </div>
            <button type="submit" class="btn btn-primary" id="btnSaveProfil">
                <i class="fas fa-save"></i> Simpan Profil
            </button>
        </form>
    </div>

    {{-- CARD 2: GANTI PASSWORD VIA OTP --}}
    <div class="card">
        <div class="card-title"><i class="fas fa-lock" style="color:#F59E0B;"></i> Ubah Kata Sandi</div>

        <div class="info-box">
            <i class="fas fa-info-circle" style="margin-top:2px;flex-shrink:0;"></i>
            <span>Untuk keamanan, kami akan mengirimkan kode OTP ke email Anda untuk verifikasi perubahan kata sandi.</span>
        </div>

        {{-- Step 1: Request OTP --}}
        <div id="step1OTP">
            <div class="form-group">
                <label>Email Anda</label>
                <input type="email" id="emailOTP" placeholder="Memuat email..." readonly>
                <small style="color:#64748B;font-size:12px;margin-top:4px;display:block;">
                    <i class="fas fa-envelope"></i> Kode OTP akan dikirim ke email ini
                </small>
            </div>
            <button class="btn btn-primary" id="btnRequestOTP" onclick="requestOTP()">
                <i class="fas fa-paper-plane"></i> Kirim Kode OTP
            </button>
        </div>

        {{-- Step 2: Verifikasi OTP + Password Baru --}}
        <div id="step2OTP" style="display:none;">
            <div class="form-group">
                <label>Kode OTP <span style="color:red">*</span></label>
                <input type="text" id="otpCode" class="otp-input" placeholder="000000" maxlength="6" pattern="[0-9]{6}">
                <small style="color:#64748B;font-size:12px;margin-top:4px;display:block;">
                    <i class="fas fa-envelope"></i> Kode OTP telah dikirim ke email Anda (berlaku 10 menit)
                </small>
            </div>
            <div class="form-group">
                <label>Kata Sandi Baru <span style="color:red">*</span></label>
                <input type="password" id="passwordBaru" placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Kata Sandi Baru <span style="color:red">*</span></label>
                <input type="password" id="passwordBaruConfirm" placeholder="Ketik ulang kata sandi baru">
            </div>
            <button class="btn btn-primary" id="btnVerifikasiOTP" onclick="verifikasiOTP()">
                <i class="fas fa-check-circle"></i> Verifikasi OTP & Ubah Password
            </button>
            <button class="btn btn-secondary" onclick="backToStep1()">
                <i class="fas fa-arrow-left"></i> Kirim Ulang OTP
            </button>
        </div>
    </div>

</div>

{{-- CARD 3: MANAJEMEN PENGGUNA (admin only) --}}
@if(Auth::user()->role === 'admin')
<div class="card">
    <div class="card-title" style="justify-content:space-between;">
        <span><i class="fas fa-users-cog" style="color:#10B981;"></i> Manajemen Pengguna Mobile (Wali Lansia)</span>
        <button class="btn btn-outline btn-sm" onclick="loadUsers()"><i class="fas fa-sync-alt"></i> Refresh</button>
    </div>
    <p style="font-size:13px;color:#64748B;margin-bottom:16px;background:#F0FDF4;padding:10px 14px;border-radius:8px;border-left:4px solid #10B981;">
        <i class="fas fa-mobile-alt" style="color:#10B981;margin-right:6px;"></i>
        Daftar pengguna yang terdaftar melalui <strong>aplikasi mobile</strong> sebagai wali lansia.
        Hapus akun untuk memutus akses mereka ke aplikasi mobile.
    </p>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF;"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card">
    <div class="access-denied">
        <i class="fas fa-lock" style="font-size:40px;color:#EF4444;margin-bottom:12px;display:block;"></i>
        <h4 style="color:#991B1B;margin:0 0 8px;">Akses Ditolak</h4>
        <p style="color:#7F1D1D;font-size:14px;margin:0;">Fitur "Kelola Pengguna" hanya tersedia untuk Administrator.</p>
    </div>
</div>
@endif

{{-- MODAL KONFIRMASI HAPUS USER --}}
<div class="modal-overlay" id="modalHapusUser">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-user-times"></i> Hapus Pengguna</h3>
            <button class="modal-close" onclick="closeModal('modalHapusUser')">&times;</button>
        </div>
        <p style="color:#374151;margin-bottom:20px;">Hapus pengguna <strong id="hapusUserNama"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-danger btn-sm" id="btnKonfirmasiHapusUser"><i class="fas fa-trash"></i> Ya, Hapus</button>
            <button class="btn btn-outline btn-sm" onclick="closeModal('modalHapusUser')">Batal</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let hapusUserId = null;

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

// ── Load profil ────────────────────────────────────────────
async function loadCurrentUser() {
    try {
        const res  = await fetch('/lansia/web/pengaturan/current-user', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        const u    = data.data;
        if (u) {
            document.getElementById('nama_lengkap').value = u.nama_lengkap || '';
            document.getElementById('email').value        = u.email || '';
            document.getElementById('no_telp').value      = u.no_telp || '';
            document.getElementById('emailOTP').value     = u.email || '';
        }
    } catch (e) { toast('Gagal memuat profil', 'error'); }
}

// ── Simpan profil ──────────────────────────────────────────
async function submitProfil(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSaveProfil');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    try {
        const res  = await fetch('/lansia/web/pengaturan/profil', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                nama_lengkap: document.getElementById('nama_lengkap').value,
                email:        document.getElementById('email').value,
                no_telp:      document.getElementById('no_telp').value,
            }),
        });
        const data = await res.json();
        if (data.success) toast('Profil berhasil disimpan!', 'success');
        else toast(data.message || 'Gagal menyimpan', 'error');
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Profil'; }
}

// ── Ganti password via OTP ─────────────────────────────────
async function requestOTP() {
    const btn = document.getElementById('btnRequestOTP');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    try {
        const res  = await fetch('/lansia/web/pengaturan/request-otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        const data = await res.json();
        if (data.success) {
            toast(data.message, 'success');
            document.getElementById('step1OTP').style.display = 'none';
            document.getElementById('step2OTP').style.display = 'block';
        } else {
            toast(data.message || 'Gagal mengirim OTP', 'error');
        }
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Kode OTP'; }
}

async function verifikasiOTP() {
    const otp     = document.getElementById('otpCode').value.trim();
    const pass    = document.getElementById('passwordBaru').value;
    const confirm = document.getElementById('passwordBaruConfirm').value;

    if (!otp || otp.length !== 6) { toast('Kode OTP harus 6 digit', 'warning'); return; }
    if (pass.length < 6)          { toast('Password minimal 6 karakter', 'warning'); return; }
    if (pass !== confirm)          { toast('Konfirmasi password tidak cocok', 'warning'); return; }

    const btn = document.getElementById('btnVerifikasiOTP');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';
    try {
        const res  = await fetch('/lansia/web/pengaturan/verifikasi-otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                otp:                      otp,
                password_baru:            pass,
                password_baru_confirmation: confirm,
            }),
        });
        const data = await res.json();
        if (data.success) {
            toast('Password berhasil diubah!', 'success');
            backToStep1();
            document.getElementById('otpCode').value      = '';
            document.getElementById('passwordBaru').value = '';
            document.getElementById('passwordBaruConfirm').value = '';
        } else {
            toast(data.message || 'Gagal verifikasi OTP', 'error');
        }
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> Verifikasi OTP & Ubah Password'; }
}

function backToStep1() {
    document.getElementById('step1OTP').style.display = 'block';
    document.getElementById('step2OTP').style.display = 'none';
}

// ── Manajemen pengguna (admin) ─────────────────────────────
@if(Auth::user()->role === 'admin')
function roleBadge(role) {
    const map = {
        admin:            'danger',
        kader:            'info',
        orangtua:         'warning',
        wali_lansia:      'success',
        orangtua_lansia:  'info',
    };
    const label = {
        admin:            'Admin',
        kader:            'Kader',
        orangtua:         'Orang Tua',
        wali_lansia:      'Wali Lansia',
        orangtua_lansia:  'Wali Lansia & Orang Tua',
    };
    return `<span class="badge badge-${map[role]||'info'}">${label[role]||role}</span>`;
}

async function loadUsers() {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF;"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';
    try {
        const res  = await fetch('/lansia/web/pengaturan/users', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        const list = data.data || [];

        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF;"><i class="fas fa-inbox"></i> Tidak ada pengguna</td></tr>';
            return;
        }

        const currentId = {{ Auth::id() }};
        tbody.innerHTML = list.map((u, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>
                    <strong>${u.username || '-'}</strong>
                    ${u.id === currentId ? '<span class="badge badge-success" style="margin-left:6px;font-size:10px;">Anda</span>' : ''}
                </td>
                <td>${u.email || '-'}</td>
                <td>${roleBadge(u.role)}</td>
                <td>
                    ${u.id !== currentId ? `
                    <button class="btn btn-danger btn-sm" onclick="confirmHapusUser(${u.id}, '${(u.username||'').replace(/'/g,"\\'")}')">
                        <i class="fas fa-trash"></i>
                    </button>` : '<span style="color:#9CA3AF;font-size:12px;">—</span>'}
                </td>
            </tr>`).join('');
    } catch (e) { toast('Gagal memuat data pengguna', 'error'); }
}

function confirmHapusUser(id, nama) {
    hapusUserId = id;
    document.getElementById('hapusUserNama').textContent = nama;
    openModal('modalHapusUser');
}

document.getElementById('btnKonfirmasiHapusUser').addEventListener('click', async () => {
    if (!hapusUserId) return;
    const btn = document.getElementById('btnKonfirmasiHapusUser');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    try {
        const res  = await fetch(`/lansia/web/pengaturan/users/${hapusUserId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (data.success) { toast(data.message, 'success'); closeModal('modalHapusUser'); loadUsers(); }
        else toast(data.message || 'Gagal menghapus', 'error');
    } catch (e) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus'; hapusUserId = null; }
});

document.addEventListener('DOMContentLoaded', () => { loadCurrentUser(); loadUsers(); });
@else
document.addEventListener('DOMContentLoaded', loadCurrentUser);
@endif

document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});
</script>
@endsection
