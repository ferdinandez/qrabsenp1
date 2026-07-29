<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$role = $_SESSION['role'] ?? 'karyawan';
$username = $_SESSION['username'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AttendX</title>
    
    <!-- PWA Meta Tags -->
    <?php include_once 'includes/pwa-head.php'; ?>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <script src="assets/js/dark-mode.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        attendx: {
                            primary: '#0066FF',
                            dark: '#1F2937',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .attendx-sidebar {
            background: linear-gradient(180deg, #2D3748 0%, #1A202C 100%);
            width: 92px;
            flex-shrink: 0;
            overflow-y: auto;
        }
        .attendx-logo {
            padding: 24px 0;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .attendx-logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0066FF 0%, #0052CC 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            color: white;
            font-size: 20px;
        }
        .attendx-logo span {
            font-size: 16px;
            font-weight: 800;
            color: white;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <aside class="attendx-sidebar">
            <div class="attendx-logo">
                <div class="attendx-logo-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <span>Attend<span style="color: #0066FF;">X</span></span>
            </div>
            <nav class="space-y-6 mt-12">
                <a href="dashboard.php" class="flex items-center justify-center w-12 h-12 rounded-xl bg-attendx-primary text-white transition mx-auto">
                    <i class="fas fa-th-large text-xl"></i>
                </a>
                <?php if ($role === 'admin'): ?>
                <a href="generate_qr.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-qrcode text-xl"></i>
                </a>
                <a href="users.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-users text-xl"></i>
                </a>
                <a href="report.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-file-alt text-xl"></i>
                </a>
                <a href="leave_approval.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </a>
                <?php else: ?>
                <a href="scan_qr.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-camera text-xl"></i>
                </a>
                <a href="absensi.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-map-marker-alt text-xl"></i>
                </a>
                <?php endif; ?>
                <a href="history.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-history text-xl"></i>
                </a>
                <?php if ($role === 'admin'): ?>
                <?php else: ?>
                <a href="leave.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-umbrella-beach text-xl"></i>
                </a>
                <?php endif; ?>
                <a href="calendar.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-calendar text-xl"></i>
                </a>
                <a href="profile.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-user text-xl"></i>
                </a>
                <div class="pt-6 border-t border-white border-opacity-10 mx-4">
                    <button onclick="handleLogout()" class="flex items-center justify-center w-12 h-12 rounded-xl text-red-400 hover:bg-red-500 hover:bg-opacity-20 transition mx-auto">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </button>
                </div>
            </nav>
        </aside>
        <main class="flex-1 overflow-y-auto">
            <div class="bg-white border-b border-gray-200 px-4 md:px-8 py-5">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4 md:gap-6">
                        <h1 class="text-2xl md:text-3xl font-black text-gray-900">Attend<span class="text-attendx-primary">X</span></h1>
                        <span class="text-xs md:text-sm text-gray-400">Dashboard</span>
                    </div>
                    <div class="flex items-center gap-3 md:gap-4">
                        <!-- Notification Bell -->
                        <div class="relative">
                            <button id="notificationBell" onclick="NotificationSystem.toggleDropdown()" class="relative p-2.5 text-gray-600 hover:text-attendx-primary hover:bg-blue-50 rounded-xl transition-all duration-200">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notificationBadge" class="hidden absolute -top-1 -right-1 bg-gradient-to-br from-red-500 to-red-600 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5 shadow-lg animate-pulse">0</span>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-[420px] bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden transform origin-top-right transition-all duration-200 opacity-0 scale-95">
                                <!-- Header -->
                                <div class="bg-gradient-to-r from-attendx-primary to-blue-600 p-5 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                            <i class="fas fa-bell text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-white text-lg">Notifikasi</h3>
                                            <p class="text-xs text-white text-opacity-80" id="notificationCount">0 notifikasi baru</p>
                                        </div>
                                    </div>
                                    <button onclick="NotificationSystem.markAllAsRead()" class="text-xs text-white bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1.5 rounded-lg transition-all backdrop-blur-sm font-medium">
                                        <i class="fas fa-check-double mr-1"></i> Baca Semua
                                    </button>
                                </div>
                                
                                <!-- Notification List -->
                                <div id="notificationList" class="overflow-y-auto max-h-[480px] bg-gray-50">
                                    <div class="p-12 text-center text-gray-400">
                                        <i class="fas fa-spinner fa-spin text-4xl mb-3 text-attendx-primary"></i>
                                        <p class="text-sm font-medium">Memuat notifikasi...</p>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="bg-gray-50 border-t border-gray-200 p-3 text-center">
                                    <a href="notifications.php" class="text-sm text-attendx-primary hover:text-blue-700 font-semibold transition-colors">
                                        Lihat Semua Notifikasi <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <span class="text-xs md:text-sm text-gray-600 whitespace-nowrap">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                        <?php include 'includes/user-avatar-header.php'; ?>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <?php if ($role === 'admin'): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-users text-xl text-blue-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="totalKaryawan">-</div>
                        <div class="text-sm text-gray-500 font-medium">Total Pegawai</div>
                    </div>
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-xl text-green-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="absensiHariIni">-</div>
                        <div class="text-sm text-gray-500 font-medium">Hadir Hari Ini</div>
                    </div>
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-xl text-orange-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="totalAbsensi">-</div>
                        <div class="text-sm text-gray-500 font-medium">Total Absensi</div>
                    </div>
                    <div class="stat-card bg-gradient-to-br from-attendx-primary to-blue-600 rounded-2xl p-6 text-white cursor-pointer" onclick="window.location.href='generate_qr.php'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-white bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-qrcode text-xl text-white"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-bold mb-1">Generate</div>
                        <div class="text-sm text-white text-opacity-90 font-medium">QR Code Absensi</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Absensi 7 Hari Terakhir</h3>
                        <div style="height: 300px;">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Persentase Kehadiran Bulan Ini</h3>
                        <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                            <canvas id="monthlyDonutChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Absensi Terbaru</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="absensiTerbaruTable" class="divide-y divide-gray-200">
                                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <a href="scan_qr.php" class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-attendx-primary transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-blue-100 group-hover:bg-attendx-primary flex items-center justify-center transition">
                                <i class="fas fa-camera text-2xl text-blue-600 group-hover:text-white transition"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Scan QR Code</h3>
                                <p class="text-sm text-gray-500">Absensi cepat dengan QR</p>
                            </div>
                        </div>
                    </a>
                    <a href="absensi.php" class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-green-500 transition group">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-green-100 group-hover:bg-green-500 flex items-center justify-center transition">
                                <i class="fas fa-map-marker-alt text-2xl text-green-600 group-hover:text-white transition"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">Absensi Manual</h3>
                                <p class="text-sm text-gray-500">Absensi dengan GPS</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-xl text-blue-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="totalAbsensiKaryawan">-</div>
                        <div class="text-sm text-gray-500 font-medium">Total Absensi Saya</div>
                    </div>
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-calendar-check text-xl text-green-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="absenBulanIni">-</div>
                        <div class="text-sm text-gray-500 font-medium">Absen Bulan Ini</div>
                    </div>
                    <div class="stat-card bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-hourglass-half text-xl text-purple-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="statusHariIni">-</div>
                        <div class="text-sm text-gray-500 font-medium">Status Hari Ini</div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Riwayat Absensi Terbaru</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Latitude</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Longitude</th>
                                </tr>
                            </thead>
                            <tbody id="historyTable" class="divide-y divide-gray-200">
                                <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="assets/js/api-config.js?v=<?= time() ?>"></script>
    <script>
        const token = '<?= $_SESSION['token'] ?>';
        const role = '<?= $role ?>';

        let weeklyChart = null;
        let monthlyDonutChart = null;
        
        // Helper function untuk API call via proxy
        async function apiCall(endpoint) {
            return API_CONFIG.apiRequest(endpoint, {
                headers: { 
                    'Authorization': `Bearer ${token}`, 
                    'Content-Type': 'application/json' 
                }
            });
        }

        async function loadDashboard() {
            try {
                if (role === 'admin') {
                    const res = await apiCall('dashboard');
                    const data = await res.json();
                    if (res.ok) {
                        document.getElementById('totalKaryawan').textContent = data.statistik.total_karyawan;
                        document.getElementById('absensiHariIni').textContent = data.statistik.absensi_hari_ini;
                        document.getElementById('totalAbsensi').textContent = data.statistik.total_absensi;
                        const tbody = document.getElementById('absensiTerbaruTable');
                        tbody.innerHTML = '';
                        data.absensi_terbaru.forEach(absen => {
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-gray-50 transition';
                            const lat = parseFloat(absen.latitude).toFixed(6);
                            const lng = parseFloat(absen.longitude).toFixed(6);
                            tr.innerHTML = `
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">${absen.user.name}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${absen.user.email}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${new Date(absen.waktu).toLocaleString('id-ID')}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${lat}, ${lng}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                        
                        // Load charts after dashboard data
                        loadCharts();
                    }
                } else {
                    const res = await apiCall('karyawan/dashboard');
                    const data = await res.json();
                    if (res.ok) {
                        document.getElementById('totalAbsensiKaryawan').textContent = data.statistik.total_absensi;
                        document.getElementById('absenBulanIni').textContent = data.statistik.absen_bulan_ini;
                        const statusEl = document.getElementById('statusHariIni');
                        if (data.statistik.sudah_absen_hari_ini) {
                            statusEl.innerHTML = '<span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">✓ Hadir</span>';
                        } else {
                            statusEl.innerHTML = '<span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">⏳ Belum</span>';
                        }
                        const tbody = document.getElementById('historyTable');
                        tbody.innerHTML = '';
                        if (data.history.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat</td></tr>';
                            return;
                        }
                        data.history.forEach(absen => {
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-gray-50 transition';
                            const lat = parseFloat(absen.latitude).toFixed(6);
                            const lng = parseFloat(absen.longitude).toFixed(6);
                            tr.innerHTML = `
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">${new Date(absen.waktu).toLocaleString('id-ID')}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${lat}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${lng}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        async function loadCharts() {
            try {
                const res = await apiCall('statistics');
                const data = await res.json();
                
                if (res.ok) {
                    // Weekly Bar Chart
                    const weeklyLabels = data.weekly_data.map(item => {
                        const date = new Date(item.date);
                        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                        return days[date.getDay()];
                    });
                    const weeklyCounts = data.weekly_data.map(item => item.count);
                    
                    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
                    if (weeklyChart) weeklyChart.destroy();
                    
                    weeklyChart = new Chart(weeklyCtx, {
                        type: 'bar',
                        data: {
                            labels: weeklyLabels,
                            datasets: [{
                                label: 'Jumlah Absensi',
                                data: weeklyCounts,
                                backgroundColor: 'rgba(0, 102, 255, 0.8)',
                                borderColor: 'rgba(0, 102, 255, 1)',
                                borderWidth: 1,
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                    
                    // Monthly Donut Chart
                    const totalKaryawan = parseInt(document.getElementById('totalKaryawan').textContent);
                    const absenBulanIni = data.statistik.bulan_ini;
                    
                    // Calculate working days in current month (approx 22 days)
                    const workingDays = 22;
                    const expectedAbsensi = totalKaryawan * workingDays;
                    const absensiPercentage = expectedAbsensi > 0 ? Math.round((absenBulanIni / expectedAbsensi) * 100) : 0;
                    const tidakHadirPercentage = 100 - absensiPercentage;
                    
                    const donutCtx = document.getElementById('monthlyDonutChart').getContext('2d');
                    if (monthlyDonutChart) monthlyDonutChart.destroy();
                    
                    monthlyDonutChart = new Chart(donutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Hadir', 'Tidak Hadir'],
                            datasets: [{
                                data: [absensiPercentage, tidakHadirPercentage],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(239, 68, 68, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(239, 68, 68, 1)'
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + context.parsed + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading charts:', error);
            }
        }
        
        loadDashboard();
    </script>
    
    <!-- Load notification script first, then initialize -->
    <script src="assets/js/notifications.js"></script>
    <script src="assets/js/modal-alert.js"></script>
    <script>
        // Initialize Notification System after script loaded
        if (typeof NotificationSystem !== 'undefined') {
            NotificationSystem.init(token);
        }
        
        // Logout handler with confirmation
        function handleLogout() {
            ModalAlert.confirm(
                'Apakah Anda yakin ingin keluar dari sistem?',
                () => {
                    // User confirmed logout
                    window.location.href = 'logout.php';
                },
                null,
                'Konfirmasi Logout'
            );
        }
    </script>
</body>
</html>
