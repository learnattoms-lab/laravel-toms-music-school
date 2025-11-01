/**
 * Session Store
 * Manages session data and operations
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/utils/api';

export const useSessionStore = defineStore('session', () => {
    // State
    const sessions = ref([]);
    const currentSession = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // Getters
    const upcomingSessions = computed(() => {
        const now = new Date();
        return sessions.value.filter(session => {
            const startTime = new Date(session.start_time);
            return startTime > now;
        }).sort((a, b) => new Date(a.start_time) - new Date(b.start_time));
    });

    const pastSessions = computed(() => {
        const now = new Date();
        return sessions.value.filter(session => {
            const startTime = new Date(session.start_time);
            return startTime <= now;
        }).sort((a, b) => new Date(b.start_time) - new Date(a.start_time));
    });

    // Actions
    const fetchSessions = async (filters = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/sessions', { params: filters });
            sessions.value = response.data.data || [];
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch sessions';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchSession = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/sessions/${id}`);
            currentSession.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch session';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const createSession = async (sessionData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post('/sessions', sessionData);
            const newSession = response.data.data;
            sessions.value.push(newSession);
            return newSession;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create session';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateSession = async (id, sessionData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put(`/sessions/${id}`, sessionData);
            const updatedSession = response.data.data;
            const index = sessions.value.findIndex(s => s.id === id);
            if (index !== -1) {
                sessions.value[index] = updatedSession;
            }
            if (currentSession.value?.id === id) {
                currentSession.value = updatedSession;
            }
            return updatedSession;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update session';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteSession = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            await api.delete(`/sessions/${id}`);
            sessions.value = sessions.value.filter(s => s.id !== id);
            if (currentSession.value?.id === id) {
                currentSession.value = null;
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to delete session';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const joinSession = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/sessions/${id}/join`);
            if (response.data.data?.google_meet_link) {
                window.open(response.data.data.google_meet_link, '_blank');
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to join session';
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
        sessions,
        currentSession,
        loading,
        error,
        // Getters
        upcomingSessions,
        pastSessions,
        // Actions
        fetchSessions,
        fetchSession,
        createSession,
        updateSession,
        deleteSession,
        joinSession,
        clearError,
    };
});

