# IMPLEMENTASI MODUL LANSIA BARU - PANDUAN LENGKAP

## 📅 Tanggal: 4 Mei 2026

---

## 🎯 OVERVIEW

Pembersihan total modul lansia dengan struktur database baru, model yang bersih, dan logika backend yang selaras dengan UI.

### ✅ Yang Sudah Dibuat

1. ✅ Migration database baru (4 tabel)
2. ✅ Model Lansia dengan relasi dan accessor
3. ✅ Model KunjunganLansia dengan kalkulasi status
4. ✅ Model JadwalLansia dengan validasi waktu 24 jam
5. ✅ Model EdukasiLansia dengan validasi URL per platform

### 🔄 Yang Perlu Dilakukan

1. ⏳ Jalankan migration
2. ⏳ Update Controller dengan logika baru
3. ⏳ Update View untuk menggunakan model baru
4. ⏳ Testing semua fitur
5. ⏳ Migrasi data lama (jika ada)

---

## 📊 STRUKTUR FILE BARU

```
poscare-laravel/
├── database/
│   └── migrations/
│       └── 2026_05_04_164717_create_lansia_tables.php ✅
├── app/
│   └── Models/
│       ├── LansiaBaru.php ✅
│       ├── KunjunganLansiaBaru.php ✅
│       ├── JadwalLansiaBaru.php ✅
│       └── EdukasiLansiaBaru.php ✅
└── docs/
    ├── STRUKTUR_DATABASE_LANSIA_BARU.md ✅
    └── IMPLEMENTASI_MODUL_LANSIA_BARU.md ✅ (file ini)
```

---

## 🚀 LANGKAH IMPLEMENTASI

### STEP 1: Backup Database

```bash
# Backup database sebelum migrasi
php artisan db:backup

# Atau manual via phpMyAdmin
# Export database → Save as SQL file
```

### STEP 2: Drop Tabel Lama (HATI-HATI!)

```sql
-- Jalankan di phpMyAdmin atau MySQL client
DROP TABLE IF EXISTS edukasi_lansia;
DROP TABLE IF EXISTS jadwal_lansia;
DROP TABLE IF EXISTS kunjungan_lansia;
DROP TABLE IF EXISTS lansia;
```

**ATAU** via Laravel:

```bash
# Rollback migration lama
php artisan migrate:rollback --step=1

# Cek status migration
php artisan migrate:status
```

### STEP 3: Jalankan Migration Baru

```bash
# Jalankan migration
php artisan migrate

# Verifikasi tabel sudah dibuat
php artisan db:show
```

### STEP 4: Rename Model (Hapus suffix "Baru")

```bash
# Rename file model
mv app/Models/LansiaBaru.php app/Models/Lansia.php
mv app/Models/KunjunganLansiaBaru.php app/Models/KunjunganLansia.php
mv app/Models/JadwalLansiaBaru.php app/Models/JadwalLansia.php
mv app/Models/EdukasiLansiaBaru.php app/Models/EdukasiLansia.php
```

**Jangan lupa update class name di dalam file!**

```php
// Dari:
class LansiaBaru extends Model

// Menjadi:
class Lansia extends Model
```

### STEP 5: Update Controller

Sekarang update semua controller untuk menggunakan model dan logika baru.

---

## 📝 CONTOH IMPLEMENTASI CONTROLLER

### 1. Dashboard Controller

