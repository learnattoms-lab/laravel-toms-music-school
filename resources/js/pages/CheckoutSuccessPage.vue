<template>
    <AppLayout>
        <div class="max-w-2xl mx-auto text-center py-12">
            <AppLoading v-if="loading" message="Verifying payment..." />
            
            <div v-else-if="order" class="space-y-6">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <!-- Success Message -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Payment Successful!
                    </h1>
                    <p class="text-lg text-gray-600">
                        You have been successfully enrolled in the course.
                    </p>
                </div>

                <!-- Order Details -->
                <AppCard>
                    <template #header>
                        <h2 class="text-xl font-semibold text-gray-900">Order Details</h2>
                    </template>
                    <div class="space-y-4 text-left">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order ID</span>
                            <span class="font-medium text-gray-900">#{{ order.id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Course</span>
                            <span class="font-medium text-gray-900">{{ order.course?.title }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount Paid</span>
                            <span class="font-medium text-gray-900">
                                ${{ order.formatted_amount || (order.amount_cents / 100) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date</span>
                            <span class="font-medium text-gray-900">
                                {{ formatDate(order.created_at) }}
                            </span>
                        </div>
                    </div>
                </AppCard>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <router-link :to="`/courses/${order.course_id}`">
                        <AppButton class="w-full sm:w-auto">
                            Start Learning
                        </AppButton>
                    </router-link>
                    <router-link to="/dashboard">
                        <AppButton variant="outline" class="w-full sm:w-auto">
                            Go to Dashboard
                        </AppButton>
                    </router-link>
                </div>
            </div>

            <!-- Error State -->
            <div v-else class="space-y-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Payment Verification Failed
                    </h1>
                    <p class="text-lg text-gray-600">
                        We couldn't verify your payment. Please contact support if you were charged.
                    </p>
                </div>
                <router-link to="/dashboard">
                    <AppButton>Go to Dashboard</AppButton>
                </router-link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const route = useRoute();
const loading = ref(true);
const order = ref(null);

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const verifyPayment = async () => {
    loading.value = true;
    try {
        const sessionId = route.query.session_id;
        const courseId = route.query.course_id;

        if (!sessionId || !courseId) {
            loading.value = false;
            return;
        }

        const response = await api.get('/checkout/success', {
            params: {
                session_id: sessionId,
                course_id: courseId,
            },
        });

        order.value = response.data.order;
    } catch (error) {
        console.error('Payment verification failed:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    verifyPayment();
});
</script>

<style scoped>
/* Checkout success page styles */
</style>

