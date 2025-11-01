<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <AppHeader v-if="showHeader" />
        
        <div class="flex flex-1">
            <AppSidebar 
                v-if="showSidebar" 
                class="hidden lg:block"
            />
            
            <main 
                :class="[
                    'flex-1 transition-all duration-300',
                    showSidebar ? 'lg:ml-64' : '',
                ]"
            >
                <div class="container mx-auto px-4 py-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
        
        <AppFooter v-if="showFooter" />
    </div>
</template>

<script setup>
import { ref, provide } from 'vue';
import { useRoute } from 'vue-router';
import AppHeader from './AppHeader.vue';
import AppSidebar from './AppSidebar.vue';
import AppFooter from './AppFooter.vue';

const props = defineProps({
    showHeader: {
        type: Boolean,
        default: true,
    },
    showSidebar: {
        type: Boolean,
        default: false,
    },
    showFooter: {
        type: Boolean,
        default: true,
    },
});

const route = useRoute();
const mobileSidebarOpen = ref(false);

// Provide sidebar toggle function to child components
const toggleMobileSidebar = () => {
    mobileSidebarOpen.value = !mobileSidebarOpen.value;
};

provide('toggleMobileSidebar', toggleMobileSidebar);
provide('mobileSidebarOpen', mobileSidebarOpen);
</script>

<style scoped>
/* Layout-specific styles */
</style>

