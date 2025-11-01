/**
 * API Utility
 * Configured Axios instance for API requests with authentication
 */

import axios from 'axios';

// Create axios instance with default configuration
const api = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Request interceptor: Attach token to requests
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor: Handle errors and token refresh
api.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        const originalRequest = error.config;

        // Handle 401 Unauthorized (token expired or invalid)
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            // Clear token and redirect to login
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            
            // Only redirect if not already on login page
            if (window.location.pathname !== '/login') {
                window.location.href = '/login';
            }
        }

        // Handle 419 CSRF token mismatch
        if (error.response?.status === 419) {
            // CSRF token refresh logic can be added here if needed
            console.error('CSRF token mismatch');
        }

        return Promise.reject(error);
    }
);

export default api;

