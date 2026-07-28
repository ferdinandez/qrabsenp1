<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Create a mock request to /api/history for user 8
$user = User::find(8);

if (!$user) {
    echo "User not found!\n";
    exit;
}

// Manually call the controller method
$controller = new \App\Http\Controllers\AbsensiController();

// Create mock request
$request = Request::create('/api/history', 'GET');
$request->setUserResolver(function () use ($user) {
    return $user;
});

// Call the method
$response = $controller->history($request);

// Get JSON content
$content = json_decode($response->getContent(), true);

echo "=== API Response for /api/history ===\n\n";
echo "Status Code: " . $response->getStatusCode() . "\n\n";
echo "Response JSON:\n";
echo json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n\n";

// Show statistik specifically
echo "=== Statistik Breakdown ===\n";
echo "Total: " . ($content['statistik']['total'] ?? 'N/A') . "\n";
echo "Hari Ini: " . ($content['statistik']['hari_ini'] ?? 'N/A') . "\n";
echo "Minggu Ini: " . ($content['statistik']['minggu_ini'] ?? 'N/A') . "\n";
echo "Bulan Ini: " . ($content['statistik']['bulan_ini'] ?? 'N/A') . "\n";
