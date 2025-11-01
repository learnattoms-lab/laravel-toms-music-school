<template>
    <div
        :class="[
            'flex items-center justify-center',
            overlay ? 'fixed inset-0 bg-black bg-opacity-50 z-50' : '',
            fullscreen ? 'min-h-screen' : 'min-h-[200px]',
        ]"
    >
        <div class="text-center">
            <svg
                class="animate-spin h-12 w-12 text-indigo-600 mx-auto"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
            </svg>
            <p v-if="message" class="mt-4 text-gray-600 font-medium">{{ message }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: {
        type: String,
        default: 'Loading...',
    },
    overlay: {
        type: Boolean,
        default: false,
    },
    fullscreen: {
        type: Boolean,
        default: false,
    },
});
</script>

<style scoped>
/* Loading-specific styles */
</style>

