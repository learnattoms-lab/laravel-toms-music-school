/**
 * Assignment Store
 * Manages assignment data and operations
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/utils/api';

export const useAssignmentStore = defineStore('assignment', () => {
    // State
    const assignments = ref([]);
    const currentAssignment = ref(null);
    const submissions = ref([]);
    const loading = ref(false);
    const error = ref(null);

    // Actions
    const fetchAssignments = async (filters = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/assignments', { params: filters });
            assignments.value = response.data.data || [];
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch assignments';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchAssignment = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/assignments/${id}`);
            currentAssignment.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch assignment';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const submitAssignment = async (id, submissionData) => {
        loading.value = true;
        error.value = null;

        try {
            const formData = new FormData();
            Object.keys(submissionData).forEach(key => {
                if (submissionData[key] !== null && submissionData[key] !== undefined) {
                    if (key === 'file' && submissionData[key] instanceof File) {
                        formData.append('file', submissionData[key]);
                    } else {
                        formData.append(key, submissionData[key]);
                    }
                }
            });

            const response = await api.post(`/assignments/${id}/submit`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to submit assignment';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const gradeAssignment = async (submissionId, gradeData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put(`/assignments/submissions/${submissionId}/grade`, gradeData);
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to grade assignment';
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
        assignments,
        currentAssignment,
        submissions,
        loading,
        error,
        // Actions
        fetchAssignments,
        fetchAssignment,
        submitAssignment,
        gradeAssignment,
        clearError,
    };
});

