<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'karyawan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - AttendX v<?= time() ?></title>
    <script src="https://cdn.tailwindcss.com?v=<?= time() ?>"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <script src="assets/js/dark-mode.js"></script>
    <script src="assets/js/modal-alert.js"></script>
    <script src="assets/js/notifications.js"></script>
    <?php include 'includes/loading-states.php'; ?>
    <script>
        function handleLogout() {
            if (typeof ModalAlert !== 'undefined') {
                ModalAlert.confirm('Apakah Anda yakin ingin keluar dari sistem?', () => { window.location.href = 'logout.php'; }, null, 'Konfirmasi Logout');
            } else {
                if (confirm('Apakah Anda yakin ingin keluar?')) window.location.href = 'logout.php';
            }
        }
    </script>
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
                <a href="dashboard.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
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
                <?php else: ?>
                <a href="scan_qr.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-camera text-xl"></i>
                </a>
                <a href="absensi.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-map-marker-alt text-xl"></i>
                </a>
                <?php endif; ?>
                <a href="history.php" class="flex items-center justify-center w-12 h-12 rounded-xl bg-attendx-primary text-white transition mx-auto">
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
                    <a href="logout.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-red-400 hover:bg-red-500 hover:bg-opacity-20 transition mx-auto">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </a>
                </div>
            </nav>
        </aside>
        <main class="flex-1 overflow-y-auto">
            <div class="bg-white border-b border-gray-200 px-8 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <h1 class="text-3xl font-black text-gray-900">Attend<span class="text-attendx-primary">X</span></h1>
                        <span class="text-sm text-gray-400">Riwayat Absensi</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <?php include 'includes/notification-bell.php'; ?>
                        <?php include 'includes/user-avatar-header.php'; ?>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-xl text-blue-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="totalAbsensi">-</div>
                        <div class="text-sm text-gray-500 font-medium">Total Absensi</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-xl text-green-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="bulanIni">-</div>
                        <div class="text-sm text-gray-500 font-medium">Bulan Ini</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-calendar-week text-xl text-orange-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="mingguIni">-</div>
                        <div class="text-sm text-gray-500 font-medium">Minggu Ini</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-calendar-day text-xl text-purple-600"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1" id="hariIni">-</div>
                        <div class="text-sm text-gray-500 font-medium">Hari Ini</div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Filter Riwayat</h3>
                        <div class="flex gap-2">
                            <button onclick="exportPDF()" class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white font-bold rounded-lg hover:shadow-lg transition flex items-center gap-2">
                                <i class="fas fa-file-pdf"></i>
                                <span>Export PDF</span>
                            </button>
                            <button onclick="exportCSV()" class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white font-bold rounded-lg hover:shadow-lg transition flex items-center gap-2">
                                <i class="fas fa-file-csv"></i>
                                <span>Export CSV</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-<?php echo $role === 'admin' ? '4' : '3'; ?> gap-4">
                            <?php if ($role === 'admin'): ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pegawai</label>
                                <select id="userFilter" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none">
                                    <option value="">Semua Pegawai</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                                <input type="date" id="startDate" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                                <input type="date" id="endDate" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none">
                            </div>
                            <div class="flex items-end">
                                <button onclick="filterData()" class="w-full px-6 py-3 bg-gradient-to-r from-attendx-primary to-blue-600 text-white font-bold rounded-xl hover:shadow-lg transition">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Riwayat Absensi</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                    <?php if ($role === 'admin'): ?>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                                    <?php endif; ?>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jam Masuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jam Pulang</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Durasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody id="historyTable" class="divide-y divide-gray-200">
                                <tr><td colspan="<?php echo $role === 'admin' ? '7' : '6'; ?>" class="px-6 py-8 text-center text-gray-400">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="assets/js/api-config.js?v=<?= time() ?>"></script>
    <script>
        console.log('=== History Page Loaded v3.0 DENGAN PROXY ===');
        const token = '<?= $_SESSION['token'] ?? '' ?>';
        const userRole = '<?= $role ?>';
        const colSpan = userRole === 'admin' ? 7 : 6;
        console.log('Token exists:', !!token);
        console.log('User role:', userRole);
        
        // Check if token exists
        if (!token) {
            console.error('Token not found in session!');
            document.getElementById('historyTable').innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i>Session expired. Please <a href="login.php" class="text-blue-600 underline">login again</a></td></tr>`;
            // Redirect instead of return
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 3000);
        }
        
        if (token) {
            console.log('Token:', token.substring(0, 20) + '...');
        }

        // Load user list for admin filter
        async function loadUserList() {
            if (userRole !== 'admin') return;
            
            try {
                const res = await API_CONFIG.apiRequest('/users', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                });
                
                if (res.ok) {
                    const data = await res.json();
                    const userFilter = document.getElementById('userFilter');
                    
                    data.users.forEach(user => {
                        if (user.role === 'karyawan') {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = `${user.name} (${user.email})`;
                            userFilter.appendChild(option);
                        }
                    });
                }
            } catch (error) {
                console.error('Failed to load users:', error);
            }
        }

        // Helper function with timeout
        async function fetchWithTimeout(url, options = {}, timeout = 10000) {
            const controller = new AbortController();
            const id = setTimeout(() => controller.abort(), timeout);
            
            try {
                const response = await fetch(url, {
                    ...options,
                    signal: controller.signal
                });
                clearTimeout(id);
                return response;
            } catch (error) {
                clearTimeout(id);
                throw error;
            }
        }

        async function loadHistory() {
            const tbody = document.getElementById('historyTable');
            
            try {
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Loading...</td></tr>`;
                
                const res = await API_CONFIG.apiRequest('/history', {
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                });
                
                if (!res.ok) {
                    // Handle authentication error
                    if (res.status === 401) {
                        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-red-400"><i class="fas fa-exclamation-triangle mr-2"></i>Session expired. Redirecting to login...</td></tr>`;
                        setTimeout(() => {
                            window.location.href = 'logout.php';
                        }, 2000);
                        return;
                    }
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                
                const data = await res.json();
                console.log('History API Response:', data);
                console.log('Statistik:', data.statistik);
                console.log('History Data:', data.history);
                console.log('Debug Info:', data.debug);
                
                if (res.ok) {
                    // Update statistik cards
                    if (data.statistik) {
                        console.log('Setting statistik values...');
                        console.log('Total:', data.statistik.total);
                        console.log('Bulan Ini:', data.statistik.bulan_ini);
                        console.log('Minggu Ini:', data.statistik.minggu_ini);
                        console.log('Hari Ini:', data.statistik.hari_ini);
                        
                        document.getElementById('totalAbsensi').textContent = data.statistik.total || 0;
                        document.getElementById('bulanIni').textContent = data.statistik.bulan_ini || 0;
                        document.getElementById('mingguIni').textContent = data.statistik.minggu_ini || 0;
                        document.getElementById('hariIni').textContent = data.statistik.hari_ini || 0;
                    } else {
                        console.error('No statistik data in response');
                        // Set to 0 if no data
                        document.getElementById('totalAbsensi').textContent = 0;
                        document.getElementById('bulanIni').textContent = 0;
                        document.getElementById('mingguIni').textContent = 0;
                        document.getElementById('hariIni').textContent = 0;
                    }
                    
                    const tbody = document.getElementById('historyTable');
                    tbody.innerHTML = '';
                    if (!data.history || data.history.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat absensi. Silakan absen terlebih dahulu.</td></tr>`;
                        return;
                    }
                    
                    console.log('Processing', data.history.length, 'attendance records');
                    
                    // Group by date
                    const groupedByDate = {};
                    data.history.forEach(absen => {
                        const date = new Date(absen.waktu).toLocaleDateString('id-ID');
                        if (!groupedByDate[date]) {
                            groupedByDate[date] = { masuk: null, pulang: null };
                        }
                        const type = absen.type || 'masuk';
                        if (type === 'masuk') {
                            groupedByDate[date].masuk = absen;
                        } else if (type === 'pulang') {
                            groupedByDate[date].pulang = absen;
                        }
                    });
                    
                    console.log('Grouped into', Object.keys(groupedByDate).length, 'dates');
                    
                    // Render grouped data
                    let rowNum = 1;
                    Object.keys(groupedByDate).forEach(date => {
                        console.log('Rendering row', rowNum, 'for date', date);
                        const attendance = groupedByDate[date];
                        
                        // Get user name (from masuk or pulang record)
                        const userName = (attendance.masuk && attendance.masuk.user) ? attendance.masuk.user.name : 
                                        (attendance.pulang && attendance.pulang.user) ? attendance.pulang.user.name : 
                                        '-';
                        
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50 transition';
                        
                        const masukTime = attendance.masuk ? new Date(attendance.masuk.waktu).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                        const pulangTime = attendance.pulang ? new Date(attendance.pulang.waktu).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                        
                        // Calculate duration
                        let duration = '-';
                        if (attendance.masuk && attendance.pulang) {
                            const masukDate = new Date(attendance.masuk.waktu);
                            const pulangDate = new Date(attendance.pulang.waktu);
                            const diffMs = pulangDate - masukDate;
                            const diffHrs = Math.floor(diffMs / 3600000);
                            const diffMins = Math.floor((diffMs % 3600000) / 60000);
                            duration = `${diffHrs}j ${diffMins}m`;
                        }
                        
                        // Status badge
                        let statusBadge = '';
                        if (attendance.masuk && attendance.pulang) {
                            statusBadge = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i>Lengkap</span>';
                        } else if (attendance.masuk) {
                            statusBadge = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700"><i class="fas fa-exclamation-circle mr-1"></i>Belum Pulang</span>';
                        } else {
                            statusBadge = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">-</span>';
                        }
                        
                        // Build row HTML based on role
                        let rowHTML = `<td class="px-6 py-4 text-sm font-bold text-blue-600">${rowNum}</td>`;
                        
                        if (userRole === 'admin') {
                            rowHTML += `<td class="px-6 py-4 text-sm font-semibold text-gray-900">${userName}</td>`;
                        }
                        
                        rowHTML += `
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">${date}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${masukTime}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${pulangTime}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${duration}</td>
                            <td class="px-6 py-4">${statusBadge}</td>
                        `;
                        
                        tr.innerHTML = rowHTML;
                        tbody.appendChild(tr);
                        rowNum++;
                    });
                    
                    console.log('Finished rendering', rowNum - 1, 'rows');
                } else {
                    console.error('Error response:', data);
                    tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-red-400"><i class="fas fa-exclamation-circle mr-2"></i>Error: ` + (data.message || 'Gagal memuat data') + '</td></tr>';
                }
            } catch (error) {
                console.error('Error:', error);
                let errorMsg = 'Error: ' + error.message;
                if (error.name === 'AbortError') {
                    errorMsg = 'Request timeout - API server tidak respond. Pastikan Laravel server running (php artisan serve)';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMsg = 'Tidak bisa connect ke API. Pastikan Laravel server running di http://127.0.0.1:8000';
                }
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>${errorMsg}
                    <br><small class="text-xs mt-2 block">Check console (F12) untuk detail error</small>
                </td></tr>`;
            }
        }

        async function filterData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const userId = userRole === 'admin' ? document.getElementById('userFilter').value : '';
            
            if (!startDate && !endDate && !userId) {
                // If no filter, just reload
                loadHistory();
                return;
            }
            
            try {
                let queryParams = '?';
                if (startDate) queryParams += `start_date=${startDate}&`;
                if (endDate) queryParams += `end_date=${endDate}&`;
                if (userId) queryParams += `user_id=${userId}&`;
                
                const res = await API_CONFIG.apiRequest(`/history${queryParams}`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                });
                const data = await res.json();
                console.log('Filter Response:', data);
                if (res.ok) {
                    const tbody = document.getElementById('historyTable');
                    tbody.innerHTML = '';
                    if (data.history.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-gray-400">Tidak ada data pada rentang tanggal tersebut</td></tr>`;
                        return;
                    }
                    
                    // Group by date
                    const groupedByDate = {};
                    data.history.forEach(absen => {
                        const date = new Date(absen.waktu).toLocaleDateString('id-ID');
                        if (!groupedByDate[date]) {
                            groupedByDate[date] = { masuk: null, pulang: null };
                        }
                        const type = absen.type || 'masuk';
                        if (type === 'masuk') {
                            groupedByDate[date].masuk = absen;
                        } else if (type === 'pulang') {
                            groupedByDate[date].pulang = absen;
                        }
                    });
                    
                    // Render grouped data
                    let rowNum = 1;
                    Object.keys(groupedByDate).forEach(date => {
                        const attendance = groupedByDate[date];
                        
                        // Get user name (from masuk or pulang record)
                        const userName = (attendance.masuk && attendance.masuk.user) ? attendance.masuk.user.name : 
                                        (attendance.pulang && attendance.pulang.user) ? attendance.pulang.user.name : 
                                        '-';
                        
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50 transition';
                        
                        const masukTime = attendance.masuk ? new Date(attendance.masuk.waktu).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                        const pulangTime = attendance.pulang ? new Date(attendance.pulang.waktu).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                        
                        // Calculate duration
                        let duration = '-';
                        if (attendance.masuk && attendance.pulang) {
                            const masukDate = new Date(attendance.masuk.waktu);
                            const pulangDate = new Date(attendance.pulang.waktu);
                            const diffMs = pulangDate - masukDate;
                            const diffHrs = Math.floor(diffMs / 3600000);
                            const diffMins = Math.floor((diffMs % 3600000) / 60000);
                            duration = `${diffHrs}j ${diffMins}m`;
                        }
                        
                        // Status badge
                        let statusBadge = '';
                        if (attendance.masuk && attendance.pulang) {
                            statusBadge = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i>Lengkap</span>';
                        } else if (attendance.masuk) {
                            statusBadge = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700"><i class="fas fa-exclamation-circle mr-1"></i>Belum Pulang</span>';
                        } else {
                            statusBadge = '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">-</span>';
                        }
                        
                        // Build row HTML based on role
                        let rowHTML = `<td class="px-6 py-4 text-sm font-bold text-blue-600">${rowNum}</td>`;
                        
                        if (userRole === 'admin') {
                            rowHTML += `<td class="px-6 py-4 text-sm font-semibold text-gray-900">${userName}</td>`;
                        }
                        
                        rowHTML += `
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">${date}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${masukTime}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${pulangTime}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${duration}</td>
                            <td class="px-6 py-4">${statusBadge}</td>
                        `;
                        
                        tr.innerHTML = rowHTML;
                        tbody.appendChild(tr);
                        rowNum++;
                    });
                } else {
                    console.error('Error response:', data);
                    tbody.innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-red-400">Error: ` + (data.message || 'Gagal memuat data') + '</td></tr>';
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('historyTable').innerHTML = `<tr><td colspan="${colSpan}" class="px-6 py-8 text-center text-red-400">Error: ` + error.message + '</td></tr>';
            }
        }

        async function exportPDF() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            let url = `${API_URL}/karyawan/export?format=pdf`;
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            
            try {
                const res = await fetch(url, {
                    headers: { 
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json' 
                    }
                });
                
                if (res.ok) {
                    const blob = await res.blob();
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = `riwayat_absensi_${startDate || 'semua'}_${endDate || 'semua'}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                    ModalAlert.success('PDF berhasil diunduh!', 'Berhasil!');
                } else {
                    ModalAlert.error('Gagal mengunduh PDF');
                }
            } catch (error) {
                console.error('Error:', error);
                ModalAlert.error('Terjadi kesalahan saat mengunduh PDF');
            }
        }

        async function exportCSV() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            let url = `${API_URL}/karyawan/export?format=csv`;
            if (startDate) url += `&start_date=${startDate}`;
            if (endDate) url += `&end_date=${endDate}`;
            
            try {
                const res = await fetch(url, {
                    headers: { 
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json' 
                    }
                });
                
                if (res.ok) {
                    const blob = await res.blob();
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = `riwayat_absensi_${startDate || 'semua'}_${endDate || 'semua'}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(downloadUrl);
                    document.body.removeChild(a);
                    ModalAlert.success('CSV berhasil diunduh!', 'Berhasil!');
                } else {
                    ModalAlert.error('Gagal mengunduh CSV');
                }
            } catch (error) {
                console.error('Error:', error);
                ModalAlert.error('Terjadi kesalahan saat mengunduh CSV');
            }
        }

        // Initialize
        if (userRole === 'admin') {
            loadUserList();
        }
        loadHistory();
    </script>
</body>
</html>

