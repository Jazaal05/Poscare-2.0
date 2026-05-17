<?php
// ⚠️ HAPUS FILE INI SEGERA SETELAH MIGRATION BERHASIL!
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', [
    '--path'  => 'database/migrations/2026_05_12_000002_add_orangtua_lansia_role.php',
    '--force' => true,
]);
echo '<pre>' . $kernel->output() . '</pre>';
echo '<p style="color:red"><strong>SEGERA HAPUS FILE INI DARI SERVER!</strong></p>';
