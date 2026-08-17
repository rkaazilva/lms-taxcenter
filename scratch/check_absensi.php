<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\LmsAbsensi::all();
echo "Total Absensi Rows: " . $rows->count() . "\n";
foreach ($rows as $r) {
    echo "{$r->email} | Mapel: '{$r->mapel}' | Time: {$r->timestamp}\n";
}
