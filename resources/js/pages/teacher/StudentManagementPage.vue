<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Student Management</h1>
                <p class="mt-2 text-gray-600">View and manage your students</p>
            </div>

            <!-- Search -->
            <AppCard>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search students by name or email..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    @input="handleSearch"
                />
            </AppCard>

            <!-- Students by Course -->
            <div v-if="coursesWithStudents.length > 0" class="space-y-6">
                <AppCard
                    v-for="course in coursesWithStudents"
                    :key="course.id"
                >
                    <template #header>
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">{{ course.title }}</h2>
                            <AppBadge>{{ course.enrollments?.length || 0 }} students</AppBadge>
                        </div>
                    </template>
                    <div class="space-y-3">
                        <div
                            v-for="enrollment in course.enrollments"
                            :key="enrollment.id"
                            class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                                    {{ (enrollment.student?.first_name?.[0] || enrollment.student?.email?.[0] || 'S').toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ enrollment.student?.first_name }} {{ enrollment.student?.last_name }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ enrollment.student?.email }}</p>
                                </div>
                            </div>
                            <AppBadge :variant="enrollment.status === 'active' ? 'success' : 'warning'">
                                {{ enrollment.status }}
                            </AppBadge>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- All Students List -->
            <AppCard>
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">All Students</h2>
                </template>
                <AppLoading v-if="loading" message="Loading students..." />
                <div v-else-if="error" class="text-red-600">{{ error }}</div>
                <div v-else-if="allStudents.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No students found</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="student in allStudents" :key="student.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                                            {{ (student.first_name?.[0] || student.email?.[0] || 'S').toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ student.first_name }} {{ student.last_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ student.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ student.enrollments_count || 0 }} courses
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <AppBadge :variant="student.is_active ? 'success' : 'error'">
                                        {{ student.is_active ? 'Active' : 'Inactive' }}
                                    </AppBadge>
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
import { ref, onMounted, computed } from 'vue';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const { user, isTeacher } = useAuth();

const loading = ref(true);
const error = ref(null);
const courses = ref([]);
const searchQuery = ref('');

const coursesWithStudents = computed(() => {
    return courses.value.filter(course => (course.enrollments?.length || 0) > 0);
});

const allStudents = computed(() => {
    const studentMap = new Map();
    
    courses.value.forEach(course => {
        course.enrollments?.forEach(enrollment => {
            if (enrollment.student && !studentMap.has(enrollment.student.id)) {
                studentMap.set(enrollment.student.id, {
                    ...enrollment.student,
                    enrollments_count: courses.value.filter(c => 
                        c.enrollments?.some(e => e.student?.id === enrollment.student.id)
                    ).length,
                });
            }
        });
    });

    let students = Array.from(studentMap.values());

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        students = students.filter(student =>
            student.first_name?.toLowerCase().includes(query) ||
            student.last_name?.toLowerCase().includes(query) ||
            student.email?.toLowerCase().includes(query)
        );
    }

    return students;
});

const handleSearch = () => {
    // Search is handled by computed property
};

const fetchCourses = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.get('/courses', {
            params: {
                teacher_id: user.value?.id,
                with_enrollments: true,
            },
        });
        courses.value = response.data.data || [];
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load students';
        console.error('Error fetching courses:', err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (isTeacher.value) {
        fetchCourses();
    }
});
</script>

<style scoped>
/* Student management specific styles */
</style>