```php
<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use App\Models\KunjunganLansia;
use App\Models\JadwalLansia;

class LansiaDashboardController extends Controller
{
    public function index()
    {
        return view('lansia.dashboard.index');
    }

    public function stats()
    {
        // Total Lansia
        $totalLansia = Lansia::aktif()->count();
        
        // Kunjungan Bulan Ini
        $kunjunganBulanIni = KunjunganLansia::bulanIni()->count();
        
        // Rata-rata Usia
        $rataUsia = round(Lansia::aktif()->get()->avg('umur'), 1);
        
        // Kondisi Tidak Normal
        $tidakNormal = KunjunganLansia::tidakNormal()
            ->whereIn('lansia_id', function ($query) {
                $query->selectRaw('lansia_id')
                    ->from('kunjungan_lansia')
                    ->groupBy('lansia_id')
                    ->havingRaw('MAX(tanggal_kunjungan) = tanggal_kunjungan');
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_lansia' => $totalLansia,
                'kunjungan_bulan_ini' => $kunjunganBulanIni,
                'rata_rata_usia' => $rataUsia,
                'tidak_normal' => $tidakNormal,
            ],
        ]);
    }

    public function chartDistribusiUsia()
    {
        $lansia = Lansia::aktif()->get();
        
        $distribusi = [
            '60-65 tahun' => 0,
            '66-70 tahun' => 0,
            '71-75 tahun' => 0,
            '76-80 tahun' => 0,
            '80+ tahun' => 0,
        ];

        foreach ($lansia as $l) {
            $rentang = $l->rentang_usia;
            if (isset($distribusi[$rentang])) {
                $distribusi[$rentang]++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => array_keys($distribusi),
                'values' => array_values($distribusi),
            ],
        ]);
    }

    public function chartStatusKesehatan()
    {
        $data = Lansia::aktif()
            ->selectRaw('status_kesehatan, COUNT(*) as jumlah')
            ->groupBy('status_kesehatan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $data->pluck('status_kesehatan')->toArray(),
                'values' => $data->pluck('jumlah')->toArray(),
            ],
        ]);
    }

    public function chartTrendKunjungan()
    {
        $bulan = [];
        $jumlah = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan[] = $date->format('M Y');
            
            $count = KunjunganLansia::whereMonth('tanggal_kunjungan', $date->month)
                ->whereYear('tanggal_kunjungan', $date->year)
                ->count();
            
            $jumlah[] = $count;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $bulan,
                'values' => $jumlah,
            ],
        ]);
    }
}
```

### 2. Kunjungan Controller

