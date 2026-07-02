<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - Website Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-clock"></i> Absensi</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="absensi.php"><i class="fas fa-user-check"></i> Absensi</a></li>
            <li><a href="report.php" class="active"><i class="fas fa-file-alt"></i> Laporan</a></li>
            <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-footer">
        </div>
    </aside>

    <div class="main-content">
        <div class="header-section">
            <h1><i class="fas fa-file-alt"></i> Laporan Absensi</h1>
            <p>Pantau dan analisis riwayat absensi Anda</p>
        </div>
        
        <div class="mb-3">
            <label for="monthSelect" class="form-label">Pilih Bulan:</label>
            <select id="monthSelect" class="form-select">
                <option value="2023-05">Mei 2023</option>
                <option value="2023-06">Juni 2023</option>
                <!-- Tambahkan opsi bulan lainnya -->
            </select>
        </div>
        
        <button id="loadReportBtn" class="btn btn-primary">Load Laporan</button>
        
        <div class="mt-4">
            <h3>Riwayat Absensi</h3>
            <div id="reportTable" class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Durasi Kerja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportBody">
                        <!-- Data akan di-load via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>