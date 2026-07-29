<?php
// Admin Sidebar Component
// Usage: include 'includes/admin_sidebar.php';
// Set $currentPage variable before including this file

$role = $_SESSION['role'] ?? 'karyawan';
$currentPage = $currentPage ?? '';
?>
<aside class="attendx-sidebar">
    <div class="attendx-logo">
        <div class="attendx-logo-icon">
            <i class="fas fa-qrcode"></i>
        </div>
        <span>Attend<span style="color: #0066FF;">X</span></span>
    </div>
    <nav class="space-y-6 mt-12">
        <a href="dashboard.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'dashboard' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-th-large text-xl"></i>
        </a>
        <?php if ($role === 'admin'): ?>
        <a href="generate_qr.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'generate_qr' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-qrcode text-xl"></i>
        </a>
        <a href="users.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'users' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-users text-xl"></i>
        </a>
        <a href="report.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'report' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-file-alt text-xl"></i>
        </a>
        <?php else: ?>
        <a href="scan_qr.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'scan_qr' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-camera text-xl"></i>
        </a>
        <a href="absensi.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'absensi' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-map-marker-alt text-xl"></i>
        </a>
        <?php endif; ?>
        <a href="history.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'history' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-history text-xl"></i>
        </a>
        <a href="calendar.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'calendar' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-calendar text-xl"></i>
        </a>
        <a href="profile.php" class="flex items-center justify-center w-12 h-12 rounded-xl <?= $currentPage === 'profile' ? 'bg-attendx-primary text-white' : 'text-white text-opacity-70 hover:bg-white hover:bg-opacity-10' ?> transition mx-auto">
            <i class="fas fa-user text-xl"></i>
        </a>
        <div class="pt-6 border-t border-white border-opacity-10 mx-4">
            <a href="logout.php" class="flex items-center justify-center w-12 h-12 rounded-xl text-red-400 hover:bg-red-500 hover:bg-opacity-20 transition mx-auto">
                <i class="fas fa-sign-out-alt text-xl"></i>
            </a>
        </div>
    </nav>
</aside>
