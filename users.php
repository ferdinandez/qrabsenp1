<?php
session_start();
if (!isset($_SESSION['token']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai - AttendX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <script src="assets/js/dark-mode.js"></script>
    <script src="assets/js/modal-alert.js"></script>
    <script src="assets/js/notifications.js"></script>
    <?php include 'includes/loading-states.php'; ?>
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            overflow-y: auto;
            padding: 40px 0;
        }
        .modal.active {
            display: block;
        }
        .modal-content {
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            margin: 0 auto;
            position: relative;
        }
        /* Custom scrollbar for modal */
        .modal-content::-webkit-scrollbar {
            width: 8px;
        }
        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb {
            background: #0066FF;
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #0052CC;
        }
        
        /* Modal Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { 
                opacity: 0;
                transform: scale(0.9) translateY(30px);
            }
            to { 
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out;
        }
        .animate-scaleIn {
            animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        /* Input hover effects */
        .group:hover input,
        .group:hover select {
            border-color: #93c5fd;
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
                <a href="generate_qr.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-qrcode text-xl"></i>
                </a>
                <a href="users.php" class="flex items-center justify-center w-12 h-12 rounded-xl bg-attendx-primary text-white transition mx-auto">
                    <i class="fas fa-users text-xl"></i>
                </a>
                <a href="report.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-file-alt text-xl"></i>
                </a>
                <a href="leave_approval.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </a>
                <a href="history.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-white text-opacity-70 hover:bg-white hover:bg-opacity-10 transition mx-auto">
                    <i class="fas fa-history text-xl"></i>
                </a>
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
            <div class="bg-white border-b border-gray-200 px-8 py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <h1 class="text-3xl font-black text-gray-900">Attend<span class="text-attendx-primary">X</span></h1>
                        <span class="text-sm text-gray-400">Data Pegawai</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Notification Bell -->
                        <?php include 'includes/notification-bell.php'; ?>
                        
                        <?php include 'includes/user-avatar-header.php'; ?>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" placeholder="Cari pegawai..." class="pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none w-80">
                    </div>
                    <button onclick="openAddModal()" class="px-6 py-3 bg-gradient-to-r from-attendx-primary to-blue-600 text-white font-bold rounded-full hover:shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Pegawai</span>
                    </button>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pegawai</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable" class="divide-y divide-gray-200">
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="userModal" class="modal">
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 z-[9999] animate-fadeIn">
            <div class="modal-content bg-white rounded-3xl max-w-2xl w-full mx-4 shadow-2xl transform animate-scaleIn overflow-hidden">
                <!-- Modal Header with Gradient -->
                <div class="bg-gradient-to-r from-attendx-primary to-blue-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-plus text-white text-xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white" id="modalTitle">Tambah Pegawai</h3>
                        </div>
                        <button onclick="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 w-10 h-10 rounded-xl transition flex items-center justify-center">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="p-8 max-h-[70vh] overflow-y-auto">
                    <form id="userForm" class="space-y-5">
                        <input type="hidden" id="userId">
                        
                        <!-- Name Field -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user text-attendx-primary"></i>
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300"
                                   placeholder="Masukkan nama lengkap">
                        </div>
                        
                        <!-- Email Field -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-attendx-primary"></i>
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300"
                                   placeholder="nama@perusahaan.com">
                        </div>
                        
                        <!-- Department & Position in Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Department Field -->
                            <div class="group">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-building text-attendx-primary"></i>
                                    Department
                                </label>
                                <input type="text" id="department" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300"
                                       placeholder="e.g. IT, HR, Finance">
                            </div>
                            
                            <!-- Position Field -->
                            <div class="group">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                    <i class="fas fa-briefcase text-attendx-primary"></i>
                                    Position
                                </label>
                                <input type="text" id="position" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300"
                                       placeholder="e.g. Software Engineer">
                            </div>
                        </div>
                        
                        <!-- Phone Field -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-phone text-attendx-primary"></i>
                                Phone
                            </label>
                            <input type="tel" id="phone" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300"
                                   placeholder="+62 812-3456-7890">
                        </div>
                        
                        <!-- Password Field -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-lock text-attendx-primary"></i>
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" id="password" 
                                       class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300"
                                       placeholder="Kosongkan jika tidak diubah">
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                    <i id="passwordIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p class="mt-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-xs text-gray-600 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                <span id="passwordInfo">Password default: <strong>password123</strong> (user harus ganti setelah login pertama)</span>
                            </p>
                        </div>
                        
                        <!-- Role Field -->
                        <div class="group">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-user-tag text-attendx-primary"></i>
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select id="role" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-attendx-primary focus:ring-4 focus:ring-blue-100 transition outline-none group-hover:border-gray-300 bg-white">
                                <option value="karyawan">Karyawan</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4 border-t mt-6">
                            <button type="button" onclick="closeModal()" 
                                    class="flex-1 px-6 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i>
                                <span>Batal</span>
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-6 py-4 bg-gradient-to-r from-attendx-primary to-blue-600 text-white font-bold rounded-xl hover:shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <i class="fas fa-check"></i>
                                <span>Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Use global API_CONFIG from api-config.js
        const API_URL = window.API_CONFIG ? window.API_CONFIG.API_BASE_URL : 'https://attendx-t6ow.onrender.com/api';
        const token = '<?= $_SESSION['token'] ?>';

        async function loadUsers() {
            // Show loading state
            LoadingStates.showTableLoading('usersTable', 6);
            
            try {
                const res = await fetch(`${API_URL}/users`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                });
                const data = await res.json();
                console.log('API Response:', data); // Debug
                const tbody = document.getElementById('usersTable');
                tbody.innerHTML = '';
                if (res.ok && data.data) {
                    if (data.data.length === 0) {
                        LoadingStates.showEmptyState('usersTable', 'Belum ada data pegawai', 6);
                        return;
                    }
                    data.data.forEach(user => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-50 transition';
                        const statusBadge = user.status === 'active' 
                            ? '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>'
                            : user.status === 'suspended'
                            ? '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Suspended</span>'
                            : '<span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>';
                        
                        // Use real avatar from database or fallback to initial
                        let avatarHTML;
                        if (user.avatar) {
                            avatarHTML = `<img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full object-cover bg-gray-100 border-2 border-white shadow">`;
                        } else {
                            const initial = user.name.charAt(0).toUpperCase();
                            avatarHTML = `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm border-2 border-white shadow">${initial}</div>`;
                        }
                        
                        tr.innerHTML = `
                            <td class="px-6 py-4 text-sm text-gray-600">${user.id}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    ${avatarHTML}
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">${user.name}</div>
                                        <div class="text-xs text-gray-500">${user.position || user.department || user.role.charAt(0).toUpperCase() + user.role.slice(1)}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">${user.email}</td>
                            <td class="px-6 py-4 text-sm"><span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">${user.role}</span></td>
                            <td class="px-6 py-4 text-sm">${statusBadge}</td>
                            <td class="px-6 py-4 text-sm">
                                <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    LoadingStates.showErrorState('usersTable', 'Error loading data', 6);
                    console.error('API Error:', data);
                }
            } catch (error) {
                console.error('Error:', error);
                LoadingStates.showErrorState('usersTable', 'Network error: ' + error.message, 6);
            }
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Pegawai';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordInfo').innerHTML = 'Password default: <strong>password123</strong> (user harus ganti setelah login pertama)';
            document.getElementById('userModal').classList.add('active');
        }

        async function editUser(id) {
            try {
                const res = await fetch(`${API_URL}/users/${id}`, {
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                });
                const data = await res.json();
                if (res.ok) {
                    document.getElementById('modalTitle').textContent = 'Edit Pegawai';
                    document.getElementById('userId').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('email').value = data.email;
                    document.getElementById('role').value = data.role;
                    document.getElementById('department').value = data.department || '';
                    document.getElementById('position').value = data.position || '';
                    document.getElementById('phone').value = data.phone || '';
                    document.getElementById('passwordInfo').innerHTML = 'Kosongkan jika tidak ingin mengubah password';
                    document.getElementById('userModal').classList.add('active');
                }
            } catch (error) {
                console.error('Error:', error);
                ModalAlert.error('Gagal memuat data pegawai. Silakan coba lagi.');
            }
        }

        async function deleteUser(id) {
            ModalAlert.confirm(
                'Apakah Anda yakin ingin menghapus pegawai ini? Data yang sudah dihapus tidak dapat dikembalikan.',
                async () => {
                    try {
                        const res = await fetch(`${API_URL}/users/${id}`, {
                            method: 'DELETE',
                            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' }
                        });
                        if (res.ok) {
                            ModalAlert.success('Pegawai berhasil dihapus!');
                            loadUsers();
                        } else {
                            ModalAlert.error('Gagal menghapus pegawai. Silakan coba lagi.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        ModalAlert.error('Terjadi kesalahan jaringan. Silakan coba lagi.');
                    }
                },
                null,
                'Hapus Pegawai?'
            );
        }

        function closeModal() {
            const modal = document.getElementById('userModal');
            modal.classList.remove('active');
            // Reset form
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        document.getElementById('userForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Disable submit button to prevent double submit
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = '⏳ Menyimpan...';
            }
            
            const id = document.getElementById('userId').value;
            const body = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                role: document.getElementById('role').value,
                department: document.getElementById('department').value,
                position: document.getElementById('position').value,
                phone: document.getElementById('phone').value,
            };
            const password = document.getElementById('password').value;
            if (password) {
                body.password = password;
                body.password_confirmation = password;
            }
            try {
                const url = id ? `${API_URL}/users/${id}` : `${API_URL}/users`;
                const method = id ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                
                // Re-enable button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
                
                if (res.ok) {
                    // Close modal first
                    closeModal();
                    
                    // Show success message
                    ModalAlert.success(
                        id ? 'Data pegawai berhasil diperbarui!' : 'Pegawai baru berhasil ditambahkan! Email notifikasi sedang dikirim.',
                        'Berhasil!'
                    );
                    
                    // Reload users
                    loadUsers();
                } else {
                    const data = await res.json();
                    ModalAlert.error(data.message || 'Gagal menyimpan data pegawai. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Re-enable button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
                
                ModalAlert.error('Terjadi kesalahan jaringan. Silakan coba lagi.');
            }
        });

        document.getElementById('searchInput').addEventListener('input', (e) => {
            const search = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTable tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        });

        // Logout handler with confirmation
        function handleLogout() {
            ModalAlert.confirm(
                'Apakah Anda yakin ingin keluar dari sistem?',
                () => {
                    window.location.href = 'logout.php';
                },
                null,
                'Konfirmasi Logout'
            );
        }

        loadUsers();
        
        // Initialize Notification System
        if (typeof NotificationSystem !== 'undefined') {
            NotificationSystem.init(token);
        }
    </script>
</body>
</html>
