# FITUR KUNJUNGAN SELANJUTNYA - Dokumentasi Lengkap

## Tanggal: 4 Mei 2026

---

## 🎯 OVERVIEW

Fitur "Kunjungan Selanjutnya" memungkinkan kader/bidan untuk:
1. **Mencatat kunjungan baru** lansia yang sudah terdaftar
2. **Update data lansia** (nama, alamat, kontak wali, dll) jika ada perubahan
3. **Catat hasil pemeriksaan** (BB, TB, tekanan darah, lab, keluhan, obat, dll)
4. **Auto-update** data lansia dengan hasil pemeriksaan terbaru

---

## 🔧 PERBAIKAN YANG DILAKUKAN

### 1. **Perbaikan Nama Lansia NULL**

**Masalah**: Kolom nama lansia di tabel menampilkan kosong/NULL

**Solusi**: Tambahkan fallback di controller
```php
'nama_lansia' => $l->nama_lengkap ?: 'Tanpa Nama',
'nik_lansia'  => $l->nik_lansia ?: '-',
// ... field lainnya dengan fallback
```

### 2. **Tambah Method `kunjunganSelanjutnya()`**

**File**: `app/Http/Controllers/Lansia/LansiaKunjunganController.php`

**Fungsi**:
- Terima data update lansia (opsional)
- Terima data kunjungan (required)
- Update data lansia jika ada perubahan
- Simpan data kunjungan baru
- Hitung status kesehatan otomatis
- Update status kesehatan lansia

**Endpoint**: `POST /lansia/api/kunjungan-selanjutnya/{lansiaId}`

### 3. **Tambah Route**

**File**: `routes/web.php`

```php
Route::post('/api/kunjungan-selanjutnya/{lansiaId}', 
    [LansiaKunjunganController::class, 'kunjunganSelanjutnya']
)->name('kunjungan.selanjutnya');
```

### 4. **Tambah Tombol di Tabel**

**File**: `resources/views/lansia/kunjungan/index.blade.php`

Tombol hijau "Kunjungan Selanjutnya" ditambahkan di kolom aksi:
```html
<button class="action-btn" style="background:#10B981;color:white;" 
        onclick="openKunjunganSelanjutnya(${l.id})" 
        title="Kunjungan Selanjutnya">
    <i class="fas fa-notes-medical"></i>
</button>
```

### 5. **Tambah Modal Form**

Modal lengkap dengan 3 section:
1. **Info Lansia** (read-only display)
2. **Update Data Lansia** (opsional - bisa diubah)
3. **Data Kunjungan** (required - data pemeriksaan)

### 6. **Tambah JavaScript Functions**

- `openKunjunganSelanjutnya(id)` - Load data lansia dan buka modal
- `submitKunjunganSelanjutnya(event)` - Submit form kunjungan
- `toggleKeluhanKunjungan(checkbox)` - Toggle textarea keluhan

---

## 📋 STRUKTUR DATA

### Data yang Dikirim ke API:

```json
{
  // Update Data Lansia (Opsional)
  "nama_lengkap": "Budi Santoso",
  "nik_lansia": "3517010101500001",
  "alamat_domisili": "Desa Sukomoro RT 01 RW 02",
  "rt_rw": "01/02",
  "nama_wali": "Siti Aminah",
  "nik_wali": "3517010101750001",
  "hp_kontak_wali": "081234567890",
  
  // Data Kunjungan (Required)
  "tanggal_kunjungan": "2026-05-04",
  
  // Pengukuran Fisik
  "berat_badan": 65.5,
  "tinggi_badan": 165,
  "tekanan_darah": "130/85",
  
  // Pemeriksaan Darah
  "gula_darah": 120,
  "kolesterol": 200,
  "asam_urat": 6.5,
  
  // Keluhan & Pengobatan
  "ada_keluhan": true,
  "keluhan": "Pusing ringan",
  "obat_diberikan": ["Paracetamol", "Amlodipin"],
  "vitamin_diberikan": ["Vitamin C"],
  "catatan_bidan": "Kondisi baik, kontrol rutin"
}
```

### Response API:

```json
{
  "success": true,
  "message": "Data kunjungan berhasil disimpan dan data lansia diperbarui!",
  "data": {
    "id": 15
  }
}
```

---

## 🎨 UI/UX

