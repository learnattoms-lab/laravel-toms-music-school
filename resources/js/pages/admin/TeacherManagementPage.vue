<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teacher Management</h1>
                <p class="mt-2 text-gray-600">Manage all platform teachers</p>
            </div>

            <!-- Teachers List -->
            <AppCard>
                <AppLoading v-if="loading" message="Loading teachers..." />
                <div v-else-if="error" class="text-red-600">{{ error }}</div>
                <div v-else-if="teachers.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No teachers found</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="teacher in teachers" :key="teacher.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                                            {{ (teacher.first_name?.[0] || teacher.email?.[0] || 'T').toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ teacher.first_name }} {{ teacher.last_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ teacher.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ teacher.courses_count || 0 }} courses
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <AppBadge :variant="teacher.is_active ? 'success' : 'error'">
                                        {{ teacher.is_active ? 'Active' : 'Inactive' }}
                                    </AppBadge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        @click="viewTeacher(teacher.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const { isAdmin } = useAuth();

const loading = ref(true);
const error = ref(null);
const teachers = ref([]);

const fetchTeachers = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.get('/admin/teachers');
        teachers.value = response.data.data || [];
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load teachers';
        console.error('Error fetching teachers:', err);
    } finally {
        loading.value = false;
    }
};

const viewTeacher = (teacherId) => {
    router.push({ name: 'teacher-detail', params: { id: teacherId } });
};

onMounted(() => {
    if (isAdmin.value) {
        fetchTeachers();
    }
});
</script>

<style scoped>
/* Teacher management specific styles */
</style>

