<template>
    <AppLayout>
        <router-view />
    </AppLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuth } from './composables/useAuth';
import AppLayout from './components/layout/AppLayout.vue';

const { initialize } = useAuth();

onMounted(() => {
    initialize();
});
</script>

<style scoped>
/* App-specific styles */
</style>

