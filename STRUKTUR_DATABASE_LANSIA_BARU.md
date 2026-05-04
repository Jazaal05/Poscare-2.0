# STRUKTUR DATABASE LANSIA - PEMBERSIHAN TOTAL

## 📅 Tanggal: 4 Mei 2026

---

## 🎯 TUJUAN PEMBERSIHAN

Menyusun ulang struktur database modul lansia agar:
1. ✅ Selaras dengan UI yang sudah ada
2. ✅ Mendukung fitur Dashboard, Kunjungan, Jadwal, Edukasi, Laporan, Pengaturan
3. ✅ Efisien untuk query agregasi dan reporting
4. ✅ Menyimpan history kunjungan dengan baik
5. ✅ Validasi data yang ketat

---

## 📊 STRUKTUR TABEL

### 1. TABEL `lansia` (Data Master)

**Fungsi**: Menyimpan data pribadi lansia dan status kesehatan terkini

```sql
CREATE TABLE lansia (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Data Pribadi
    nik_lansia VARCHAR(16) UNIQUE NULL,
    nama_lengkap VARCHAR(255) NOT NULL,
    tgl_lahir DATE NOT NULL,
    tempat_lahir VARCHAR(100) NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    
    -- Alamat
    alamat_domisili TEXT NULL,
    rt_rw VARCHAR(10) NULL,
    
    -- Data Keluarga
    nama_kk VARCHAR(255) NULL,
    nama_wali VARCHAR(255) NULL,
    nik_wali VARCHAR(16) NULL,
    hp_kontak_wali VARCHAR(20) NULL,
    
    -- Data Kesehatan Terkini (dari kunjungan terakhir)
    berat_badan DECIMAL(5,2) NULL,
    tinggi_badan DECIMAL(5,2) NULL,
    tekanan_darah VARCHAR(20) NULL,
    gula_darah DECIMAL(5,2) NULL,
    kolesterol DECIMAL(5,2) NULL,
    asam_urat DECIMAL(5,2) NULL,
    
    -- Status Kesehatan
    status_kesehatan ENUM(
        'Sehat',
        'Hipertensi',
        'Diabetes',
        'Kolesterol Tinggi',
        'Asam Urat Tinggi',
        'Penyakit Jantung',
        'Stroke',
        'Lainnya'
    ) DEFAULT 'Sehat',
    
    tanggal_pemeriksaan_terakhir DATE NULL,
    
    -- Metadata
    dicatat_oleh BIGINT UNSIGNED NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (dicatat_oleh) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_nik (nik_lansia),
    INDEX idx_nama (nama_lengkap),
    INDEX idx_deleted (is_deleted),
    INDEX idx_status (status_kesehatan)
);
```

**Field Penting**:
- `tgl_lahir`: Untuk menghitung usia (YEAR(NOW()) - YEAR(tgl_lahir))
- `status_kesehatan`: Untuk grafik dashboard
- `is_deleted`: Soft delete, data tidak benar-benar dihapus
- Data kesehatan: Selalu diupdate dari kunjungan terakhir

---

### 2. TABEL `kunjungan_lansia` (History Pemeriksaan)

**Fungsi**: Menyimpan history semua kunjungan dan pemeriksaan kesehatan

```sql
CREATE TABLE kunjungan_lansia (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    lansia_id BIGINT UNSIGNED NOT NULL,
    tanggal_kunjungan DATE NOT NULL,
    
    -- Pengukuran Fisik
    berat_badan DECIMAL(5,2) NULL,
    tinggi_badan DECIMAL(5,2) NULL,
    tekanan_darah VARCHAR(20) NULL,
    status_tensi ENUM('normal', 'prehipertensi', 'hipertensi1', 'hipertensi2') NULL,
    
    -- Pemeriksaan Darah
    gula_darah DECIMAL(5,2) NULL,
    status_gula ENUM('rendah', 'normal', 'tinggi', 'sangat_tinggi') NULL,
    kolesterol DECIMAL(5,2) NULL,
    status_kolesterol ENUM('normal', 'batas', 'tinggi') NULL,
    asam_urat DECIMAL(5,2) NULL,
    status_asam_urat ENUM('normal', 'tinggi') NULL,
    
    -- Keluhan & Pengobatan
    ada_keluhan BOOLEAN DEFAULT FALSE,
    keluhan TEXT NULL,
    obat_diberikan JSON NULL,
    vitamin_diberikan JSON NULL,
    
    -- Catatan
    catatan_bidan TEXT NULL,
    dicatat_oleh BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (lansia_id) REFERENCES lansia(id) ON DELETE CASCADE,
    FOREIGN KEY (dicatat_oleh) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_lansia (lansia_id),
    INDEX idx_tanggal (tanggal_kunjungan),
    INDEX idx_lansia_tanggal (lansia_id, tanggal_kunjungan)
);
```

