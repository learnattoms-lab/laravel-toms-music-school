<template>
    <TransitionGroup
        name="toast"
        tag="div"
        class="fixed top-4 right-4 z-50 space-y-2 max-w-md w-full"
    >
        <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
                'rounded-lg shadow-lg p-4 flex items-start space-x-3 animate-in fade-in slide-in-from-right',
                getToastClasses(notification.type),
            ]"
        >
            <!-- Icon -->
            <div class="flex-shrink-0">
                <svg
                    v-if="notification.type === 'success'"
                    class="h-5 w-5 text-green-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg
                    v-else-if="notification.type === 'error'"
                    class="h-5 w-5 text-red-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <svg
                    v-else-if="notification.type === 'warning'"
                    class="h-5 w-5 text-yellow-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <svg
                    v-else
                    class="h-5 w-5 text-blue-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <p v-if="notification.title" class="text-sm font-medium text-gray-900">
                    {{ notification.title }}
                </p>
                <p class="text-sm text-gray-600 mt-1">
                    {{ notification.message }}
                </p>
            </div>

            <!-- Close Button -->
            <button
                @click="removeNotification(notification.id)"
                class="flex-shrink-0 text-gray-400 hover:text-gray-600"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </TransitionGroup>
</template>

<script setup>
import { computed } from 'vue';
import { useNotificationStore } from '@/stores/notification';

const notificationStore = useNotificationStore();

const notifications = computed(() => notificationStore.notifications);

const getToastClasses = (type) => {
    const classes = {
        success: 'bg-green-50 border border-green-200',
        error: 'bg-red-50 border border-red-200',
        warning: 'bg-yellow-50 border border-yellow-200',
        info: 'bg-blue-50 border border-blue-200',
    };
    return classes[type] || classes.info;
};

const removeNotification = (id) => {
    notificationStore.removeNotification(id);
};
</script>

<style scoped>
/* Toast animations */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.toast-move {
    transition: transform 0.3s ease;
}
</style>

