<template>
    <div>
        <label
            v-if="label"
            :for="selectId"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-600">*</span>
        </label>

        <select
            :id="selectId"
            :value="modelValue"
            :disabled="disabled"
            :required="required"
            :class="[
                'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base',
                error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '',
                disabled ? 'bg-gray-100 cursor-not-allowed' : '',
            ]"
            @change="$emit('update:modelValue', $event.target.value)"
            @blur="$emit('blur', $event)"
            @focus="$emit('focus', $event)"
        >
            <option v-if="placeholder" value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="getOptionValue(option)"
                :value="getOptionValue(option)"
            >
                {{ getOptionLabel(option) }}
            </option>
        </select>

        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-else-if="hint" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    options: {
        type: Array,
        required: true,
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
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
    optionValue: {
        type: String,
        default: 'value',
    },
    optionLabel: {
        type: String,
        default: 'label',
    },
    id: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);

const selectId = computed(() => props.id || `select-${Math.random().toString(36).substr(2, 9)}`);

const getOptionValue = (option) => {
    if (typeof option === 'object') {
        return option[props.optionValue];
    }
    return option;
};

const getOptionLabel = (option) => {
    if (typeof option === 'object') {
        return option[props.optionLabel];
    }
    return option;
};
</script>

<style scoped>
/* Select-specific styles */
</style>

