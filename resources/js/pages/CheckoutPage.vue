<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <AppLoading v-if="loading || processing" message="Processing..." />
            
            <div v-else-if="checkoutData" class="space-y-6">
                <!-- Checkout Header -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Complete Your Purchase</h1>
                    <p class="mt-2 text-gray-600">Review your order before proceeding to payment</p>
                </div>

                <!-- Course Summary -->
                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">Course Details</h2>
                    </template>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-24 h-24 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center">
                            <span v-if="!course?.cover_file?.url" class="text-4xl text-white">🎵</span>
                            <img
                                v-else
                                :src="course.cover_file.url"
                                :alt="course.title"
                                class="w-full h-full object-cover rounded-lg"
                            />
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ course.title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ course.instrument }} • {{ course.level }}</p>
                            <div class="mt-3 flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Instructor: {{ course.teacher?.display_name || 'Instructor' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">
                                ${{ course.price_dollars || (course.price_cents / 100) }}
                            </div>
                            <p class="text-sm text-gray-600">One-time payment</p>
                        </div>
                    </div>
                </AppCard>

                <!-- Order Summary -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <AppCard>
                            <template #header>
                                <h2 class="text-xl font-semibold text-gray-900">Order Summary</h2>
                            </template>
                            <div class="space-y-4">
                                <div class="flex justify-between text-gray-700">
                                    <span>Course: {{ course.title }}</span>
                                    <span>${{ course.price_dollars || (course.price_cents / 100) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-4 flex justify-between font-semibold text-lg">
                                    <span>Total</span>
                                    <span>${{ course.price_dollars || (course.price_cents / 100) }}</span>
                                </div>
                            </div>
                        </AppCard>
                    </div>

                    <div>
                        <AppCard>
                            <template #header>
                                <h3 class="font-semibold text-gray-900">Secure Payment</h3>
                            </template>
                            <div class="space-y-4">
                                <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
                                    <span>Powered by</span>
                                    <svg class="h-6" viewBox="0 0 283 64">
                                        <path fill="#635BFF" d="M141 16c-11 0-19 7-19 18s9 18 20 18c7 0 13-3 16-7l-7-5c-2 3-6 4-9 4-5 0-9-3-10-7h28v-3c0-11-8-18-19-18zm-9 15c1-4 4-7 9-7s8 3 9 7h-18zm117-15c-11 0-19 7-19 18s9 18 20 18c6 0 12-3 16-7l-8-5c-2 3-5 4-8 4-5 0-9-3-11-7h28l1-3c0-11-8-18-19-18zm-10 15c2-4 5-7 10-7s8 3 9 7h-19zm-39 3c0 6 4 10 10 10 4 0 7-2 9-5l8 5c-3 5-9 8-17 8-11 0-19-7-19-18s8-18 19-18c8 0 14 3 17 8l-8 5c-2-3-5-5-9-5-6 0-10 4-10 10zm83-29v46h-9V5h9zM37 0l37 64H0L37 0zm92 5-27 48L74 5h10l18 30 17-30h10zm59 12v11l8-8 8 8v-11h14v46h-14V44l-8 8-8-8v13h-14V17h14z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-gray-500 text-center">
                                    Your payment information is secure and encrypted
                                </div>
                            </div>
                        </AppCard>
                    </div>
                </div>

                <!-- Checkout Button -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <AppButton
                        variant="outline"
                        class="flex-1"
                        @click="$router.push(`/courses/${course.id}`)"
                    >
                        Cancel
                    </AppButton>
                    <AppButton
                        class="flex-1"
                        :loading="processing"
                        @click="redirectToCheckout"
                    >
                        Proceed to Payment
                    </AppButton>
                </div>
            </div>

            <!-- Error State -->
            <AppCard v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Checkout unavailable</h3>
                <p class="mt-2 text-gray-600">{{ error || 'Unable to process checkout at this time.' }}</p>
                <router-link to="/courses" class="mt-6 inline-block">
                    <AppButton>Browse Courses</AppButton>
                </router-link>
            </AppCard>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const route = useRoute();
const router = useRouter();
const { isAuthenticated } = useAuth();

const loading = ref(true);
const processing = ref(false);
const checkoutData = ref(null);
const course = ref(null);
const error = ref(null);

const fetchCourse = async () => {
    try {
        const response = await api.get(`/courses/${route.params.courseId || route.params.id}`);
        course.value = response.data.data;
    } catch (err) {
        console.error('Failed to fetch course:', err);
    }
};

const initializeCheckout = async () => {
    if (!isAuthenticated.value) {
        router.push({ name: 'login', query: { redirect: route.fullPath } });
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const courseId = route.params.courseId || route.params.id;
        const response = await api.post(`/checkout/course/${courseId}`);
        checkoutData.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to initialize checkout';
        console.error('Checkout error:', err);
    } finally {
        loading.value = false;
    }
};

const redirectToCheckout = () => {
    if (checkoutData.value?.checkout_url) {
        processing.value = true;
        window.location.href = checkoutData.value.checkout_url;
    }
};

onMounted(async () => {
    await fetchCourse();
    await initializeCheckout();
});
</script>

<style scoped>
/* Checkout page styles */
</style>

