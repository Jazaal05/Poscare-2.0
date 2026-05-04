

<?php $__env->startSection('title', 'Data Anak'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
    .container { max-width:1340px; margin:0 auto; padding:16px 20px; }
    .card { background:#fff; border-radius:16px; padding:20px; box-shadow:0 6px 24px rgba(16,24,40,0.06); margin-bottom:24px; }
    .page-title { font-size:28px; font-weight:700; color:#1E3A5F; margin-bottom:4px; }
    .page-subtitle { font-size:14px; color:#64748B; margin-bottom:24px; }

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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="toast"></div>

<div class="page-header" style="margin-bottom:20px;">
    <h1 class="page-title">Data Anak</h1>
    <p class="page-subtitle"><i class="fas fa-shield-alt" style="color:#246BCE; margin-right:6px;"></i> Halaman Admin - Kelola data anak dan registrasi orang tua</p>
</div>


<div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('tabel')"><i class="fas fa-table"></i> Tabel Data Anak</button>
    <button class="tab-btn" onclick="switchTab('registrasi')"><i class="fas fa-user-plus"></i> Registrasi Data Anak (Lengkap)</button>
    <button class="tab-btn" onclick="switchTab('tambah')"><i class="fas fa-plus-circle"></i> Tambah Data Anak (Orang Tua Existing)</button>
</div>


<div id="tab-tabel" class="tab-content active">
    <div class="card">
        <div class="search-bar">
            <div class="search-wrapper" style="flex:1;">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" style="width:100%;" placeholder="Cari nama anak, NIK, atau nama orang tua..." oninput="debounceSearch()">
            </div>
        </div>
        <div class="table-nav-wrapper" style="display:flex;align-items:center;gap:8px;">
            <button class="table-nav-arrow" id="scrollLeft" onclick="scrollTable(-300)" title="Geser kiri">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="table-slider-container" id="tableSlider">
                <div class="table-wrapper">
                    <table id="anakTable" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NAMA ANAK</th>
                                <th>NIK ANAK</th>
                                <th>JK</th>
                                <th>TANGGAL LAHIR</th>
                                <th>TEMPAT LAHIR</th>
                                <th>USIA</th>
                                <th>BB (KG)</th>
                                <th>TB (CM)</th>
                                <th>LK (CM)</th>
                                <th>STATUS GIZI</th>
                                <th>ALAMAT</th>
                                <th>NAMA IBU</th>
                                <th>NIK IBU</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="anakTableBody">
                            <tr class="loading-row"><td colspan="15"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>
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


<div id="tab-registrasi" class="tab-content">
    <div class="card">
        <h3 style="font-size:18px;font-weight:700;color:#1E3A5F;margin-bottom:20px;"><i class="fas fa-user-plus" style="color:#246BCE;"></i> Registrasi Data Anak Lengkap dengan Data Orang Tua</h3>
        <form id="formRegistrasi" onsubmit="submitRegistrasi(event)">
            <div class="form-grid">
                <div class="form-section-title"><i class="fas fa-home"></i> Data Orang Tua & Keluarga</div>
                <div class="form-group">
                    <label>Nama Kepala Keluarga <span style="color:red">*</span></label>
                    <input type="text" name="nama_kk" placeholder="Nama KK" required>
                </div>
                <div class="form-group">
                    <label>Nama Ibu <span style="color:red">*</span></label>
                    <input type="text" name="nama_ibu" placeholder="Nama lengkap ibu" required>
                </div>
                <div class="form-group">
                    <label>NIK Ibu <span style="color:red">*</span></label>
                    <input type="text" name="nik_ibu" placeholder="16 digit NIK ibu" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>Nama Ayah <span style="color:red">*</span></label>
                    <input type="text" name="nama_ayah" placeholder="Nama lengkap ayah" required>
                </div>
                <div class="form-group">
                    <label>NIK Ayah <span style="color:red">*</span></label>
                    <input type="text" name="nik_ayah" placeholder="16 digit NIK ayah" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>No HP Kontak <span style="color:red">*</span></label>
                    <input type="text" name="no_hp_ibu" placeholder="+62..." required>
                    <small style="color:#64748B;font-size:12px;">Klik untuk otomatis +62, lalu ketik nomor HP</small>
                </div>
                <div class="form-group">
                    <label>RT <span style="color:red">*</span></label>
                    <input type="text" name="rt" placeholder="001" maxlength="3" required>
                    <small style="color:#64748B;font-size:12px;">Contoh: 001 atau 12</small>
                </div>
                <div class="form-group">
                    <label>RW <span style="color:red">*</span></label>
                    <input type="text" name="rw" placeholder="005" maxlength="3" required>
                    <small style="color:#64748B;font-size:12px;">Contoh: 005 atau 8</small>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" placeholder="Jalan, Kelurahan, Kecamatan, Kota" rows="2" class="form-group input" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>

                <div class="form-section-title"><i class="fas fa-baby"></i> Data Anak</div>
                <div class="form-group">
                    <label>Nama Anak <span style="color:red">*</span></label>
                    <input type="text" name="nama_anak" placeholder="Nama lengkap anak" required>
                </div>
                <div class="form-group">
                    <label>NIK Anak <span style="color:red">*</span></label>
                    <input type="text" name="nik_anak" placeholder="16 digit NIK anak" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <span style="color:red">*</span></label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_lahir" max="<?php echo e(date('Y-m-d')); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tempat Lahir <span style="color:red">*</span></label>
                    <input type="text" name="tempat_lahir" placeholder="Kota/kabupaten lahir" required>
                </div>

                <div class="form-section-title"><i class="fas fa-weight"></i> Data Pengukuran (Opsional)</div>
                <div class="form-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number" name="berat_badan" placeholder="Contoh: 7.5" step="0.1" min="1.5" max="30">
                </div>
                <div class="form-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number" name="tinggi_badan" placeholder="Contoh: 65" step="0.1" min="40" max="130">
                </div>
                <div class="form-group">
                    <label>Lingkar Kepala (cm)</label>
                    <input type="number" name="lingkar_kepala" placeholder="Contoh: 42" step="0.1" min="20" max="60">
                </div>
                <div class="form-group">
                    <label>Cara Ukur</label>
                    <select name="cara_ukur">
                        <option value="">-- Pilih --</option>
                        <option value="berbaring">Berbaring (< 2 tahun)</option>
                        <option value="berdiri">Berdiri (≥ 2 tahun)</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end;">
                <button type="reset" class="btn btn-outline"><i class="fas fa-undo"></i> Reset Form</button>
                <button type="submit" class="btn btn-primary" id="btnRegistrasi"><i class="fas fa-save"></i> Simpan Registrasi</button>
            </div>
        </form>
    </div>
</div>


<div id="tab-tambah" class="tab-content">
    <div class="card">
        <h3 style="font-size:18px;font-weight:700;color:#1E3A5F;margin-bottom:8px;"><i class="fas fa-plus-circle" style="color:#246BCE;"></i> Tambah Data Anak (Anak ke-2, ke-3, dst)</h3>
        <p style="font-size:13px;color:#64748B;margin-bottom:20px;">Gunakan fitur ini untuk menambah anak berikutnya dari orang tua yang sudah terdaftar.</p>
        <form id="formTambah" onsubmit="submitTambah(event)">
            <div class="form-grid">
                <div class="form-section-title"><i class="fas fa-search"></i> Cari Orang Tua</div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Cari Nama Ibu <span style="color:red">*</span></label>
                    <div class="autocomplete-wrapper">
                        <input type="text" id="parentSearch" class="autocomplete-input" placeholder="Ketik nama ibu (min 2 karakter)..." autocomplete="off">
                        <div class="autocomplete-dropdown" id="parentDropdown"></div>
                    </div>
                    <input type="hidden" id="selectedParentId" name="parent_id">
                    <div class="autocomplete-selected-info" id="parentSelectedInfo">
                        <div class="info-name" id="parentSelectedName"></div>
                        <div class="info-detail" id="parentSelectedDetail"></div>
                    </div>
                </div>

                <div class="form-section-title"><i class="fas fa-baby"></i> Data Anak Baru</div>
                <div class="form-group">
                    <label>Nama Anak <span style="color:red">*</span></label>
                    <input type="text" name="nama_anak" placeholder="Nama lengkap anak" required>
                </div>
                <div class="form-group">
                    <label>NIK Anak <span style="color:red">*</span></label>
                    <input type="text" name="nik_anak" placeholder="16 digit NIK anak" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <span style="color:red">*</span></label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_lahir" max="<?php echo e(date('Y-m-d')); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tempat Lahir <span style="color:red">*</span></label>
                    <input type="text" name="tempat_lahir" placeholder="Kota/kabupaten lahir" required>
                </div>
                <div class="form-group">
                    <label>Anak Ke- <span style="color:red">*</span></label>
                    <input type="number" name="anak_ke" id="anakKeInput" value="2" min="1" max="20" readonly style="background:#f9fafb;">
                    <small style="color:#64748B;font-size:12px;"><i class="fas fa-info-circle"></i> Otomatis terisi setelah memilih orang tua</small>
                </div>

                <div class="form-section-title"><i class="fas fa-weight"></i> Data Pengukuran</div>
                <div class="form-group">
                    <label>Berat Badan (kg) <span style="color:red">*</span></label>
                    <input type="number" name="berat_badan" placeholder="0.0" step="0.1" min="1.5" max="30" required>
                    <small style="color:#64748B;font-size:12px;">Masukkan berat badan dalam kg (contoh: 12.5)</small>
                </div>
                <div class="form-group">
                    <label>Tinggi Badan (cm) <span style="color:red">*</span></label>
                    <input type="number" name="tinggi_badan" placeholder="0" step="0.1" min="40" max="130" required>
                    <small style="color:#64748B;font-size:12px;">Masukkan tinggi badan dalam cm (contoh: 83.5)</small>
                </div>
                <div class="form-group">
                    <label>Lingkar Kepala (cm) <span style="color:red">*</span></label>
                    <input type="number" name="lingkar_kepala" placeholder="0.0" step="0.1" min="20" max="60" required>
                    <small style="color:#64748B;font-size:12px;">Masukkan lingkar kepala dalam cm (contoh: 43.2)</small>
                </div>
                <div class="form-group">
                    <label>Cara Ukur <span style="color:red">*</span></label>
                    <select name="cara_ukur" required>
                        <option value="">-- Pilih Cara Ukur --</option>
                        <option value="berbaring">Berbaring (< 2 tahun)</option>
                        <option value="berdiri">Berdiri (≥ 2 tahun)</option>
                    </select>
                    <small style="color:#64748B;font-size:12px;">Pilih metode pengukuran tinggi badan</small>
                </div>
            </div>
            <div style="margin-top:24px;display:flex;gap:12px;justify-content:flex-end;">
                <button type="reset" class="btn btn-outline" onclick="clearParentSelection()"><i class="fas fa-undo"></i> Reset Form</button>
                <button type="submit" class="btn btn-primary" id="btnTambah"><i class="fas fa-save"></i> Simpan Data Anak</button>
            </div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="modalDetail">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-user-circle" style="color:#246BCE;"></i> Detail Data Anak</h3>
            <button class="modal-close" onclick="closeModal('modalDetail')">&times;</button>
        </div>
        <div id="detailContent">Memuat...</div>
        <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-warning" id="btnEditFromDetail" onclick="openEditFromDetail()"><i class="fas fa-edit"></i> Edit</button>
            <button class="btn btn-outline" onclick="closeModal('modalDetail')">Tutup</button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-edit" style="color:#F59E0B;"></i> Edit Data Anak</h3>
            <button class="modal-close" onclick="closeModal('modalEdit')">&times;</button>
        </div>
        <form id="formEdit" onsubmit="submitEdit(event)">
            <input type="hidden" id="editId">
            <div class="form-grid">
                <div class="form-section-title"><i class="fas fa-baby"></i> Data Anak</div>
                <div class="form-group">
                    <label>Nama Anak <span style="color:red">*</span></label>
                    <input type="text" id="editNamaAnak" name="nama_anak" required>
                </div>
                <div class="form-group">
                    <label>NIK Anak <span style="color:red">*</span></label>
                    <input type="text" id="editNikAnak" name="nik_anak" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <span style="color:red">*</span></label>
                    <select id="editJenisKelamin" name="jenis_kelamin" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <span style="color:red">*</span></label>
                    <input type="date" id="editTanggalLahir" name="tanggal_lahir" max="<?php echo e(date('Y-m-d')); ?>" required>
                </div>
                <div class="form-group">
                    <label>Tempat Lahir <span style="color:red">*</span></label>
                    <input type="text" id="editTempatLahir" name="tempat_lahir" required>
                </div>
                <div class="form-group">
                    <label>Anak Ke-</label>
                    <input type="number" id="editAnakKe" name="anak_ke" min="1" max="20">
                </div>

                <div class="form-section-title"><i class="fas fa-home"></i> Data Orang Tua & Keluarga</div>
                <div class="form-group">
                    <label>Nama Kepala Keluarga</label>
                    <input type="text" id="editNamaKK" name="nama_kk">
                </div>
                <div class="form-group">
                    <label>Nama Ibu <span style="color:red">*</span></label>
                    <input type="text" id="editNamaIbu" name="nama_ibu" required>
                </div>
                <div class="form-group">
                    <label>NIK Ibu <span style="color:red">*</span></label>
                    <input type="text" id="editNikIbu" name="nik_ibu" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>Nama Ayah <span style="color:red">*</span></label>
                    <input type="text" id="editNamaAyah" name="nama_ayah" required>
                </div>
                <div class="form-group">
                    <label>NIK Ayah <span style="color:red">*</span></label>
                    <input type="text" id="editNikAyah" name="nik_ayah" maxlength="16" required>
                </div>
                <div class="form-group">
                    <label>No HP Kontak <span style="color:red">*</span></label>
                    <input type="text" id="editHp" name="hp_kontak_ortu" required>
                </div>
                <div class="form-group">
                    <label>RT</label>
                    <input type="text" id="editRt" name="rt" maxlength="3">
                </div>
                <div class="form-group">
                    <label>RW</label>
                    <input type="text" id="editRw" name="rw" maxlength="3">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Alamat Lengkap</label>
                    <textarea id="editAlamat" name="alamat" rows="2" style="padding:10px 14px;border:2px solid #E5E7EB;border-radius:8px;font-size:14px;resize:vertical;"></textarea>
                </div>
            </div>
            <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end;">
                <button type="submit" class="btn btn-warning" id="btnSaveEdit"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
            </div>
        </form>
    </div>
</div>


<div class="modal-overlay" id="modalHapus">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Data Anak</h3>
            <button class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
        </div>
        <p style="color:#374151;margin-bottom:20px;">Apakah Anda yakin ingin menghapus data anak <strong id="hapusNama"></strong>? Data tidak akan hilang permanen (soft delete).</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-danger" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Ya, Hapus</button>
            <button class="btn btn-outline" onclick="closeModal('modalHapus')">Batal</button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// ============================================================
// STATE
// ============================================================
let allAnak = [], currentPage = 1, perPage = 10, searchTimer = null, currentDetailId = null, hapusId = null;

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
    document.querySelectorAll('.tab-btn').forEach((b,i) => b.classList.toggle('active', ['tabel','registrasi','tambah'][i] === tab));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
}

// ============================================================
// LOAD DATA ANAK
// ============================================================
async function loadAnak() {
    const search = document.getElementById('searchInput').value.trim();
    const tbody  = document.getElementById('anakTableBody');
    tbody.innerHTML = '<tr class="loading-row"><td colspan="15"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>';

    try {
        const url = `<?php echo e(route('anak.list')); ?>?q=${encodeURIComponent(search)}&limit=200`;
        const res = await fetch(url, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        const data = await res.json();

        if (!data.success) { toast(data.message || 'Gagal memuat data', 'error'); return; }

        allAnak = data.data;
        const tc = document.getElementById('totalCount');
        if (tc) tc.textContent = allAnak.length;
        currentPage = 1;
        renderTable();
    } catch (e) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="15"><i class="fas fa-exclamation-triangle"></i> Gagal memuat data</td></tr>';
    }
}

function renderTable() {
    const tbody = document.getElementById('anakTableBody');
    const start = (currentPage - 1) * perPage;
    const page  = allAnak.slice(start, start + perPage);

    if (!page.length) {
        tbody.innerHTML = '<tr class="empty-row"><td colspan="15"><i class="fas fa-inbox"></i> Tidak ada data</td></tr>';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }

    const statusBadge = (s) => {
        const map = {
            'Gizi Baik':            'badge-gizi-baik',
            'Stunting':             'badge-stunting',
            'Risiko Stunting':      'badge-risk-stunting',
            'Gizi Kurang':          'badge-gizi-kurang',
            'Beresiko Gizi Kurang': 'badge-risk-gizi-kurang',
            'Beresiko Gizi Lebih':  'badge-risk-gizi-lebih',
            'Gizi Lebih':           'badge-gizi-lebih',
            'Obesitas':             'badge-obesitas',
        };
        const cls = map[s] || 'badge-secondary';
        return `<span class="badge ${cls}">${s || '-'}</span>`;
    };

    const jkBadge = (jk) => {
        if (jk === 'L') return `<span class="badge badge-info" style="font-size:11px;padding:3px 8px;">L</span>`;
        if (jk === 'P') return `<span class="badge badge-warning" style="font-size:11px;padding:3px 8px;">P</span>`;
        return '-';
    };

    tbody.innerHTML = page.map((a) => `
        <tr id="row-${a.id}">
            <td>${a.id}</td>
            <td class="nama-anak-cell"><strong>${a.nama_anak}</strong></td>
            <td><code style="font-size:12px;">${a.nik_anak || '-'}</code></td>
            <td>${jkBadge(a.jenis_kelamin)}</td>
            <td>${a.tanggal_lahir ? new Date(a.tanggal_lahir).toLocaleDateString('id-ID') : '-'}</td>
            <td>${a.tempat_lahir || '-'}</td>
            <td>${a.usia || '-'}</td>
            <td>${a.berat_badan ? parseFloat(a.berat_badan).toFixed(2) : '-'}</td>
            <td>${a.tinggi_badan ? parseFloat(a.tinggi_badan).toFixed(2) : '-'}</td>
            <td>${a.lingkar_kepala ? parseFloat(a.lingkar_kepala).toFixed(2) : '-'}</td>
            <td>${statusBadge(a.status_gizi)}</td>
            <td>${a.alamat_domisili || '-'}</td>
            <td>${a.nama_ibu || '-'}</td>
            <td><code style="font-size:12px;">${a.nik_ibu || '-'}</code></td>
            <td>
                <div class="action-group">
                    <a href="/grafik-pertumbuhan/${a.id}" class="action-btn -chart" title="Grafik Pertumbuhan"><i class="fas fa-chart-line"></i></a>
                    <button class="action-btn -view" onclick="openDetail(${a.id})" title="Lihat Detail"><i class="fas fa-eye"></i></button>
                    <button class="action-btn -edit" onclick="openEdit(${a.id})" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn -delete" onclick="confirmHapus(${a.id}, '${a.nama_anak.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>
    `).join('');

    renderPagination();
    updateScrollArrows();
}

function renderPagination() {
    const total = Math.ceil(allAnak.length / perPage);
    const container = document.getElementById('paginationContainer');

    if (total <= 1) { container.style.display = 'none'; return; }
    container.style.display = 'flex';

    document.getElementById('btnPrev').disabled = currentPage === 1;
    document.getElementById('btnNext').disabled = currentPage === total;
    document.getElementById('pageInfo').textContent = `Halaman ${currentPage} dari ${total}`;
    document.getElementById('pageTotal').textContent = `${allAnak.length} total data`;

    // Render dots
    let dots = '';
    for (let i = 1; i <= total; i++) {
        dots += `<div class="pagination-dot ${i===currentPage?'active':''}" onclick="goPage(${i})" title="Halaman ${i}"></div>`;
    }
    document.getElementById('paginationDots').innerHTML = dots;
}

function goPage(p) {
    const total = Math.ceil(allAnak.length / perPage);
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
    searchTimer = setTimeout(loadAnak, 400);
}

// ============================================================
// DETAIL
// ============================================================
async function openDetail(id) {
    currentDetailId = id;
    document.getElementById('detailContent').innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x" style="color:#246BCE;"></i></div>';
    openModal('modalDetail');

    try {
        const res  = await fetch(`/api/anak/${id}`, { headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }, credentials: 'same-origin' });
        const data = await res.json();
        if (!data.success) { document.getElementById('detailContent').innerHTML = '<p style="color:red;">Gagal memuat detail.</p>'; return; }

        const d = data.data;
        document.getElementById('detailContent').innerHTML = `
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-baby"></i> Data Anak</div>
                <div class="detail-grid">
                    <div class="detail-item"><label>Nama Anak</label><span>${d.nama_anak}</span></div>
                    <div class="detail-item"><label>NIK Anak</label><span>${d.nik_anak||'-'}</span></div>
                    <div class="detail-item"><label>Jenis Kelamin</label><span>${d.jenis_kelamin==='L'?'Laki-laki':'Perempuan'}</span></div>
                    <div class="detail-item"><label>Tanggal Lahir</label><span>${d.tanggal_lahir||'-'}</span></div>
                    <div class="detail-item"><label>Tempat Lahir</label><span>${d.tempat_lahir||'-'}</span></div>
                    <div class="detail-item"><label>Usia</label><span>${d.umur_display||'-'}</span></div>
                    <div class="detail-item"><label>Anak Ke</label><span>${d.anak_ke||1}</span></div>
                    <div class="detail-item"><label>Status Gizi</label><span>${d.status_gizi||'Belum diukur'}</span></div>
                </div>
            </div>
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-home"></i> Data Orang Tua</div>
                <div class="detail-grid">
                    <div class="detail-item"><label>Nama Ibu</label><span>${d.nama_ibu||'-'}</span></div>
                    <div class="detail-item"><label>NIK Ibu</label><span>${d.nik_ibu||'-'}</span></div>
                    <div class="detail-item"><label>Nama Ayah</label><span>${d.nama_ayah||'-'}</span></div>
                    <div class="detail-item"><label>NIK Ayah</label><span>${d.nik_ayah||'-'}</span></div>
                    <div class="detail-item"><label>No HP</label><span>${d.hp_kontak_ortu||'-'}</span></div>
                    <div class="detail-item"><label>Nama KK</label><span>${d.nama_kk||'-'}</span></div>
                    <div class="detail-item" style="grid-column:1/-1"><label>Alamat</label><span>${d.alamat_domisili||'-'} ${d.rt_rw?'RT/RW '+d.rt_rw:''}</span></div>
                </div>
            </div>
            ${d.berat_badan ? `
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-weight"></i> Pengukuran Terakhir</div>
                <div class="detail-grid">
                    <div class="detail-item"><label>Berat Badan</label><span>${d.berat_badan} kg</span></div>
                    <div class="detail-item"><label>Tinggi Badan</label><span>${d.tinggi_badan} cm</span></div>
                    ${d.lingkar_kepala ? `<div class="detail-item"><label>Lingkar Kepala</label><span>${d.lingkar_kepala} cm</span></div>` : ''}
                    <div class="detail-item"><label>Tanggal Ukur</label><span>${d.tanggal_penimbangan_terakhir||'-'}</span></div>
                </div>
            </div>` : ''}
            <div class="detail-section">
                <div class="detail-section-title"><i class="fas fa-syringe"></i> Imunisasi (${d.imunisasi_count} kali)</div>
                ${d.riwayat_imunisasi?.length ? `<div style="display:flex;flex-wrap:wrap;gap:8px;">${d.riwayat_imunisasi.map(i=>`<span class="badge badge-success">${i.nama_vaksin} (${i.tanggal})</span>`).join('')}</div>` : '<p style="color:#9CA3AF;font-size:13px;">Belum ada riwayat imunisasi</p>'}
            </div>
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
    const anak = allAnak.find(a => a.id === id);
    if (!anak) { toast('Data tidak ditemukan', 'error'); return; }

    // Parse RT/RW from combined rt_rw field (format: "001/005" or "1/5")
    let rt = '', rw = '';
    if (anak.rt_rw) {
        const parts = anak.rt_rw.split('/');
        rt = parts[0] || '';
        rw = parts[1] || '';
    }

    document.getElementById('editId').value          = anak.id;
    document.getElementById('editNamaAnak').value    = anak.nama_anak || '';
    document.getElementById('editNikAnak').value     = anak.nik_anak || '';
    document.getElementById('editJenisKelamin').value= anak.jenis_kelamin || 'L';
    document.getElementById('editTanggalLahir').value= anak.tanggal_lahir || '';
    document.getElementById('editTempatLahir').value = anak.tempat_lahir || '';
    document.getElementById('editAnakKe').value      = anak.anak_ke || 1;
    document.getElementById('editNamaKK').value      = anak.nama_kk || '';
    document.getElementById('editNamaIbu').value     = anak.nama_ibu || '';
    document.getElementById('editNikIbu').value      = anak.nik_ibu || '';
    document.getElementById('editNamaAyah').value    = anak.nama_ayah || '';
    document.getElementById('editNikAyah').value     = anak.nik_ayah || '';
    document.getElementById('editHp').value          = anak.hp_kontak_ortu || '';
    document.getElementById('editRt').value          = rt;
    document.getElementById('editRw').value          = rw;
    document.getElementById('editAlamat').value      = anak.alamat_domisili || '';

    openModal('modalEdit');
}

async function submitEdit(e) {
    e.preventDefault();
    const id  = document.getElementById('editId').value;
    const btn = document.getElementById('btnSaveEdit');
    const form = document.getElementById('formEdit');
    const formData = new FormData(form);
    const payload  = {};
    
    // Collect form data
    formData.forEach((v, k) => { if (v) payload[k] = v; });
    
    // Combine RT and RW into rt_rw field (format: "001/005")
    if (payload.rt || payload.rw) {
        payload.rt_rw = `${payload.rt || ''}/${payload.rw || ''}`;
        delete payload.rt;
        delete payload.rw;
    }
    
    // Rename alamat to alamat_domisili to match backend
    if (payload.alamat) {
        payload.alamat_domisili = payload.alamat;
        delete payload.alamat;
    }

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch(`/api/anak/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            toast('Data berhasil diperbarui!', 'success');
            closeModal('modalEdit');
            loadAnak();
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
        const res  = await fetch(`/api/anak/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        const data = await res.json();

        if (data.success) {
            toast(data.message, 'success');
            closeModal('modalHapus');
            loadAnak();
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
// REGISTRASI LENGKAP
// ============================================================
async function submitRegistrasi(e) {
    e.preventDefault();
    const btn  = document.getElementById('btnRegistrasi');
    const form = document.getElementById('formRegistrasi');
    const fd   = new FormData(form);
    const payload = {};
    fd.forEach((v, k) => { if (v) payload[k] = v; });

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch('<?php echo e(route("anak.store")); ?>', {
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
            loadAnak();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Registrasi';
    }
}

// ============================================================
// TAMBAH ANAK BARU + AUTOCOMPLETE
// ============================================================
let parentSearchTimer = null;

document.getElementById('parentSearch').addEventListener('input', function() {
    clearTimeout(parentSearchTimer);
    const q = this.value.trim();
    if (q.length < 2) { document.getElementById('parentDropdown').style.display = 'none'; return; }
    parentSearchTimer = setTimeout(() => searchParents(q), 300);
});

async function searchParents(q) {
    try {
        const res  = await fetch('<?php echo e(route("anak.parents")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ search: q }),
        });
        const data = await res.json();
        const dd   = document.getElementById('parentDropdown');

        if (!data.success || !data.data.length) {
            dd.innerHTML = '<div class="autocomplete-item"><div class="detail">Tidak ada hasil</div></div>';
            dd.style.display = 'block';
            return;
        }

        dd.innerHTML = data.data.map(p => `
            <div class="autocomplete-item" onclick="selectParent(${JSON.stringify(p).replace(/"/g,'&quot;')})">
                <div class="name">${p.nama_ibu}</div>
                <div class="detail">NIK: ${p.nik_ibu} | ${p.jumlah_anak} anak | ${p.alamat||'-'}</div>
            </div>
        `).join('');
        dd.style.display = 'block';
    } catch (e) {}
}

function selectParent(p) {
    document.getElementById('selectedParentId').value = p.user_id || '';
    document.getElementById('parentSearch').value     = p.nama_ibu;
    document.getElementById('parentDropdown').style.display = 'none';

    const info = document.getElementById('parentSelectedInfo');
    document.getElementById('parentSelectedName').textContent   = `✅ ${p.nama_ibu} (NIK: ${p.nik_ibu})`;
    document.getElementById('parentSelectedDetail').textContent = `Sudah punya ${p.jumlah_anak} anak | ${p.alamat||'-'}`;
    info.style.display = 'block';
}

function clearParentSelection() {
    document.getElementById('selectedParentId').value = '';
    document.getElementById('parentSearch').value     = '';
    document.getElementById('parentSelectedInfo').style.display = 'none';
    document.getElementById('parentDropdown').style.display     = 'none';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.autocomplete-wrapper')) {
        document.getElementById('parentDropdown').style.display = 'none';
    }
});

async function submitTambah(e) {
    e.preventDefault();
    const parentId = document.getElementById('selectedParentId').value;
    if (!parentId) { toast('Pilih orang tua terlebih dahulu!', 'warning'); return; }

    const btn  = document.getElementById('btnTambah');
    const form = document.getElementById('formTambah');
    const fd   = new FormData(form);
    const payload = { parent_id: parseInt(parentId) };
    fd.forEach((v, k) => { if (v && k !== 'parent_id') payload[k] = v; });

    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
        const res  = await fetch('<?php echo e(route("anak.storeTambah")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            toast(data.message, 'success');
            form.reset();
            clearParentSelection();
            switchTab('tabel');
            loadAnak();
        } else {
            toast(data.message || 'Gagal menyimpan', 'error');
        }
    } catch (err) {
        toast('Koneksi gagal', 'error');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Tambah Anak';
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
    loadAnak();
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');
    if (tab === 'tambah' || tab === 'registrasi') switchTab(tab);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\asus\VSCode\poscare-laravel\resources\views/anak/index.blade.php ENDPATH**/ ?>