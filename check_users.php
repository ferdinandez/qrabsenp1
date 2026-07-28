<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Absensi;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== All Users and Their Absensi Count ===\n\n";

$users = User::all();

foreach ($users as $user) {
    $count = Absensi::where('user_id', $user->id)->count();
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$user->role} | Absensi: {$count}\n";
}
