@extends('layouts.lansia')

@section('title', 'Kunjungan Lansia')

@section('styles')
<style>
    .container { max-width:1340px; margin:0 auto; padding:16px 20px; }
    .card { background:#fff; border-radius:16px; padding:20px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

    /* Menu Navigasi Lansia */
    .lansia-menu { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px; }
    .menu-card { background:#fff; border-radius:12px; padding:20px; box-shadow:0 2px 12px rgba(0,0,0,0.08); text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:12px; transition:all 0.3s; border:2px solid transparent; }
    .menu-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,0.12); border-color:#246BCE; }
    .menu-card.active { border-color:#246BCE; background:linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); }
    .menu-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px; }
    .menu-icon.blue { background:linear-gradient(135deg, #3B82F6, #2563EB); color:#fff; }
    .menu-icon.green { background:linear-gradient(135deg, #10B981, #059669); color:#fff; }
    .menu-icon.purple { background:linear-gradient(135deg, #8B5CF6, #7C3AED); color:#fff; }
    .menu-icon.orange { background:linear-gradient(135deg, #F59E0B, #D97706); color:#fff; }
    .menu-icon.red { background:linear-gradient(135deg, #EF4444, #DC2626); color:#fff; }
    .menu-icon.indigo { background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; }
    .menu-icon.teal { background:linear-gradient(135deg, #14B8A6, #0D9488); color:#fff; }
    .menu-title { font-size:15px; font-weight:600; color:#1E3A5F; text-align:center; }
    .menu-desc { font-size:12px; color:#64748B; text-align:center; }

    /* Tabs */
    .tab-nav { display:flex; gap:8px; margin-bottom:20px; border-bottom:2px solid #E5E7EB; padding-bottom:0; }
    .tab-btn { padding:10px 20px; border:none; background:none; font-size:14px; font-weight:600; color:#64748B; cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-2px; transition:all 0.2s; border-radius:8px 8px 0 0; }
    .tab-btn.active { color:#246BCE; border-bottom-color:#246BCE; background:rgba(36,107,206,0.05); }
    .tab-btn:hover:not(.active) { color:#246BCE; background:rgba(36,107,206,0.03); }
    .tab-content { display:none; } .tab-content.active { display:block; }

    /* Search & Filter */
    .search-bar { display:flex; gap:12px; margin-bottom:16px; align-items:center; flex-wrap:wrap; }
    .search-input { flex:1; min-width:200px; padding:10px 16px 10px 40px; border:2px solid #E5E7EB; border-radius:10px; font-size:14px; transition:all 0.2s; }
    .search-input:focus { outline:none; border-color:#246BCE; box-shadow:0 0 0 3px rgba(36,107,206,0.1); }
    .search-wrapper { position:relative; flex:1; }
    .search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9CA3AF; }

    /* Table */
    .table-wrapper { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:14px; }
    thead th { background:#F8FAFC; padding:12px 16px; text-align:left; font-weight:600; color:#4A6FA3; border-bottom:2px solid #E5E7EB; white-space:nowrap; }
    tbody td { padding:12px 16px; border-bottom:1px solid #F1F5F9; color:#374151; vertical-align:middle; }
    tbody tr:hover { background:#F8FAFC; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .badge-success { background:#D1FAE5; color:#065F46; }
    .badge-warning { background:#FEF3C7; color:#92400E; }
    .badge-danger  { background:#FEE2E2; color:#991B1B; }
    .badge-info    { background:#DBEAFE; color:#1E40AF; }
    .badge-secondary { background:#F3F4F6; color:#374151; }

    /* Buttons */
    .btn { padding:8px 16px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary { background:#246BCE; color:#fff; } .btn-primary:hover { background:#1D58A8; }
    .btn-success { background:#10B981; color:#fff; } .btn-success:hover { background:#059669; }
    .btn-warning { background:#F59E0B; color:#fff; } .btn-warning:hover { background:#D97706; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-sm { padding:5px 10px; font-size:12px; }
    .btn-outline { background:transparent; border:2px solid #246BCE; color:#246BCE; } .btn-outline:hover { background:#246BCE; color:#fff; }

    /* Form */
    .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
    @media(max-width:768px){ .form-grid{ grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-group label { font-size:13px; font-weight:600; color:#374151; }
    .form-group input, .form-group select, .form-group textarea { padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; transition:all 0.2s; }
    .form-group input:focus, .form-group select:focus { outline:none; border-color:#246BCE; box-shadow:0 0 0 3px rgba(36,107,206,0.1); }
    .form-section-title { font-size:15px; font-weight:700; color:#1E3A5F; margin:16px 0 8px; padding-bottom:8px; border-bottom:2px solid #E5E7EB; grid-column:1/-1; }

    /* Modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:20px; padding:28px; width:90%; max-width:700px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease; }
    .modal-box.modal-lg { max-width:900px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:16px; border-bottom:2px solid #E5E7EB; }
    .modal-title { font-size:20px; font-weight:700; color:#1E3A5F; }
    .modal-close { background:none; border:none; font-size:24px; color:#9CA3AF; cursor:pointer; transition:all 0.2s; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; transform:rotate(90deg); }

    /* Pagination */
    .pagination { display:flex; gap:6px; justify-content:center; align-items:center; margin-top:20px; }
    .page-btn { padding:6px 12px; border:1px solid #E5E7EB; background:#fff; border-radius:6px; cursor:pointer; font-size:13px; transition:all 0.2s; }
    .page-btn:hover { background:#F3F4F6; border-color:#246BCE; }
    .page-btn.active { background:#246BCE; color:#fff; border-color:#246BCE; }
    .page-btn:disabled { opacity:0.4; cursor:not-allowed; }

    /* Autocomplete */
    .autocomplete-wrapper { position:relative; }
    .autocomplete-input { width:100%; padding:10px 14px; border:2px solid #E5E7EB; border-radius:8px; font-size:14px; }
    .autocomplete-input:focus { outline:none; border-color:#246BCE; }
    .autocomplete-dropdown { position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #E5E7EB; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.12); z-index:100; max-height:300px; overflow-y:auto; display:none; }
    .autocomplete-item { padding:12px 16px; cursor:pointer; border-bottom:1px solid #F1F5F9; transition:background 0.15s; }
    .autocomplete-item:hover, .autocomplete-item.selected { background:#EFF6FF; }
    .autocomplete-item .name { font-weight:600; color:#1E3A5F; font-size:14px; }
    .autocomplete-item .detail { font-size:12px; color:#64748B; margin-top:2px; }
    .autocomplete-selected-info { background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px; padding:12px 16px; margin-top:8px; display:none; }
    .autocomplete-selected-info .info-name { font-weight:700; color:#1E3A5F; }
    .autocomplete-selected-info .info-detail { font-size:12px; color:#64748B; margin-top:4px; }

    /* Detail modal */
    .detail-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; }
    @media(max-width:600px){ .detail-grid{ grid-template-columns:1fr; } }
    .detail-item label { font-size:11px; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:2px; }
    .detail-item span { font-size:14px; color:#1E3A5F; font-weight:500; }
    .detail-section { margin-bottom:20px; }
    .detail-section-title { font-size:14px; font-weight:700; color:#246BCE; margin-bottom:12px; padding-bottom:8px; border-bottom:2px solid #DBEAFE; display:flex; align-items:center; gap:8px; }

    /* Badge Status Gizi */
    .badge-gizi-baik { background:#D1FAE5; color:#065F46; }
    .badge-stunting { background:#FEE2E2; color:#991B1B; }
    .badge-risk-stunting { background:#FED7AA; color:#9A3412; }
    .badge-gizi-kurang { background:#FECACA; color:#7F1D1D; }
    .badge-risk-gizi-kurang { background:#FEF3C7; color:#78350F; }
    .badge-risk-gizi-lebih { background:#FEF3C7; color:#92400E; }
    .badge-gizi-lebih { background:#E9D5FF; color:#6B21A8; }
    .badge-obesitas { background:#DDD6FE; color:#5B21B6; }

    /* Badge Status Kesehatan Lansia */
    .badge-sehat { background:#D1FAE5; color:#065F46; }
    .badge-hipertensi { background:#FEE2E2; color:#991B1B; }
    .badge-diabetes { background:#FED7AA; color:#9A3412; }
    .badge-kolesterol-tinggi { background:#FEF3C7; color:#92400E; }
    .badge-asam-urat-tinggi { background:#E9D5FF; color:#6B21A8; }

    /* Notification */
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:14px 20px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:280px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; }
    .toast-error   { background:#EF4444; }
    .toast-warning { background:#F59E0B; }

    /* Loading */
    .loading-row td { text-align:center; padding:40px; color:#9CA3AF; }
    .empty-row td { text-align:center; padding:40px; color:#9CA3AF; }

    /* Table nav arrows */
    .table-nav-wrapper { position:relative; display:flex; align-items:center; gap:8px; }
    .table-nav-arrow { flex-shrink:0; width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#4A90E2,#357ABD); border:none; box-shadow:0 2px 8px rgba(74,144,226,0.3); display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:10; transition:all 0.3s ease; color:white; font-size:14px; opacity:0.3; pointer-events:none; }
    .table-nav-arrow.visible { opacity:1; pointer-events:auto; }
    .table-nav-arrow:hover { background:linear-gradient(135deg,#357ABD,#2868A8); transform:scale(1.1); }
    .table-slider-container { flex:1; overflow-x:auto; overflow-y:visible; scroll-behavior:smooth; scrollbar-width:thin; scrollbar-color:#CBD5E1 #F1F5F9; padding-bottom:8px; }
    .table-slider-container::-webkit-scrollbar { height:8px; }
    .table-slider-container::-webkit-scrollbar-track { background:#F1F5F9; border-radius:999px; }
    .table-slider-container::-webkit-scrollbar-thumb { background:#CBD5E1; border-radius:999px; }

    /* Data table */
    .data-table { width:max-content; border-collapse:separate; border-spacing:0; font-size:14px; }
    .data-table thead th { background:#F6F8FB; color:#4A5E7D; font-size:13px; font-weight:600; text-transform:uppercase; letter-spacing:0.02em; padding:14px 16px; text-align:left; border-bottom:2px solid #E7ECF3; white-space:nowrap; }
    .data-table tbody td { padding:12px 16px; vertical-align:middle; border-bottom:1px solid #E7ECF3; white-space:nowrap; font-size:14px; }
    .data-table tbody tr:nth-child(odd) { background:#FCFDFE; }
    .data-table tbody tr:nth-child(even) { background:#fff; }
    .data-table tbody tr:hover { background:#F1F5FF; }

    /* Pagination */
    .pagination-container { display:flex; justify-content:center; align-items:center; gap:12px; margin-top:20px; padding:16px; background:linear-gradient(135deg,#f0f9ff,#e0f2fe); border-radius:16px; border:1px solid #bae6fd; }
    .pagination-btn { display:flex; align-items:center; gap:8px; padding:10px 22px; border:2px solid #3b82f6; border-radius:12px; background:white; color:#3b82f6; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.3s ease; min-width:130px; justify-content:center; }
    .pagination-btn:hover:not(:disabled) { background:#3b82f6; color:white; transform:translateY(-2px); box-shadow:0 4px 12px rgba(59,130,246,0.3); }
    .pagination-btn:disabled { opacity:0.4; cursor:not-allowed; border-color:#cbd5e1; color:#94a3b8; }
    .pagination-info { display:flex; flex-direction:column; align-items:center; padding:8px 20px; background:white; border-radius:12px; border:1px solid #e2e8f0; min-width:160px; }
    .pagination-info .page-number { font-size:18px; font-weight:700; color:#1e40af; }
    .pagination-info .page-text { font-size:12px; color:#64748b; margin-top:2px; }
    .pagination-dots { display:flex; gap:6px; margin-top:8px; }
    .pagination-dot { width:10px; height:10px; border-radius:50%; background:#cbd5e1; cursor:pointer; transition:all 0.3s ease; }
    .pagination-dot.active { background:#3b82f6; transform:scale(1.2); }
    .pagination-dot:hover:not(.active) { background:#93c5fd; }

    /* Action buttons */
    .action-group { display:flex; gap:6px; justify-content:center; }
    .action-btn { width:36px; height:36px; border-radius:6px; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:all 0.2s ease; font-size:15px; }
    .action-btn.-chart { background:linear-gradient(135deg,#10B981,#059669); color:#fff; box-shadow:0 2px 8px rgba(16,185,129,0.3); }
    .action-btn.-chart:hover { background:linear-gradient(135deg,#059669,#047857); transform:translateY(-2px); }
    .action-btn.-view { background:#E8F1FF; color:#2563EB; }
    .action-btn.-edit { background:#FFF7E6; color:#D97706; }
    .action-btn.-delete { background:#FEECEF; color:#DC2626; }
    .action-btn:hover { transform:translateY(-1px); box-shadow:0 2px 8px rgba(0,0,0,0.1); }
</style>
@endsection

@section('content')
<div id="toast"></div>

<div class="page-header" style="margin-bottom:20px;">
    <h1 style="font-size:28px;font-weight:700;color:#065F46;margin-bottom:4px;">Kunjungan Lansia</h1>
    <p style="font-size:14px;color:#64748B;"><i class="fas fa-calendar-check" style="color:#10B981;margin-right:6px;"></i> Kelola Data Kunjungan Lansia</p>
</div>

{{-- TABS --}}
<div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('tabel')"><i class="fas fa-table"></i> Kunjungan</button>
    <button class="tab-btn" onclick="switchTab('tambah')"><i class="fas fa-plus-circle"></i> Tambah Data Kunjungan</button>
</div>

{{-- TAB 1: TABEL DATA LANSIA --}}
<div id="tab-tabel" class="tab-content active">
    <div class="card">
        <div class="search-bar">
            <div class="search-wrapper" style="flex:1;">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" style="width:100%;" placeholder="Cari nama lansia, NIK, atau nama wali..." oninput="debounceSearch()">
            </div>
        </div>
        <div class="table-nav-wrapper" style="display:flex;align-items:center;gap:8px;">
            <button class="table-nav-arrow" id="scrollLeft" onclick="scrollTable(-300)" title="Geser kiri">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="table-slider-container" id="tableSlider">
                <div class="table-wrapper">
                    <table id="lansiaTable" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NAMA LANSIA</th>
                                <th>NIK LANSIA</th>
                                <th>JK</th>
                                <th>TANGGAL LAHIR</th>
                                <th>TEMPAT LAHIR</th>
                                <th>USIA</th>
                                <th>BB (KG)</th>
                                <th>TB (CM)</th>
                                <th>TEKANAN DARAH</th>
                                <th>GULA DARAH</th>
                                <th>KOLESTEROL</th>
                                <th>ASAM URAT</th>
                                <th>STATUS KESEHATAN</th>
                                <th>ALAMAT</th>
                                <th>NAMA WALI</th>
                                <th>NIK WALI</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="lansiaTableBody">
                            <tr class="loading-row"><td colspan="18"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <button class="table-nav-arrow visible" id="scrollRight" onclick="scrollTable(300)" title="Geser kanan">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="pagination-container" id="paginationContainer" style="display:none;">
            <button class="pagination-btn" id="btnPrev" onclick="goPage(currentPage-1)" disabled>
                <i class="fas fa-chevron-left"></i> Sebelumnya
            </button>
            <div class="pagination-info">
                <div class="page-number" id="pageInfo">Halaman 1 dari 1</div>
                <div class="page-text" id="pageTotal">0 total data</div>
                <div class="pagination-dots" id="paginationDots"></div>
            </div>
            <button class="pagination-btn" id="btnNext" onclick="goPage(currentPage+1)">
                Berikutnya <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

{{-- TAB 2: TAMBAH DATA KUNJUNGAN --}}
<div id="tab-tambah" class="tab-content">
    <div class="card">
        <h3 style="font-size:18px;font-weight:700;color:#1E3A5F;margin-bottom:8px;"><i class="fas fa-plus-circle" style="color:#10B981;"></i> Tambah Data Kunjungan</h3>
        <p style="font-size:13px;color:#64748B;margin-bottom:20px;">Catat kunjungan dan pemeriksaan kesehatan lansia.</p>
        <form id="formTambah" onsubmit="submitTambah(event)">
            <div class="form-grid">
                <div class="form-section-title"><i class="fas fa-user-check"></i> Pilih Lansia</div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Pilih Lansia <span style="color:red">*</span></label>
                    <select name="lansia_id" id="selectLansia" required onchange="loadLansiaInfo(this.value)">
                        <option value="">-- Pilih Lansia --</option>
                    </select>
                    <small style="color:#64748B;font-size:12px;">Pilih lansia yang akan diperiksa</small>
                </div>
                
                {{-- Info Lansia Terpilih --}}
                <div id="lansiaInfo" style="grid-column:1/-1;display:none;background:#F0FDF4;border:2px solid #10B981;border-radius:8px;padding:16px;margin-bottom:8px;">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;font-size:13px;">
                        <div><strong>Nama:</strong> <span id="infoNama">-</span></div>
                        <div><strong>NIK:</strong> <span id="infoNik">-</span></div>
                        <div><strong>Usia:</strong> <span id="infoUsia">-</span></div>
                        <div><strong>Jenis Kelamin:</strong> <span id="infoJK">-</span></div>
                    </div>
                </div>

                <div class="form-section-title"><i class="fas fa-calendar-alt"></i> Tanggal Kunjungan</div>
                <div class="form-group">
                    <label>Tanggal Kunjungan <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_kunjungan" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-section-title"><i class="fas fa-heartbeat"></i> Pengukuran Fisik</div>
                <div class="form-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number" name="berat_badan" placeholder="Contoh: 65.5" step="0.1" min="30" max="150">
                </div>
                <div class="form-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number" name="tinggi_badan" placeholder="Contoh: 160" step="0.1" min="100" max="200">
                </div>
                <div class="form-group">
                    <label>Tekanan Darah (mmHg)</label>
                    <input type="text" name="tekanan_darah" placeholder="Contoh: 120/80" maxlength="20">
                    <small style="color:#64748B;font-size:12px;">Format: sistolik/diastolik (contoh: 120/80)</small>
                </div>

                <div class="form-section-title"><i class="fas fa-vial"></i> Pemeriksaan Darah</div>
                <div class="form-group">
                    <label>Gula Darah (mg/dL)</label>
                    <input type="number" name="gula_darah" placeholder="Contoh: 95" step="0.1" min="50" max="500">
                </div>
                <div class="form-group">
                    <label>Kolesterol (mg/dL)</label>
                    <input type="number" name="kolesterol" placeholder="Contoh: 180" step="0.1" min="100" max="400">
                </div>
                <div class="form-group">
                    <label>Asam Urat (mg/dL)</label>
                    <input type="number" name="asam_urat" placeholder="Contoh: 5.5" step="0.1" min="1" max="15">
                </div>

                <div class="form-section-title"><i class="fas fa-notes-medical"></i> Keluhan & Pengobatan</div>
                <div class="form-group" style="grid-column:1/-1">
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="ada_keluhan" value="1" onchange="toggleKeluhan(this)">
                        <span>Ada Keluhan</span>
                    </label>
                </div>
                <div class="form-group" style="grid-column:1/-1;display:none;" id="groupKeluhan">
                    <label>Keluhan</label>
                    <textarea name="keluhan" placeholder="Jelaskan keluhan yang dialami..." rows="3" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Obat Diberikan</label>
                    <select name="obat_diberikan[]" multiple style="height:120px;">
                        <option value="Paracetamol">Paracetamol</option>
                        <option value="Amlodipin">Amlodipin</option>
                        <option value="Metformin">Metformin</option>
                        <option value="Captopril">Captopril</option>
                        <option value="Simvastatin">Simvastatin</option>
                        <option value="Antasida">Antasida</option>
                        <option value="Asam Mefenamat">Asam Mefenamat</option>
                        <option value="Glibenklamid">Glibenklamid</option>
                        <option value="Furosemid">Furosemid</option>
                        <option value="Lisinopril">Lisinopril</option>
                    </select>
                    <small style="color:#64748B;font-size:12px;">Tahan Ctrl (Windows) atau Cmd (Mac) untuk pilih lebih dari satu</small>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Vitamin Diberikan</label>
                    <select name="vitamin_diberikan[]" multiple style="height:100px;">
                        <option value="Vitamin C">Vitamin C</option>
                        <option value="Vitamin D">Vitamin D</option>
                        <option value="Vitamin B12">Vitamin B12</option>
                        <option value="Kalsium">Kalsium</option>
                        <option value="Asam Folat">Asam Folat</option>
                        <option value="Zinc">Zinc</option>
                        <option value="Vitamin E">Vitamin E</option>
                        <option value="Omega-3">Omega-3</option>
                        <option value="Multivitamin">Multivitamin</option>
                    </select>
                    <small style="color:#64748B;font-size:12px;">Tahan Ctrl (Windows) atau Cmd (Mac) untuk pilih lebih dari satu</small>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Catatan Bidan/Kader</label>
                    <textarea name="catatan_bidan" placeholder="Catatan tambahan..." rows="3" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>
            </div>
            <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end;">
                <button type="reset" class="btn btn-outline"><i class="fas fa-undo"></i> Reset Form</button>
                <button type="submit" class="btn btn-primary" id="btnTambah"><i class="fas fa-save"></i> Simpan Data Kunjungan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-user-circle" style="color:#246BCE;"></i> Detail Data Lansia</h3>
            <button class="modal-close" onclick="closeModal('modalDetail')">&times;</button>
        </div>
        <div id="detailContent">Memuat...</div>
        <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-warning" id="btnEditFromDetail" onclick="openEditFromDetail()"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn btn-outline" onclick="closeModal('modalDetail')">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-edit" style="color:#F59E0B;"></i> Edit Data Lansia</h3>
            <button class="modal-close" onclick="closeModal('modalEdit')">&times;</button>
        </div>
        <form id="formEdit" onsubmit="submitEdit(event)">
            <input type="hidden" id="editId">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lansia</label>
                    <input type="text" id="editNamaLansia" name="nama_lansia">
                </div>
                <div class="form-group">
                    <label>NIK Lansia</label>
                    <input type="text" id="editNikLansia" name="nik_lansia" maxlength="16">
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select id="editJenisKelamin" name="jenis_kelamin">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" id="editTanggalLahir" name="tanggal_lahir" max="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <label>Tempat Lahir</label>
                    <input type="text" id="editTempatLahir" name="tempat_lahir">
                </div>
                <div class="form-group">
                    <label>No HP Wali</label>
                    <input type="text" id="editHp" name="hp_kontak_wali">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Alamat Domisili</label>
                    <input type="text" id="editAlamat" name="alamat_domisili">
                </div>
                <div class="form-group">
                    <label>RT/RW</label>
                    <input type="text" id="editRtRw" name="rt_rw">
                </div>
                <div class="form-group">
                    <label>Nama Wali</label>
                    <input type="text" id="editNamaWali" name="nama_wali">
                </div>
                <div class="form-group">
                    <label>NIK Wali</label>
                    <input type="text" id="editNikWali" name="nik_wali" maxlength="16">
                </div>
                <div class="form-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number" id="editBeratBadan" name="berat_badan" step="0.1">
                </div>
                <div class="form-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number" id="editTinggiBadan" name="tinggi_badan" step="0.1">
                </div>
                <div class="form-group">
                    <label>Tekanan Darah</label>
                    <input type="text" id="editTekananDarah" name="tekanan_darah" placeholder="120/80">
                </div>
                <div class="form-group">
                    <label>Gula Darah (mg/dL)</label>
                    <input type="number" id="editGulaDarah" name="gula_darah" step="0.1">
                </div>
                <div class="form-group">
                    <label>Kolesterol (mg/dL)</label>
                    <input type="number" id="editKolesterol" name="kolesterol" step="0.1">
                </div>
                <div class="form-group">
                    <label>Asam Urat (mg/dL)</label>
                    <input type="number" id="editAsamUrat" name="asam_urat" step="0.1">
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
                <button type="submit" class="btn btn-warning" id="btnSaveEdit"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Data Lansia</h3>
            <button class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
        </div>
        <p style="color:#374151;margin-bottom:20px;">Apakah Anda yakin ingin menghapus data lansia <strong id="hapusNama"></strong>? Data tidak akan hilang permanen (soft delete).</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-danger" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Ya, Hapus</button>
            <button class="btn btn-outline" onclick="closeModal('modalHapus')">Batal</button>
        </div>
    </div>
</div>

{{-- MODAL KUNJUNGAN SELANJUTNYA --}}
<div class="modal-overlay" id="modalKunjunganSelanjutnya">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#10B981;"><i class="fas fa-notes-medical"></i> Kunjungan Selanjutnya</h3>
            <button class="modal-close" onclick="closeModal('modalKunjunganSelanjutnya')">&times;</button>
        </div>
        <form id="formKunjunganSelanjutnya" onsubmit="submitKunjunganSelanjutnya(event)">
            <input type="hidden" id="kunjunganLansiaId">
            
            {{-- Info Lansia --}}
            <div style="background:#F0FDF4;border:2px solid #10B981;border-radius:8px;padding:16px;margin-bottom:20px;">
                <h4 style="font-size:14px;font-weight:700;color:#065F46;margin-bottom:12px;"><i class="fas fa-user-circle"></i> Data Lansia</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;font-size:13px;">
                    <div><strong>Nama:</strong> <span id="kunjNama">-</span></div>
                    <div><strong>NIK:</strong> <span id="kunjNik">-</span></div>
                    <div><strong>Usia:</strong> <span id="kunjUsia">-</span></div>
                    <div><strong>Jenis Kelamin:</strong> <span id="kunjJK">-</span></div>
                </div>
            </div>

            <div class="form-grid">
                {{-- Data yang bisa diupdate --}}
                <div class="form-section-title"><i class="fas fa-user-edit"></i> Update Data Lansia (Opsional)</div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="kunjNamaLengkap" placeholder="Nama lengkap lansia">
                </div>
                <div class="form-group">
                    <label>NIK Lansia</label>
                    <input type="text" name="nik_lansia" id="kunjNikLansia" placeholder="16 digit NIK" maxlength="16">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Alamat Domisili</label>
                    <textarea name="alamat_domisili" id="kunjAlamat" placeholder="Alamat lengkap" rows="2" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>
                <div class="form-group">
                    <label>RT/RW</label>
                    <input type="text" name="rt_rw" id="kunjRtRw" placeholder="001/005" maxlength="10">
                </div>
                <div class="form-group">
                    <label>Nama Wali</label>
                    <input type="text" name="nama_wali" id="kunjNamaWali" placeholder="Nama wali/keluarga">
                </div>
                <div class="form-group">
                    <label>NIK Wali</label>
                    <input type="text" name="nik_wali" id="kunjNikWali" placeholder="16 digit NIK wali" maxlength="16">
                </div>
                <div class="form-group">
                    <label>No HP Kontak Wali</label>
                    <input type="text" name="hp_kontak_wali" id="kunjHpWali" placeholder="+62...">
                </div>

                {{-- Data Kunjungan --}}
                <div class="form-section-title"><i class="fas fa-calendar-alt"></i> Data Kunjungan</div>
                <div class="form-group">
                    <label>Tanggal Kunjungan <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_kunjungan" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-section-title"><i class="fas fa-heartbeat"></i> Pengukuran Fisik</div>
                <div class="form-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number" name="berat_badan" placeholder="Contoh: 65.5" step="0.1" min="30" max="150">
                </div>
                <div class="form-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number" name="tinggi_badan" placeholder="Contoh: 160" step="0.1" min="100" max="200">
                </div>
                <div class="form-group">
                    <label>Tekanan Darah (mmHg)</label>
                    <input type="text" name="tekanan_darah" placeholder="Contoh: 120/80" maxlength="20">
                </div>

                <div class="form-section-title"><i class="fas fa-vial"></i> Pemeriksaan Darah</div>
                <div class="form-group">
                    <label>Gula Darah (mg/dL)</label>
                    <input type="number" name="gula_darah" placeholder="Contoh: 95" step="0.1" min="50" max="500">
                </div>
                <div class="form-group">
                    <label>Kolesterol (mg/dL)</label>
                    <input type="number" name="kolesterol" placeholder="Contoh: 180" step="0.1" min="100" max="400">
                </div>
                <div class="form-group">
                    <label>Asam Urat (mg/dL)</label>
                    <input type="number" name="asam_urat" placeholder="Contoh: 5.5" step="0.1" min="1" max="15">
                </div>

                <div class="form-section-title"><i class="fas fa-notes-medical"></i> Keluhan & Pengobatan</div>
                <div class="form-group" style="grid-column:1/-1">
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="ada_keluhan" value="1" onchange="toggleKeluhanKunjungan(this)">
                        <span>Ada Keluhan</span>
                    </label>
                </div>
                <div class="form-group" style="grid-column:1/-1;display:none;" id="groupKeluhanKunjungan">
                    <label>Keluhan</label>
                    <textarea name="keluhan" placeholder="Jelaskan keluhan yang dialami..." rows="3" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Obat Diberikan</label>
                    <select name="obat_diberikan[]" multiple style="height:120px;">
                        <option value="Paracetamol">Paracetamol</option>
                        <option value="Amlodipin">Amlodipin</option>
                        <option value="Metformin">Metformin</option>
                        <option value="Captopril">Captopril</option>
                        <option value="Simvastatin">Simvastatin</option>
                        <option value="Antasida">Antasida</option>
                        <option value="Asam Mefenamat">Asam Mefenamat</option>
                        <option value="Glibenklamid">Glibenklamid</option>
                        <option value="Furosemid">Furosemid</option>
                        <option value="Lisinopril">Lisinopril</option>
                    </select>
                    <small style="color:#64748B;font-size:12px;">Tahan Ctrl (Windows) atau Cmd (Mac) untuk pilih lebih dari satu</small>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Vitamin Diberikan</label>
                    <select name="vitamin_diberikan[]" multiple style="height:100px;">
                        <option value="Vitamin C">Vitamin C</option>
                        <option value="Vitamin D">Vitamin D</option>
                        <option value="Vitamin B12">Vitamin B12</option>
                        <option value="Kalsium">Kalsium</option>
                        <option value="Asam Folat">Asam Folat</option>
                        <option value="Zinc">Zinc</option>
                        <option value="Vitamin E">Vitamin E</option>
                        <option value="Omega-3">Omega-3</option>
                        <option value="Multivitamin">Multivitamin</option>
                    </select>
                    <small style="color:#64748B;font-size:12px;">Tahan Ctrl (Windows) atau Cmd (Mac) untuk pilih lebih dari satu</small>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Catatan Bidan/Kader</label>
                    <textarea name="catatan_bidan" placeholder="Catatan tambahan..." rows="3" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>
            </div>
            <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalKunjunganSelanjutnya')">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnKunjunganSelanjutnya"><i class="fas fa-save"></i> Simpan Kunjungan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// ============================================================
// STATE
// ============================================================
let allLansia = [], currentPage = 1, perPage = 10, searchTimer = null, currentDetailId = null, hapusId = null;

// ============================================================
// TOAST NOTIFICATION
// ============================================================
function toast(msg, type = 'success') {
    const icons = { success:'check-circle', error:'times-circle', warning:'exclamation-triangle' };
    const el = document.createElement('div');
    el.className = `toast-item toast-${type}`;
    el.innerHTML = `<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// ============================================================
// TABS
// ============================================================
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach((b,i) => b.classList.toggle('active', ['tabel','tambah'][i] === tab));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
}

// ============================================================
// LOAD DATA LANSIA
// ============================================================
async function loadLansia() {
    const search = document.getElementById('searchInput').value.trim();
    const tbody  = document.getElementById('lansiaTableBody');
    tbody.innerHTML = '<tr class="loading-row"><td colspan="18"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>';

    try {
        const url = `/lansia/api/kunjungan?q=${encodeURIComponent(search)}&limit=200`;
        console.log('Fetching lansia data from:', url);
        
        const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        console.log('Response status:', res.status, res.statusText);
        
        const data = await res.json();
        console.log('Response data:', data);

        if (!data.success) { 
            console.error('API returned success=false:', data.message);
            toast(data.message || 'Gagal memuat data', 'error'); 
            tbody.innerHTML = '<tr class="empty-row"><td colspan="18"><i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'Gagal memuat data') + '</td></tr>';
            return; 
        }

        allLansia = data.data;
        console.log('Total lansia loaded:', allLansia.length);
        
        const tc = document.getElementById('totalCount');
        if (tc) tc.textContent = allLansia.length;
        currentPage = 1;
        renderTable();
    } catch (e) {
        console.error('Error loading lansia:', e);
        tbody.innerHTML = '<tr class="empty-row"><td colspan="18"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data: ' + e.message + '</td></tr>';
    }
}

function renderTable() {
    const tbody = document.getElementById('lansiaTableBody');
    const start = (currentPage - 1) * perPage;
    const page  = allLansia.slice(start, start + perPage);

    if (!page.length) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="18"><i class="fas fa-inbox"></i> Tidak ada data</td></tr>';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }

    const statusBadge = (s) => {
        if (!s || s === 'Sehat') return '<span class="badge badge-sehat">Sehat</span>';
        
        // Handle multiple conditions
        const conditions = s.split(', ');
        return conditions.map(cond => {
            const map = {
                'Hipertensi': 'badge-hipertensi',
                'Diabetes': 'badge-diabetes',
                'Kolesterol Tinggi': 'badge-kolesterol-tinggi',
                'Asam Urat Tinggi': 'badge-asam-urat-tinggi',
            };
            const cls = map[cond] || 'badge-secondary';
            return `<span class="badge ${cls}">${cond}</span>`;
        }).join(' ');
    };

    const jkBadge = (jk) => {
        if (jk === 'L') return `<span class="badge badge-info" style="font-size:11px;padding:3px 8px;">L</span>`;
        if (jk === 'P') return `<span class="badge badge-warning" style="font-size:11px;padding:3px 8px;">P</span>`;
        return '-';
    };

    tbody.innerHTML = page.map((l) => `
        <tr id="row-${l.id}">
            <td>${l.id}</td>
            <td class="nama-lansia-cell"><strong>${l.nama_lansia || '-'}</strong></td>
            <td><code style="font-size:12px;">${l.nik_lansia || '-'}</code></td>
            <td>${jkBadge(l.jenis_kelamin)}</td>
            <td>${l.tanggal_lahir ? new Date(l.tanggal_lahir).toLocaleDateString('id-ID') : '-'}</td>
            <td>${l.tempat_lahir || '-'}</td>
            <td>${l.usia || '-'}</td>
            <td>${l.berat_badan ? parseFloat(l.berat_badan).toFixed(1) : '-'}</td>
            <td>${l.tinggi_badan ? parseFloat(l.tinggi_badan).toFixed(1) : '-'}</td>
            <td>${l.tekanan_darah || '-'}</td>
            <td>${l.gula_darah ? parseFloat(l.gula_darah).toFixed(1) : '-'}</td>
            <td>${l.kolesterol ? parseFloat(l.kolesterol).toFixed(1) : '-'}</td>
            <td>${l.asam_urat ? parseFloat(l.asam_urat).toFixed(1) : '-'}</td>
            <td>${statusBadge(l.status_kesehatan)}</td>
            <td>${l.alamat_domisili || '-'}</td>
            <td>${l.nama_wali || '-'}</td>
            <td><code style="font-size:12px;">${l.nik_wali || '-'}</code></td>
            <td>
                <div class="action-group">
                    <button class="action-btn -view" onclick="openDetail(${l.id})" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                    <button class="action-btn" style="background:#10B981;color:white;" onclick="openKunjunganSelanjutnya(${l.id})" title="Kunjungan Selanjutnya"><i class="fas fa-notes-medical"></i></button>
                    <button class="action-btn -edit" onclick="openEdit(${l.id})" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn -delete" onclick="confirmHapus(${l.id}, '${(l.nama_lansia || 'Data').replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
    `).join('');

    renderPagination();
    updateScrollArrows();
}

function renderPagination() {
    const total = Math.ceil(allLansia.length / perPage);
    const container = document.getElementById('paginationContainer');

    if (total <= 1) { container.style.display = 'none'; return; }
    container.style.display = 'flex';

    document.getElementById('btnPrev').disabled = currentPage === 1;
    document.getElementById('btnNext').disabled = currentPage === total;
    document.getElementById('pageInfo').textContent = `Halaman ${currentPage} dari ${total}`;
    document.getElementById('pageTotal').textContent = `${allLansia.length} total data`;

    // Render dots
    let dots = '';
    for (let i = 1; i <= total; i++) {
        dots += `<div class="pagination-dot ${i===currentPage?'active':''}" onclick="goPage(${i})" title="Halaman ${i}"></div>`;
    }
    document.getElementById('paginationDots').innerHTML = dots;
}

function goPage(p) {
    const total = Math.ceil(allLansia.length / perPage);
    if (p < 1 || p > total) return;
    currentPage = p;
    renderTable();
}

function scrollTable(amount) {
    const slider = document.getElementById('tableSlider');
    if (slider) slider.scrollLeft += amount;
    setTimeout(updateScrollArrows, 100);
}

function updateScrollArrows() {
    const slider = document.getElementById('tableSlider');
    if (!slider) return;
    const leftBtn  = document.getElementById('scrollLeft');
    const rightBtn = document.getElementById('scrollRight');
    if (leftBtn)  leftBtn.classList.toggle('visible', slider.scrollLeft > 10);
    if (rightBtn) rightBtn.classList.toggle('visible', slider.scrollLeft < slider.scrollWidth - slider.clientWidth - 10);
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(loadLansia, 400);
}

// ============================================================
// DETAIL
// ============================================================
async function openDetail(id) {
    currentDetailId = id;
    document.getElementById('detailContent').innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x" style="color:#246BCE;"></i></div>';
    openModal('modalDetail');

    try {
        const res  = await fetch(`/lansia/api/kunjungan/${id}`, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        const data = await res.json();
        if (!data.success) { document.getElementById('detailContent').innerHTML = '<p style="color:red;">Gagal memuat detail.</p>'; return; }

        const d = data.data;
        document.getElementById('detailContent').innerHTML = `
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-user"></i> Data Lansia</div>
                <div class="detail-grid">
                    <div class="detail-item"><label>Nama Lansia</label><span>${d.nama_lansia}</span></div>
                    <div class="detail-item"><label>NIK Lansia</label><span>${d.nik_lansia||'-'}</span></div>
                    <div class="detail-item"><label>Jenis Kelamin</label><span>${d.jenis_kelamin==='L'?'Laki-laki':'Perempuan'}</span></div>
                    <div class="detail-item"><label>Tanggal Lahir</label><span>${d.tanggal_lahir||'-'}</span></div>
                    <div class="detail-item"><label>Tempat Lahir</label><span>${d.tempat_lahir||'-'}</span></div>
                    <div class="detail-item"><label>Usia</label><span>${d.umur_display||'-'}</span></div>
                    <div class="detail-item"><label>Status Kesehatan</label><span>${d.status_kesehatan||'Belum diperiksa'}</span></div>
                </div>
            </div>
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-home"></i> Data Keluarga</div>
                <div class="detail-grid">
                    <div class="detail-item"><label>Nama Wali</label><span>${d.nama_wali||'-'}</span></div>
                    <div class="detail-item"><label>NIK Wali</label><span>${d.nik_wali||'-'}</span></div>
                    <div class="detail-item"><label>No HP Wali</label><span>${d.hp_kontak_wali||'-'}</span></div>
                    <div class="detail-item"><label>Nama KK</label><span>${d.nama_kk||'-'}</span></div>
                    <div class="detail-item" style="grid-column:1/-1"><label>Alamat</label><span>${d.alamat_domisili||'-'} ${d.rt_rw?'RT/RW '+d.rt_rw:''}</span></div>
                </div>
            </div>
            ${d.berat_badan ? `
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-heartbeat"></i> Data Kesehatan Terakhir</div>
                <div class="detail-grid">
                    <div class="detail-item"><label>Berat Badan</label><span>${d.berat_badan} kg</span></div>
                    <div class="detail-item"><label>Tinggi Badan</label><span>${d.tinggi_badan} cm</span></div>
                    ${d.tekanan_darah ? `<div class="detail-item"><label>Tekanan Darah</label><span>${d.tekanan_darah} mmHg</span></div>` : ''}
                    ${d.gula_darah ? `<div class="detail-item"><label>Gula Darah</label><span>${d.gula_darah} mg/dL</span></div>` : ''}
                    ${d.kolesterol ? `<div class="detail-item"><label>Kolesterol</label><span>${d.kolesterol} mg/dL</span></div>` : ''}
                    ${d.asam_urat ? `<div class="detail-item"><label>Asam Urat</label><span>${d.asam_urat} mg/dL</span></div>` : ''}
                    <div class="detail-item"><label>Tanggal Pemeriksaan</label><span>${d.tanggal_pemeriksaan_terakhir||'-'}</span></div>
                </div>
            </div>` : ''}
            ${d.status_detail_array ? `
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-notes-medical"></i> Detail Status Kesehatan</div>
                <div style="display:flex;flex-wrap:wrap;gap:12px;">
                    ${d.status_detail_array.tekanan_darah ? `<div style="flex:1;min-width:200px;padding:12px;background:#F8FAFC;border-radius:8px;"><strong>Tekanan Darah:</strong> ${d.status_detail_array.tekanan_darah.nilai} - ${d.status_detail_array.tekanan_darah.kategori}</div>` : ''}
                    ${d.status_detail_array.gula_darah ? `<div style="flex:1;min-width:200px;padding:12px;background:#F8FAFC;border-radius:8px;"><strong>Gula Darah:</strong> ${d.status_detail_array.gula_darah.nilai} mg/dL - ${d.status_detail_array.gula_darah.kategori}</div>` : ''}
                    ${d.status_detail_array.kolesterol ? `<div style="flex:1;min-width:200px;padding:12px;background:#F8FAFC;border-radius:8px;"><strong>Kolesterol:</strong> ${d.status_detail_array.kolesterol.nilai} mg/dL - ${d.status_detail_array.kolesterol.kategori}</div>` : ''}
                    ${d.status_detail_array.asam_urat ? `<div style="flex:1;min-width:200px;padding:12px;background:#F8FAFC;border-radius:8px;"><strong>Asam Urat:</strong> ${d.status_detail_array.asam_urat.nilai} mg/dL - ${d.status_detail_array.asam_urat.kategori}</div>` : ''}
                    ${d.status_detail_array.bmi ? `<div style="flex:1;min-width:200px;padding:12px;background:#F8FAFC;border-radius:8px;"><strong>BMI:</strong> ${d.status_detail_array.bmi.nilai} - ${d.status_detail_array.bmi.kategori}</div>` : ''}
                </div>
            </div>` : ''}
        `;
    } catch (e) {
        document.getElementById('detailContent').innerHTML = '<p style="color:red;">Gagal memuat detail.</p>';
    }
}

function openEditFromDetail() {
    closeModal('modalDetail');
    if (currentDetailId) openEdit(currentDetailId);
}

// ============================================================
// EDIT
// ============================================================
async function openEdit(id) {
    const lansia = allLansia.find(l => l.id === id);
    if (!lansia) { toast('Data tidak ditemukan', 'error'); return; }

    document.getElementById('editId').value             = lansia.id;
    document.getElementById('editNamaLansia').value     = lansia.nama_lansia || '';
    document.getElementById('editNikLansia').value      = lansia.nik_lansia || '';
    document.getElementById('editJenisKelamin').value   = lansia.jenis_kelamin || 'L';
    document.getElementById('editTanggalLahir').value   = lansia.tanggal_lahir || '';
    document.getElementById('editTempatLahir').value    = lansia.tempat_lahir || '';
    document.getElementById('editHp').value             = lansia.hp_kontak_wali || '';
    document.getElementById('editAlamat').value         = lansia.alamat_domisili || '';
    document.getElementById('editRtRw').value           = lansia.rt_rw || '';
    document.getElementById('editNamaWali').value       = lansia.nama_wali || '';
    document.getElementById('editNikWali').value        = lansia.nik_wali || '';
    document.getElementById('editBeratBadan').value     = lansia.berat_badan || '';
    document.getElementById('editTinggiBadan').value    = lansia.tinggi_badan || '';
    document.getElementById('editTekananDarah').value   = lansia.tekanan_darah || '';
    document.getElementById('editGulaDarah').value      = lansia.gula_darah || '';
    document.getElementById('editKolesterol').value     = lansia.kolesterol || '';
    document.getElementById('editAsamUrat').value       = lansia.asam_urat || '';

    openModal('modalEdit');
}

async function submitEdit(e) {
    e.preventDefault();
    const id  = document.getElementById('editId').value;
    const btn = document.getElementById('btnSaveEdit');
    const form = document.getElementById('formEdit');
    const formData = new FormData(form);
    const payload  = {};
    formData.forEach((v, k) => { if (v) payload[k] = v; });

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch(`/lansia/api/kunjungan/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            toast('Data berhasil diperbarui!', 'success');
            closeModal('modalEdit');
            loadLansia();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
    }
}

// ============================================================
// HAPUS
// ============================================================
function confirmHapus(id, nama) {
    hapusId = id;
    document.getElementById('hapusNama').textContent = nama;
    document.getElementById('btnKonfirmasiHapus').onclick = () => doHapus(id);
    openModal('modalHapus');
}

async function doHapus(id) {
    const btn = document.getElementById('btnKonfirmasiHapus');
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

    try {
        const res  = await fetch(`/lansia/api/kunjungan/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        const data = await res.json();

        if (data.success) {
            toast(data.message, 'success');
            closeModal('modalHapus');
            loadLansia();
        } else {
            toast(data.message || 'Gagal menghapus', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-trash"></i> Ya, Hapus';
    }
}

// ============================================================
// TAMBAH LANSIA BARU
// ============================================================
async function submitTambah(e) {
    e.preventDefault();
    const btn  = document.getElementById('btnTambah');
    const form = document.getElementById('formTambah');
    const fd   = new FormData(form);
    const payload = {};
    fd.forEach((v, k) => { if (v) payload[k] = v; });

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch('/lansia/api/kunjungan', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            toast(data.message, 'success');
            form.reset();
            switchTab('tabel');
            loadLansia();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Data Kunjungan';
    }
}

// ============================================================
// MODAL HELPERS
// ============================================================
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });
});

// ============================================================
// INIT
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    loadLansia();
    loadLansiaSelect(); // Load daftar lansia untuk select
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab === 'tambah') switchTab(tab);
});

// ============================================================
// LOAD DAFTAR LANSIA UNTUK SELECT
// ============================================================
async function loadLansiaSelect() {
    try {
        const res = await fetch('/lansia/api/kunjungan?limit=500', {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const result = await res.json();
        
        if (!result.success) {
            console.error('Failed to load lansia list');
            return;
        }
        
        const select = document.getElementById('selectLansia');
        if (!select) return;
        
        select.innerHTML = '<option value="">-- Pilih Lansia --</option>';
        result.data.forEach(l => {
            const option = document.createElement('option');
            option.value = l.id;
            option.textContent = `${l.nama_lansia || 'Tanpa Nama'} - ${l.nik_lansia || 'Tanpa NIK'} (${l.usia || '-'})`;
            option.dataset.nama = l.nama_lansia || '-';
            option.dataset.nik = l.nik_lansia || '-';
            option.dataset.usia = l.usia || '-';
            option.dataset.jk = l.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
            select.appendChild(option);
        });
    } catch (err) {
        console.error('Error loading lansia select:', err);
    }
}

// ============================================================
// LOAD INFO LANSIA TERPILIH
// ============================================================
function loadLansiaInfo(lansiaId) {
    const select = document.getElementById('selectLansia');
    const info = document.getElementById('lansiaInfo');
    
    if (!lansiaId || !select || !info) {
        if (info) info.style.display = 'none';
        return;
    }
    
    const option = select.options[select.selectedIndex];
    if (!option || !option.dataset.nama) {
        info.style.display = 'none';
        return;
    }
    
    document.getElementById('infoNama').textContent = option.dataset.nama;
    document.getElementById('infoNik').textContent = option.dataset.nik;
    document.getElementById('infoUsia').textContent = option.dataset.usia;
    document.getElementById('infoJK').textContent = option.dataset.jk;
    
    info.style.display = 'block';
}

// ============================================================
// TOGGLE KELUHAN
// ============================================================
function toggleKeluhan(checkbox) {
    const group = document.getElementById('groupKeluhan');
    if (group) {
        group.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            const textarea = group.querySelector('textarea');
            if (textarea) textarea.value = '';
        }
    }
}

// ============================================================
// TOGGLE KELUHAN KUNJUNGAN
// ============================================================
function toggleKeluhanKunjungan(checkbox) {
    const group = document.getElementById('groupKeluhanKunjungan');
    if (group) {
        group.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            const textarea = group.querySelector('textarea');
            if (textarea) textarea.value = '';
        }
    }
}

// ============================================================
// OPEN KUNJUNGAN SELANJUTNYA
// ============================================================
async function openKunjunganSelanjutnya(id) {
    try {
        const res = await fetch(`/lansia/api/kunjungan/${id}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin'
        });
        const data = await res.json();
        
        if (!data.success) {
            toast('Gagal memuat data lansia', 'error');
            return;
        }
        
        const d = data.data;
        
        // Set ID
        document.getElementById('kunjunganLansiaId').value = id;
        
        // Set info lansia (read-only display)
        document.getElementById('kunjNama').textContent = d.nama_lansia || 'Tanpa Nama';
        document.getElementById('kunjNik').textContent = d.nik_lansia || '-';
        document.getElementById('kunjUsia').textContent = d.umur_display || '-';
        document.getElementById('kunjJK').textContent = d.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
        
        // Pre-fill form dengan data lansia saat ini (bisa diupdate)
        document.getElementById('kunjNamaLengkap').value = d.nama_lansia || '';
        document.getElementById('kunjNikLansia').value = d.nik_lansia || '';
        document.getElementById('kunjAlamat').value = d.alamat_domisili || '';
        document.getElementById('kunjRtRw').value = d.rt_rw || '';
        document.getElementById('kunjNamaWali').value = d.nama_wali || '';
        document.getElementById('kunjNikWali').value = d.nik_wali || '';
        document.getElementById('kunjHpWali').value = d.hp_kontak_wali || '';
        
        // Reset form kunjungan
        const form = document.getElementById('formKunjunganSelanjutnya');
        const kunjunganFields = ['tanggal_kunjungan', 'berat_badan', 'tinggi_badan', 'tekanan_darah', 
                                 'gula_darah', 'kolesterol', 'asam_urat', 'catatan_bidan'];
        kunjunganFields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input && field !== 'tanggal_kunjungan') input.value = '';
        });
        
        // Reset keluhan
        form.querySelector('[name="ada_keluhan"]').checked = false;
        form.querySelector('[name="keluhan"]').value = '';
        document.getElementById('groupKeluhanKunjungan').style.display = 'none';
        
        // Reset obat dan vitamin
        form.querySelectorAll('[name="obat_diberikan[]"] option').forEach(opt => opt.selected = false);
        form.querySelectorAll('[name="vitamin_diberikan[]"] option').forEach(opt => opt.selected = false);
        
        openModal('modalKunjunganSelanjutnya');
    } catch (err) {
        console.error('Error loading lansia data:', err);
        toast('Koneksi gagal', 'error');
    }
}

// ============================================================
// SUBMIT KUNJUNGAN SELANJUTNYA
// ============================================================
async function submitKunjunganSelanjutnya(e) {
    e.preventDefault();
    const btn = document.getElementById('btnKunjunganSelanjutnya');
    const form = document.getElementById('formKunjunganSelanjutnya');
    const lansiaId = document.getElementById('kunjunganLansiaId').value;
    const fd = new FormData(form);
    const payload = {};
    
    fd.forEach((v, k) => {
        if (k.endsWith('[]')) {
            if (!payload[k]) payload[k] = [];
            if (v) payload[k].push(v);
        } else {
            if (v) payload[k] = v;
        }
    });
    
    // Convert array keys
    if (payload['obat_diberikan[]']) {
        payload['obat_diberikan'] = payload['obat_diberikan[]'];
        delete payload['obat_diberikan[]'];
    }
    if (payload['vitamin_diberikan[]']) {
        payload['vitamin_diberikan'] = payload['vitamin_diberikan[]'];
        delete payload['vitamin_diberikan[]'];
    }
    
    // Convert ada_keluhan to boolean
    payload['ada_keluhan'] = payload['ada_keluhan'] === '1';
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    
    try {
        const res = await fetch(`/lansia/api/kunjungan-selanjutnya/${lansiaId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        
        if (data.success) {
            toast(data.message, 'success');
            form.reset();
            closeModal('modalKunjunganSelanjutnya');
            loadLansia(); // Reload table
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        console.error('Error submitting kunjungan:', err);
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan Kunjungan';
    }
}
</script>
@endsection
