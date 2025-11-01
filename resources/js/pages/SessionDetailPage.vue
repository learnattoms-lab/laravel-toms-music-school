<template>
    <AppLayout :show-sidebar="true">
        <AppLoading v-if="loading" message="Loading session..." />
        
        <div v-else-if="session" class="space-y-6">
            <!-- Session Header -->
            <AppCard>
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <h1 class="text-3xl font-bold text-gray-900">
                                {{ session.session_title || 'Music Lesson Session' }}
                            </h1>
                            <AppBadge
                                :variant="getStatusColor(session.status)"
                                :text="session.status"
                            />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ formatDateTime(session.start_at_utc) }}
                            </div>
                            <div class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatDuration(session.start_at_utc, session.end_at_utc) }}
                            </div>
                            <div v-if="session.course" class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ session.course.title }}
                            </div>
                            <div v-if="session.max_students" class="flex items-center text-gray-600">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ session.students?.length || 0 }} / {{ session.max_students }} students
                            </div>
                        </div>
                        <p v-if="session.notes" class="text-gray-700 mt-4">
                            {{ session.notes }}
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6 flex flex-col gap-3">
                        <AppButton
                            v-if="session.join_url || session.google_meet_link"
                            class="w-full sm:w-auto"
                            @click="joinSession"
                        >
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Join Session
                        </AppButton>
                        <AppButton
                            v-if="isTutor"
                            variant="outline"
                            class="w-full sm:w-auto"
                            @click="$router.push(`/teacher/sessions/${session.id}/edit`)"
                        >
                            Edit Session
                        </AppButton>
                    </div>
                </div>
            </AppCard>

            <!-- Google Meet Link -->
            <AppCard v-if="session.google_meet_link || session.join_url">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Join Information</h2>
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Google Meet Link
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                :value="session.google_meet_link || session.join_url"
                                readonly
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm"
                            />
                            <AppButton
                                size="sm"
                                variant="outline"
                                @click="copyLink"
                            >
                                Copy
                            </AppButton>
                        </div>
                    </div>
                </div>
            </AppCard>

            <!-- Students -->
            <AppCard v-if="session.students && session.students.length > 0">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Enrolled Students</h2>
                </template>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="student in session.students"
                        :key="student.id"
                        class="flex items-center p-3 bg-gray-50 rounded-lg"
                    >
                        <div
                            class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium"
                        >
                            {{ (student.first_name?.[0] || student.email?.[0] || 'S').toUpperCase() }}
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">
                                {{ student.display_name || student.email }}
                            </p>
                            <p v-if="student.email" class="text-sm text-gray-600">{{ student.email }}</p>
                        </div>
                    </div>
                </div>
            </AppCard>

            <!-- Materials -->
            <AppCard v-if="session.materials_json && session.materials_json.length > 0">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Session Materials</h2>
                </template>
                <div class="space-y-3">
                    <div
                        v-for="(material, index) in session.materials_json"
                        :key="index"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-gray-900">{{ material.name || `Material ${index + 1}` }}</span>
                        </div>
                        <AppButton
                            size="sm"
                            variant="outline"
                            @click="downloadMaterial(material)"
                        >
                            Download
                        </AppButton>
                    </div>
                </div>
            </AppCard>
        </div>

        <!-- Error State -->
        <AppCard v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Session not found</h3>
            <p class="mt-2 text-gray-600">The session you're looking for doesn't exist or has been removed.</p>
            <router-link to="/sessions" class="mt-6 inline-block">
                <AppButton>View All Sessions</AppButton>
            </router-link>
        </AppCard>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';
import AppAlert from '@/components/ui/AppAlert.vue';

const route = useRoute();
const router = useRouter();
const { user } = useAuth();

const loading = ref(true);
const session = ref(null);
const error = ref(null);

const isTutor = computed(() => {
    return session.value && user.value && session.value.tutor_id === user.value.id;
});

const getStatusColor = (status) => {
    const colors = {
        scheduled: 'info',
        ongoing: 'warning',
        completed: 'success',
        cancelled: 'error',
    };
    return colors[status] || 'gray';
};

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const formatDuration = (startString, endString) => {
    if (!startString || !endString) return 'N/A';
    const start = new Date(startString);
    const end = new Date(endString);
    const diff = end - start;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) {
        return `${hours}h ${mins}m`;
    }
    return `${mins}min`;
};

const joinSession = () => {
    const joinUrl = session.value?.join_url || session.value?.google_meet_link;
    if (joinUrl) {
        window.open(joinUrl, '_blank');
    }
};

const copyLink = () => {
    const link = session.value?.google_meet_link || session.value?.join_url;
    if (link) {
        navigator.clipboard.writeText(link).then(() => {
            // TODO: Show toast notification
            alert('Link copied to clipboard!');
        });
    }
};

const downloadMaterial = (material) => {
    if (material.url) {
        window.open(material.url, '_blank');
    }
};

const fetchSession = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await api.get(`/sessions/${route.params.id}`);
        session.value = response.data.data;
    } catch (err) {
        console.error('Failed to fetch session:', err);
        error.value = err.response?.data?.message || 'Failed to load session';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchSession();
});
</script>

<style scoped>
/* Session detail page styles */
</style>

