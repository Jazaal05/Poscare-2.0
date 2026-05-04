@extends('layouts.lansia')
@section('title', 'Jadwal Lansia')

@section('styles')
<style>
    body { background:linear-gradient(135deg,#D1FAE5 0%,#A7F3D0 50%,#6EE7B7 100%) !important; }
    .container { max-width:1340px; margin:0 auto; padding:16px 20px; }
    .card { background:#fff; border-radius:16px; padding:20px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#065F46; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

    .btn { padding:8px 16px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#10B981; color:#fff; } .btn-primary:hover { background:#059669; }
    .btn-warning { background:#F59E0B; color:#fff; } .btn-warning:hover { background:#D97706; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-outline { background:transparent; border:2px solid #10B981; color:#10B981; } .btn-outline:hover { background:#10B981; color:#fff; }

    .calendar-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:8px; margin-top:20px; }
    .calendar-header { text-align:center; font-weight:700; color:#065F46; padding:12px; background:#F0FDF4; border-radius:8px; }
    .calendar-day { min-height:100px; padding:8px; border:2px solid #E5E7EB; border-radius:8px; background:#fff; cursor:pointer; transition:all 0.2s; position:relative; }
    .calendar-day:hover { border-color:#10B981; box-shadow:0 4px 12px rgba(16,185,129,0.2); }
    .calendar-day.today { border-color:#10B981; background:#F0FDF4; }
    .calendar-day.other-month { opacity:0.3; }
    .day-number { font-weight:700; color:#065F46; margin-bottom:4px; }
    .event-item { font-size:11px; padding:4px 6px; background:#DBEAFE; color:#1E40AF; border-radius:4px; margin-bottom:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .event-item.lansia { background:#D1FAE5; color:#065F46; }

    .month-nav { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .month-title { font-size:24px; font-weight:700; color:#065F46; }

    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:20px; padding:28px; width:90%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); }
    .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid #E5E7EB; }
    .modal-title { font-size:20px; font-weight:700; color:#065F46; }
    .modal-close { background:none; border:none; font-size:24px; color:#9CA3AF; cursor:pointer; transition:all 0.2s; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; transform:rotate(90deg); }

    .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select, .form-group textarea { padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; transition:all 0.2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.1); }

    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:14px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:280px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; }
    .toast-error   { background:#EF4444; }

    .jadwal-list { display:flex; flex-direction:column; gap:12px; }
    .jadwal-card { padding:16px; border:2px solid #E5E7EB; border-radius:12px; background:#F9FAFB; transition:all 0.2s; }
    .jadwal-card:hover { border-color:#10B981; background:#F0FDF4; }
    .jadwal-title { font-weight:700; color:#065F46; margin-bottom:4px; }
    .jadwal-meta { font-size:12px; color:#64748B; display:flex; gap:12px; flex-wrap:wrap; }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div class="page-header">
    <h1 class="page-title">Jadwal Lansia</h1>
    <p class="page-subtitle"><i class="fas fa-calendar-alt" style="color:#10B981;margin-right:6px;"></i> Jadwal Kegiatan dan Pemeriksaan Lansia</p>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3 style="font-size:18px;font-weight:700;color:#065F46;"><i class="fas fa-calendar"></i> Kalender Jadwal</h3>
        <button class="btn btn-primary" onclick="openModalTambah()"><i class="fas fa-plus"></i> Tambah Jadwal</button>
    </div>

    <div class="month-nav">
        <button class="btn btn-outline" onclick="prevMonth()"><i class="fas fa-chevron-left"></i> Bulan Sebelumnya</button>
        <h2 class="month-title" id="monthTitle">Loading...</h2>
        <button class="btn btn-outline" onclick="nextMonth()">Bulan Berikutnya <i class="fas fa-chevron-right"></i></button>
    </div>

    <div class="calendar-grid" id="calendarGrid">
        <div class="calendar-header">Min</div>
        <div class="calendar-header">Sen</div>
        <div class="calendar-header">Sel</div>
        <div class="calendar-header">Rab</div>
        <div class="calendar-header">Kam</div>
        <div class="calendar-header">Jum</div>
        <div class="calendar-header">Sab</div>
    </div>
</div>

<div class="card">
    <h3 style="font-size:18px;font-weight:700;color:#065F46;margin-bottom:16px;"><i class="fas fa-list"></i> Daftar Jadwal Bulan Ini</h3>
    <div class="jadwal-list" id="jadwalList">
        <p style="text-align:center;color:#9CA3AF;padding:20px;">Memuat jadwal...</p>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT --}}
<div class="modal-overlay" id="modalJadwal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle"><i class="fas fa-plus"></i> Tambah Jadwal</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formJadwal" onsubmit="submitJadwal(event)">
            <input type="hidden" id="jadwalId">
            <div class="form-group">
                <label for="judulKegiatan">Judul Kegiatan <span style="color:red">*</span></label>
                <input type="text" id="judulKegiatan" name="judul_kegiatan" placeholder="Contoh: Pemeriksaan Kesehatan Rutin" required>
            </div>
            <div class="form-group">
                <label for="tanggalKegiatan">Tanggal <span style="color:red">*</span></label>
                <input type="date" id="tanggalKegiatan" name="tanggal_kegiatan" required>
            </div>
            <div class="form-group">
                <label for="waktuMulai">Waktu Mulai <span style="color:red">*</span></label>
                <input type="text" id="waktuMulai" name="waktu_mulai" required
                    placeholder="Contoh: 08:00 atau 14:30"
                    maxlength="5"
                    pattern="^([01]\d|2[0-3]):[0-5]\d$"
                    oninput="formatWaktuInput(this)"
                    autocomplete="off"
                    style="letter-spacing:2px;font-size:16px;font-family:monospace;">
                <small style="color:#64748B;font-size:12px;">Format 24 jam: 00:00 – 23:59 WIB</small>
            </div>
            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi" placeholder="Contoh: Posyandu Melati">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan..."></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
                <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-info-circle"></i> Detail Jadwal</h3>
            <button class="modal-close" onclick="closeModalDetail()">&times;</button>
        </div>
        <div id="detailContent"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <button class="btn btn-warning" onclick="editFromDetail()"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn btn-danger" onclick="hapusFromDetail()"><i class="fas fa-trash"></i> Hapus</button>
            <button class="btn btn-outline" onclick="closeModalDetail()">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentDate = new Date();
let allJadwal = [];
let currentJadwalId = null;

// ── Format waktu otomatis saat mengetik ──────────────────────
function formatWaktuInput(input) {
    // Hapus semua karakter selain angka
    let val = input.value.replace(/\D/g, '');

    // Batasi 4 digit
    if (val.length > 4) val = val.slice(0, 4);

    // Sisipkan titik dua setelah 2 digit pertama
    if (val.length >= 3) {
        val = val.slice(0, 2) + ':' + val.slice(2);
    }

    input.value = val;

    // Validasi range saat sudah 5 karakter (HH:mm)
    if (val.length === 5) {
        const [hh, mm] = val.split(':').map(Number);
        if (hh > 23 || mm > 59) {
            input.style.borderColor = '#EF4444';
            input.title = 'Jam harus 00-23, menit harus 00-59';
        } else {
            input.style.borderColor = '#10B981';
            input.title = '';
        }
    } else {
        input.style.borderColor = '';
    }
}

function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':'times-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

async function loadJadwal() {
    try {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;
        const res = await fetch(`/lansia/api/jadwal?year=${year}&month=${month}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (data.success) {
            allJadwal = data.data;
            renderCalendar();
            renderJadwalList();
        }
    } catch (e) {
        console.error('Gagal memuat jadwal:', e);
    }
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    document.getElementById('monthTitle').textContent = 
        new Date(year, month).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();
    
    const grid = document.getElementById('calendarGrid');
    const headers = grid.querySelectorAll('.calendar-header');
    grid.innerHTML = '';
    headers.forEach(h => grid.appendChild(h));
    
    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = prevMonthDays - i;
        const div = document.createElement('div');
        div.className = 'calendar-day other-month';
        div.innerHTML = `<div class="day-number">${day}</div>`;
        grid.appendChild(div);
    }
    
    // Current month days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const div = document.createElement('div');
        const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
        div.className = 'calendar-day' + (isToday ? ' today' : '');
        
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const events = allJadwal.filter(j => (j.tanggal_kegiatan || j.tanggal) === dateStr);
        
        let html = `<div class="day-number">${day}</div>`;
        events.forEach(e => {
            html += `<div class="event-item lansia" onclick="openDetail(${e.id})" title="${e.judul_kegiatan || e.nama_kegiatan}">${e.judul_kegiatan || e.nama_kegiatan}</div>`;
        });
        
        div.innerHTML = html;
        div.onclick = (evt) => {
            if (!evt.target.classList.contains('event-item')) {
                openModalTambah(dateStr);
            }
        };
        grid.appendChild(div);
    }
    
    // Next month days
    const totalCells = grid.children.length - 7;
    const remainingCells = 42 - totalCells;
    for (let day = 1; day <= remainingCells; day++) {
        const div = document.createElement('div');
        div.className = 'calendar-day other-month';
        div.innerHTML = `<div class="day-number">${day}</div>`;
        grid.appendChild(div);
    }
}

function renderJadwalList() {
    const container = document.getElementById('jadwalList');
    if (!allJadwal.length) {
        container.innerHTML = '<p style="text-align:center;color:#9CA3AF;padding:20px;">Tidak ada jadwal bulan ini</p>';
        return;
    }
    
    container.innerHTML = allJadwal.map(j => `
        <div class="jadwal-card" onclick="openDetail(${j.id})">
            <div class="jadwal-title">${j.judul_kegiatan || j.nama_kegiatan || '-'}</div>
            <div class="jadwal-meta">
                <span><i class="fas fa-calendar"></i> ${new Date((j.tanggal_kegiatan || j.tanggal) + 'T00:00:00').toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'})}</span>
                <span><i class="fas fa-clock"></i> ${(j.waktu_mulai || '').substring(0,5)} WIB</span>
                ${j.lokasi ? `<span><i class="fas fa-map-marker-alt"></i> ${j.lokasi}</span>` : ''}
            </div>
        </div>
    `).join('');
}

function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    loadJadwal();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    loadJadwal();
}

function openModalTambah(date = null) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Tambah Jadwal';
    document.getElementById('formJadwal').reset();
    document.getElementById('jadwalId').value = '';
    // Set minimum tanggal = hari ini
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggalKegiatan').min = today;
    if (date) document.getElementById('tanggalKegiatan').value = date;
    document.getElementById('modalJadwal').classList.add('active');
}

function closeModal() {
    document.getElementById('modalJadwal').classList.remove('active');
}

async function submitJadwal(e) {
    e.preventDefault();
    const id = document.getElementById('jadwalId').value;
    const btn = document.getElementById('btnSubmit');
    const form = document.getElementById('formJadwal');

    // Validasi format waktu manual
    const waktu = document.getElementById('waktuMulai').value;
    const waktuRegex = /^([01]\d|2[0-3]):[0-5]\d$/;
    if (!waktuRegex.test(waktu)) {
        toast('Format waktu tidak valid. Gunakan format 24 jam, contoh: 08:00 atau 14:30', 'error');
        document.getElementById('waktuMulai').focus();
        return;
    }

    const fd = new FormData(form);
    const payload = {};
    fd.forEach((v, k) => { if (v !== '') payload[k] = v; });
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    try {
        const url = id ? `/lansia/api/jadwal/${id}` : '/lansia/api/jadwal';
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        
        if (data.success) {
            toast(data.message, 'success');
            closeModal();
            loadJadwal();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    }
}

async function openDetail(id) {
    currentJadwalId = id;
    document.getElementById('detailContent').innerHTML = '<p style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin"></i> Memuat...</p>';
    document.getElementById('modalDetail').classList.add('active');
    
    try {
        const res = await fetch(`/lansia/api/jadwal/${id}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        
        if (data.success) {
            const j = data.data;
            document.getElementById('detailContent').innerHTML = `
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div><strong>Judul:</strong> ${j.judul_kegiatan || j.nama_kegiatan || '-'}</div>
                    <div><strong>Tanggal:</strong> ${new Date(((j.tanggal_kegiatan || j.tanggal) + 'T00:00:00')).toLocaleDateString('id-ID', {weekday:'long', year:'numeric', month:'long', day:'numeric'})}</div>
                    <div><strong>Waktu:</strong> ${(j.waktu_mulai || '').substring(0,5)} WIB</div>
                    ${j.lokasi ? `<div><strong>Lokasi:</strong> ${j.lokasi}</div>` : ''}
                    ${j.keterangan ? `<div><strong>Keterangan:</strong> ${j.keterangan}</div>` : ''}
                </div>
            `;
        }
    } catch (e) {
        document.getElementById('detailContent').innerHTML = '<p style="color:red;">Gagal memuat detail</p>';
    }
}

function closeModalDetail() {
    document.getElementById('modalDetail').classList.remove('active');
    currentJadwalId = null;
}

async function editFromDetail() {
    if (!currentJadwalId) return;
    
    const jadwal = allJadwal.find(j => j.id === currentJadwalId);
    if (!jadwal) return;
    
    closeModalDetail();
    
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Jadwal';
    document.getElementById('jadwalId').value = jadwal.id;
    document.getElementById('judulKegiatan').value = jadwal.judul_kegiatan || jadwal.nama_kegiatan || '';
    document.getElementById('tanggalKegiatan').min = today;
    document.getElementById('tanggalKegiatan').value = jadwal.tanggal_kegiatan || jadwal.tanggal || '';
    // Pastikan format HH:mm (potong detik jika ada)
    document.getElementById('waktuMulai').value = (jadwal.waktu_mulai || '').substring(0, 5);
    document.getElementById('lokasi').value = jadwal.lokasi || '';
    document.getElementById('keterangan').value = jadwal.keterangan || '';
    document.getElementById('modalJadwal').classList.add('active');
}

async function hapusFromDetail() {
    if (!currentJadwalId) return;
    if (!confirm('Yakin ingin menghapus jadwal ini?')) return;
    
    try {
        const res = await fetch(`/lansia/api/jadwal/${currentJadwalId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        
        if (data.success) {
            toast(data.message, 'success');
            closeModalDetail();
            loadJadwal();
        } else {
            toast(data.message || 'Gagal menghapus', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    }
}

document.addEventListener('DOMContentLoaded', loadJadwal);
</script>
@endsection
