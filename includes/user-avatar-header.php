<?php
/**
 * User Avatar Header Component
 * Menampilkan avatar user di header (ujung kanan)
 * 
 * Usage:
 * <?php include 'includes/user-avatar-header.php'; ?>
 * 
 * Prerequisites:
 * - Session harus sudah dimulai dengan session_start()
 * - $_SESSION['username'] harus ada
 * - $_SESSION['token'] harus ada untuk fetch avatar dari API
 */

$header_username = $_SESSION['username'] ?? 'User';
$header_user_id = $_SESSION['user_id'] ?? null;
?>

<div id="headerAvatarContainer" class="relative">
    <!-- Loading state -->
    <div id="headerAvatarLoading" class="w-10 h-10 rounded-full bg-gray-200 animate-pulse"></div>
    
    <!-- Avatar image (hidden by default, shown when loaded) -->
    <img id="headerAvatarImage" 
         class="hidden w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm cursor-pointer hover:shadow-md transition" 
         alt="<?php echo htmlspecialchars($header_username); ?>"
         onclick="window.location.href='profile.php'"
         title="Click to view profile">
    
    <!-- Initial fallback (shown if no avatar) -->
    <div id="headerAvatarInitial" 
         class="hidden w-10 h-10 rounded-full bg-gradient-to-br from-attendx-primary to-blue-600 flex items-center justify-center text-white font-bold cursor-pointer hover:shadow-md transition"
         onclick="window.location.href='profile.php'"
         title="Click to view profile">
        <?php echo strtoupper(substr($header_username, 0, 1)); ?>
    </div>
</div>

<script>
(function() {
    const API_URL = window.API_CONFIG ? window.API_CONFIG.API_BASE_URL : 'https://attendx-production-00d1.up.railway.app/api';
    const token = '<?= $_SESSION['token'] ?? '' ?>';
    
    async function loadHeaderAvatar() {
        // Get elements
        const loading = document.getElementById('headerAvatarLoading');
        const avatarImage = document.getElementById('headerAvatarImage');
        const avatarInitial = document.getElementById('headerAvatarInitial');
        
        if (!token) {
            // No token, show initial
            loading.classList.add('hidden');
            avatarInitial.classList.remove('hidden');
            return;
        }
        
        try {
            const res = await fetch(`${API_URL}/profile`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });
            
            if (!res.ok) {
                throw new Error('Failed to load profile');
            }
            
            const data = await res.json();
            
            // Hide loading
            loading.classList.add('hidden');
            
            if (data.user && data.user.avatar) {
                // Show avatar image
                avatarImage.src = data.user.avatar;
                avatarImage.classList.remove('hidden');
            } else {
                // Show initial
                avatarInitial.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error loading header avatar:', error);
            // Hide loading, show initial
            loading.classList.add('hidden');
            avatarInitial.classList.remove('hidden');
        }
    }
    
    // Load avatar when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadHeaderAvatar);
    } else {
        loadHeaderAvatar();
    }
})();
</script>
