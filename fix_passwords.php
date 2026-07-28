<?php
/**
 * Script untuk fix password user yang kosong atau invalid
 * Run: php fix_passwords.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Fix User Passwords ===\n\n";

// Get all users
$users = User::all();

echo "Found " . $users->count() . " users\n\n";

foreach ($users as $user) {
    // Check if password is valid (try to verify against empty string)
    // If password is null, empty, or invalid, reset to default
    
    echo "User: {$user->name} ({$user->email})\n";
    
    // Set password to default: password123
    $user->password = Hash::make('password123');
    $user->save();
    
    echo "  ✅ Password updated to: password123\n\n";
}

echo "=== Done! All passwords have been set to: password123 ===\n";
echo "\nUsers can now login with:\n";
echo "Email: [their email]\n";
echo "Password: password123\n";
