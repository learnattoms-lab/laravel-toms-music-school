<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto text-center py-12">
            <div class="space-y-6">
                <!-- Cancel Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <!-- Message -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Checkout Cancelled
                    </h1>
                    <p class="text-lg text-gray-600">
                        Your checkout was cancelled. No payment was processed.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <router-link
                        v-if="courseId"
                        :to="`/courses/${courseId}`"
                    >
                        <AppButton class="w-full sm:w-auto">
                            Try Again
                        </AppButton>
                    </router-link>
                    <router-link to="/courses">
                        <AppButton variant="outline" class="w-full sm:w-auto">
                            Browse Courses
                        </AppButton>
                    </router-link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppButton from '@/components/ui/AppButton.vue';

const route = useRoute();
const courseId = computed(() => route.query.course_id);
</script>

<style scoped>
/* Checkout cancel page styles */
</style>

