<template>
    <AppLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Browse Courses</h1>
                    <p class="mt-2 text-gray-600">
                        Discover amazing music courses taught by expert instructors
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <AppButton v-if="isTeacher" @click="$router.push('/teacher/courses/create')">
                        Create Course
                    </AppButton>
                </div>
            </div>

            <!-- Filters and Search -->
            <AppCard>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="Search courses..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base"
                                @input="debouncedSearch"
                            />
                        </div>
                    </div>

                    <!-- Instrument Filter -->
                    <AppSelect
                        v-model="filters.instrument"
                        :options="instrumentOptions"
                        placeholder="All Instruments"
                        @change="applyFilters"
                    />

                    <!-- Level Filter -->
                    <AppSelect
                        v-model="filters.level"
                        :options="levelOptions"
                        placeholder="All Levels"
                        @change="applyFilters"
                    />
                </div>
            </AppCard>

            <!-- Courses Grid -->
            <AppLoading v-if="loading" message="Loading courses..." />
            
            <div v-else-if="courses?.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <AppCard
                    v-for="course in courses"
                    :key="course.id"
                    class="hoverable cursor-pointer"
                    @click="$router.push(`/courses/${course.id}`)"
                >
                    <div class="aspect-video bg-gray-200 rounded-lg mb-4 overflow-hidden">
                        <img
                            v-if="course.cover_file?.url"
                            :src="course.cover_file.url"
                            :alt="course.title"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-400 to-purple-500">
                            <span class="text-4xl text-white">🎵</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <AppBadge :variant="getLevelColor(course.level)" :text="course.level" />
                            <span class="text-lg font-bold text-gray-900">
                                ${{ course.price_dollars || (course.price_cents / 100) }}
                            </span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ course.title }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ course.description }}
                        </p>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ course.teacher?.display_name || 'Instructor' }}
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ course.lessons_count || 0 }} lessons
                            </div>
                        </div>
                    </div>
                </AppCard>
            </div>

            <!-- Empty State -->
            <AppCard v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No courses found</h3>
                <p class="mt-2 text-gray-600">Try adjusting your filters</p>
            </AppCard>

            <!-- Pagination -->
            <div v-if="pagination && pagination.total > pagination.per_page" class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} courses
                </div>
                <div class="flex space-x-2">
                    <AppButton
                        variant="outline"
                        size="sm"
                        :disabled="pagination.current_page === 1"
                        @click="changePage(pagination.current_page - 1)"
                    >
                        Previous
                    </AppButton>
                    <AppButton
                        variant="outline"
                        size="sm"
                        :disabled="pagination.current_page === pagination.last_page"
                        @click="changePage(pagination.current_page + 1)"
                    >
                        Next
                    </AppButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppSelect from '@/components/ui/AppSelect.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const router = useRouter();
const route = useRoute();
const { isTeacher } = useAuth();

const loading = ref(true);
const courses = ref([]);
const pagination = ref(null);

const filters = ref({
    search: '',
    instrument: '',
    level: '',
    status: 'published',
});

const instrumentOptions = [
    { value: '', label: 'All Instruments' },
    { value: 'piano', label: 'Piano' },
    { value: 'guitar', label: 'Guitar' },
    { value: 'violin', label: 'Violin' },
    { value: 'drums', label: 'Drums' },
    { value: 'voice', label: 'Voice' },
    { value: 'bass', label: 'Bass' },
    { value: 'saxophone', label: 'Saxophone' },
    { value: 'trumpet', label: 'Trumpet' },
];

const levelOptions = [
    { value: '', label: 'All Levels' },
    { value: 'beginner', label: 'Beginner' },
    { value: 'intermediate', label: 'Intermediate' },
    { value: 'advanced', label: 'Advanced' },
];

const getLevelColor = (level) => {
    const colors = {
        beginner: 'info',
        intermediate: 'warning',
        advanced: 'success',
    };
    return colors[level] || 'primary';
};

let searchTimeout;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const applyFilters = () => {
    fetchCourses();
};

const changePage = (page) => {
    fetchCourses(page);
};

const fetchCourses = async (page = 1) => {
    loading.value = true;
    try {
        const params = {
            page,
            per_page: 12,
            ...filters.value,
        };
        // Remove empty filters
        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key];
            }
        });

        const response = await api.get('/courses', { params });
        courses.value = response.data.data || [];
        pagination.value = response.data.meta || null;
    } catch (error) {
        console.error('Failed to fetch courses:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    // Initialize filters from query params
    if (route.query.instrument) filters.value.instrument = route.query.instrument;
    if (route.query.level) filters.value.level = route.query.level;
    if (route.query.search) filters.value.search = route.query.search;
    
    fetchCourses();
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

