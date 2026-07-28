<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Absensi - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #0066FF;
        }
        .header h1 {
            color: #0066FF;
            font-size: 28px;
            margin: 0 0 5px 0;
        }
        .header h2 {
            color: #666;
            font-size: 18px;
            margin: 5px 0;
            font-weight: normal;
        }
        .employee-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #0066FF;
        }
        .employee-info h3 {
            margin: 0 0 10px 0;
            color: #0066FF;
            font-size: 16px;
        }
        .employee-info p {
            margin: 5px 0;
        }
        .info {
            margin-bottom: 20px;
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background: #0066FF;
            color: white;
        }
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tbody tr:hover {
            background-color: #e3f2fd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 5px;
            border-left: 4px solid #4caf50;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #2e7d32;
            font-size: 14px;
        }
        .summary p {
            margin: 5px 0;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AttendX</h1>
        <h2>Riwayat Absensi Pribadi</h2>
    </div>

    <div class="employee-info">
        <h3>Informasi Karyawan</h3>
        <p><strong>Nama:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        @if($user->department)
        <p><strong>Departemen:</strong> {{ $user->department }}</p>
        @endif
        @if($user->position)
        <p><strong>Posisi:</strong> {{ $user->position }}</p>
        @endif
    </div>

    <div class="info">
        <p><strong>Periode:</strong> {{ $start_date }} s/d {{ $end_date }}</p>
        <p><strong>Dicetak pada:</strong> {{ $generated_at }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 30%;">Tanggal</th>
                <th style="width: 20%;">Waktu</th>
                <th style="width: 21%;">Latitude</th>
                <th style="width: 21%;">Longitude</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $absen)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($absen->waktu)->format('l, d F Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($absen->waktu)->format('H:i:s') }}</td>
                <td>{{ number_format($absen->latitude, 6) }}</td>
                <td>{{ number_format($absen->longitude, 6) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                    Tidak ada data absensi pada periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Ringkasan</h3>
        <p><strong>Total Kehadiran:</strong> {{ count($data) }} hari</p>
        <p><strong>Status:</strong> Dokumen ini merupakan riwayat absensi resmi dari sistem AttendX</p>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem AttendX</p>
        <p>&copy; {{ date('Y') }} AttendX - Attendance Management System</p>
        <p><em>Untuk {{ $user->name }} - Rahasia dan Pribadi</em></p>
    </div>
</body>
</html>
