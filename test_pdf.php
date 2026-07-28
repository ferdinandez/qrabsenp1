<?php
require __DIR__ . '/vendor/autoload.php';

use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing PDF Generation ===\n\n";

// Get data
$data = Absensi::with('user:id,name,email')->orderBy('waktu', 'asc')->take(5)->get();

echo "Total records for PDF: " . $data->count() . "\n";

// Check data before PDF
echo "\nData preview:\n";
foreach ($data as $index => $absen) {
    echo ($index + 1) . ". {$absen->user->name} - {$absen->waktu}\n";
}

// Generate PDF
echo "\nGenerating PDF...\n";

try {
    $pdf = Pdf::loadView('exports.absensi', [
        'data' => $data,
        'start_date' => '2026-05-01',
        'end_date' => '2026-07-06',
        'generated_at' => now()->format('d/m/Y H:i:s')
    ]);
    
    $filename = 'test_laporan_' . date('Ymd_His') . '.pdf';
    $pdf->save(storage_path('app/' . $filename));
    
    echo "PDF saved to: storage/app/{$filename}\n";
    echo "File size: " . filesize(storage_path('app/' . $filename)) . " bytes\n";
    echo "\nSUCCESS! Please check the file.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
