<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\Absensi;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Test Absensi for Admin (User ID: 7) ===\n\n";

$waktu = Carbon::now('Asia/Jakarta');
echo "Waktu sekarang: {$waktu}\n";

$absen = Absensi::create([
    'user_id' => 7,
    'waktu' => $waktu,
    'latitude' => -6.9132,
    'longitude' => 107.5046
]);

echo "Created Absensi ID: {$absen->id}\n";
echo "Waktu tersimpan: {$absen->waktu}\n";
echo "User ID: {$absen->user_id}\n\n";

// Check count for user 7
$count = Absensi::where('user_id', 7)->count();
echo "Total Absensi untuk User ID 7: {$count}\n";
