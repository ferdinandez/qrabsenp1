<!-- Loading States Components -->

<!-- Table Loading Skeleton -->
<script id="tableLoadingTemplate" type="text/template">
<tr class="loading-row">
    <td colspan="100%" class="px-6 py-12">
        <div class="flex flex-col items-center justify-center gap-4">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-attendx-primary rounded-full border-t-transparent animate-spin"></div>
            </div>
            <div class="text-center">
                <p class="text-gray-600 font-semibold mb-1">Memuat data...</p>
                <p class="text-gray-400 text-sm">Mohon tunggu sebentar</p>
            </div>
        </div>
    </td>
</tr>
</script>

<!-- Card Loading Skeleton -->
<script id="cardLoadingTemplate" type="text/template">
<div class="animate-pulse">
    <div class="bg-gray-200 h-8 w-3/4 rounded mb-4"></div>
    <div class="bg-gray-200 h-4 w-1/2 rounded mb-2"></div>
    <div class="bg-gray-200 h-4 w-5/6 rounded"></div>
</div>
</script>

<!-- Spinner Loading (Inline) -->
<div id="spinnerTemplate" class="hidden">
    <div class="flex items-center justify-center gap-2">
        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Loading...</span>
    </div>
</div>

<!-- Global CSS for Loading States -->
<style>
    /* Spinner Animation */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Pulse Animation for Skeleton */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .loading-overlay.show {
        opacity: 1;
    }
    
    .loading-spinner {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
</style>

<!-- JavaScript Loading Utilities -->
<script>
const LoadingStates = {
    // Show table loading
    showTableLoading(tableId, colspan = 6) {
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12">
                    <div class="flex flex-col items-center justify-center gap-4">
                        <div class="relative w-16 h-16">
                            <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-attendx-primary rounded-full border-t-transparent animate-spin"></div>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-600 font-semibold mb-1">Memuat data...</p>
                            <p class="text-gray-400 text-sm">Mohon tunggu sebentar</p>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    },
    
    // Show empty state
    showEmptyState(tableId, message = 'Tidak ada data', colspan = 6) {
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-16">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-500 mb-1">${message}</p>
                        <p class="text-sm text-gray-400">Data akan muncul di sini</p>
                    </div>
                </td>
            </tr>
        `;
    },
    
    // Show error state
    showErrorState(tableId, message = 'Gagal memuat data', colspan = 6) {
        const tbody = document.getElementById(tableId);
        if (!tbody) return;
        
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-16">
                    <div class="flex flex-col items-center justify-center text-red-400">
                        <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-exclamation-circle text-3xl text-red-500"></i>
                        </div>
                        <p class="text-lg font-semibold text-red-600 mb-1">${message}</p>
                        <p class="text-sm text-gray-400">Silakan coba lagi</p>
                    </div>
                </td>
            </tr>
        `;
    },
    
    // Show full page loading overlay
    showOverlay(message = 'Loading...') {
        // Remove existing overlay
        this.hideOverlay();
        
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-spinner">
                <div class="relative w-16 h-16">
                    <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-attendx-primary rounded-full border-t-transparent animate-spin"></div>
                </div>
                <p class="text-gray-700 font-semibold">${message}</p>
            </div>
        `;
        document.body.appendChild(overlay);
        
        // Trigger animation
        setTimeout(() => overlay.classList.add('show'), 10);
    },
    
    // Hide overlay
    hideOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('show');
            setTimeout(() => overlay.remove(), 300);
        }
    },
    
    // Show button loading
    showButtonLoading(buttonElement, text = 'Loading...') {
        if (!buttonElement) return;
        
        buttonElement.disabled = true;
        buttonElement.dataset.originalText = buttonElement.innerHTML;
        buttonElement.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                <span>${text}</span>
            </div>
        `;
    },
    
    // Hide button loading
    hideButtonLoading(buttonElement) {
        if (!buttonElement) return;
        
        buttonElement.disabled = false;
        buttonElement.innerHTML = buttonElement.dataset.originalText || 'Submit';
    }
};
</script>
