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
    <title>Absensi - Website Absensi</title>
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
            <li><a href="absensi.php" class="active"><i class="fas fa-user-check"></i> Absensi</a></li>
            <li><a href="report.php"><i class="fas fa-file-alt"></i> Laporan</a></li>
            <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-footer">
        </div>
    </aside>

    <div class="main-content">
        <div class="header-section">
            <h1><i class="fas fa-user-check"></i> Absensi Hari Ini</h1>
            <p>Waktu saat ini: <span id="currentTime"></span></p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button id="checkInBtn" class="btn btn-success btn-lg w-100 mb-3"><i class="fas fa-arrow-right-to-bracket"></i> Check In</button>
            </div>
            <div class="col-md-6">
                <button id="checkOutBtn" class="btn btn-danger btn-lg w-100 mb-3"><i class="fas fa-arrow-right-from-bracket"></i> Check Out</button>
            </div>
        </div>
        
        <div class="card mt-4" style="border-left: 4px solid #667eea;">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-barcode"></i> Input Barcode</h5>
                <form id="barcodeForm">
                    <div class="mb-3">
                        <label for="barcodeInput" class="form-label">Scan atau masukkan barcode ID karyawan:</label>
                        <input type="text" id="barcodeInput" class="form-control form-control-lg" placeholder="Arahkan scanner ke sini..." autofocus required>
                        <small class="text-muted">Barcode scanner akan otomatis mendeteksi kartu ID</small>
                    </div>
                    <div class="mb-3">
                        <label for="checkInType" class="form-label">Tipe Absensi:</label>
                        <select id="checkInType" class="form-select form-select-lg" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="checkin">Check In</option>
                            <option value="checkout">Check Out</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-check"></i> Proses Absensi
                    </button>
                </form>
            </div>
        </div>
        
        <div id="status" class="alert d-none"></div>
        
        <div class="mt-4">
            <h3>Status Absensi Hari Ini</h3>
            <div class="card mt-3" style="border-left: 4px solid #667eea;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted"><i class="fas fa-arrow-right-to-bracket"></i> Check In</h6>
                            <p id="checkInTime" class="h5"><span class="badge bg-warning">Belum Check In</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted"><i class="fas fa-arrow-right-from-bracket"></i> Check Out</h6>
                            <p id="checkOutTime" class="h5"><span class="badge bg-secondary">Belum Check Out</span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted"><i class="fas fa-hourglass-end"></i> Durasi Kerja</h6>
                            <p id="workDuration" class="h5"><span class="badge bg-info">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted"><i class="fas fa-calendar-check"></i> Status</h6>
                            <p id="absensiStatus" class="h5"><span class="badge bg-secondary">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>