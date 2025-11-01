<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Welcome back, {{ user?.first_name || user?.email }}!
                </h1>
                <p class="mt-2 text-gray-600">Your teaching dashboard</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">My Courses</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.total_courses || 0 }}</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.total_students || 0 }}</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Upcoming Sessions</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.upcoming_sessions || 0 }}</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending Assignments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.pending_assignments || 0 }}</p>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">My Courses</h2>
                    </template>
                    <AppLoading v-if="loading" message="Loading courses..." />
                    <div v-else-if="courses.length === 0" class="text-center py-12">
                        <p class="text-gray-500 mb-4">You haven't created any courses yet</p>
                        <router-link to="/teacher/courses/create">
                            <AppButton>Create Your First Course</AppButton>
                        </router-link>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="course in courses.slice(0, 5)"
                            :key="course.id"
                            class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors cursor-pointer"
                            @click="viewCourse(course.id)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ course.title }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ course.enrollments_count || 0 }} students enrolled</p>
                                </div>
                                <AppBadge :variant="course.status === 'published' ? 'success' : 'warning'">
                                    {{ course.status }}
                                </AppBadge>
                            </div>
                        </div>
                        <router-link to="/teacher/courses" class="block text-center text-indigo-600 hover:text-indigo-700 font-medium">
                            View All Courses →
                        </router-link>
                    </div>
                </AppCard>

                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Sessions</h2>
                    </template>
                    <AppLoading v-if="loading" message="Loading sessions..." />
                    <div v-else-if="sessions.length === 0" class="text-center py-12">
                        <p class="text-gray-500 mb-4">No upcoming sessions</p>
                        <router-link to="/teacher/sessions/create">
                            <AppButton>Schedule a Session</AppButton>
                        </router-link>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="session in sessions.slice(0, 5)"
                            :key="session.id"
                            class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors cursor-pointer"
                            @click="viewSession(session.id)"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ session.title || 'Session' }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ formatDateTime(session.start_time) }}</p>
                                </div>
                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        <router-link to="/teacher/sessions" class="block text-center text-indigo-600 hover:text-indigo-700 font-medium">
                            View All Sessions →
                        </router-link>
                    </div>
                </AppCard>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const { user, isTeacher } = useAuth();

const loading = ref(true);
const stats = ref(null);
const courses = ref([]);
const sessions = ref([]);
const error = ref(null);

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const fetchDashboardData = async () => {
    loading.value = true;
    error.value = null;

    try {
        // Fetch teacher's courses
        const coursesResponse = await api.get('/courses', {
            params: { teacher_id: user.value?.id, per_page: 10 },
        });
        courses.value = coursesResponse.data.data || [];

        // Fetch upcoming sessions
        const sessionsResponse = await api.get('/sessions', {
            params: { teacher_id: user.value?.id, upcoming: true, per_page: 10 },
        });
        sessions.value = sessionsResponse.data.data || [];

        // Calculate stats
        stats.value = {
            total_courses: courses.value.length,
            total_students: courses.value.reduce((sum, course) => sum + (course.enrollments_count || 0), 0),
            upcoming_sessions: sessions.value.length,
            pending_assignments: 0, // Can be calculated from assignments
        };
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load dashboard data';
        console.error('Error fetching teacher dashboard:', err);
    } finally {
        loading.value = false;
    }
};

const viewCourse = (courseId) => {
    router.push({ name: 'teacher-course-detail', params: { id: courseId } });
};

const viewSession = (sessionId) => {
    router.push({ name: 'teacher-session-detail', params: { id: sessionId } });
};

onMounted(() => {
    if (isTeacher.value) {
        fetchDashboardData();
    }
});
</script>

<style scoped>
/* Teacher dashboard specific styles */
</style>

