<template>
    <aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white border-r border-gray-200 overflow-y-auto z-50 lg:z-40">
        <nav class="p-4 space-y-1">
            <!-- Dashboard -->
            <router-link
                to="/dashboard"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                :class="
                    $route.path === '/dashboard'
                        ? 'bg-indigo-100 text-indigo-700 font-medium'
                        : 'text-gray-700 hover:bg-gray-100'
                "
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                <span>Dashboard</span>
            </router-link>

            <!-- My Courses -->
            <router-link
                to="/dashboard/courses"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                :class="
                    $route.path.startsWith('/dashboard/courses')
                        ? 'bg-indigo-100 text-indigo-700 font-medium'
                        : 'text-gray-700 hover:bg-gray-100'
                "
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                    />
                </svg>
                <span>My Courses</span>
            </router-link>

            <!-- Upcoming Sessions -->
            <router-link
                to="/dashboard/sessions"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                :class="
                    $route.path.startsWith('/dashboard/sessions')
                        ? 'bg-indigo-100 text-indigo-700 font-medium'
                        : 'text-gray-700 hover:bg-gray-100'
                "
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>
                <span>Upcoming Sessions</span>
            </router-link>

            <!-- Assignments -->
            <router-link
                to="/dashboard/assignments"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                :class="
                    $route.path.startsWith('/dashboard/assignments')
                        ? 'bg-indigo-100 text-indigo-700 font-medium'
                        : 'text-gray-700 hover:bg-gray-100'
                "
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                <span>Assignments</span>
            </router-link>

            <!-- Progress -->
            <router-link
                to="/dashboard/progress"
                class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                :class="
                    $route.path.startsWith('/dashboard/progress')
                        ? 'bg-indigo-100 text-indigo-700 font-medium'
                        : 'text-gray-700 hover:bg-gray-100'
                "
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    />
                </svg>
                <span>Progress</span>
            </router-link>

            <!-- Teacher Section -->
            <div v-if="isTeacher" class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Teacher
                </p>

                <router-link
                    to="/teacher/dashboard"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                    :class="
                        $route.path.startsWith('/teacher')
                            ? 'bg-indigo-100 text-indigo-700 font-medium'
                            : 'text-gray-700 hover:bg-gray-100'
                    "
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                    <span>Teacher Dashboard</span>
                </router-link>

                <router-link
                    to="/teacher/courses"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                    :class="
                        $route.path.startsWith('/teacher/courses')
                            ? 'bg-indigo-100 text-indigo-700 font-medium'
                            : 'text-gray-700 hover:bg-gray-100'
                    "
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                        />
                    </svg>
                    <span>My Courses</span>
                </router-link>

                <router-link
                    to="/teacher/sessions"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                    :class="
                        $route.path.startsWith('/teacher/sessions')
                            ? 'bg-indigo-100 text-indigo-700 font-medium'
                            : 'text-gray-700 hover:bg-gray-100'
                    "
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span>Sessions</span>
                </router-link>
            </div>

            <!-- Admin Section -->
            <div v-if="isAdmin" class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Admin
                </p>

                <router-link
                    to="/admin/dashboard"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
                    :class="
                        $route.path.startsWith('/admin')
                            ? 'bg-indigo-100 text-indigo-700 font-medium'
                            : 'text-gray-700 hover:bg-gray-100'
                    "
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                        />
                    </svg>
                    <span>Admin Dashboard</span>
                </router-link>
            </div>
        </nav>
    </aside>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';

const { isTeacher, isAdmin } = useAuth();
</script>

<style scoped>
/* Sidebar-specific styles */
</style>

