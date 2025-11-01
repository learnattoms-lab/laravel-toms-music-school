<template>
    <AppLayout>
        <AppLoading v-if="loading" message="Loading course..." :fullscreen="true" />
        
        <AppAlert
            v-if="error"
            variant="error"
            :title="error"
            :dismissible="true"
            @dismiss="error = null"
        />
        
        <div v-else-if="course" class="space-y-6">
            <!-- Course Header -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="aspect-video bg-gradient-to-br from-indigo-400 to-purple-500 relative">
                    <img
                        v-if="course.cover_file?.url"
                        :src="course.cover_file.url"
                        :alt="course.title"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="text-8xl text-white">🎵</span>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <AppBadge :variant="getLevelColor(course.level)" :text="course.level" />
                                <AppBadge variant="info" :text="course.instrument" />
                                <span v-if="course.status === 'published'" class="text-sm text-gray-600">
                                    Published {{ formatDate(course.published_at) }}
                                </span>
                            </div>
                            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                                {{ course.title }}
                            </h1>
                            <p class="text-lg text-gray-600 mb-6">
                                {{ course.description }}
                            </p>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ course.teacher?.display_name || 'Instructor' }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    {{ course.lessons_count || 0 }} Lessons
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Self-paced
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 sm:mt-0 sm:ml-8">
                            <AppCard class="min-w-[280px]">
                                <div class="text-center mb-6">
                                    <div class="text-4xl font-bold text-gray-900 mb-2">
                                        ${{ course.price_dollars || (course.price_cents / 100) }}
                                    </div>
                                    <p class="text-sm text-gray-600">One-time payment</p>
                                </div>
                                <div class="space-y-3">
                                    <AppButton
                                        v-if="!isEnrolled"
                                        class="w-full"
                                        @click="handleEnroll"
                                        :loading="enrolling"
                                    >
                                        {{ course.price_cents > 0 ? 'Enroll Now' : 'Enroll for Free' }}
                                    </AppButton>
                                    <AppButton
                                        v-else
                                        variant="outline"
                                        class="w-full"
                                        @click="$router.push(`/courses/${course.id}/lessons`)"
                                    >
                                        Continue Learning
                                    </AppButton>
                                    <AppButton
                                        variant="ghost"
                                        class="w-full"
                                        @click="toggleWishlist"
                                    >
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                    </AppButton>
                                </div>
                            </AppCard>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- What You'll Learn -->
                    <AppCard>
                        <template #header>
                            <h2 class="text-xl font-semibold text-gray-900">What You'll Learn</h2>
                        </template>
                        <ul v-if="course.learning_objectives?.length > 0" class="space-y-3">
                            <li
                                v-for="(objective, index) in course.learning_objectives"
                                :key="index"
                                class="flex items-start"
                            >
                                <svg class="h-5 w-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-700">{{ objective }}</span>
                            </li>
                        </ul>
                        <p v-else class="text-gray-600">
                            Master the fundamentals of {{ course.instrument }} through structured lessons and practice exercises.
                        </p>
                    </AppCard>

                    <!-- Course Curriculum -->
                    <AppCard>
                        <template #header>
                            <h2 class="text-xl font-semibold text-gray-900">Course Curriculum</h2>
                        </template>
                        <div v-if="lessons?.length > 0" class="space-y-4">
                            <div
                                v-for="(lesson, index) in lessons"
                                :key="lesson.id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start flex-1">
                                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-semibold mr-4">
                                            {{ index + 1 }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ lesson.title }}</h3>
                                            <p v-if="lesson.description" class="text-sm text-gray-600 mt-1">
                                                {{ lesson.description }}
                                            </p>
                                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                                <span v-if="lesson.duration_min">
                                                    {{ lesson.duration_min }} min
                                                </span>
                                                <span v-if="lesson.is_required" class="text-indigo-600 font-medium">
                                                    Required
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <AppButton
                                        v-if="isEnrolled"
                                        size="sm"
                                        variant="outline"
                                        @click="$router.push(`/courses/${course.id}/lessons/${lesson.id}`)"
                                    >
                                        View
                                    </AppButton>
                                    <AppBadge v-else variant="gray" text="Locked" />
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-600 text-center py-8">
                            Course curriculum coming soon...
                        </p>
                    </AppCard>

                    <!-- Instructor -->
                    <AppCard v-if="course.teacher">
                        <template #header>
                            <h2 class="text-xl font-semibold text-gray-900">About the Instructor</h2>
                        </template>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <img
                                    v-if="course.teacher.profile_picture"
                                    :src="course.teacher.profile_picture"
                                    :alt="course.teacher.display_name"
                                    class="h-16 w-16 rounded-full object-cover"
                                />
                                <div
                                    v-else
                                    class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xl"
                                >
                                    {{ (course.teacher.first_name?.[0] || course.teacher.email?.[0] || 'T').toUpperCase() }}
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ course.teacher.display_name }}
                                </h3>
                                <p v-if="course.teacher.teacher_bio" class="text-gray-600 mt-2">
                                    {{ course.teacher.teacher_bio }}
                                </p>
                                <div v-if="course.teacher.teacher_specialties?.length > 0" class="mt-4">
                                    <p class="text-sm font-medium text-gray-900 mb-2">Specialties:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <AppBadge
                                            v-for="specialty in course.teacher.teacher_specialties"
                                            :key="specialty"
                                            variant="info"
                                            :text="specialty"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </AppCard>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Info -->
                    <AppCard>
                        <template #header>
                            <h3 class="font-semibold text-gray-900">Course Includes</h3>
                        </template>
                        <ul class="space-y-3">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ course.lessons_count || 0 }} Video Lessons
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Downloadable Resources
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Assignments & Quizzes
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Certificate of Completion
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Lifetime Access
                            </li>
                        </ul>
                    </AppCard>

                    <!-- Reviews -->
                    <AppCard>
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">Student Reviews</h3>
                                <span class="text-sm text-gray-600">4.5 ⭐</span>
                            </div>
                        </template>
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-600">No reviews yet</p>
                            <p class="text-xs text-gray-500 mt-1">Be the first to review this course!</p>
                        </div>
                    </AppCard>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <AppCard v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Course not found</h3>
            <p class="mt-2 text-gray-600">The course you're looking for doesn't exist or has been removed.</p>
            <router-link to="/courses" class="mt-6 inline-block">
                <AppButton>Browse Courses</AppButton>
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
const course = ref(null);
const lessons = ref([]);
const isEnrolled = ref(false);
const enrolling = ref(false);
const inWishlist = ref(false);
const error = ref(null);

