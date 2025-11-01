<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Welcome back, {{ user?.first_name || user?.email }}!
                </h1>
                <p class="mt-2 text-gray-600">
                    Here's an overview of your learning journey
                </p>
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
                            <p class="text-sm font-medium text-gray-600">Enrolled Courses</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.total_courses || 0 }}</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Completed Lessons</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.completed_lessons || 0 }}</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Practice Hours</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.practice_hours || 0 }}</p>
                        </div>
                    </div>
                </AppCard>

                <AppCard>
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Level</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ stats?.level || 1 }}</p>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Enrolled Courses -->
                    <AppCard>
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">My Courses</h2>
                                <router-link
                                    to="/courses"
                                    class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                                >
                                    View All
                                </router-link>
                            </div>
                        </template>

                        <AppLoading v-if="loading" message="Loading courses..." />
                        <div v-else-if="enrollments?.length > 0" class="space-y-4">
                            <div
                                v-for="enrollment in enrollments"
                                :key="enrollment.id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ enrollment.course?.title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ enrollment.course?.instrument }} • {{ enrollment.course?.level }}
                                        </p>
                                        <div class="mt-3 flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2 mr-4">
                                                <div
                                                    class="bg-indigo-600 h-2 rounded-full transition-all"
                                                    :style="{ width: `${enrollment.progress_pct || 0}%` }"
                                                />
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ enrollment.progress_pct || 0 }}%
                                            </span>
                                        </div>
                                    </div>
                                    <router-link
                                        :to="`/courses/${enrollment.course_id}`"
                                        class="ml-4"
                                    >
                                        <AppButton size="sm" variant="outline">Continue</AppButton>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="mt-4 text-gray-600">You haven't enrolled in any courses yet</p>
                            <router-link to="/courses" class="mt-4 inline-block">
                                <AppButton>Browse Courses</AppButton>
                            </router-link>
                        </div>
                    </AppCard>

                    <!-- Upcoming Sessions -->
                    <AppCard>
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">Upcoming Sessions</h2>
                                <router-link
                                    to="/dashboard/sessions"
                                    class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
                                >
                                    View All
                                </router-link>
                            </div>
                        </template>

                        <div v-if="upcomingSessions?.length > 0" class="space-y-4">
                            <div
                                v-for="session in upcomingSessions"
                                :key="session.id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ session.session_title || 'Music Lesson' }}
                                        </h3>
                                        <div class="mt-2 flex flex-wrap gap-4 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ formatDate(session.start_at_utc) }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ formatTime(session.start_at_utc) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <AppButton
                                            v-if="session.join_url || session.google_meet_link"
                                            size="sm"
                                            @click="joinSession(session)"
                                        >
                                            Join
                                        </AppButton>
                                        <router-link :to="`/sessions/${session.id}`">
                                            <AppButton size="sm" variant="outline">Details</AppButton>
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-gray-600">No upcoming sessions</p>
                        </div>
                    </AppCard>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Progress Overview -->
                    <AppCard>
                        <template #header>
                            <h2 class="text-xl font-semibold text-gray-900">Progress Overview</h2>
                        </template>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Overall Progress</span>
                                    <span class="font-medium text-gray-900">
                                        {{ overallProgress }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="bg-indigo-600 h-2 rounded-full transition-all"
                                        :style="{ width: `${overallProgress}%` }"
                                    />
                                </div>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Experience Points</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ stats?.experience_points || 0 }} XP
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Next Level</span>
                                    <span class="text-sm font-medium text-indigo-600">
                                        {{ nextLevelXP }} XP needed
                                    </span>
                                </div>
                            </div>
                        </div>
                    </AppCard>

                    <!-- Recent Assignments -->
                    <AppCard>
                        <template #header>
                            <h2 class="text-xl font-semibold text-gray-900">Recent Assignments</h2>
                        </template>
                        <div v-if="recentAssignments?.length > 0" class="space-y-3">
                            <div
                                v-for="assignment in recentAssignments"
                                :key="assignment.id"
                                class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <h4 class="font-medium text-gray-900 text-sm">
                                    {{ assignment.title }}
                                </h4>
                                <p class="text-xs text-gray-600 mt-1">
                                    Due: {{ formatDate(assignment.due_at) }}
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-600 text-center py-4">
                            No recent assignments
                        </p>
                    </AppCard>

                    <!-- Quick Actions -->
                    <AppCard>
                        <template #header>
                            <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                        </template>
                        <div class="space-y-2">
                            <router-link to="/courses">
                                <AppButton variant="outline" class="w-full justify-start">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Browse Courses
                                </AppButton>
                            </router-link>
                            <router-link to="/dashboard/progress">
                                <AppButton variant="outline" class="w-full justify-start">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    View Progress
                                </AppButton>
                            </router-link>
                            <router-link to="/dashboard/achievements">
                                <AppButton variant="outline" class="w-full justify-start">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    Achievements
                                </AppButton>
                            </router-link>
                        </div>
                    </AppCard>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const { user } = useAuth();

const loading = ref(true);
const stats = ref(null);
const enrollments = ref([]);
const upcomingSessions = ref([]);
const recentAssignments = ref([]);

const overallProgress = computed(() => {
    if (!enrollments.value || enrollments.value.length === 0) return 0;
    const total = enrollments.value.reduce((sum, e) => sum + (e.progress_pct || 0), 0);
    return Math.round(total / enrollments.value.length);
});

const nextLevelXP = computed(() => {
    const current = stats.value?.experience_points || 0;
    const nextLevel = (stats.value?.level || 1) * 1000;
    return Math.max(0, nextLevel - current);
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatTime = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
};

const joinSession = (session) => {
    const joinUrl = session.join_url || session.google_meet_link;
    if (joinUrl) {
        window.open(joinUrl, '_blank');
    }
};

const fetchDashboardData = async () => {
    loading.value = true;
    try {
        const response = await api.get('/user/dashboard');
        const data = response.data;
        
        stats.value = data.stats;
        enrollments.value = data.enrollments || [];
        upcomingSessions.value = data.upcoming_sessions || [];
        recentAssignments.value = data.recent_assignments || [];
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (user.value) {
        fetchDashboardData();
    }
});
</script>

<style scoped>
/* Dashboard page styles */
</style>

