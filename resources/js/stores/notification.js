/**
 * Notification Store
 * Manages toast notifications
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
    // State
    const notifications = ref([]);

    // Actions
    const addNotification = (notification) => {
        const id = Date.now() + Math.random();
        const newNotification = {
            id,
            type: notification.type || 'info',
            title: notification.title || '',
            message: notification.message || '',
            duration: notification.duration || 5000,
            ...notification,
        };

        notifications.value.push(newNotification);

        // Auto-dismiss after duration
        if (newNotification.duration > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, newNotification.duration);
        }

        return id;
    };

    const removeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id);
        if (index !== -1) {
            notifications.value.splice(index, 1);
        }
    };

    const success = (message, title = 'Success') => {
        return addNotification({
            type: 'success',
            title,
            message,
        });
    };

    const error = (message, title = 'Error') => {
        return addNotification({
            type: 'error',
            title,
            message,
        });
    };

    const warning = (message, title = 'Warning') => {
        return addNotification({
            type: 'warning',
            title,
            message,
        });
    };

    const info = (message, title = 'Info') => {
        return addNotification({
            type: 'info',
            title,
            message,
        });
    };

    const clearAll = () => {
        notifications.value = [];
    };

    return {
        // State
        notifications,
        // Actions
        addNotification,
        removeNotification,
        success,
        error,
        warning,
        info,
        clearAll,
    };
});

