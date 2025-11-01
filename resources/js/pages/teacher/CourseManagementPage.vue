<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Course Management</h1>
                    <p class="mt-2 text-gray-600">Manage your courses</p>
                </div>
                <router-link to="/teacher/courses/create">
                    <AppButton>Create New Course</AppButton>
                </router-link>
            </div>

            <!-- Courses List -->
            <AppCard>
                <AppLoading v-if="loading" message="Loading courses..." />
                <div v-else-if="error" class="text-red-600">{{ error }}</div>
                <div v-else-if="courses.length === 0" class="text-center py-12">
                    <p class="text-gray-500 mb-4">You haven't created any courses yet</p>
                    <router-link to="/teacher/courses/create">
                        <AppButton>Create Your First Course</AppButton>
                    </router-link>
                </div>
                <div v-else class="space-y-4">
                    <div
                        v-for="course in courses"
                        :key="course.id"
                        class="border border-gray-200 rounded-lg p-6 hover:border-indigo-300 transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ course.title }}</h3>
                                    <AppBadge :variant="course.status === 'published' ? 'success' : 'warning'">
                                        {{ course.status }}
                                    </AppBadge>
                                </div>
                                <p class="text-gray-600 mt-2">{{ course.description }}</p>
                                <div class="mt-4 flex items-center space-x-6 text-sm text-gray-500">
                                    <span>{{ course.enrollments_count || 0 }} students</span>
                                    <span>{{ course.lessons_count || 0 }} lessons</span>
                                    <span v-if="course.price" class="font-semibold text-gray-900">${{ course.price }}</span>
                                    <span v-else class="text-green-600 font-semibold">Free</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <AppButton
                                    variant="outline"
                                    size="sm"
                                    @click="editCourse(course.id)"
                                >
                                    Edit
                                </AppButton>
                                <AppButton
                                    variant="outline"
                                    size="sm"
                                    @click="viewCourse(course.id)"
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
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const { user, isTeacher } = useAuth();

const loading = ref(true);
const error = ref(null);
const courses = ref([]);

const fetchCourses = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.get('/courses', {
            params: { teacher_id: user.value?.id },
        });
        courses.value = response.data.data || [];
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load courses';
        console.error('Error fetching courses:', err);
    } finally {
        loading.value = false;
    }
};

const editCourse = (courseId) => {
    router.push({ name: 'teacher-course-edit', params: { id: courseId } });
};

const viewCourse = (courseId) => {
    router.push({ name: 'course-detail', params: { id: courseId } });
};

onMounted(() => {
    if (isTeacher.value) {
        fetchCourses();
    }
});
</script>

<style scoped>
/* Course management specific styles */
</style>

