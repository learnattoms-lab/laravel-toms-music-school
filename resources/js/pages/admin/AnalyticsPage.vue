<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Analytics</h1>
                <p class="mt-2 text-gray-600">Platform insights and statistics</p>
            </div>

            <!-- Time Range Selector -->
            <AppCard>
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Time Range:</label>
                    <select
                        v-model="timeRange"
                        @change="fetchAnalytics"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    >
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
            </AppCard>

            <!-- Analytics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <AppCard>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ analytics?.users?.total || 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">+{{ analytics?.users?.new_this_period || 0 }} new</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-semibold text-gray-900">${{ formatCurrency(analytics?.revenue?.total || 0) }}</p>
                            <p class="text-xs text-gray-500 mt-1">${{ formatCurrency(analytics?.revenue?.this_period || 0) }} this period</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Courses</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ analytics?.courses?.total || 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ analytics?.courses?.published || 0 }} published</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Enrollments</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ analytics?.enrollments?.total || 0 }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ analytics?.enrollments?.active || 0 }} active</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- Charts Section (Placeholder) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">User Growth</h2>
                    </template>
                    <div class="h-64 flex items-center justify-center text-gray-500">
                        <p class="text-sm">Chart visualization can be added here (Chart.js)</p>
                    </div>
                </AppCard>

                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">Revenue Trend</h2>
                    </template>
                    <div class="h-64 flex items-center justify-center text-gray-500">
                        <p class="text-sm">Chart visualization can be added here (Chart.js)</p>
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

const { isAdmin } = useAuth();

const loading = ref(true);
const error = ref(null);
const analytics = ref(null);
const timeRange = ref('30');

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

const fetchAnalytics = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.get('/admin/analytics', {
            params: { range: timeRange.value },
        });
        analytics.value = response.data.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load analytics';
        console.error('Error fetching analytics:', err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (isAdmin.value) {
        fetchAnalytics();
    }
});
</script>

<style scoped>
/* Analytics specific styles */
</style>

