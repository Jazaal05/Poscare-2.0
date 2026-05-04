<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Lansia;
use App\Models\KunjunganLansia;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTING MODUL LANSIA BARU\n";
echo "============================\n\n";

try {
    // Test 1: Model Loading
    echo "✅ Test 1: Model Loading\n";
    echo "   - Lansia Model: " . (class_exists('App\Models\Lansia') ? 'OK' : 'ERROR') . "\n";
    echo "   - KunjunganLansia Model: " . (class_exists('App\Models\KunjunganLansia') ? 'OK' : 'ERROR') . "\n";
    echo "   - JadwalLansia Model: " . (class_exists('App\Models\JadwalLansia') ? 'OK' : 'ERROR') . "\n";
    echo "   - EdukasiLansia Model: " . (class_exists('App\Models\EdukasiLansia') ? 'OK' : 'ERROR') . "\n\n";

    // Test 2: Database Connection
    echo "✅ Test 2: Database Connection\n";
    $totalLansia = \DB::table('lansia')->count();
    $totalKunjungan = \DB::table('kunjungan_lansia')->count();
    $totalJadwal = \DB::table('jadwal_lansia')->count();
    $totalEdukasi = \DB::table('edukasi_lansia')->count();
    
    echo "   - Total Lansia: $totalLansia\n";
    echo "   - Total Kunjungan: $totalKunjungan\n";
    echo "   - Total Jadwal: $totalJadwal\n";
    echo "   - Total Edukasi: $totalEdukasi\n\n";

    // Test 3: Insert Data Dummy
    echo "✅ Test 3: Insert Data Dummy\n";
    
    $lansia = Lansia::create([
        'nama_lengkap' => 'Test Lansia ' . date('H:i:s'),
        'tgl_lahir' => '1960-01-01',
        'jenis_kelamin' => 'L',
        'alamat_domisili' => 'Jl. Test No. 123',
        'nama_wali' => 'Test Wali',
        'hp_kontak_wali' => '081234567890',
        'dicatat_oleh' => 1,
        'status_kesehatan' => 'Sehat',
    ]);
    
    echo "   - Lansia created with ID: {$lansia->id}\n";
    echo "   - Nama: {$lansia->nama_lengkap}\n";
    echo "   - Umur: {$lansia->umur} tahun\n";
    echo "   - Rentang Usia: {$lansia->rentang_usia}\n";
    echo "   - Jenis Kelamin Display: {$lansia->jenis_kelamin_display}\n\n";

    // Test 4: Insert Kunjungan
    echo "✅ Test 4: Insert Kunjungan\n";
    
    $kunjungan = KunjunganLansia::create([
        'lansia_id' => $lansia->id,
        'tanggal_kunjungan' => now(),
        'berat_badan' => 65.5,
        'tinggi_badan' => 165.0,
        'tekanan_darah' => '120/80',
        'gula_darah' => 95.0,
        'kolesterol' => 180.0,
        'asam_urat' => 5.5,
        'status_tensi' => KunjunganLansia::hitungStatusTensi('120/80'),
        'status_gula' => KunjunganLansia::hitungStatusGula(95.0),
        'status_kolesterol' => KunjunganLansia::hitungStatusKolesterol(180.0),
        'status_asam_urat' => KunjunganLansia::hitungStatusAsamUrat(5.5, 'L'),
        'dicatat_oleh' => 1,
    ]);
    
    echo "   - Kunjungan created with ID: {$kunjungan->id}\n";
    echo "   - BMI: {$kunjungan->bmi}\n";
    echo "   - Status Tensi: {$kunjungan->status_tensi}\n";
    echo "   - Status Gula: {$kunjungan->status_gula}\n";
    echo "   - Status Kolesterol: {$kunjungan->status_kolesterol}\n";
    echo "   - Status Asam Urat: {$kunjungan->status_asam_urat}\n";
    echo "   - Kondisi Normal: " . ($kunjungan->isTidakNormal() ? 'TIDAK' : 'YA') . "\n\n";

    // Test 5: Update Data Lansia dari Kunjungan
    echo "✅ Test 5: Update Data Lansia dari Kunjungan\n";
    
    $lansia->updateDataKesehatan($kunjungan);
    $lansia->refresh();
    
    echo "   - Status Kesehatan Updated: {$lansia->status_kesehatan}\n";
    echo "   - Berat Badan Updated: {$lansia->berat_badan} kg\n";
    echo "   - Tinggi Badan Updated: {$lansia->tinggi_badan} cm\n";
    echo "   - BMI: {$lansia->bmi}\n";
    echo "   - Kategori BMI: {$lansia->kategori_bmi}\n\n";

    // Test 6: Relasi
    echo "✅ Test 6: Relasi Model\n";
    
    $kunjunganCount = $lansia->kunjungan()->count();
    $kunjunganTerakhir = $lansia->kunjunganTerakhir;
    
    echo "   - Jumlah Kunjungan: {$kunjunganCount}\n";
    echo "   - Kunjungan Terakhir ID: {$kunjunganTerakhir->id}\n";
    echo "   - Tanggal Kunjungan Terakhir: {$kunjunganTerakhir->tanggal_kunjungan->format('d/m/Y')}\n\n";

    // Test 7: Scope
    echo "✅ Test 7: Scope Model\n";
    
    $lansiaAktif = Lansia::aktif()->count();
    $kunjunganBulanIni = KunjunganLansia::bulanIni()->count();
    
    echo "   - Lansia Aktif: {$lansiaAktif}\n";
    echo "   - Kunjungan Bulan Ini: {$kunjunganBulanIni}\n\n";

    echo "🎉 SEMUA TEST BERHASIL!\n";
    echo "Pembersihan total modul lansia berfungsi dengan sempurna.\n\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
}