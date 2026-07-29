/**
 * API Configuration for Vercel Deployment
 * No proxy needed - direct to Render API!
 */

// NO PROXY needed on Vercel!
const USE_PROXY = false;

// Direct to Render API
const API_BASE_URL = 'https://attendx-t6ow.onrender.com/api';

console.log('🚀 VERCEL MODE - Direct API calls');
console.log('API_BASE_URL:', API_BASE_URL);

/**
 * Helper function untuk fetch API
 */
async function apiRequest(endpoint, options = {}) {
    // Remove leading slash if present
    endpoint = endpoint.replace(/^\//, '');

    const url = `${API_BASE_URL}/${endpoint}`;

    console.log('🌐 API Request:', url);

    return fetch(url, options);
}

// Export
window.API_CONFIG = {
    USE_PROXY,
    API_BASE_URL,
    apiRequest
};

console.log('✅ API_CONFIG ready (Vercel mode)');
