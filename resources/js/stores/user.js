/**
 * User Store
 * Manages user profile data and operations
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/utils/api';

export const useUserStore = defineStore('user', () => {
    // State
    const profile = ref(null);
    const stats = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // Actions
    const fetchProfile = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/user/profile');
            profile.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch profile';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateProfile = async (profileData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put('/user/profile', profileData);
            profile.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update profile';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchDashboard = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/user/dashboard');
            stats.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch dashboard';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchProgress = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/user/progress');
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch progress';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const clearError = () => {
        error.value = null;
    };

    return {
        // State
        profile,
        stats,
        loading,
        error,
        // Actions
        fetchProfile,
        updateProfile,
        fetchDashboard,
        fetchProgress,
        clearError,
    };
});

