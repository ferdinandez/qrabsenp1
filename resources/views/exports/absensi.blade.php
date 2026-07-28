<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
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
        .info {
            margin-bottom: 20px;
            background: #f8f9fa;
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
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            color: #0066FF;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AttendX</h1>
        <h2>Laporan Data Absensi</h2>
    </div>

    <div class="info">
        <p><strong>Periode:</strong> {{ $start_date }} s/d {{ $end_date }}</p>
        <p><strong>Dicetak pada:</strong> {{ $generated_at }}</p>
        <p><strong>Total Data:</strong> {{ count($data) }} record</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 20%;">Waktu</th>
                <th style="width: 12.5%;">Latitude</th>
                <th style="width: 12.5%;">Longitude</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $absen)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $absen->user->name ?? '-' }}</td>
                <td>{{ $absen->user->email ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($absen->waktu)->format('d/m/Y H:i') }}</td>
                <td>{{ number_format($absen->latitude, 6) }}</td>
                <td>{{ number_format($absen->longitude, 6) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px; color: #999;">
                    Tidak ada data absensi
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        Total: {{ count($data) }} data absensi
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem AttendX</p>
        <p>&copy; {{ date('Y') }} AttendX - Attendance Management System</p>
    </div>
</body>
</html>
