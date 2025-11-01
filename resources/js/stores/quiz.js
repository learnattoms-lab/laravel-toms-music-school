/**
 * Quiz Store
 * Manages quiz data and operations
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/utils/api';

export const useQuizStore = defineStore('quiz', () => {
    // State
    const quizzes = ref([]);
    const currentQuiz = ref(null);
    const currentAttempt = ref(null);
    const attempts = ref([]);
    const loading = ref(false);
    const error = ref(null);

    // Actions
    const fetchQuizzes = async (filters = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/quizzes', { params: filters });
            quizzes.value = response.data.data || [];
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch quizzes';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchQuiz = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/quizzes/${id}`);
            currentQuiz.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch quiz';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const startQuizAttempt = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/quizzes/${id}/attempt`);
            currentAttempt.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to start quiz attempt';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const submitQuiz = async (id, answers) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/quizzes/${id}/submit`, { answers });
            currentAttempt.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to submit quiz';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchAttempts = async (quizId) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/quizzes/${quizId}/attempts`);
            attempts.value = response.data.data || [];
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch attempts';
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
        quizzes,
        currentQuiz,
        currentAttempt,
        attempts,
        loading,
        error,
        // Actions
        fetchQuizzes,
        fetchQuiz,
        startQuizAttempt,
        submitQuiz,
        fetchAttempts,
        clearError,
    };
});

