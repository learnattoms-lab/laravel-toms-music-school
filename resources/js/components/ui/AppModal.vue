<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="modelValue"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="handleBackdropClick"
            >
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" />

                <!-- Modal -->
                <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
                    <div
                        class="relative bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto transform transition-all"
                        role="dialog"
                        aria-modal="true"
                        :aria-labelledby="title ? 'modal-title' : undefined"
                    >
                        <!-- Header -->
                        <div
                            v-if="title || showClose"
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-200"
                        >
                            <h3
                                v-if="title"
                                id="modal-title"
                                class="text-lg font-semibold text-gray-900"
                            >
                                {{ title }}
                            </h3>
                            <button
                                v-if="showClose"
                                @click="handleClose"
                                class="ml-auto text-gray-400 hover:text-gray-600 focus:outline-none"
                                aria-label="Close"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="px-6 py-4">
                            <slot />
                        </div>

                        <!-- Footer -->
                        <div
                            v-if="$slots.footer"
                            class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3"
                        >
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    showClose: {
        type: Boolean,
        default: true,
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue', 'close']);

const handleClose = () => {
    emit('update:modelValue', false);
    emit('close');
};

const handleBackdropClick = () => {
    if (props.closeOnBackdrop) {
        handleClose();
    }
};

// Close on Escape key
const handleEscape = (event) => {
    if (event.key === 'Escape' && props.modelValue) {
        handleClose();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape);
});
</script>

<script>
import { onMounted, onUnmounted } from 'vue';
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>