const getLevelColor = (level) => {
    const colors = {
        beginner: 'info',
        intermediate: 'warning',
        advanced: 'success',
    };
    return colors[level] || 'primary';
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
};

const fetchCourse = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await api.get(`/courses/${route.params.id}`);
        course.value = response.data.data;
        isEnrolled.value = response.data.is_enrolled || false;
        lessons.value = course.value.lessons || [];
    } catch (err) {
        console.error('Failed to fetch course:', err);
        error.value = err.response?.data?.message || 'Failed to load course';
    } finally {
        loading.value = false;
    }
};

const handleEnroll = async () => {
    if (!user.value) {
        router.push({ name: 'login', query: { redirect: route.fullPath } });
        return;
    }

    enrolling.value = true;
    try {
        if (course.value.price_cents > 0) {
            // Redirect to checkout
            router.push(`/checkout/course/${course.value.id}`);
        } else {
            // Free course - enroll directly
            await api.post(`/courses/${course.value.id}/enroll`);
            isEnrolled.value = true;
            // TODO: Show success notification
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to enroll';
    } finally {
        enrolling.value = false;
    }
};

const toggleWishlist = () => {
    // TODO: Implement wishlist functionality
    inWishlist.value = !inWishlist.value;
};

onMounted(() => {
    fetchCourse();
});
</script>

<style scoped>
/* Course detail page styles */
</style>

