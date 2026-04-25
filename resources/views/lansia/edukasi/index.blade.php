@extends('layouts.lansia')
@section('title', 'Edukasi Lansia')

@section('styles')
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }

    /* ── Tab Nav ── */
    .tab-nav-top { display:flex; gap:0; margin-bottom:28px; background:#fff; border-radius:12px;
                   box-shadow:0 2px 8px rgba(0,0,0,0.06); overflow:hidden; }
    .tab-nav-top .tab-btn { flex:1; padding:14px 20px; border:none; background:none; font-size:14px;
                            font-weight:600; color:#64748B; cursor:pointer; transition:all 0.2s;
                            display:inline-flex; align-items:center; justify-content:center; gap:8px; }
    .tab-nav-top .tab-btn.active { background:#246BCE; color:#fff; }
    .tab-nav-top .tab-btn:hover:not(.active) { background:#F1F5F9; color:#246BCE; }
    .tab-content { display:none; } .tab-content.active { display:block; }

    /* ── Konten Edukasi Grid ── */
    .card-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
    .edu-card { background:#fff; border-radius:14px; box-shadow:0 4px 16px rgba(16,24,40,0.07);
                overflow:hidden; transition:transform 0.2s,box-shadow 0.2s; display:flex; flex-direction:column; }
    .edu-card:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(16,24,40,0.12); }
    .edu-thumb-wrap { position:relative; width:100%; height:170px; overflow:hidden; background:#E5E7EB; }
    .edu-thumb-wrap img { width:100%; height:100%; object-fit:cover; display:block; }
    .edu-thumb-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:52px; }
    .platform-badge { position:absolute; top:10px; left:10px; padding:3px 10px; border-radius:20px;
                      font-size:11px; font-weight:700; display:flex; align-items:center; gap:5px; }
    .pb-youtube   { background:#FF0000; color:#fff; }
    .pb-tiktok    { background:#000; color:#fff; }
    .pb-facebook  { background:#1877F2; color:#fff; }
    .pb-instagram { background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color:#fff; }
    .pb-article   { background:#246BCE; color:#fff; }

    .edu-card-body { padding:14px 16px; flex:1; display:flex; flex-direction:column; gap:8px; }
    .edu-cat-label { font-size:11px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:0.5px; }
    .edu-card-title { font-size:14px; font-weight:700; color:#1E3A5F; line-height:1.4;
                      display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .edu-card-date { font-size:12px; color:#94A3B8; display:flex; align-items:center; gap:5px; margin-top:auto; }
    .btn-open { display:flex; align-items:center; justify-content:center; gap:7px; padding:9px 14px;
                background:#246BCE; color:#fff; border:none; border-radius:8px; font-size:13px;
                font-weight:600; cursor:pointer; text-decoration:none; transition:all 0.2s; margin-top:10px; }
    .btn-open:hover { background:#1D58A8; transform:translateY(-1px); }

    /* ── Kelola Konten ── */
    .kelola-card { background:#fff; border-radius:16px; padding:28px; box-shadow:0 4px 16px rgba(16,24,40,0.06); margin-bottom:24px; }
    .kelola-title { font-size:1.15rem; font-weight:700; color:#1E3A5F; margin-bottom:20px;
                    display:flex; align-items:center; gap:10px; }
    .kelola-title i { color:#246BCE; }

    /* Form tambah konten */
    .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:16px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select, .form-group textarea {
        padding:10px 14px; border:1px solid #E2E8F0; border-radius:8px; font-size:14px;
        transition:all 0.2s; width:100%; box-sizing:border-box; background:#fff; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline:none; border-color:#246BCE; box-shadow:0 0 0 3px rgba(36,107,206,0.1); }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media(max-width:768px){ .form-grid{ grid-template-columns:1fr; } }
    .platform-hint { font-size:12px; color:#64748B; margin-top:4px; display:flex; align-items:center; gap:5px; }
    .platform-hint.warn { color:#F59E0B; }
    .platform-hint.info { color:#246BCE; }

    /* Daftar konten di kelola */
    .konten-list-item { display:flex; align-items:center; gap:14px; padding:14px 0;
                        border-bottom:1px solid #F1F5F9; }
    .konten-list-item:last-child { border-bottom:none; }
    .konten-thumb-sm { width:60px; height:44px; border-radius:6px; object-fit:cover;
                       background:#E5E7EB; flex-shrink:0; }
    .konten-thumb-sm-placeholder { width:60px; height:44px; border-radius:6px; background:#E5E7EB;
                                   display:flex; align-items:center; justify-content:center;
                                   font-size:20px; flex-shrink:0; }
    .konten-info { flex:1; min-width:0; }
    .konten-title { font-size:14px; font-weight:600; color:#1E3A5F;
                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .konten-meta { font-size:12px; color:#64748B; margin-top:2px; }
    .konten-url { font-size:11px; color:#246BCE; white-space:nowrap; overflow:hidden;
                  text-overflow:ellipsis; display:block; max-width:300px; }
    .konten-actions { display:flex; gap:6px; flex-shrink:0; }

    /* Buttons */
    .btn { padding:8px 16px; border:none; border-radius:8px; font-size:13px; font-weight:600;
           cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn:hover { transform:translateY(-1px); }
    .btn-primary { background:#246BCE; color:#fff; } .btn-primary:hover { background:#1D58A8; }
    .btn-success { background:#10B981; color:#fff; } .btn-success:hover { background:#059669; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-outline { background:transparent; border:2px solid #246BCE; color:#246BCE; }
    .btn-outline:hover { background:#246BCE; color:#fff; }
    .btn-sm { padding:5px 10px; font-size:12px; }

    /* Filter */
    .filter-row { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; margin-bottom:20px; }
    .filter-group { display:flex; flex-direction:column; gap:5px; }
    .filter-group label { font-size:12px; font-weight:600; color:#374151; }
    .filter-group select { padding:8px 12px; border:1px solid #E2E8F0; border-radius:8px; font-size:13px; }

    /* Toast */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600;
                  box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease;
                  display:flex; align-items:center; gap:10px; min-width:260px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }

    .empty-state { text-align:center; padding:50px 20px; color:#9CA3AF; grid-column:1/-1; }
    .empty-state i { font-size:3rem; margin-bottom:12px; display:block; }
    .loading-center { text-align:center; padding:40px; color:#9CA3AF; }
</style>
@endsection

@section('content')
<div id="toast"></div>

{{-- Tab Nav --}}
<div class="tab-nav-top">
    <button class="tab-btn active" id="tabBtnKonten" onclick="switchTab('konten')">
        <i class="fas fa-play-circle"></i> Konten Edukasi
    </button>
    @if(Auth::user()->role === 'admin')
    <button class="tab-btn" id="tabBtnKelola" onclick="switchTab('kelola')">
        <i class="fas fa-cog"></i> Kelola Konten
    </button>
    @endif
</div>

{{-- ===== TAB 1: KONTEN EDUKASI ===== --}}
<div id="tab-konten" class="tab-content active">
    <div class="filter-row">
        <div class="filter-group">
            <label>Platform</label>
            <select id="filterPlatform" onchange="loadEdukasi()">
                <option value="">Semua Platform</option>
                <option value="youtube">YouTube</option>
                <option value="tiktok">TikTok</option>
                <option value="facebook">Facebook</option>
                <option value="instagram">Instagram</option>
                <option value="article">Artikel</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Kategori</label>
            <select id="filterCategory" onchange="loadEdukasi()">
                <option value="">Semua Kategori</option>
                <option value="gizi">Gizi &amp; Nutrisi</option>
                <option value="tumbuh-kembang">Tumbuh Kembang</option>
                <option value="kesehatan">Kesehatan</option>
                <option value="imunisasi">Imunisasi</option>
                <option value="tips">Tips Parenting</option>
            </select>
        </div>
    </div>
    <div id="edukasiGrid" class="card-grid">
        <div class="loading-center" style="grid-column:1/-1;">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat konten...
        </div>
    </div>
</div>

{{-- ===== TAB 2: KELOLA KONTEN (admin only) ===== --}}
@if(Auth::user()->role === 'admin')
<div id="tab-kelola" class="tab-content">

    {{-- Form Tambah Konten --}}
    <div class="kelola-card">
        <h2 class="kelola-title"><i class="fas fa-plus-circle"></i> Tambah Konten Edukasi</h2>
        <form id="formEdukasi" onsubmit="submitEdukasi(event)">
            <div class="form-grid">
                <div class="form-group">
                    <label>Platform <span style="color:red">*</span></label>
                    <select id="platform" onchange="onPlatformChange()" required>
                        <option value="">-- Pilih Platform --</option>
                        <option value="youtube">YouTube</option>
                        <option value="tiktok">TikTok</option>
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="article">Artikel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kategori <span style="color:red">*</span></label>
                    <select id="category" required>
                        <option value="">Pilih Kategori...</option>
                        <option value="gizi">Gizi &amp; Nutrisi</option>
                        <option value="tumbuh-kembang">Tumbuh Kembang</option>
                        <option value="kesehatan">Kesehatan</option>
                        <option value="imunisasi">Imunisasi</option>
                        <option value="tips">Tips Parenting</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Tautan Konten <span style="color:red">*</span></label>
                <input type="url" id="url" placeholder="https://..." required>
                <div id="platformHint" class="platform-hint" style="display:none;"></div>
            </div>

            <div class="form-group">
                <label>Judul Konten <span style="color:red">*</span></label>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="title" placeholder="Judul konten edukasi" required style="flex:1;">
                    <button type="button" class="btn btn-outline btn-sm" id="btnFetch" onclick="fetchInfo()">
                        <i class="fas fa-magic"></i> Auto-fetch
                    </button>
                </div>
                <small class="platform-hint info"><i class="fas fa-info-circle"></i> Judul dan gambar thumbnail akan diambil otomatis dari link (khusus YouTube &amp; Artikel)</small>
            </div>

            <div class="form-group">
                <label>URL Thumbnail <small style="color:#94A3B8;">(opsional)</small></label>
                <input type="url" id="thumbnail" placeholder="https://... (opsional, kosongkan untuk auto)">
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="submit" class="btn btn-success" id="btnSubmit" style="flex:1;justify-content:center;">
                    <i class="fas fa-share-alt"></i> Bagikan Konten
                </button>
                <button type="reset" class="btn btn-outline" onclick="resetForm()">
                    <i class="fas fa-times"></i> Cancel / Reset
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Konten yang Dibagikan --}}
    <div class="kelola-card">
        <h2 class="kelola-title"><i class="fas fa-list"></i> Konten yang Dibagikan</h2>
        <div id="kontenList">
            <div class="loading-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
// ── Helpers ────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const icons = { success:'check-circle', error:'times-circle', warning:'exclamation-triangle' };
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

function switchTab(tab) {
    ['konten','kelola'].forEach(t => {
        const el = document.getElementById('tab-' + t);
        const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
        if (el)  el.classList.toggle('active', t === tab);
        if (btn) btn.classList.toggle('active', t === tab);
    });
    if (tab === 'kelola') loadKontenList();
}

// ── Platform config ────────────────────────────────────────
const PLATFORM_CONFIG = {
    youtube:   { icon:'fab fa-youtube',   badgeClass:'pb-youtube',   label:'YOUTUBE',   btnText:'Buka di YouTube',   placeholder:'https://www.youtube.com/watch?v=...' },
    tiktok:    { icon:'fab fa-tiktok',    badgeClass:'pb-tiktok',    label:'TIKTOK',    btnText:'Buka di TikTok',    placeholder:'https://www.tiktok.com/@user/video/...' },
    facebook:  { icon:'fab fa-facebook',  badgeClass:'pb-facebook',  label:'FACEBOOK',  btnText:'Buka di Facebook',  placeholder:'https://www.facebook.com/watch?v=... (hanya konten publik)' },
    instagram: { icon:'fab fa-instagram', badgeClass:'pb-instagram', label:'INSTAGRAM', btnText:'Buka di Instagram', placeholder:'https://www.instagram.com/p/... (hanya konten publik)' },
    article:   { icon:'fas fa-newspaper', badgeClass:'pb-article',   label:'ARTIKEL',   btnText:'Baca Artikel',      placeholder:'https://...' },
};

const PLATFORM_HINTS = {
    youtube:   { cls:'info', text:'<i class="fas fa-info-circle"></i> Format: youtube.com/watch?v=... | youtu.be/... | youtube.com/shorts/...' },
    tiktok:    { cls:'info', text:'<i class="fas fa-info-circle"></i> Format: tiktok.com/@username/video/... (pastikan video publik)' },
    facebook:  { cls:'warn', text:'<i class="fas fa-exclamation-triangle"></i> HANYA konten Facebook publik yang dapat diakses. Konten privat tidak akan bisa dibuka.' },
    instagram: { cls:'warn', text:'<i class="fas fa-exclamation-triangle"></i> HANYA konten Instagram publik yang dapat diakses. Akun privat tidak akan bisa dibuka.' },
    article:   { cls:'info', text:'<i class="fas fa-info-circle"></i> Masukkan URL artikel dari website manapun' },
};

function onPlatformChange() {
    const platform = document.getElementById('platform').value;
    const urlInput = document.getElementById('url');
    const hint     = document.getElementById('platformHint');
    const cfg      = PLATFORM_CONFIG[platform];
    const hintCfg  = PLATFORM_HINTS[platform];

    if (cfg) {
        urlInput.placeholder = cfg.placeholder;
    }
    if (hintCfg) {
        hint.className = `platform-hint ${hintCfg.cls}`;
        hint.innerHTML = hintCfg.text;
        hint.style.display = 'flex';
    } else {
        hint.style.display = 'none';
    }
}

// ── Format tanggal ─────────────────────────────────────────
function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
}

// ── Build card konten ──────────────────────────────────────
function buildEduCard(item) {
    const cfg = PLATFORM_CONFIG[item.platform] || PLATFORM_CONFIG.article;
    const catLabel = {
        'gizi':'GIZI & NUTRISI', 'tumbuh-kembang':'TUMBUH KEMBANG',
        'kesehatan':'KESEHATAN', 'imunisasi':'IMUNISASI', 'tips':'TIPS PARENTING'
    }[item.category] || (item.category || '').toUpperCase();

    const thumbHtml = item.thumbnail
        ? `<img src="${item.thumbnail}" alt="${item.title}" onerror="this.parentElement.innerHTML='<div class=\\'edu-thumb-placeholder\\'><i class=\\'${cfg.icon}\\'></i></div>'">`
        : `<div class="edu-thumb-placeholder"><i class="${cfg.icon}"></i></div>`;

    return `
        <div class="edu-card">
            <div class="edu-thumb-wrap">
                ${thumbHtml}
                <span class="platform-badge ${cfg.badgeClass}">
                    <i class="${cfg.icon}"></i> ${cfg.label}
                </span>
            </div>
            <div class="edu-card-body">
                <div class="edu-cat-label">${catLabel}</div>
                <div class="edu-card-title">${item.title}</div>
                <div class="edu-card-date"><i class="fas fa-calendar-alt"></i> ${formatDate(item.created_at)}</div>
                <a href="${item.url}" target="_blank" rel="noopener noreferrer" class="btn-open">
                    <i class="${cfg.icon}"></i> ${cfg.btnText}
                </a>
            </div>
        </div>`;
}

// ── Load konten edukasi (tab 1) ────────────────────────────
async function loadEdukasi() {
    const grid     = document.getElementById('edukasiGrid');
    const platform = document.getElementById('filterPlatform').value;
    const category = document.getElementById('filterCategory').value;
    grid.innerHTML = '<div class="loading-center" style="grid-column:1/-1;"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat konten...</div>';

    const params = new URLSearchParams();
    if (platform) params.append('platform', platform);
    if (category) params.append('category', category);

    try {
        const res  = await fetch(`{{ route('edukasi.list') }}${params.toString() ? '?' + params.toString() : ''}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        const list = data.data || [];

        if (!list.length) {
            grid.innerHTML = '<div class="empty-state"><i class="fas fa-book-open"></i><p>Belum ada konten edukasi</p></div>';
            return;
        }
        grid.innerHTML = list.map(buildEduCard).join('');
    } catch (e) {
        grid.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat konten</p></div>';
        toast('Gagal memuat konten edukasi', 'error');
    }
}

@if(Auth::user()->role === 'admin')
// ── Load daftar konten (tab kelola) ───────────────────────
async function loadKontenList() {
    const el = document.getElementById('kontenList');
    el.innerHTML = '<div class="loading-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>';

    try {
        const res  = await fetch('{{ route("edukasi.list") }}', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        const list = data.data || [];

        if (!list.length) {
            el.innerHTML = '<div style="text-align:center;padding:30px;color:#9CA3AF;">Belum ada konten</div>';
            return;
        }

        el.innerHTML = list.map(item => {
            const cfg = PLATFORM_CONFIG[item.platform] || PLATFORM_CONFIG.article;
            const thumbHtml = item.thumbnail
                ? `<img class="konten-thumb-sm" src="${item.thumbnail}" onerror="this.style.display='none'">`
                : `<div class="konten-thumb-sm-placeholder"><i class="${cfg.icon}"></i></div>`;
            return `
                <div class="konten-list-item">
                    ${thumbHtml}
                    <div class="konten-info">
                        <div class="konten-title">${item.title}</div>
                        <div class="konten-meta">• ${item.category || '-'}</div>
                        <a href="${item.url}" target="_blank" class="konten-url">${item.url}</a>
                    </div>
                    <div class="konten-actions">
                        <button class="btn btn-primary btn-sm" onclick="editKonten(${item.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="hapusKonten(${item.id}, '${item.title.replace(/'/g,"\\'")}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>`;
        }).join('');
    } catch (e) {
        el.innerHTML = '<div style="text-align:center;padding:30px;color:#EF4444;">Gagal memuat daftar konten</div>';
    }
}

// ── Auto-fetch info ────────────────────────────────────────
async function fetchInfo() {
    const url      = document.getElementById('url').value.trim();
    const platform = document.getElementById('platform').value;
    const btn      = document.getElementById('btnFetch');

    if (!url)      { toast('Masukkan URL terlebih dahulu', 'warning'); return; }
    if (!platform) { toast('Pilih platform terlebih dahulu', 'warning'); return; }

    // Hanya YouTube dan artikel yang bisa auto-fetch
    if (!['youtube','article'].includes(platform)) {
        toast('Auto-fetch hanya tersedia untuk YouTube dan Artikel', 'warning');
        return;
    }

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const res  = await fetch('{{ route("edukasi.fetchInfo") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ platform, url }),
        });
        const data = await res.json();
        if (data.success && data.data) {
            if (data.data.title)     document.getElementById('title').value     = data.data.title;
            if (data.data.thumbnail) document.getElementById('thumbnail').value = data.data.thumbnail;
            toast('Judul & thumbnail berhasil diambil!', 'success');
        } else {
            toast('Tidak dapat auto-fetch. Isi judul secara manual.', 'warning');
        }
    } catch (e) { toast('Gagal fetch info', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-magic"></i> Auto-fetch'; }
}

// ── Submit tambah konten ───────────────────────────────────
async function submitEdukasi(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const body = {
        platform:  document.getElementById('platform').value,
        category:  document.getElementById('category').value,
        title:     document.getElementById('title').value,
        url:       document.getElementById('url').value,
        thumbnail: document.getElementById('thumbnail').value || null,
    };

    try {
        const res  = await fetch('{{ route("edukasi.store") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success !== false && res.ok) {
            toast('Konten berhasil dibagikan!', 'success');
            resetForm();
            loadKontenList();
            loadEdukasi();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) { toast('Koneksi gagal', 'error'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-share-alt"></i> Bagikan Konten'; }
}

// ── Edit konten ────────────────────────────────────────────
async function editKonten(id) {
    try {
        const res  = await fetch(`{{ url('/api/edukasi') }}/${id}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (!data.success) { toast('Gagal memuat data', 'error'); return; }
        const item = data.data;
        document.getElementById('platform').value  = item.platform;
        document.getElementById('category').value  = item.category;
        document.getElementById('title').value     = item.title;
        document.getElementById('url').value       = item.url;
        document.getElementById('thumbnail').value = item.thumbnail || '';
        onPlatformChange();
        // Ganti tombol submit jadi update
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
        btn.onclick = async (e) => {
            e.preventDefault();
            btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            try {
                const r = await fetch(`{{ url('/api/edukasi') }}/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        platform:  document.getElementById('platform').value,
                        category:  document.getElementById('category').value,
                        title:     document.getElementById('title').value,
                        url:       document.getElementById('url').value,
                        thumbnail: document.getElementById('thumbnail').value || null,
                    }),
                });
                const d = await r.json();
                if (d.success !== false && r.ok) {
                    toast('Konten berhasil diperbarui!', 'success');
                    resetForm();
                    loadKontenList();
                    loadEdukasi();
                } else { toast(d.message || 'Gagal update', 'error'); }
            } catch (err) { toast('Koneksi gagal', 'error'); }
            finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-share-alt"></i> Bagikan Konten'; btn.onclick = null; }
        };
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (e) { toast('Gagal memuat data', 'error'); }
}

// ── Hapus konten ───────────────────────────────────────────
async function hapusKonten(id, nama) {
    if (!confirm(`Hapus konten "${nama}"?`)) return;
    try {
        const res  = await fetch(`{{ url('/api/edukasi') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        if (data.success) {
            toast('Konten berhasil dihapus!', 'success');
            loadKontenList();
            loadEdukasi();
        } else { toast(data.message || 'Gagal menghapus', 'error'); }
    } catch (e) { toast('Koneksi gagal', 'error'); }
}

function resetForm() {
    document.getElementById('formEdukasi').reset();
    document.getElementById('platformHint').style.display = 'none';
    const btn = document.getElementById('btnSubmit');
    btn.innerHTML = '<i class="fas fa-share-alt"></i> Bagikan Konten';
    btn.onclick = null;
}
@endif

document.addEventListener('DOMContentLoaded', function() {
    loadEdukasi();
    const params = new URLSearchParams(window.location.search);
    if (params.get('action') === 'tambah') switchTab('kelola');
});
</script>
@endsection