```php
<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use App\Models\KunjunganLansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LansiaKunjunganController extends Controller
{
    // List lansia untuk tabel
    public function list(Request $request)
    {
        $search = $request->get('q', '');
        $limit = $request->get('limit', 200);

        $query = Lansia::aktif()->with('kunjunganTerakhir');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_lansia', 'like', "%{$search}%")
                  ->orWhere('nama_wali', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($l) {
                return [
                    'id' => $l->id,
                    'nama_lansia' => $l->nama_lengkap ?: 'Tanpa Nama',
                    'nik_lansia' => $l->nik_lansia ?: '-',
                    'jenis_kelamin' => $l->jenis_kelamin,
                    'tanggal_lahir' => $l->tgl_lahir->format('Y-m-d'),
                    'tempat_lahir' => $l->tempat_lahir ?: '-',
                    'usia' => $l->umur_display,
                    'berat_badan' => $l->berat_badan,
                    'tinggi_badan' => $l->tinggi_badan,
                    'tekanan_darah' => $l->tekanan_darah,
                    'gula_darah' => $l->gula_darah,
                    'kolesterol' => $l->kolesterol,
                    'asam_urat' => $l->asam_urat,
                    'status_kesehatan' => $l->status_kesehatan,
                    'alamat_domisili' => $l->alamat_domisili ?: '-',
                    'nama_wali' => $l->nama_wali ?: '-',
                    'nik_wali' => $l->nik_wali ?: '-',
                    'hp_kontak_wali' => $l->hp_kontak_wali ?: '-',
                ];
            });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Tambah data lansia baru + kunjungan pertama
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik_lansia' => 'nullable|string|size:16|unique:lansia,nik_lansia',
            'tgl_lahir' => 'required|date|before:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat_domisili' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:10',
            'nama_kk' => 'nullable|string|max:255',
            'nama_wali' => 'nullable|string|max:255',
            'nik_wali' => 'nullable|string|size:16',
            'hp_kontak_wali' => 'nullable|string|max:20',
            'berat_badan' => 'nullable|numeric|min:30|max:150',
            'tinggi_badan' => 'nullable|numeric|min:100|max:200',
            'tekanan_darah' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'gula_darah' => 'nullable|numeric|min:50|max:500',
            'kolesterol' => 'nullable|numeric|min:100|max:400',
            'asam_urat' => 'nullable|numeric|min:1|max:15',
        ]);

        DB::transaction(function () use ($data) {
            // 1. Insert lansia
            $lansia = Lansia::create([
                'nama_lengkap' => $data['nama_lengkap'],
                'nik_lansia' => $data['nik_lansia'] ?? null,
                'tgl_lahir' => $data['tgl_lahir'],
                'tempat_lahir' => $data['tempat_lahir'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'alamat_domisili' => $data['alamat_domisili'] ?? null,
                'rt_rw' => $data['rt_rw'] ?? null,
                'nama_kk' => $data['nama_kk'] ?? null,
                'nama_wali' => $data['nama_wali'] ?? null,
                'nik_wali' => $data['nik_wali'] ?? null,
                'hp_kontak_wali' => $data['hp_kontak_wali'] ?? null,
                'dicatat_oleh' => Auth::id(),
            ]);

            // 2. Insert kunjungan pertama
            $kunjungan = KunjunganLansia::create([
                'lansia_id' => $lansia->id,
                'tanggal_kunjungan' => now(),
                'berat_badan' => $data['berat_badan'] ?? null,
                'tinggi_badan' => $data['tinggi_badan'] ?? null,
                'tekanan_darah' => $data['tekanan_darah'] ?? null,
                'status_tensi' => KunjunganLansia::hitungStatusTensi($data['tekanan_darah'] ?? null),
                'gula_darah' => $data['gula_darah'] ?? null,
                'status_gula' => KunjunganLansia::hitungStatusGula($data['gula_darah'] ?? null),
                'kolesterol' => $data['kolesterol'] ?? null,
                'status_kolesterol' => KunjunganLansia::hitungStatusKolesterol($data['kolesterol'] ?? null),
                'asam_urat' => $data['asam_urat'] ?? null,
                'status_asam_urat' => KunjunganLansia::hitungStatusAsamUrat($data['asam_urat'] ?? null, $data['jenis_kelamin']),
                'dicatat_oleh' => Auth::id(),
            ]);

            // 3. Update data kesehatan lansia
            $lansia->updateDataKesehatan($kunjungan);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data lansia berhasil ditambahkan!',
        ], 201);
    }

    // Kunjungan selanjutnya
    public function kunjunganSelanjutnya(Request $request, $lansiaId)
    {
        $lansia = Lansia::aktif()->findOrFail($lansiaId);

        $data = $request->validate([
            // Update data lansia (opsional)
            'nama_lengkap' => 'sometimes|string|max:255',
            'nik_lansia' => 'nullable|string|size:16|unique:lansia,nik_lansia,' . $lansiaId,
            'alamat_domisili' => 'nullable|string',
            'rt_rw' => 'nullable|string|max:10',
            'nama_wali' => 'nullable|string|max:255',
            'nik_wali' => 'nullable|string|size:16',
            'hp_kontak_wali' => 'nullable|string|max:20',
            
            // Data kunjungan (required)
            'tanggal_kunjungan' => 'required|date',
            'berat_badan' => 'nullable|numeric|min:30|max:150',
            'tinggi_badan' => 'nullable|numeric|min:100|max:200',
            'tekanan_darah' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'gula_darah' => 'nullable|numeric|min:50|max:500',
            'kolesterol' => 'nullable|numeric|min:100|max:400',
            'asam_urat' => 'nullable|numeric|min:1|max:15',
            'ada_keluhan' => 'nullable|boolean',
            'keluhan' => 'nullable|string',
            'obat_diberikan' => 'nullable|array',
            'vitamin_diberikan' => 'nullable|array',
            'catatan_bidan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($lansia, $data) {
            // 1. Update data lansia jika ada perubahan
            $updateLansia = [];
            if (isset($data['nama_lengkap'])) $updateLansia['nama_lengkap'] = $data['nama_lengkap'];
            if (isset($data['nik_lansia'])) $updateLansia['nik_lansia'] = $data['nik_lansia'];
            if (isset($data['alamat_domisili'])) $updateLansia['alamat_domisili'] = $data['alamat_domisili'];
            if (isset($data['rt_rw'])) $updateLansia['rt_rw'] = $data['rt_rw'];
            if (isset($data['nama_wali'])) $updateLansia['nama_wali'] = $data['nama_wali'];
            if (isset($data['nik_wali'])) $updateLansia['nik_wali'] = $data['nik_wali'];
            if (isset($data['hp_kontak_wali'])) $updateLansia['hp_kontak_wali'] = $data['hp_kontak_wali'];

            if (!empty($updateLansia)) {
                $lansia->update($updateLansia);
            }

            // 2. Insert kunjungan baru
            $kunjungan = KunjunganLansia::create([
                'lansia_id' => $lansia->id,
                'tanggal_kunjungan' => $data['tanggal_kunjungan'],
                'berat_badan' => $data['berat_badan'] ?? null,
                'tinggi_badan' => $data['tinggi_badan'] ?? null,
                'tekanan_darah' => $data['tekanan_darah'] ?? null,
                'status_tensi' => KunjunganLansia::hitungStatusTensi($data['tekanan_darah'] ?? null),
                'gula_darah' => $data['gula_darah'] ?? null,
                'status_gula' => KunjunganLansia::hitungStatusGula($data['gula_darah'] ?? null),
                'kolesterol' => $data['kolesterol'] ?? null,
                'status_kolesterol' => KunjunganLansia::hitungStatusKolesterol($data['kolesterol'] ?? null),
                'asam_urat' => $data['asam_urat'] ?? null,
                'status_asam_urat' => KunjunganLansia::hitungStatusAsamUrat($data['asam_urat'] ?? null, $lansia->jenis_kelamin),
                'ada_keluhan' => $data['ada_keluhan'] ?? false,
                'keluhan' => $data['keluhan'] ?? null,
                'obat_diberikan' => $data['obat_diberikan'] ?? [],
                'vitamin_diberikan' => $data['vitamin_diberikan'] ?? [],
                'catatan_bidan' => $data['catatan_bidan'] ?? null,
                'dicatat_oleh' => Auth::id(),
            ]);

            // 3. Update data kesehatan lansia
            $lansia->updateDataKesehatan($kunjungan);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil disimpan dan data lansia diperbarui!',
        ], 201);
    }
}
```

