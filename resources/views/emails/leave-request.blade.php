<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Cuti Baru</title>
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
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
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
        .details-box {
            background-color: #F9FAFB;
            border-left: 4px solid #0066FF;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #E5E7EB;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #374151;
            width: 140px;
            flex-shrink: 0;
        }
        .detail-value {
            color: #1F2937;
            flex: 1;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 5px;
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
            <h1>🏖️ Pengajuan Cuti Baru</h1>
            <p>AttendX Attendance Management System</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo Admin,
            </div>
            
            <div class="message">
                Ada pengajuan cuti baru yang memerlukan persetujuan Anda.
            </div>
            
            <div class="details-box">
                <div class="detail-row">
                    <div class="detail-label">Nama Karyawan:</div>
                    <div class="detail-value"><strong>{{ $user->name }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Departemen:</div>
                    <div class="detail-value">{{ $user->department ?? '-' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Posisi:</div>
                    <div class="detail-value">{{ $user->position ?? '-' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Jenis Cuti:</div>
                    <div class="detail-value"><strong>{{ $leaveRequest->leaveType->name }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal Mulai:</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->format('d F Y') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Tanggal Selesai:</div>
                    <div class="detail-value">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->format('d F Y') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Hari:</div>
                    <div class="detail-value"><strong>{{ $leaveRequest->total_days }} hari</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Alasan:</div>
                    <div class="detail-value">{{ $leaveRequest->reason }}</div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <p style="color: #6B7280; margin-bottom: 20px;">Silakan login ke sistem untuk menyetujui atau menolak pengajuan ini</p>
                <a href="{{ config('app.url') }}/qrabsenp1/leave_approval.php" class="button">
                    Lihat Pengajuan
                </a>
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
