@extends('layouts.lansia')
@section('title', 'Jadwal Posyandu Lansia')

@section('styles')
<style>
    body { background: linear-gradient(135deg,#E8F4FF 0%,#D4E9FF 50%,#C5E2FF 100%) !important; }
    .main-card { background:#fff; border-radius:16px; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.08); }
    .page-title { font-size:2rem; font-weight:700; color:#1E293B; margin-bottom:6px; }
    .page-subtitle { color:#64748B; font-size:1rem; margin-bottom:24px; }
    .tab-nav { display:flex; gap:0; margin-bottom:28px; border-bottom:2px solid #E2E8F0; }
    .tab-btn { padding:12px 24px; border:none; background:none; font-size:0.95rem; font-weight:600; color:#64748B; cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-2px; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; }
    .tab-btn.active { color:#10B981; border-bottom-color:#10B981; }
    .tab-btn:hover:not(.active) { color:#10B981; }
    .tab-content { display:none; } .tab-content.active { display:block; animation:fadeIn 0.3s ease; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
    .form-section { background:#fff; border-radius:12px; padding:28px; margin-bottom:28px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .section-title { font-size:1.25rem; font-weight:700; color:#1E293B; margin-bottom:22px; display:flex; align-items:center; gap:10px; }
    .section-title i { color:#10B981; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
    @media(max-width:768px){ .form-row{ grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-group label { font-weight:600; color:#374151; font-size:0.9rem; }
    .form-group input, .form-group select, .form-group textarea { padding:11px 14px; border:1px solid #E2E8F0; border-radius:8px; font-size:0.95rem; color:#1F2937; transition:all 0.2s; background:#fff; width:100%; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.1); }
    .form-group textarea { resize:vertical; min-height:100px; }
    .form-actions { display:flex; gap:14px; margin-top:22px; }
    .error-hint { color:#EF4444; font-size:12px; display:none; margin-top:4px; }
    .btn { padding:10px 22px; border:none; border-radius:8px; font-size:0.9rem; font-weight:600; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:7px; }
    .btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
    .btn-success { background:#10B981; color:#fff; } .btn-success:hover { background:#059669; }
    .btn-secondary { background:#F3F4F6; color:#4B5563; } .btn-secondary:hover { background:#E5E7EB; transform:none; box-shadow:none; }
    .btn-primary { background:#10B981; color:#fff; } .btn-primary:hover { background:#059669; }
    .btn-danger  { background:#EF4444; color:#fff; } .btn-danger:hover  { background:#DC2626; }
    .btn-sm { padding:7px 14px; font-size:0.82rem; }
    .jadwal-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:20px; margin-top:8px; }
    .jadwal-card { background:#fff; border-radius:12px; padding:20px; border-left:4px solid #10B981; box-shadow:0 2px 8px rgba(0,0,0,0.07); transition:all 0.3s ease; }
    .jadwal-card:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,0.12); }
    .jadwal-card.selesai { border-left-color:#6B7280; } .jadwal-card.dibatalkan { border-left-color:#EF4444; }
    .jadwal-status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:0.72rem; font-weight:700; text-transform:uppercase; margin-bottom:10px; }
    .badge-terjadwal { background:#10B981; color:#fff; } .badge-selesai { background:#6B7280; color:#fff; } .badge-dibatalkan { background:#EF4444; color:#fff; }
    .jadwal-title { font-size:1.05rem; font-weight:700; color:#1E293B; margin-bottom:14px; }
    .jadwal-info { display:flex; flex-direction:column; gap:8px; margin-bottom:16px; }
    .info-row { display:flex; align-items:flex-start; gap:10px; color:#4B5563; font-size:0.9rem; }
    .info-row i { color:#10B981; width:16px; flex-shrink:0; margin-top:2px; }
    .keterangan-box { background:#FEF3C7; border-radius:8px; padding:10px 12px; margin-top:8px; }
    .keterangan-box i { color:#F59E0B !important; }
    .keterangan-box span { color:#78350F; font-size:0.88rem; line-height:1.5; }
    .keterangan-label { font-weight:700; color:#92400E; display:block; margin-bottom:3px; font-size:0.82rem; }
    .jadwal-actions { display:flex; gap:10px; margin-top:16px; }
    .jadwal-actions .btn { flex:1; justify-content:center; }
    .empty-state { text-align:center; padding:50px 20px; color:#94A3B8; grid-column:1/-1; }
    .empty-state i { font-size:3.5rem; margin-bottom:16px; display:block; }
    .empty-state h3 { color:#64748B; margin-bottom:8px; }
    #toast { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
    .toast-item { padding:13px 18px; border-radius:10px; color:#fff; font-size:14px; font-weight:600; box-shadow:0 4px 16px rgba(0,0,0,0.15); animation:slideInRight 0.3s ease; display:flex; align-items:center; gap:10px; min-width:260px; }
    @keyframes slideInRight { from{opacity:0;transform:translateX(100%)} to{opacity:1;transform:translateX(0)} }
    .toast-success { background:#10B981; } .toast-error { background:#EF4444; } .toast-warning { background:#F59E0B; }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:16px; padding:28px; width:90%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:slideUp 0.3s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:14px; border-bottom:2px solid #E5E7EB; }
    .modal-title { font-size:1.15rem; font-weight:700; color:#1E3A5F; }
    .modal-close { background:none; border:none; font-size:22px; color:#9CA3AF; cursor:pointer; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:#FEE2E2; color:#EF4444; }
</style>
@endsection

@section('content')
<div id="toast"></div>
<div class="main-card">
    <div>
        <h1 class="page-title">Jadwal Posyandu Lansia</h1>
        <p class="page-subtitle">Kelola jadwal kegiatan posyandu lansia</p>
    </div>
    <div class="tab-nav">
        <button class="tab-btn active" id="tabBtnJadwal" onclick="switchTab('jadwal')">
            <i class="fas fa-calendar-alt"></i> Jadwal Bulanan Posyandu
        </button>
        <button class="tab-btn" id="tabBtnImunisasi" onclick="switchTab('imunisasi')">
            <i class="fas fa-syringe"></i> Jadwal Imunisasi
        </button>
    </div>

    <div id="tab-jadwal" class="tab-content active">
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-calendar-plus"></i> Buat Jadwal Bulanan Posyandu Baru</h2>
            <form id="formJadwal" onsubmit="submitJadwal(event)">
                <input type="hidden" id="jadwalId">
                <div class="form-row">
                    <div class="form-group"><label>Nama Kegiatan</label><input type="text" id="namaKegiatan" placeholder="Contoh: Posyandu Lansia Bulan November" required></div>
                    <div class="form-group"><label>Tanggal</label><input type="date" id="tanggalJadwal" required><small class="error-hint" id="errorTanggal">❌ Tanggal tidak boleh di masa lalu!</small></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Waktu Mulai</label><input type="time" id="waktuJadwal" required></div>
                    <div class="form-group"><label>Lokasi</label>
                        <select id="lokasiJadwal" required>
                            <option value="">-- Pilih Lokasi Posyandu --</option>
                            <option>Posyandu Sedap Malam di Ds. Bagorwetan 1</option>
                            <option>Posyandu Kenanga di Ds. Bagorwetan 2</option>
                            <option>Posyandu Anggrek di Dsn. Padasan</option>
                            <option>Posyandu Teratai di Dsn. Ngronggo</option>
                            <option>Posyandu Flamboyan di Dsn. Jogolewon</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Keterangan &amp; Catatan Khusus</label><textarea id="keteranganJadwal" placeholder="Catatan tambahan..."></textarea></div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success" id="btnSubmitJadwal"><i class="fas fa-check-circle"></i> Buat Jadwal</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFormJadwal()"><i class="fas fa-times"></i> Cancel / Reset</button>
                </div>
            </form>
        </div>
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-list"></i> Daftar Jadwal Bulanan Posyandu</h2>
            <div class="jadwal-grid" id="jadwalCardsContainer"><div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div></div>
        </div>
    </div>

    <div id="tab-imunisasi" class="tab-content">
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-calendar-plus"></i> Buat Jadwal Imunisasi Baru</h2>
            <form id="formImunisasi" onsubmit="submitImunisasi(event)">
                <input type="hidden" id="imunisasiId">
                <div class="form-row">
                    <div class="form-group"><label>Tanggal</label><input type="date" id="tanggalImunisasi" required><small class="error-hint" id="errorTanggalImunisasi">❌ Tanggal tidak boleh di masa lalu!</small></div>
                    <div class="form-group"><label>Waktu Mulai</label><input type="time" id="waktuImunisasi" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Jenis Imunisasi</label><select id="jenisImunisasi" required><option value="">-- Pilih Jenis Imunisasi --</option></select></div>
                    <div class="form-group"><label>Lokasi</label>
                        <select id="lokasiImunisasi" required>
                            <option value="">-- Pilih Lokasi Posyandu --</option>
                            <option>Posyandu Sedap Malam di Ds. Bagorwetan 1</option>
                            <option>Posyandu Kenanga di Ds. Bagorwetan 2</option>
                            <option>Posyandu Anggrek di Dsn. Padasan</option>
                            <option>Posyandu Teratai di Dsn. Ngronggo</option>
                            <option>Posyandu Flamboyan di Dsn. Jogolewon</option>
                        </select>
                    </div>
                </div>
                <div class="form-group"><label>Keterangan &amp; Catatan Khusus</label><textarea id="keteranganImunisasi" placeholder="Catatan tambahan..."></textarea></div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success" id="btnSubmitImunisasi"><i class="fas fa-check-circle"></i> Buat Jadwal</button>
                    <button type="button" class="btn btn-secondary" onclick="resetFormImunisasi()"><i class="fas fa-times"></i> Cancel / Reset</button>
                </div>
            </form>
        </div>
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-list"></i> Daftar Jadwal Imunisasi</h2>
            <div class="jadwal-grid" id="imunisasiCardsContainer"><div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Memuat data...</p></div></div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalHapus">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="color:#EF4444;"><i class="fas fa-trash"></i> Hapus Jadwal</h3>
            <button class="modal-close" onclick="closeModal('modalHapus')">&times;</button>
        </div>
        <p style="color:#374151;margin-bottom:20px;">Hapus jadwal <strong id="hapusNama"></strong>?</p>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button class="btn btn-danger" id="btnKonfirmasiHapus"><i class="fas fa-trash"></i> Ya, Hapus</button>
            <button class="btn btn-secondary" onclick="closeModal('modalHapus')">Batal</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Script identik dengan jadwal balita --}}
<script>
let hapusId = null, allJadwal = [], allImunisasi = [];
function toast(msg, type='success') {
    const icons={success:'check-circle',error:'times-circle',warning:'exclamation-triangle'};
    const el=document.createElement('div'); el.className=`toast-item toast-${type}`;
    el.innerHTML=`<i class="fas fa-${icons[type]||'info-circle'}"></i> ${msg}`;
    document.getElementById('toast').appendChild(el); setTimeout(()=>el.remove(),4000);
}
function openModal(id){document.getElementById(id).classList.add('active');}
function closeModal(id){document.getElementById(id).classList.remove('active');}
function formatTgl(d){if(!d)return'-';return new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});}
function switchTab(tab){
    ['jadwal','imunisasi'].forEach(t=>{
        document.getElementById('tab-'+t).classList.toggle('active',t===tab);
        document.getElementById('tabBtn'+t.charAt(0).toUpperCase()+t.slice(1)).classList.toggle('active',t===tab);
    });
}
function buildCard(j,isImunisasi=false){
    const status=j.status||'Terjadwal';
    const badgeClass=status==='Terjadwal'?'badge-terjadwal':status==='Selesai'?'badge-selesai':'badge-dibatalkan';
    const cardClass=status==='Selesai'?'selesai':status==='Dibatalkan'?'dibatalkan':'';
    const icon=isImunisasi?'💉':'✅';
    const ket=j.keterangan?`<div class="info-row keterangan-box"><i class="fas fa-sticky-note"></i><span><span class="keterangan-label">📝 Catatan:</span>${j.keterangan}</span></div>`:'';
    return `<div class="jadwal-card ${cardClass}" id="card-${j.id}">
        <span class="jadwal-status-badge ${badgeClass}">${icon} ${status}</span>
        <h3 class="jadwal-title">${j.nama_kegiatan}</h3>
        <div class="jadwal-info">
            <div class="info-row"><i class="fas fa-calendar"></i><span>${formatTgl(j.tanggal)}</span></div>
            <div class="info-row"><i class="fas fa-clock"></i><span>${j.waktu_mulai||'-'} WIB</span></div>
            <div class="info-row"><i class="fas fa-map-marker-alt"></i><span>${j.lokasi||'-'}</span></div>
            ${ket}
        </div>
        <div class="jadwal-actions">
            <button class="btn btn-primary btn-sm" onclick='openEdit(${JSON.stringify(j)},${isImunisasi})'><i class="fas fa-edit"></i> Edit</button>
            <button class="btn btn-danger btn-sm" onclick="confirmHapus(${j.id},'${j.nama_kegiatan.replace(/'/g,"\\'")}')"><i class="fas fa-trash"></i> Hapus</button>
        </div>
    </div>`;
}
async function loadJadwal(){
    try{
        const res=await fetch('{{ route("jadwal.list") }}',{headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin'});
        const data=await res.json(); const list=data.data||[];
        allJadwal=list.filter(j=>j.jenis_kegiatan!=='Imunisasi');
        allImunisasi=list.filter(j=>j.jenis_kegiatan==='Imunisasi');
        renderCards();
    }catch(e){toast('Gagal memuat data jadwal','error');}
}
function renderCards(){
    document.getElementById('jadwalCardsContainer').innerHTML=allJadwal.length?allJadwal.map(j=>buildCard(j,false)).join(''):`<div class="empty-state"><i class="fas fa-calendar-times"></i><h3>Belum Ada Jadwal</h3></div>`;
    document.getElementById('imunisasiCardsContainer').innerHTML=allImunisasi.length?allImunisasi.map(j=>buildCard(j,true)).join(''):`<div class="empty-state"><i class="fas fa-syringe"></i><h3>Belum Ada Jadwal Imunisasi</h3></div>`;
}
async function loadVaksin(){
    try{
        const res=await fetch('{{ route("vaksin.list") }}',{headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin'});
        const data=await res.json(); const sel=document.getElementById('jenisImunisasi');
        (data.data||[]).forEach(v=>{const o=document.createElement('option');o.value=v.nama_vaksin;o.textContent=v.nama_vaksin;sel.appendChild(o);});
    }catch(e){}
}
async function submitJadwal(e){
    e.preventDefault(); const id=document.getElementById('jadwalId').value; const isEdit=!!id;
    const tgl=document.getElementById('tanggalJadwal').value;
    const errTgl=document.getElementById('errorTanggal');
    if(!isEdit&&tgl&&new Date(tgl)<new Date(new Date().toDateString())){errTgl.style.display='block';return;}
    errTgl.style.display='none';
    const btn=document.getElementById('btnSubmitJadwal');
    btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const body={nama_kegiatan:document.getElementById('namaKegiatan').value,jenis_kegiatan:'Penimbangan',tanggal:tgl,waktu_mulai:document.getElementById('waktuJadwal').value,lokasi:document.getElementById('lokasiJadwal').value,keterangan:document.getElementById('keteranganJadwal').value,status:'Terjadwal'};
    try{
        const res=await fetch(isEdit?`/api/jadwal/${id}`:'/api/jadwal',{method:isEdit?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin',body:JSON.stringify(body)});
        const data=await res.json();
        if(data.success){toast(data.message||(isEdit?'Jadwal diperbarui!':'Jadwal berhasil dibuat!'),'success');resetFormJadwal();loadJadwal();}
        else toast(data.message||'Gagal menyimpan','error');
    }catch(err){toast('Koneksi gagal','error');}
    finally{btn.disabled=false;btn.innerHTML='<i class="fas fa-check-circle"></i> '+(isEdit?'Simpan Perubahan':'Buat Jadwal');}
}
async function submitImunisasi(e){
    e.preventDefault(); const id=document.getElementById('imunisasiId').value; const isEdit=!!id;
    const tgl=document.getElementById('tanggalImunisasi').value;
    const errTgl=document.getElementById('errorTanggalImunisasi');
    if(!isEdit&&tgl&&new Date(tgl)<new Date(new Date().toDateString())){errTgl.style.display='block';return;}
    errTgl.style.display='none';
    const btn=document.getElementById('btnSubmitImunisasi');
    btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    const jenisVal=document.getElementById('jenisImunisasi').value;
    const body={nama_kegiatan:jenisVal?`Imunisasi ${jenisVal}`:'Jadwal Imunisasi',jenis_kegiatan:'Imunisasi',tanggal:tgl,waktu_mulai:document.getElementById('waktuImunisasi').value,lokasi:document.getElementById('lokasiImunisasi').value,keterangan:document.getElementById('keteranganImunisasi').value,status:'Terjadwal'};
    try{
        const res=await fetch(isEdit?`/api/jadwal/${id}`:'/api/jadwal',{method:isEdit?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin',body:JSON.stringify(body)});
        const data=await res.json();
        if(data.success){toast(data.message||(isEdit?'Jadwal diperbarui!':'Jadwal imunisasi berhasil dibuat!'),'success');resetFormImunisasi();loadJadwal();}
        else toast(data.message||'Gagal menyimpan','error');
    }catch(err){toast('Koneksi gagal','error');}
    finally{btn.disabled=false;btn.innerHTML='<i class="fas fa-check-circle"></i> '+(isEdit?'Simpan Perubahan':'Buat Jadwal');}
}
function openEdit(j,isImunisasi){
    if(isImunisasi){
        switchTab('imunisasi');
        document.getElementById('imunisasiId').value=j.id;
        document.getElementById('tanggalImunisasi').value=j.tanggal;
        document.getElementById('waktuImunisasi').value=j.waktu_mulai;
        document.getElementById('lokasiImunisasi').value=j.lokasi;
        document.getElementById('keteranganImunisasi').value=j.keterangan||'';
        document.getElementById('btnSubmitImunisasi').innerHTML='<i class="fas fa-check-circle"></i> Simpan Perubahan';
    }else{
        switchTab('jadwal');
        document.getElementById('jadwalId').value=j.id;
        document.getElementById('namaKegiatan').value=j.nama_kegiatan;
        document.getElementById('tanggalJadwal').value=j.tanggal;
        document.getElementById('waktuJadwal').value=j.waktu_mulai;
        document.getElementById('lokasiJadwal').value=j.lokasi;
        document.getElementById('keteranganJadwal').value=j.keterangan||'';
        document.getElementById('btnSubmitJadwal').innerHTML='<i class="fas fa-check-circle"></i> Simpan Perubahan';
    }
    window.scrollTo({top:0,behavior:'smooth'});
}
function resetFormJadwal(){document.getElementById('formJadwal').reset();document.getElementById('jadwalId').value='';document.getElementById('btnSubmitJadwal').innerHTML='<i class="fas fa-check-circle"></i> Buat Jadwal';document.getElementById('errorTanggal').style.display='none';}
function resetFormImunisasi(){document.getElementById('formImunisasi').reset();document.getElementById('imunisasiId').value='';document.getElementById('btnSubmitImunisasi').innerHTML='<i class="fas fa-check-circle"></i> Buat Jadwal';document.getElementById('errorTanggalImunisasi').style.display='none';}
function confirmHapus(id,nama){hapusId=id;document.getElementById('hapusNama').textContent=nama;openModal('modalHapus');}
document.getElementById('btnKonfirmasiHapus').addEventListener('click',async()=>{
    if(!hapusId)return;
    const btn=document.getElementById('btnKonfirmasiHapus');
    btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin"></i> Menghapus...';
    try{
        const res=await fetch(`/api/jadwal/${hapusId}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},credentials:'same-origin'});
        const data=await res.json();
        if(data.success){toast('Jadwal berhasil dihapus!','success');closeModal('modalHapus');loadJadwal();}
        else toast(data.message||'Gagal menghapus','error');
    }catch(err){toast('Koneksi gagal','error');}
    finally{btn.disabled=false;btn.innerHTML='<i class="fas fa-trash"></i> Ya, Hapus';hapusId=null;}
});
document.querySelectorAll('.modal-overlay').forEach(m=>m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('active');}));
document.addEventListener('DOMContentLoaded',()=>{loadJadwal();loadVaksin();});
</script>
@endsection