**Logika Penting**:
- Setiap kunjungan = 1 baris baru (HISTORY)
- Status auto-calculated berdasarkan nilai pengukuran
- JSON untuk obat & vitamin (array)
- Cascade delete: jika lansia dihapus, kunjungan ikut terhapus

---

### 3. TABEL `jadwal_lansia` (Jadwal Kegiatan)

**Fungsi**: Menyimpan jadwal kegiatan posyandu lansia

```sql
CREATE TABLE jadwal_lansia (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    judul_kegiatan VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    tanggal DATE NOT NULL,
    waktu_mulai TIME NOT NULL, -- Format 24 jam (HH:mm)
    lokasi VARCHAR(255) NULL,
    jenis_kegiatan ENUM(
        'Posyandu',
        'Senam Lansia',
        'Penyuluhan',
        'Pemeriksaan Kesehatan',
        'Lainnya'
    ) DEFAULT 'Posyandu',
    status ENUM('dijadwalkan', 'terlaksana', 'dibatalkan') DEFAULT 'dijadwalkan',
    
    -- Metadata
    dibuat_oleh BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_tanggal (tanggal),
    INDEX idx_status (status),
    INDEX idx_tanggal_status (tanggal, status)
);
```

**Validasi**:
- `waktu_mulai`: Format 24 jam (00:00 - 23:59)
- `tanggal`: Tidak boleh masa lalu (>= today)
- Tidak ada `waktu_selesai` (dihapus sesuai instruksi)

---

### 4. TABEL `edukasi_lansia` (Konten Edukasi)

**Fungsi**: Menyimpan konten edukasi dari berbagai platform

```sql
CREATE TABLE edukasi_lansia (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    platform ENUM('Youtube', 'Tiktok', 'Facebook', 'Instagram', 'Artikel') NOT NULL,
    tautan VARCHAR(500) NOT NULL,
    thumbnail VARCHAR(500) NULL,
    kategori ENUM(
        'Kesehatan Lansia',
        'Pola Hidup Sehat',
        'Pencegahan Penyakit',
        'Gizi Lansia',
        'Olahraga Lansia',
        'Tips Lansia',
        'Lainnya'
    ) DEFAULT 'Kesehatan Lansia',
    
    -- Metadata
    dibuat_oleh BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_platform (platform),
    INDEX idx_kategori (kategori),
    INDEX idx_active (is_active)
);
```

**Validasi URL**:
- Youtube: Harus mengandung "youtube.com" atau "youtu.be"
- Tiktok: Harus mengandung "tiktok.com"
- Facebook: Harus mengandung "facebook.com" atau "fb.com"
- Instagram: Harus mengandung "instagram.com"
- Artikel: URL valid dengan http/https

---

## 🔄 RELASI ANTAR TABEL

```
users (1) ──┬─> (N) lansia [dicatat_oleh]
            ├─> (N) kunjungan_lansia [dicatat_oleh]
            ├─> (N) jadwal_lansia [dibuat_oleh]
            └─> (N) edukasi_lansia [dibuat_oleh]

lansia (1) ──> (N) kunjungan_lansia [lansia_id]
```

---

## 📈 QUERY UNTUK DASHBOARD

### 1. Total Lansia
```sql
SELECT COUNT(*) as total_lansia
FROM lansia
WHERE is_deleted = FALSE;
```

### 2. Kunjungan Bulan Ini
```sql
SELECT COUNT(*) as kunjungan_bulan_ini
FROM kunjungan_lansia
WHERE MONTH(tanggal_kunjungan) = MONTH(CURDATE())
  AND YEAR(tanggal_kunjungan) = YEAR(CURDATE());
```

### 3. Grafik Distribusi Usia
```sql
SELECT 
    CASE
        WHEN YEAR(CURDATE()) - YEAR(tgl_lahir) BETWEEN 60 AND 65 THEN '60-65 tahun'
        WHEN YEAR(CURDATE()) - YEAR(tgl_lahir) BETWEEN 66 AND 70 THEN '66-70 tahun'
        WHEN YEAR(CURDATE()) - YEAR(tgl_lahir) BETWEEN 71 AND 75 THEN '71-75 tahun'
        WHEN YEAR(CURDATE()) - YEAR(tgl_lahir) BETWEEN 76 AND 80 THEN '76-80 tahun'
        ELSE '80+ tahun'
    END as rentang_usia,
    COUNT(*) as jumlah
FROM lansia
WHERE is_deleted = FALSE
GROUP BY rentang_usia
ORDER BY rentang_usia;
```

