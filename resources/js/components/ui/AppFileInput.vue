<template>
    <div>
        <label
            v-if="label"
            :for="fileInputId"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-600">*</span>
        </label>

        <div
            :class="[
                'mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md',
                isDragging ? 'border-indigo-500 bg-indigo-50' : 'hover:border-gray-400',
                error ? 'border-red-300' : '',
                disabled ? 'bg-gray-100 cursor-not-allowed' : 'cursor-pointer',
            ]"
            @drop.prevent="handleDrop"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
        >
            <div class="space-y-1 text-center">
                <svg
                    class="mx-auto h-12 w-12 text-gray-400"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 48 48"
                    aria-hidden="true"
                >
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-4h-4m-4 0h4"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
                <div class="flex flex-col sm:flex-row text-sm text-gray-600 items-center sm:items-start">
                    <label
                        :for="fileInputId"
                        class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
                    >
                        <span>{{ dragText }}</span>
                        <input
                            :id="fileInputId"
                            ref="fileInput"
                            type="file"
                            :accept="accept"
                            :multiple="multiple"
                            :disabled="disabled"
                            :required="required"
                            class="sr-only"
                            @change="handleFileChange"
                        />
                    </label>
                    <p class="pl-0 sm:pl-1 mt-1 sm:mt-0">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ hint || `${acceptText || 'files'} up to ${maxSizeText}` }}</p>
            </div>
        </div>

        <!-- Selected Files Preview -->
        <div v-if="selectedFiles.length > 0" class="mt-4">
            <div class="flex flex-wrap gap-2">
                <div
                    v-for="(file, index) in selectedFiles"
                    :key="index"
                    class="flex items-center space-x-2 bg-gray-100 rounded-md px-3 py-2"
                >
                    <span class="text-sm text-gray-700">{{ file.name }}</span>
                    <button
                        v-if="!disabled"
                        @click="removeFile(index)"
                        class="text-red-600 hover:text-red-800"
                        type="button"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [FileList, Array],
        default: () => [],
    },
    label: {
        type: String,
        default: '',
    },
    accept: {
        type: String,
        default: '',
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    maxSize: {
        type: Number,
        default: 5242880, // 5MB in bytes
    },
    error: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    dragText: {
        type: String,
        default: 'Upload a file',
    },
    id: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const fileInput = ref(null);
const isDragging = ref(false);
const selectedFiles = ref([]);

const fileInputId = computed(() => props.id || `file-input-${Math.random().toString(36).substr(2, 9)}`);

const maxSizeText = computed(() => {
    const sizeInMB = props.maxSize / 1024 / 1024;
    return `${sizeInMB}MB`;
});

const acceptText = computed(() => {
    if (!props.accept) return '';
    return props.accept.split(',').map(a => a.trim()).join(', ');
});

const handleFileChange = (event) => {
    const files = Array.from(event.target.files || []);
    validateAndSetFiles(files);
};

const handleDrop = (event) => {
    isDragging.value = false;
    const files = Array.from(event.dataTransfer.files || []);
    validateAndSetFiles(files);
};

const validateAndSetFiles = (files) => {
    const validFiles = files.filter(file => {
        if (file.size > props.maxSize) {
            return false;
        }
        return true;
    });

    selectedFiles.value = validFiles;
    
    if (props.multiple) {
        emit('update:modelValue', validFiles);
    } else {
        emit('update:modelValue', validFiles[0] || null);
    }
    
    emit('change', validFiles);
};

const removeFile = (index) => {
    selectedFiles.value.splice(index, 1);
    
    if (props.multiple) {
        emit('update:modelValue', selectedFiles.value);
    } else {
        emit('update:modelValue', null);
    }
};
</script>

<style scoped>
/* File input-specific styles */
</style>

