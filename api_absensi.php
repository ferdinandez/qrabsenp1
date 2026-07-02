<?php
session_start();

// Headers untuk JSON response
header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Silakan login terlebih dahulu.']);
    exit;
}

// Handle different actions
$request = json_decode(file_get_contents('php://input'), true);
$action = $request['action'] ?? '';

if ($action === 'process_barcode') {
    processBarcode($request);
} elseif ($action === 'get_today_status') {
    getTodayStatus();
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function processBarcode($request) {
    $barcode = $request['barcode'] ?? '';
    $type = $request['type'] ?? '';
    $timestamp = $request['timestamp'] ?? date('Y-m-d H:i:s');
    
    // Validasi barcode (ganti dengan logika validasi yang sesuai)
    if (empty($barcode)) {
        echo json_encode(['success' => false, 'message' => 'Barcode tidak boleh kosong']);
        return;
    }
    
    if (!in_array($type, ['checkin', 'checkout'])) {
        echo json_encode(['success' => false, 'message' => 'Tipe absensi tidak valid']);
        return;
    }
    
    // Validasi barcode format (contoh: hanya angka, panjang 4-10 digit)
    if (!preg_match('/^\d{4,10}$/', $barcode)) {
        echo json_encode(['success' => false, 'message' => 'Format barcode tidak valid (harus 4-10 digit)']);
        return;
    }
    
    // Validasi employee (barcode harus sesuai dengan employee ID)
    // Untuk demo, kita asumsikan barcode adalah employee ID
    $employeeId = (int)$barcode;
    
    // Cek apakah employee ID sesuai dengan session user_id (bisa disesuaikan)
    // Untuk sekarang kita gunakan barcode sebagai identifier
    
    $today = date('Y-m-d');
    $time = date('H:i:s');
    
    // Inisialisasi attendance data di session jika belum ada
    if (!isset($_SESSION['attendance'])) {
        $_SESSION['attendance'] = [];
    }
    
    $attendanceKey = $today . '_' . $employeeId;
    
    if ($type === 'checkin') {
        // Check if already checked in today
        if (isset($_SESSION['attendance'][$attendanceKey]['check_in'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Anda sudah melakukan check-in hari ini pada ' . $_SESSION['attendance'][$attendanceKey]['check_in']
            ]);
            return;
        }
        
        $_SESSION['attendance'][$attendanceKey]['check_in'] = $time;
        $_SESSION['attendance'][$attendanceKey]['employee_id'] = $employeeId;
        $_SESSION['attendance'][$attendanceKey]['date'] = $today;
        
        echo json_encode([
            'success' => true,
            'message' => 'Check-in berhasil pada ' . $time . ' untuk Employee ID: ' . $employeeId,
            'type' => 'checkin',
            'time' => $time,
            'employee_id' => $employeeId
        ]);
    } elseif ($type === 'checkout') {
        // Check if checked in
        if (!isset($_SESSION['attendance'][$attendanceKey]['check_in'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Check-in terlebih dahulu sebelum check-out'
            ]);
            return;
        }
        
        // Check if already checked out
        if (isset($_SESSION['attendance'][$attendanceKey]['check_out'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini pada ' . $_SESSION['attendance'][$attendanceKey]['check_out']
            ]);
            return;
        }
        
        $_SESSION['attendance'][$attendanceKey]['check_out'] = $time;
        
        // Calculate working duration
        $checkInTime = strtotime($_SESSION['attendance'][$attendanceKey]['check_in']);
        $checkOutTime = strtotime($time);
        $duration = $checkOutTime - $checkInTime;
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        
        $_SESSION['attendance'][$attendanceKey]['duration'] = sprintf('%d jam %d menit', $hours, $minutes);
        $_SESSION['attendance'][$attendanceKey]['status'] = 'present';
        
        echo json_encode([
            'success' => true,
            'message' => 'Check-out berhasil pada ' . $time . ' (Durasi kerja: ' . $_SESSION['attendance'][$attendanceKey]['duration'] . ')',
            'type' => 'checkout',
            'time' => $time,
            'employee_id' => $employeeId,
            'duration' => $_SESSION['attendance'][$attendanceKey]['duration']
        ]);
    }
}

function getTodayStatus() {
    $today = date('Y-m-d');
    $employeeId = $_SESSION['user_id'] ?? 1;
    $attendanceKey = $today . '_' . $employeeId;
    
    if (!isset($_SESSION['attendance']) || !isset($_SESSION['attendance'][$attendanceKey])) {
        echo json_encode([
            'success' => true,
            'data' => [
                'check_in' => null,
                'check_out' => null,
                'status' => 'belum_absensi'
            ]
        ]);
        return;
    }
    
    $attendance = $_SESSION['attendance'][$attendanceKey];
    echo json_encode([
        'success' => true,
        'data' => [
            'check_in' => $attendance['check_in'] ?? null,
            'check_out' => $attendance['check_out'] ?? null,
            'duration' => $attendance['duration'] ?? null,
            'status' => $attendance['status'] ?? 'partial'
        ]
    ]);
}
?>
