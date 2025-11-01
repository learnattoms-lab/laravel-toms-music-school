/**
 * Authentication Composable
 * Provides authentication functionality for Vue components
 */

import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/utils/api';

const TOKEN_KEY = 'auth_token';
const USER_KEY = 'user';

export function useAuth() {
    const router = useRouter();
    
    // Reactive state
    const user = ref(null);
    const token = ref(localStorage.getItem(TOKEN_KEY));
    const loading = ref(false);
    const error = ref(null);

    // Computed properties
    const isAuthenticated = computed(() => !!token.value && !!user.value);

    /**
     * Initialize auth state from localStorage
     */
    const initAuth = () => {
        const storedToken = localStorage.getItem(TOKEN_KEY);
        const storedUser = localStorage.getItem(USER_KEY);
        
        if (storedToken) {
            token.value = storedToken;
        }
        
        if (storedUser) {
            try {
                user.value = JSON.parse(storedUser);
            } catch (e) {
                console.error('Failed to parse stored user:', e);
                localStorage.removeItem(USER_KEY);
            }
        }
    };

    /**
     * Set authentication data
     */
    const setAuth = (authToken, userData) => {
        token.value = authToken;
        user.value = userData;
        
        localStorage.setItem(TOKEN_KEY, authToken);
        localStorage.setItem(USER_KEY, JSON.stringify(userData));
    };

    /**
     * Clear authentication data
     */
    const clearAuth = () => {
        token.value = null;
        user.value = null;
        
        localStorage.removeItem(TOKEN_KEY);
        localStorage.removeItem(USER_KEY);
    };

    /**
     * Login with email and password
     * @param {Object} credentials - { email, password }
     * @returns {Promise}
     */
    const login = async (credentials) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post('/auth/login', {
                email: credentials.email,
                password: credentials.password,
            });

            const { token: authToken, user: userData } = response.data;
            
            setAuth(authToken, userData);
            
            return { success: true, user: userData };
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'Login failed';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Register a new user
     * @param {Object} data - Registration data
     * @returns {Promise}
     */
    const register = async (data) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post('/auth/register', {
                email: data.email,
                password: data.password,
                password_confirmation: data.password_confirmation,
                first_name: data.first_name,
                last_name: data.last_name,
                ...data,
            });

            const { token: authToken, user: userData } = response.data;
            
            setAuth(authToken, userData);
            
            return { success: true, user: userData };
        } catch (err) {
            error.value = err.response?.data?.message || err.response?.data?.errors || err.message || 'Registration failed';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Logout current user
     */
    const logout = async () => {
        loading.value = true;
        error.value = null;

        try {
            // Call logout endpoint to revoke token on server
            await api.post('/auth/logout');
        } catch (err) {
            // Even if logout fails, clear local state
            console.error('Logout error:', err);
        } finally {
            clearAuth();
            loading.value = false;
            
            // Redirect to login page
            router.push('/login');
        }
    };

    /**
     * Fetch current user from API
     * @returns {Promise}
     */
    const fetchUser = async () => {
        if (!token.value) {
            return { success: false };
        }

        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/auth/user');
            const userData = response.data;
            
            user.value = userData;
            localStorage.setItem(USER_KEY, JSON.stringify(userData));
            
            return { success: true, user: userData };
        } catch (err) {
            // If user fetch fails, clear auth and redirect
            if (err.response?.status === 401) {
                clearAuth();
                router.push('/login');
            }
            
            error.value = err.response?.data?.message || err.message || 'Failed to fetch user';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Refresh authentication token (if needed in future)
     * @returns {Promise}
     */
    const refreshToken = async () => {
        // Token refresh logic can be implemented here if needed
        // For now, Sanctum tokens don't require refresh
        return fetchUser();
    };

    /**
     * Check if user has a specific role
     * @param {string} role - Role to check (e.g., 'ROLE_ADMIN')
     * @returns {boolean}
     */
    const hasRole = (role) => {
        if (!user.value || !user.value.roles) {
            return false;
        }
        return user.value.roles.includes(role);
    };

    /**
     * Check if user is admin
     * @returns {boolean}
     */
    const isAdmin = computed(() => hasRole('ROLE_ADMIN'));

    /**
     * Check if user is teacher
     * @returns {boolean}
     */
    const isTeacher = computed(() => user.value?.is_teacher || hasRole('ROLE_TEACHER'));

    /**
     * Check if user is student
     * @returns {boolean}
     */
    const isStudent = computed(() => !isTeacher.value && (hasRole('ROLE_USER') || !user.value?.roles?.length));

    // Initialize auth state on mount
    onMounted(() => {
        initAuth();
        
        // If token exists but user doesn't, fetch user
        if (token.value && !user.value) {
            fetchUser();
        }
    });

    return {
        // State
        user,
        token,
        loading,
        error,
        isAuthenticated,
        
        // Computed
        isAdmin,
        isTeacher,
        isStudent,
        
        // Methods
        login,
        register,
        logout,
        fetchUser,
        refreshToken,
        hasRole,
        clearAuth,
        initAuth,
    };
}

