/**
 * Course Store
 * Manages course data and operations
 */

import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '@/utils/api';

export const useCourseStore = defineStore('course', () => {
    // State
    const courses = ref([]);
    const currentCourse = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // Getters
    const publishedCourses = computed(() => {
        return courses.value.filter(course => course.status === 'published');
    });

    const myCourses = computed(() => {
        return courses.value.filter(course => course.is_enrolled || course.is_teacher);
    });

    // Actions
    const fetchCourses = async (filters = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get('/courses', { params: filters });
            courses.value = response.data.data || [];
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch courses';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const fetchCourse = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.get(`/courses/${id}`);
            currentCourse.value = response.data.data;
            return response.data.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to fetch course';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const createCourse = async (courseData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post('/courses', courseData);
            const newCourse = response.data.data;
            courses.value.push(newCourse);
            return newCourse;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to create course';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateCourse = async (id, courseData) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.put(`/courses/${id}`, courseData);
            const updatedCourse = response.data.data;
            const index = courses.value.findIndex(c => c.id === id);
            if (index !== -1) {
                courses.value[index] = updatedCourse;
            }
            if (currentCourse.value?.id === id) {
                currentCourse.value = updatedCourse;
            }
            return updatedCourse;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to update course';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteCourse = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            await api.delete(`/courses/${id}`);
            courses.value = courses.value.filter(c => c.id !== id);
            if (currentCourse.value?.id === id) {
                currentCourse.value = null;
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to delete course';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const enrollInCourse = async (id) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post(`/courses/${id}/enroll`);
            if (currentCourse.value?.id === id) {
                currentCourse.value.is_enrolled = true;
            }
            return response.data;
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to enroll in course';
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
        courses,
        currentCourse,
        loading,
        error,
        // Getters
        publishedCourses,
        myCourses,
        // Actions
        fetchCourses,
        fetchCourse,
        createCourse,
        updateCourse,
        deleteCourse,
        enrollInCourse,
        clearError,
    };
});

