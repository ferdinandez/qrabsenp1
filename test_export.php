<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\Absensi;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Export Data with User Relationship ===\n\n";

// Get data like in exportReport
$data = Absensi::with('user:id,name,email')->orderBy('waktu', 'asc')->get();

echo "Total records: " . $data->count() . "\n\n";

echo "First 3 records:\n";
foreach ($data->take(3) as $index => $absen) {
    echo "\n" . ($index + 1) . ". Absensi ID: {$absen->id}\n";
    echo "   User ID: {$absen->user_id}\n";
    echo "   Waktu: {$absen->waktu}\n";
    
    if ($absen->user) {
        echo "   User Name: {$absen->user->name}\n";
        echo "   User Email: {$absen->user->email}\n";
    } else {
        echo "   User: NULL (relationship not loaded!)\n";
    }
}

echo "\n\n=== Checking if user relationship exists ===\n";
$firstAbsen = Absensi::with('user')->first();
if ($firstAbsen) {
    echo "First absensi user_id: {$firstAbsen->user_id}\n";
    echo "User object: " . ($firstAbsen->user ? "EXISTS" : "NULL") . "\n";
    if ($firstAbsen->user) {
        echo "User name: {$firstAbsen->user->name}\n";
    }
} else {
    echo "No absensi found\n";
}
