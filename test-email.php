<?php
/**
 * Test Email Configuration
 * Jalankan: php test-email.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Load config
$config = [
    'MAIL_MAILER' => env('MAIL_MAILER'),
    'MAIL_HOST' => env('MAIL_HOST'),
    'MAIL_PORT' => env('MAIL_PORT'),
    'MAIL_USERNAME' => env('MAIL_USERNAME'),
    'MAIL_PASSWORD' => env('MAIL_PASSWORD') ? '***' . substr(env('MAIL_PASSWORD'), -4) : 'NOT SET',
    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
];

echo "==========================================\n";
echo "EMAIL CONFIGURATION TEST\n";
echo "==========================================\n\n";

foreach ($config as $key => $value) {
    echo sprintf("%-20s: %s\n", $key, $value);
}

echo "\n==========================================\n";
echo "TESTING EMAIL SEND...\n";
echo "==========================================\n\n";

try {
    $testEmail = env('MAIL_USERNAME');
    
    if (!$testEmail || $testEmail === 'your-email@gmail.com') {
        echo "❌ ERROR: MAIL_USERNAME belum diset dengan benar!\n";
        echo "   Silakan update .env file\n";
        exit(1);
    }
    
    echo "Sending test email to: $testEmail\n\n";
    
    \Illuminate\Support\Facades\Mail::raw('Test email dari AttendX Laravel. Kalau email ini sampai, berarti konfigurasi sudah benar! ✅', function($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('Test Email AttendX - ' . date('Y-m-d H:i:s'));
    });
    
    echo "✅ SUCCESS! Email berhasil dikirim!\n";
    echo "   Check inbox Anda: $testEmail\n";
    echo "   (Kalau tidak ada di inbox, cek folder Spam/Junk)\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "Common errors:\n";
    echo "- Invalid credentials: App Password salah\n";
    echo "- Connection timeout: Firewall memblokir port 587\n";
    echo "- Authentication failed: Username atau password salah\n\n";
    exit(1);
}

echo "==========================================\n";
echo "TEST COMPLETED\n";
echo "==========================================\n";
