<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Absensi;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a test user (user_id = 8 based on latest absensi)
$user = User::find(8);

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "=== Testing for User: {$user->name} (ID: {$user->id}) ===\n\n";

// Test 1: Total Absensi
$totalAbsensi = Absensi::where('user_id', $user->id)->count();
echo "Total Absensi: {$totalAbsensi}\n";

// Test 2: Hari Ini (using whereBetween)
$today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
$todayEnd = \Carbon\Carbon::now('Asia/Jakarta')->endOfDay();

echo "\nToday Range:\n";
echo "  Start: {$today->toDateTimeString()}\n";
echo "  End: {$todayEnd->toDateTimeString()}\n";

$absensiHariIni = Absensi::where('user_id', $user->id)
    ->whereBetween('waktu', [$today, $todayEnd])
    ->get();

echo "\nAbsensi Hari Ini Count: {$absensiHariIni->count()}\n";

if ($absensiHariIni->count() > 0) {
    echo "Records:\n";
    foreach ($absensiHariIni as $a) {
        echo "  - ID: {$a->id} | Waktu: {$a->waktu}\n";
    }
}

// Test 3: Bulan Ini
$startOfMonth = \Carbon\Carbon::now('Asia/Jakarta')->startOfMonth();
$endOfMonth = \Carbon\Carbon::now('Asia/Jakarta')->endOfMonth();

$absenBulanIni = Absensi::where('user_id', $user->id)
    ->whereBetween('waktu', [$startOfMonth, $endOfMonth])
    ->count();

echo "\nAbsen Bulan Ini: {$absenBulanIni}\n";

// Test 4: All absensis for this user
echo "\n=== All Absensis for User {$user->id} ===\n";
$allAbsensis = Absensi::where('user_id', $user->id)
    ->orderBy('waktu', 'desc')
    ->get();

foreach ($allAbsensis as $a) {
    $waktuCarbon = \Carbon\Carbon::parse($a->waktu)->timezone('Asia/Jakarta');
    echo "ID: {$a->id} | Waktu: {$waktuCarbon->toDateTimeString()} | Date: {$waktuCarbon->toDateString()}\n";
}
