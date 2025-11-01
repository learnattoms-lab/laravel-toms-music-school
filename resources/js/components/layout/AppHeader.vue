<template>
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <nav class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <router-link 
                        to="/" 
                        class="flex items-center space-x-2 text-xl sm:text-2xl font-bold text-indigo-600 hover:text-indigo-700"
                    >
                        <span>🎵</span>
                        <span class="hidden sm:inline">Tom's Music School</span>
                        <span class="sm:hidden">TMS</span>
                    </router-link>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <router-link
                        v-for="item in navigationItems"
                        :key="item.path"
                        :to="item.path"
                        class="text-gray-700 hover:text-indigo-600 font-medium transition-colors"
                        :class="{ 'text-indigo-600': $route.path === item.path }"
                    >
                        {{ item.label }}
                    </router-link>
                </div>

                <!-- Right Side: User Menu or Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button -->
                    <button
                        @click="toggleMobileMenu"
                        class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100"
                        aria-label="Toggle menu"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                v-if="!mobileMenuOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                    <!-- Authenticated User Menu -->
                    <div v-if="isAuthenticated" class="relative">
                        <button
                            @click="toggleUserMenu"
                            class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100 focus:outline-none"
                        >
                            <img
                                v-if="user?.profile_picture"
                                :src="user.profile_picture"
                                :alt="user.display_name || user.email"
                                class="h-8 w-8 rounded-full object-cover"
                            />
                            <div
                                v-else
                                class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium"
                            >
                                {{ (user?.first_name?.[0] || user?.email?.[0] || 'U').toUpperCase() }}
                            </div>
                            <span class="hidden md:block text-gray-700 font-medium">
                                {{ user?.display_name || user?.email }}
                            </span>
                            <svg
                                class="h-4 w-4 text-gray-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-200 z-50"
                        >
                            <router-link
                                to="/dashboard"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Dashboard
                            </router-link>
                            <router-link
                                to="/profile"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Profile
                            </router-link>
                            <router-link
                                v-if="isTeacher"
                                to="/teacher"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Teacher Dashboard
                            </router-link>
                            <router-link
                                v-if="isAdmin"
                                to="/admin"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Admin Dashboard
                            </router-link>
                            <hr class="my-1 border-gray-200" />
                            <button
                                @click="handleLogout"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            >
                                Logout
                            </button>
                        </div>
                    </div>

                    <!-- Guest Auth Buttons -->
                    <div v-else class="flex items-center space-x-2 sm:space-x-3">
                        <router-link
                            to="/login"
                            class="text-gray-700 hover:text-indigo-600 font-medium text-sm sm:text-base"
                        >
                            <span class="hidden sm:inline">Login</span>
                            <span class="sm:hidden">Login</span>
                        </router-link>
                        <router-link
                            to="/register"
                            class="bg-indigo-600 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-md hover:bg-indigo-700 font-medium transition-colors text-sm sm:text-base"
                        >
                            <span class="hidden sm:inline">Sign Up</span>
                            <span class="sm:hidden">Sign Up</span>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div
                v-if="mobileMenuOpen"
                class="md:hidden border-t border-gray-200 py-4 space-y-2"
            >
                <router-link
                    v-for="item in navigationItems"
                    :key="item.path"
                    :to="item.path"
                    @click="mobileMenuOpen = false"
                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md"
                    :class="{ 'bg-indigo-50 text-indigo-600': $route.path === item.path }"
                >
                    {{ item.label }}
                </router-link>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const router = useRouter();
const { user, isAuthenticated, isAdmin, isTeacher, logout } = useAuth();

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);

const navigationItems = computed(() => {
    const items = [
        { path: '/', label: 'Home' },
        { path: '/courses', label: 'Courses' },
    ];

    // Add test page in development
    if (import.meta.env.DEV) {
        items.push({ path: '/test-components', label: 'Test' });
    }

    if (isAuthenticated.value) {
        items.push({ path: '/dashboard', label: 'Dashboard' });
    }

    return items;
});

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
    userMenuOpen.value = false;
};

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
    mobileMenuOpen.value = false;
};

const handleLogout = async () => {
    userMenuOpen.value = false;
    await logout();
};

// Close menus when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        userMenuOpen.value = false;
        mobileMenuOpen.value = false;
    }
};

// Close menus on route change
router.afterEach(() => {
    mobileMenuOpen.value = false;
    userMenuOpen.value = false;
});
</script>

<style scoped>
/* Header-specific styles */
</style>