---

## ✅ CHECKLIST IMPLEMENTASI

### Database
- [x] Migration dibuat
- [ ] Migration dijalankan
- [ ] Tabel terverifikasi di database
- [ ] Index terverifikasi

### Model
- [x] Model Lansia dibuat
- [x] Model KunjunganLansia dibuat
- [x] Model JadwalLansia dibuat
- [x] Model EdukasiLansia dibuat
- [ ] Model di-rename (hapus suffix "Baru")
- [ ] Relasi ditest

### Controller
- [ ] Dashboard Controller diupdate
- [ ] Kunjungan Controller diupdate
- [ ] Jadwal Controller diupdate
- [ ] Laporan Controller diupdate
- [ ] Edukasi Controller diupdate
- [ ] Pengaturan Controller diupdate

### View
- [ ] View diupdate untuk field baru (tgl_lahir, dll)
- [ ] JavaScript diupdate
- [ ] Validasi form diupdate

### Testing
- [ ] Test tambah lansia baru
- [ ] Test kunjungan selanjutnya
- [ ] Test dashboard stats
- [ ] Test grafik
- [ ] Test jadwal
- [ ] Test edukasi
- [ ] Test laporan export

---

## 🎉 STATUS

**Migration & Model**: ✅ SELESAI  
**Controller**: ⏳ PERLU DIUPDATE  
**View**: ⏳ PERLU DIUPDATE  
**Testing**: ⏳ BELUM DIMULAI

**Next Step**: Jalankan migration dan update controller!

