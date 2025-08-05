/**
 * Global CSRF Token Handler for Admin Panel
 * This file ensures all AJAX requests include CSRF tokens
 */

// Global CSRF token handling
function getCsrfToken() {
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // If token is not available, try to refresh it
    if (!token) {
        fetch('/csrf-token')
            .then(response => response.json())
            .then(data => {
                token = data.token;
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', token);
            })
            .catch(() => {
                console.error('Unable to get CSRF token. Please refresh the page.');
                return null;
            });
    }
    
    return token;
}

// Enhanced fetch function with CSRF token
function fetchWithCsrf(url, options = {}) {
    const token = getCsrfToken();
    
    // Set default headers
    options.headers = {
        'X-CSRF-TOKEN': token,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers
    };
    
    return fetch(url, options);
}

// Override global fetch to automatically include CSRF token
(function() {
    const originalFetch = window.fetch;
    
    window.fetch = function(url, options = {}) {
        // Only add CSRF token for POST, PUT, DELETE requests to admin routes
        if (options.method && ['POST', 'PUT', 'DELETE', 'PATCH'].includes(options.method.toUpperCase())) {
            const token = getCsrfToken();
            
            if (token) {
                options.headers = {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...options.headers
                };
            }
        }
        
        return originalFetch.call(this, url, options);
    };
})();

// Handle CSRF token errors globally
document.addEventListener('DOMContentLoaded', function() {
    // Intercept fetch responses to handle CSRF errors
    const originalFetch = window.fetch;
    
    window.fetch = function(url, options = {}) {
        return originalFetch.call(this, url, options).then(response => {
            if (response.status === 419) {
                // CSRF token mismatch, try to refresh token
                return fetch('/csrf-token')
                    .then(tokenResponse => tokenResponse.json())
                    .then(tokenData => {
                        // Update token in meta tag
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', tokenData.token);
                        
                        // Retry original request with new token
                        if (options.method && ['POST', 'PUT', 'DELETE', 'PATCH'].includes(options.method.toUpperCase())) {
                            options.headers = {
                                'X-CSRF-TOKEN': tokenData.token,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...options.headers
                            };
                        }
                        
                        return originalFetch.call(this, url, options);
                    })
                    .catch(() => {
                        // If token refresh fails, reload page
                        alert('Session expired. Please refresh the page and try again.');
                        location.reload();
                        return response;
                    });
            }
            return response;
        });
    };
});

// Export for use in other scripts
window.AdminCsrf = {
    getCsrfToken,
    fetchWithCsrf
}; 