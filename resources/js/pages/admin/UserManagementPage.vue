<template>
    <AppLayout :show-sidebar="true">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
                    <p class="mt-2 text-gray-600">Manage all platform users</p>
                </div>
            </div>

            <!-- Filters -->
            <AppCard>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input
                            v-model="filters.search"
                            type="text"
                            placeholder="Search by name or email..."
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            @input="handleFilter"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select
                            v-model="filters.role"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            @change="handleFilter"
                        >
                            <option value="">All Roles</option>
                            <option value="ROLE_USER">Student</option>
                            <option value="ROLE_TEACHER">Teacher</option>
                            <option value="ROLE_ADMIN">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select
                            v-model="filters.is_active"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                            @change="handleFilter"
                        >
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <AppButton @click="handleFilter" class="w-full">Apply Filters</AppButton>
                    </div>
                </div>
            </AppCard>

            <!-- Users Table -->
            <AppCard>
                <AppLoading v-if="loading" message="Loading users..." />
                <div v-else-if="error" class="text-red-600">{{ error }}</div>
                <div v-else-if="users.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No users found</p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium">
                                            {{ (user.first_name?.[0] || user.email?.[0] || 'U').toUpperCase() }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ user.first_name }} {{ user.last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <AppBadge
                                        :variant="getRoleBadgeVariant(user.roles)"
                                    >
                                        {{ getRoleDisplayName(user.roles) }}
                                    </AppBadge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <AppBadge :variant="user.is_active ? 'success' : 'error'">
                                        {{ user.is_active ? 'Active' : 'Inactive' }}
                                    </AppBadge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        @click="viewUser(user.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="pagination && pagination.last_page > 1" class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing {{ pagination.per_page * (pagination.current_page - 1) + 1 }} to
                            {{ Math.min(pagination.per_page * pagination.current_page, pagination.total) }} of
                            {{ pagination.total }} results
                        </div>
                        <div class="flex space-x-2">
                            <AppButton
                                variant="outline"
                                :disabled="pagination.current_page === 1"
                                @click="changePage(pagination.current_page - 1)"
                            >
                                Previous
                            </AppButton>
                            <AppButton
                                variant="outline"
                                :disabled="pagination.current_page === pagination.last_page"
                                @click="changePage(pagination.current_page + 1)"
                            >
                                Next
                            </AppButton>
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
const { isAdmin } = useAuth();

const loading = ref(true);
const error = ref(null);
const users = ref([]);
const pagination = ref(null);
const filters = ref({
    search: '',
    role: '',
    is_active: '',
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getRoleDisplayName = (roles) => {
    if (!roles || !Array.isArray(roles) || roles.length === 0) return 'Student';
    if (roles.includes('ROLE_ADMIN')) return 'Admin';
    if (roles.includes('ROLE_TEACHER')) return 'Teacher';
    return 'Student';
};

const getRoleBadgeVariant = (roles) => {
    if (!roles || !Array.isArray(roles)) return 'info';
    if (roles.includes('ROLE_ADMIN')) return 'error';
    if (roles.includes('ROLE_TEACHER')) return 'warning';
    return 'info';
};

const fetchUsers = async () => {
    loading.value = true;
    error.value = null;

    try {
        const params = {
            page: pagination.value?.current_page || 1,
            per_page: 15,
            ...filters.value,
        };

        // Remove empty filters
        Object.keys(params).forEach(key => {
            if (params[key] === '' || params[key] === null) {
                delete params[key];
            }
        });

        const response = await api.get('/admin/users', { params });
        users.value = response.data.data;
        pagination.value = response.data.meta;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load users';
        console.error('Error fetching users:', err);
    } finally {
        loading.value = false;
    }
};

const handleFilter = () => {
    pagination.value = null;
    fetchUsers();
};

const changePage = (page) => {
    if (pagination.value) {
        pagination.value.current_page = page;
    }
    fetchUsers();
};

const viewUser = (userId) => {
    router.push({ name: 'user-detail', params: { id: userId } });
};

onMounted(() => {
    if (isAdmin.value) {
        fetchUsers();
    }
});
</script>

<style scoped>
/* User management specific styles */
</style>

