<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Memulai Impor Data dari Google Sheets ke Database Native...\n";
$gs = new \App\Services\GoogleSheetService();
$res = $gs->syncFromSheetsToNativeDb();
echo json_encode($res, JSON_PRETTY_PRINT);
