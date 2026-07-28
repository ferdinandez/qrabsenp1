<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .icon-welcome {
            width: 80px;
            height: 80px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1F2937;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #4B5563;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .welcome-message {
            background-color: #EDE9FE;
            border-left: 4px solid #8B5CF6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            color: #5B21B6;
            font-size: 16px;
            font-weight: 600;
        }
        .credentials-box {
            background-color: #F9FAFB;
            border: 2px solid #8B5CF6;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .credentials-title {
            font-weight: 700;
            color: #5B21B6;
            margin-bottom: 15px;
            font-size: 18px;
            text-align: center;
        }
        .credential-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #E5E7EB;
            align-items: center;
        }
        .credential-row:last-child {
            border-bottom: none;
        }
        .credential-label {
            font-weight: 600;
            color: #374151;
            width: 120px;
            flex-shrink: 0;
        }
        .credential-value {
            color: #1F2937;
            flex: 1;
            font-family: 'Courier New', monospace;
            background-color: #FEF3C7;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 700;
        }
        .warning-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .warning-title {
            font-weight: 700;
            color: #92400E;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .warning-text {
            color: #78350F;
            line-height: 1.6;
            font-size: 14px;
        }
        .steps-box {
            background-color: #DBEAFE;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .step {
            display: flex;
            margin-bottom: 15px;
            align-items: start;
        }
        .step:last-child {
            margin-bottom: 0;
        }
        .step-number {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-text {
            color: #1E40AF;
            font-size: 15px;
            padding-top: 5px;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
        }
        .footer {
            background-color: #F9FAFB;
            padding: 30px;
            text-align: center;
            color: #6B7280;
            font-size: 14px;
        }
        .footer-logo {
            font-size: 20px;
            font-weight: 800;
            color: #1F2937;
            margin-bottom: 10px;
        }
        .footer-logo span {
            color: #0066FF;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon-welcome">👋</div>
            <h1>Selamat Datang!</h1>
            <p>AttendX Attendance Management System</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo {{ $user->name }},
            </div>
            
            <div class="welcome-message">
                🎉 Selamat bergabung dengan tim kami! Akun AttendX Anda telah dibuat oleh administrator.
            </div>
            
            <div class="message">
                Kami senang menyambut Anda di sistem AttendX. Berikut adalah informasi akun Anda:
            </div>
            
            <div class="credentials-box">
                <div class="credentials-title">🔐 Kredensial Login Anda</div>
                <div class="credential-row">
                    <div class="credential-label">Username:</div>
                    <div class="credential-value">{{ $user->email }}</div>
                </div>
                <div class="credential-row">
                    <div class="credential-label">Password:</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
            </div>
            
            <div class="warning-box">
                <div class="warning-title">⚠️ Penting - Keamanan Akun</div>
                <div class="warning-text">
                    Untuk keamanan akun Anda, <strong>segera ubah password default</strong> setelah login pertama kali. 
                    Jangan bagikan password Anda kepada siapapun.
                </div>
            </div>
            
            <div class="steps-box">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-text">Login ke sistem AttendX menggunakan kredensial di atas</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text">Buka halaman Profile dan ubah password Anda</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Lengkapi informasi profil Anda (foto, nomor telepon, dll)</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text">Mulai gunakan sistem untuk absensi dan pengajuan cuti</div>
                </div>
            </div>
            
            <div class="message">
                <strong>Fitur yang Tersedia:</strong><br>
                • 📱 Absensi dengan QR Code<br>
                • 🏖️ Pengajuan Cuti Online<br>
                • 📊 Dashboard & Laporan<br>
                • 📅 Kalender Kehadiran<br>
                • 🔔 Notifikasi Real-time
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #6B7280; margin-bottom: 20px;">Klik tombol di bawah untuk login ke sistem</p>
                <a href="{{ config('app.url') }}/qrabsenp1/login.php" class="button">
                    Login Sekarang
                </a>
            </div>
            
            <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #E5E7EB;">
                <p style="color: #6B7280; font-size: 14px; line-height: 1.6;">
                    Jika Anda mengalami kesulitan login atau memiliki pertanyaan, 
                    silakan hubungi administrator atau tim HR Anda.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-logo">Attend<span>X</span></div>
            <p>Email otomatis dari sistem AttendX</p>
            <p>© {{ date('Y') }} AttendX. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