### 4. Grafik Status Kesehatan
```sql
SELECT 
    status_kesehatan,
    COUNT(*) as jumlah
FROM lansia
WHERE is_deleted = FALSE
GROUP BY status_kesehatan
ORDER BY jumlah DESC;
```

### 5. Trend Kunjungan (6 Bulan Terakhir)
```sql
SELECT 
    DATE_FORMAT(tanggal_kunjungan, '%Y-%m') as bulan,
    COUNT(*) as jumlah_kunjungan
FROM kunjungan_lansia
WHERE tanggal_kunjungan >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY bulan
ORDER BY bulan;
```

---

## 🔧 LOGIKA FITUR

### 1. TAMBAH DATA LANSIA BARU

**Flow**:
1. Insert ke tabel `lansia`
2. Insert ke tabel `kunjungan_lansia` (kunjungan pertama)
3. Update `lansia.tanggal_pemeriksaan_terakhir`

```php
// Controller logic
DB::transaction(function () use ($data) {
    // 1. Insert lansia
    $lansia = Lansia::create([
        'nama_lengkap' => $data['nama_lengkap'],
        'tgl_lahir' => $data['tgl_lahir'],
        // ... field lainnya
    ]);
    
    // 2. Insert kunjungan pertama
    $kunjungan = KunjunganLansia::create([
        'lansia_id' => $lansia->id,
        'tanggal_kunjungan' => now(),
        'berat_badan' => $data['berat_badan'],
        // ... field lainnya
    ]);
    
    // 3. Update data kesehatan lansia
    $lansia->update([
        'berat_badan' => $data['berat_badan'],
        'tinggi_badan' => $data['tinggi_badan'],
        'status_kesehatan' => $this->hitungStatusKesehatan($kunjungan),
        'tanggal_pemeriksaan_terakhir' => now(),
    ]);
});
```

### 2. KUNJUNGAN SELANJUTNYA

**Flow**:
1. Insert baris baru ke `kunjungan_lansia` (HISTORY)
2. Update data kesehatan di tabel `lansia` (LATEST)
3. Update `status_kesehatan` berdasarkan hasil pemeriksaan

```php
// Controller logic
DB::transaction(function () use ($lansiaId, $data) {
    // 1. Insert kunjungan baru
    $kunjungan = KunjunganLansia::create([
        'lansia_id' => $lansiaId,
        'tanggal_kunjungan' => $data['tanggal_kunjungan'],
        'berat_badan' => $data['berat_badan'],
        // ... field lainnya
        'status_tensi' => $this->hitungStatusTensi($data['tekanan_darah']),
        'status_gula' => $this->hitungStatusGula($data['gula_darah']),
        // ... status lainnya
    ]);
    
    // 2. Update lansia dengan data terbaru
    $lansia = Lansia::find($lansiaId);
    $lansia->update([
        'berat_badan' => $data['berat_badan'],
        'tinggi_badan' => $data['tinggi_badan'],
        'tekanan_darah' => $data['tekanan_darah'],
        'gula_darah' => $data['gula_darah'],
        'kolesterol' => $data['kolesterol'],
        'asam_urat' => $data['asam_urat'],
        'status_kesehatan' => $this->hitungStatusKesehatan($kunjungan),
        'tanggal_pemeriksaan_terakhir' => $data['tanggal_kunjungan'],
    ]);
});
```

### 3. LAPORAN (Export Excel)

**Query**:
```sql
SELECT 
    l.nama_lengkap,
    l.nik_lansia,
    l.jenis_kelamin,
    YEAR(CURDATE()) - YEAR(l.tgl_lahir) as usia,
    k.tanggal_kunjungan,
    k.berat_badan,
    k.tinggi_badan,
    k.tekanan_darah,
    k.status_tensi,
    k.gula_darah,
    k.status_gula,
    k.kolesterol,
    k.status_kolesterol,
    k.asam_urat,
    k.status_asam_urat,
    k.ada_keluhan,
    k.keluhan,
    k.obat_diberikan,
    k.vitamin_diberikan,
    k.catatan_bidan
FROM kunjungan_lansia k
JOIN lansia l ON k.lansia_id = l.id
WHERE l.is_deleted = FALSE
ORDER BY k.tanggal_kunjungan DESC, l.nama_lengkap ASC;
```

