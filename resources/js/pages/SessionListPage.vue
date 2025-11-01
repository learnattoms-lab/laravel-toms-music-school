<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Sessions</h1>
                    <p class="mt-2 text-gray-600">
                        View and manage your upcoming music lessons
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <AppButton
                        v-if="isTeacher"
                        @click="$router.push('/teacher/sessions/create')"
                    >
                        Create Session
                    </AppButton>
                </div>
            </div>

            <!-- Filters -->
            <AppCard>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <AppSelect
                        v-model="filters.status"
                        :options="statusOptions"
                        placeholder="All Status"
                        @change="applyFilters"
                    />
                    <AppSelect
                        v-model="filters.filter"
                        :options="filterOptions"
                        placeholder="Filter by..."
                        @change="applyFilters"
                    />
                </div>
            </AppCard>

            <!-- Sessions List -->
            <AppLoading v-if="loading" message="Loading sessions..." />
            
            <div v-else-if="sessions?.length > 0" class="space-y-4">
                <AppCard
                    v-for="session in sessions"
                    :key="session.id"
                    class="hoverable"
                    @click="$router.push(`/sessions/${session.id}`)"
                >
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ session.session_title || 'Music Lesson Session' }}
                                </h3>
                                <AppBadge
                                    :variant="getStatusColor(session.status)"
                                    :text="session.status"
                                />
                            </div>
                            <p v-if="session.course" class="text-sm text-gray-600 mb-3">
                                {{ session.course.title }} • {{ session.course.instrument }}
                            </p>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
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
                                    {{ formatTime(session.start_at_utc) }} - {{ formatTime(session.end_at_utc) }}
                                </div>
                                <div v-if="session.max_students" class="flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ session.students?.length || 0 }} / {{ session.max_students }} students
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 sm:ml-6 flex flex-col sm:flex-row gap-2">
                            <AppButton
                                v-if="session.join_url || session.google_meet_link"
                                size="sm"
                                @click.stop="joinSession(session)"
                            >
                                Join Session
                            </AppButton>
                            <AppButton
                                size="sm"
                                variant="outline"
                                @click.stop="$router.push(`/sessions/${session.id}`)"
                            >
                                View Details
                            </AppButton>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- Empty State -->
            <AppCard v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No sessions found</h3>
                <p class="mt-2 text-gray-600">Try adjusting your filters</p>
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
import AppSelect from '@/components/ui/AppSelect.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const { isTeacher } = useAuth();

const loading = ref(true);
const sessions = ref([]);

const filters = ref({
    status: '',
    filter: 'upcoming',
});

const statusOptions = [
    { value: '', label: 'All Status' },
    { value: 'scheduled', label: 'Scheduled' },
    { value: 'ongoing', label: 'Ongoing' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const filterOptions = [
    { value: 'upcoming', label: 'Upcoming' },
    { value: 'past', label: 'Past' },
    { value: 'all', label: 'All Sessions' },
];

const getStatusColor = (status) => {
    const colors = {
        scheduled: 'info',
        ongoing: 'warning',
        completed: 'success',
        cancelled: 'error',
    };
    return colors[status] || 'gray';
};

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

const applyFilters = () => {
    fetchSessions();
};

const fetchSessions = async () => {
    loading.value = true;
    try {
        const params = { ...filters.value };
        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key];
            }
        });

        const response = await api.get('/sessions', { params });
        sessions.value = response.data.data || [];
    } catch (error) {
        console.error('Failed to fetch sessions:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchSessions();
});
</script>

<style scoped>
/* Session list page styles */
</style>

