<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Session Management</h1>
                    <p class="mt-2 text-gray-600">Manage your teaching sessions</p>
                </div>
                <router-link to="/teacher/sessions/create">
                    <AppButton>Schedule New Session</AppButton>
                </router-link>
            </div>

            <!-- Filters -->
            <AppCard>
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Filter:</label>
                    <select
                        v-model="filter"
                        @change="fetchSessions"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    >
                        <option value="all">All Sessions</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="past">Past</option>
                    </select>
                </div>
            </AppCard>

            <!-- Sessions List -->
            <AppCard>
                <AppLoading v-if="loading" message="Loading sessions..." />
                <div v-else-if="error" class="text-red-600">{{ error }}</div>
                <div v-else-if="sessions.length === 0" class="text-center py-12">
                    <p class="text-gray-500 mb-4">No sessions found</p>
                    <router-link to="/teacher/sessions/create">
                        <AppButton>Schedule Your First Session</AppButton>
                    </router-link>
                </div>
                <div v-else class="space-y-4">
                    <div
                        v-for="session in sessions"
                        :key="session.id"
                        class="border border-gray-200 rounded-lg p-6 hover:border-indigo-300 transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900">{{ session.title || getSessionTitle(session) }}</h3>
                                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ formatDateTime(session.start_time) }}
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ session.students?.length || 0 }} students
                                    </span>
                                </div>
                                <div v-if="session.google_meet_link" class="mt-3">
                                    <a
                                        :href="session.google_meet_link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-indigo-600 hover:text-indigo-700 font-medium text-sm flex items-center"
                                    >
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Join Google Meet
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <AppButton
                                    variant="outline"
                                    size="sm"
                                    @click="editSession(session.id)"
                                >
                                    Edit
                                </AppButton>
                                <AppButton
                                    variant="outline"
                                    size="sm"
                                    @click="viewSession(session.id)"
                                >
                                    View
                                </AppButton>
                            </div>
                        </div>
                    </div>
                </div>
            </AppCard>
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
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const { user, isTeacher } = useAuth();

const loading = ref(true);
const error = ref(null);
const sessions = ref([]);
const filter = ref('all');

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const getSessionTitle = (session) => {
    if (session.course) {
        return `${session.course.title} - Session`;
    }
    return 'Teaching Session';
};

const fetchSessions = async () => {
    loading.value = true;
    error.value = null;

    try {
        const params = { teacher_id: user.value?.id };
        
        if (filter.value === 'upcoming') {
            params.upcoming = true;
        } else if (filter.value === 'past') {
            params.past = true;
        }

        const response = await api.get('/sessions', { params });
        sessions.value = response.data.data || [];
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load sessions';
        console.error('Error fetching sessions:', err);
    } finally {
        loading.value = false;
    }
};

const editSession = (sessionId) => {
    router.push({ name: 'teacher-session-edit', params: { id: sessionId } });
};

const viewSession = (sessionId) => {
    router.push({ name: 'session-detail', params: { id: sessionId } });
};

onMounted(() => {
    if (isTeacher.value) {
        fetchSessions();
    }
});
</script>

<style scoped>
/* Session management specific styles */
</style>

