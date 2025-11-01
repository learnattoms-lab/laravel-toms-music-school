<template>
    <div
        :class="[
            'bg-white rounded-lg shadow-md w-full',
            paddingClasses,
            hoverable ? 'transition-shadow hover:shadow-lg cursor-pointer' : '',
        ]"
        @click="handleClick"
    >
        <div v-if="$slots.header" class="border-b border-gray-200 pb-4 mb-4">
            <slot name="header" />
        </div>

        <slot />

        <div v-if="$slots.footer" class="border-t border-gray-200 pt-4 mt-4">
            <slot name="footer" />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    padding: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'none'].includes(value),
    },
    hoverable: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['click']);

const paddingClasses = computed(() => {
    const paddings = {
        sm: 'p-4',
        md: 'p-6',
        lg: 'p-8',
        none: '',
    };
    return paddings[props.padding] || paddings.md;
});

const handleClick = () => {
    if (props.hoverable) {
        emit('click');
    }
};
</script>

<script>
import { computed } from 'vue';
</script>

<style scoped>
/* Card-specific styles */
</style>