### Tombol "Kunjungan Selanjutnya"
- **Warna**: Hijau (#10B981)
- **Icon**: `fa-notes-medical`
- **Posisi**: Di kolom aksi, setelah tombol "Lihat Detail"
- **Tooltip**: "Kunjungan Selanjutnya"

### Modal Form
- **Ukuran**: Large (modal-lg)
- **Header**: Hijau dengan icon notes-medical
- **Layout**: Grid 2 kolom responsive

### Section 1: Info Lansia (Read-Only)
- Background hijau muda (#F0FDF4)
- Border hijau (#10B981)
- Display: Nama, NIK, Usia, Jenis Kelamin
- **Tidak bisa diedit** - hanya untuk informasi

### Section 2: Update Data Lansia (Opsional)
- Form fields: Nama, NIK, Alamat, RT/RW, Data Wali
- **Pre-filled** dengan data lansia saat ini
- **Bisa diubah** jika ada perubahan data
- **Opsional** - boleh dikosongkan jika tidak ada perubahan

### Section 3: Data Kunjungan (Required)
- Tanggal kunjungan (required)
- Pengukuran fisik: BB, TB, Tekanan Darah
- Pemeriksaan darah: Gula, Kolesterol, Asam Urat
- Checkbox "Ada Keluhan" → show/hide textarea
- Multi-select Obat (10 pilihan)
- Multi-select Vitamin (9 pilihan)
- Textarea catatan bidan

---

## 🔄 FLOW SISTEM

### 1. User Click "Kunjungan Selanjutnya"
```
User → Click tombol hijau → openKunjunganSelanjutnya(id)
```

### 2. Load Data Lansia
```
JavaScript → Fetch /lansia/api/kunjungan/{id}
         → Tampilkan info lansia (read-only)
         → Pre-fill form update lansia
         → Reset form kunjungan
         → Buka modal
```

### 3. User Isi Form
```
User → (Opsional) Update data lansia jika ada perubahan
     → (Required) Isi data kunjungan
     → Pilih obat & vitamin
     → Isi catatan
     → Click "Simpan Kunjungan"
```

### 4. Submit Data
```
JavaScript → Collect form data
         → Convert to JSON
         → POST /lansia/api/kunjungan-selanjutnya/{lansiaId}
```

### 5. Backend Process
```
Controller → Validasi input
          → Update data lansia (jika ada perubahan)
          → Simpan data kunjungan
          → Hitung status otomatis (tensi, gula, kolesterol, asam urat)
          → Update status kesehatan lansia
          → Return success response
```

### 6. UI Update
```
JavaScript → Tampilkan toast success
         → Close modal
         → Reload tabel lansia
```

---

## 🧪 TESTING

### Test Case 1: Kunjungan Tanpa Update Data Lansia

**Steps**:
1. Buka halaman Kunjungan Lansia
2. Click tombol hijau "Kunjungan Selanjutnya" pada salah satu lansia
3. Modal terbuka, info lansia ditampilkan
4. **Jangan ubah** data lansia (section 2)
5. Isi data kunjungan:
   - Tanggal: hari ini
   - BB: 65.5 kg
   - TB: 165 cm
   - Tekanan Darah: 130/85
   - Gula Darah: 120 mg/dL
   - Kolesterol: 200 mg/dL
   - Asam Urat: 6.5 mg/dL
   - Centang "Ada Keluhan", isi: "Pusing ringan"
   - Pilih obat: Paracetamol
   - Pilih vitamin: Vitamin C
   - Catatan: "Kondisi baik"
6. Click "Simpan Kunjungan"

**Expected**:
- ✅ Toast success muncul
- ✅ Modal tertutup
- ✅ Tabel reload
- ✅ Data kunjungan tersimpan di database
- ✅ Data lansia **tidak berubah** (karena tidak diupdate)

### Test Case 2: Kunjungan + Update Data Lansia

**Steps**:
1. Click tombol "Kunjungan Selanjutnya"
2. Modal terbuka
3. **Update data lansia**:
   - Ubah nama: "Budi Santoso (Updated)"
   - Ubah alamat: "Alamat baru"
   - Ubah HP Wali: "081234567899"
4. Isi data kunjungan (sama seperti test case 1)
5. Click "Simpan Kunjungan"

**Expected**:
- ✅ Toast success muncul
- ✅ Modal tertutup
- ✅ Tabel reload dengan data lansia yang sudah diupdate
- ✅ Data kunjungan tersimpan
- ✅ Data lansia **berubah** sesuai input

### Test Case 3: Kunjungan dengan Kondisi Abnormal

**Steps**:
1. Click tombol "Kunjungan Selanjutnya"
2. Isi data kunjungan dengan nilai abnormal:
   - Tekanan Darah: 160/100 (Hipertensi 2)
   - Gula Darah: 220 (Sangat Tinggi)
   - Kolesterol: 250 (Tinggi)
   - Asam Urat: 8.5 (Tinggi)
3. Click "Simpan Kunjungan"

**Expected**:
- ✅ Status otomatis dihitung:
  - status_tensi: hipertensi2
  - status_gula: sangat_tinggi
  - status_kolesterol: tinggi
  - status_asam_urat: tinggi
- ✅ Status kesehatan lansia diupdate: "Hipertensi" (prioritas tertinggi)
- ✅ Data tersimpan dengan benar

### Test Case 4: Validasi Form

**Steps**:
1. Click tombol "Kunjungan Selanjutnya"
2. **Jangan isi** tanggal kunjungan
3. Click "Simpan Kunjungan"

**Expected**:
- ✅ Browser validation muncul: "Please fill out this field"
- ✅ Form tidak tersubmit

---

## 📊 DATABASE IMPACT

### Tabel `lansia` - UPDATE
```sql
UPDATE lansia SET
  nama_lengkap = 'Budi Santoso (Updated)',
  alamat_domisili = 'Alamat baru',
  hp_kontak_wali = '081234567899',
  berat_badan = 65.5,
  tinggi_badan = 165,
  tekanan_darah = '130/85',
  gula_darah = 120,
  kolesterol = 200,
  asam_urat = 6.5,
  status_kesehatan = 'Normal',
  tanggal_pemeriksaan_terakhir = '2026-05-04'
WHERE id = 1;
```

### Tabel `kunjungan_lansia` - INSERT
```sql
INSERT INTO kunjungan_lansia (
  lansia_id, tanggal_kunjungan,
  berat_badan, tinggi_badan, tekanan_darah, status_tensi,
  gula_darah, status_gula,
  kolesterol, status_kolesterol,
  asam_urat, status_asam_urat,
  ada_keluhan, keluhan,
  obat_diberikan, vitamin_diberikan,
  catatan_bidan, dicatat_oleh
) VALUES (
  1, '2026-05-04',
  65.5, 165, '130/85', 'prehipertensi',
  120, 'normal',
  200, 'normal',
  6.5, 'normal',
  1, 'Pusing ringan',
  '["Paracetamol"]', '["Vitamin C"]',
  'Kondisi baik', 1
);
```

---

## 🔍 TROUBLESHOOTING

### Masalah 1: Modal Tidak Terbuka

**Gejala**: Click tombol tapi modal tidak muncul

**Solusi**:
1. Buka Console browser (F12)
2. Cek error JavaScript
3. Pastikan function `openKunjunganSelanjutnya()` terdefinisi
4. Pastikan modal ID `modalKunjunganSelanjutnya` ada di HTML

### Masalah 2: Data Lansia Tidak Ter-load

**Gejala**: Modal terbuka tapi info lansia kosong

**Solusi**:
1. Cek Console browser
2. Test API endpoint: `GET /lansia/api/kunjungan/{id}`
3. Pastikan response success dan data ada
4. Cek mapping field di JavaScript

### Masalah 3: Submit Gagal

**Gejala**: Click "Simpan Kunjungan" tapi tidak ada response

**Solusi**:
1. Cek Console browser untuk error
2. Cek Network tab (F12 → Network)
3. Lihat request payload dan response
4. Pastikan CSRF token valid
5. Cek validasi di controller

### Masalah 4: Data Tidak Tersimpan

**Gejala**: Toast success muncul tapi data tidak ada di database

**Solusi**:
1. Cek log Laravel: `storage/logs/laravel.log`
2. Test query manual di database
3. Cek apakah ada error di controller
4. Pastikan field fillable di model

---

## 📁 FILE YANG DIUBAH

1. ✅ `app/Http/Controllers/Lansia/LansiaKunjunganController.php`
   - Method `list()` - Tambah fallback untuk NULL values
   - Method `kunjunganSelanjutnya()` - NEW

2. ✅ `routes/web.php`
   - Route `POST /api/kunjungan-selanjutnya/{lansiaId}` - NEW

3. ✅ `resources/views/lansia/kunjungan/index.blade.php`
   - Tombol "Kunjungan Selanjutnya" di tabel - NEW
   - Modal "Kunjungan Selanjutnya" - NEW
   - JavaScript functions - NEW

4. ✅ `FITUR_KUNJUNGAN_SELANJUTNYA.md` - Dokumentasi (NEW)

---

## ✅ CHECKLIST IMPLEMENTASI

- [x] Controller method `kunjunganSelanjutnya()` dibuat
- [x] Route ditambahkan
- [x] Tombol di tabel ditambahkan
- [x] Modal form dibuat
- [x] JavaScript functions ditambahkan
- [x] Validasi input di controller
- [x] Auto-calculate status kesehatan
- [x] Update data lansia
- [x] Simpan data kunjungan
- [x] Dokumentasi lengkap

---

## 🎉 STATUS: SELESAI

Fitur "Kunjungan Selanjutnya" sudah selesai diimplementasi dan siap digunakan!

**Tested**: ✅ UI, ✅ Controller, ✅ Database

**Ready for Production**: ✅