**Organisasi Excel**:
- Sheet 1: Data Terbaru (kunjungan 3 bulan terakhir)
- Sheet 2: Data Lama (kunjungan > 3 bulan)
- Atau: Grouping berdasarkan bulan

---

## 🎯 VALIDASI

### 1. Lansia
```php
$rules = [
    'nama_lengkap' => 'required|string|max:255',
    'nik_lansia' => 'nullable|string|size:16|unique:lansia,nik_lansia',
    'tgl_lahir' => 'required|date|before:today',
    'jenis_kelamin' => 'required|in:L,P',
    'berat_badan' => 'nullable|numeric|min:30|max:150',
    'tinggi_badan' => 'nullable|numeric|min:100|max:200',
    'tekanan_darah' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
    'gula_darah' => 'nullable|numeric|min:50|max:500',
    'kolesterol' => 'nullable|numeric|min:100|max:400',
    'asam_urat' => 'nullable|numeric|min:1|max:15',
];
```

### 2. Jadwal
```php
$rules = [
    'judul_kegiatan' => 'required|string|max:255',
    'tanggal' => 'required|date|after_or_equal:today',
    'waktu_mulai' => 'required|date_format:H:i', // Format 24 jam
    'jenis_kegiatan' => 'required|in:Posyandu,Senam Lansia,Penyuluhan,Pemeriksaan Kesehatan,Lainnya',
];
```

### 3. Edukasi
```php
$rules = [
    'judul' => 'required|string|max:255',
    'platform' => 'required|in:Youtube,Tiktok,Facebook,Instagram,Artikel',
    'tautan' => [
        'required',
        'url',
        function ($attribute, $value, $fail) use ($request) {
            $platform = $request->platform;
            $valid = match($platform) {
                'Youtube' => str_contains($value, 'youtube.com') || str_contains($value, 'youtu.be'),
                'Tiktok' => str_contains($value, 'tiktok.com'),
                'Facebook' => str_contains($value, 'facebook.com') || str_contains($value, 'fb.com'),
                'Instagram' => str_contains($value, 'instagram.com'),
                'Artikel' => true,
                default => false,
            };
            
            if (!$valid) {
                $fail("Tautan tidak sesuai dengan platform $platform");
            }
        }
    ],
    'kategori' => 'required|in:Kesehatan Lansia,Pola Hidup Sehat,Pencegahan Penyakit,Gizi Lansia,Olahraga Lansia,Tips Lansia,Lainnya',
];
```

---

## 🚀 MIGRASI

### Jalankan Migration Baru
```bash
# Backup database dulu!
php artisan db:backup

# Drop tabel lama (HATI-HATI!)
php artisan migrate:rollback --step=1

# Jalankan migration baru
php artisan migrate

# Seed data dummy (opsional)
php artisan db:seed --class=LansiaSeeder
```

### Atau Manual
```sql
-- Drop tabel lama
DROP TABLE IF EXISTS edukasi_lansia;
DROP TABLE IF EXISTS jadwal_lansia;
DROP TABLE IF EXISTS kunjungan_lansia;
DROP TABLE IF EXISTS lansia;

-- Jalankan migration baru
php artisan migrate
```

---

## ✅ CHECKLIST IMPLEMENTASI

- [x] Migration database baru
- [ ] Model Lansia dengan relasi
- [ ] Model KunjunganLansia dengan relasi
- [ ] Model JadwalLansia
- [ ] Model EdukasiLansia
- [ ] Controller Dashboard (agregasi)
- [ ] Controller Kunjungan (tambah + history)
- [ ] Controller Jadwal (validasi 24 jam)
- [ ] Controller Laporan (export Excel)
- [ ] Controller Edukasi (validasi URL)
- [ ] Controller Pengaturan (manajemen user mobile)
- [ ] Seeder untuk data dummy
- [ ] Testing semua fitur

---

## 📝 CATATAN PENTING

1. **Backup Database**: Selalu backup sebelum migrasi!
2. **Data Lama**: Jika ada data lama, buat script migrasi data
3. **Testing**: Test semua query sebelum production
4. **Index**: Sudah ditambahkan untuk performa optimal
5. **Soft Delete**: Gunakan `is_deleted` bukan hard delete

---

## 🎉 STATUS

Migration database baru sudah siap! ✅

**Next Steps**:
1. Review struktur database
2. Jalankan migration
3. Buat Model dengan relasi
4. Implementasi Controller
5. Testing

