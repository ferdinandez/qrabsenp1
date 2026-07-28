/**
 * API Configuration for Production (Railway Direct Connection)
 * Browser langsung connect ke Railway API (no proxy needed)
 */

// Direct connection ke Railway API
const USE_PROXY = false;

// Railway API URL
const API_BASE_URL = 'https://attendx-production-00d1.up.railway.app/api';

/**
 * Helper function untuk fetch API langsung
 */
async function apiRequest(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    return fetch(url, options);
}

// Export untuk digunakan di halaman lain
window.API_CONFIG = {
    USE_PROXY,
    API_BASE_URL,
    apiRequest
};
