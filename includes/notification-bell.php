<!-- Notification Bell Component -->
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
