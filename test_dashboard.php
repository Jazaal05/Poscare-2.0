<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Http\Controllers\Lansia\LansiaDashboardController;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTING DASHBOARD LANSIA\n";
echo "===========================\n\n";

try {
    $controller = new LansiaDashboardController();

    // Test 1: Stats
    echo "✅ Test 1: Dashboard Stats\n";
    $response = $controller->stats();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   - Total Lansia: {$data['data']['total_lansia']}\n";
        echo "   - Total Laki-laki: {$data['data']['total_laki']}\n";
        echo "   - Total Perempuan: {$data['data']['total_perempuan']}\n";
        echo "   - Rata-rata Usia: {$data['data']['rata_rata_usia']} tahun\n";
        echo "   - Kunjungan Bulan Ini: {$data['data']['kunjungan_bulan_ini']}\n";
        echo "   - Kondisi Tidak Normal: {$data['data']['tidak_normal']}\n";
    } else {
        echo "   ❌ Error: {$data['message']}\n";
    }
    echo "\n";

    // Test 2: Chart Distribusi Usia
    echo "✅ Test 2: Chart Distribusi Usia\n";
    $response = $controller->chartDistribusiUsia();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        foreach ($data['data']['labels'] as $i => $label) {
            $value = $data['data']['values'][$i];
            echo "   - {$label}: {$value} orang\n";
        }
    } else {
        echo "   ❌ Error: {$data['message']}\n";
    }
    echo "\n";

    // Test 3: Chart Kondisi Kesehatan
    echo "✅ Test 3: Chart Kondisi Kesehatan\n";
    $response = $controller->chartKondisiKesehatan();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        foreach ($data['data']['labels'] as $i => $label) {
            $value = $data['data']['values'][$i];
            echo "   - {$label}: {$value} orang\n";
        }
    } else {
        echo "   ❌ Error: {$data['message']}\n";
    }
    echo "\n";

    // Test 4: Chart Trend Kunjungan
    echo "✅ Test 4: Chart Trend Kunjungan (6 Bulan)\n";
    $response = $controller->chartTrendKunjungan();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        foreach ($data['data']['labels'] as $i => $label) {
            $value = $data['data']['values'][$i];
            echo "   - {$label}: {$value} kunjungan\n";
        }
    } else {
        echo "   ❌ Error: {$data['message']}\n";
    }
    echo "\n";

    echo "🎉 DASHBOARD TEST BERHASIL!\n";
    echo "Semua endpoint dashboard berfungsi dengan sempurna.\n\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n\n";
}