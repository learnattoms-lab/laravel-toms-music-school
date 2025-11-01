<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="mt-2 text-gray-600">Overview of platform statistics and activity</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.users?.total || 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">+{{ stats?.users?.new_this_period || 0 }} this period</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Courses</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.courses?.total || 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ stats?.courses?.published || 0 }} published</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Enrollments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.enrollments?.total || 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ stats?.enrollments?.active || 0 }} active</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-semibold text-gray-900">${{ formatCurrency(stats?.revenue?.total || 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">${{ formatCurrency(stats?.revenue?.this_month || 0) }} this month</p>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                    </template>
                    <div class="grid grid-cols-2 gap-4">
                        <router-link
                            to="/admin/users"
                            class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                        >
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Manage Users</p>
                                    <p class="text-sm text-gray-500">View all users</p>
                                </div>
                            </div>
                        </router-link>

                        <router-link
                            to="/admin/teachers"
                            class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                        >
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Manage Teachers</p>
                                    <p class="text-sm text-gray-500">View teachers</p>
                                </div>
                            </div>
                        </router-link>

                        <router-link
                            to="/admin/analytics"
                            class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                        >
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">View Analytics</p>
                                    <p class="text-sm text-gray-500">Platform insights</p>
                                </div>
                            </div>
                        </router-link>

                        <router-link
                            to="/courses"
                            class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors"
                        >
                            <div class="flex items-center space-x-3">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Manage Courses</p>
                                    <p class="text-sm text-gray-500">View all courses</p>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </AppCard>

                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
                    </template>
                    <div class="space-y-4">
                        <p class="text-gray-500 text-sm">Recent activity will be displayed here</p>
                        <!-- Activity feed can be added here -->
                    </div>
                </AppCard>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const { user, isAdmin } = useAuth();
const loading = ref(true);
const stats = ref(null);
const error = ref(null);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

const fetchDashboardData = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.get('/admin/dashboard');
        stats.value = response.data.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load dashboard data';
        console.error('Error fetching admin dashboard:', err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (isAdmin.value) {
        fetchDashboardData();
    }
});
</script>

<style scoped>
/* Admin dashboard specific styles */
</style>

