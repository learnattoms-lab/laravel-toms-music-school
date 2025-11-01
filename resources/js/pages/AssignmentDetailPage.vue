<template>
    <AppLayout :show-sidebar="true">
        <AppLoading v-if="loading" message="Loading assignment..." />
        
        <div v-else-if="assignment" class="space-y-6">
            <!-- Assignment Header -->
            <AppCard>
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <h1 class="text-3xl font-bold text-gray-900">{{ assignment.title }}</h1>
                            <AppBadge
                                v-if="assignment.is_required"
                                variant="warning"
                                text="Required"
                            />
                        </div>
                        <p v-if="assignment.description" class="text-gray-700 mb-4">
                            {{ assignment.description }}
                        </p>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Due: {{ formatDate(assignment.due_at) }}
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Max Points: {{ assignment.max_points || 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6">
                        <AppButton
                            v-if="!hasSubmitted && isStudent"
                            @click="showSubmitModal = true"
                        >
                            Submit Assignment
                        </AppButton>
                        <AppBadge
                            v-else-if="hasSubmitted"
                            variant="success"
                            text="Submitted"
                        />
                    </div>
                </div>
            </AppCard>

            <!-- Instructions -->
            <AppCard v-if="assignment.instructions_html">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Instructions</h2>
                </template>
                <div
                    class="prose max-w-none"
                    v-html="assignment.instructions_html"
                />
            </AppCard>

            <!-- Attachments -->
            <AppCard v-if="assignment.attachments && assignment.attachments.length > 0">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Attachments</h2>
                </template>
                <div class="space-y-3">
                    <div
                        v-for="(attachment, index) in assignment.attachments"
                        :key="index"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                    >
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-900">{{ attachment.name || `Attachment ${index + 1}` }}</span>
                        </div>
                        <AppButton
                            size="sm"
                            variant="outline"
                            @click="downloadAttachment(attachment)"
                        >
                            Download
                        </AppButton>
                    </div>
                </div>
            </AppCard>

            <!-- Submission -->
            <AppCard v-if="hasSubmitted && submission">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Your Submission</h2>
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Submitted Content</label>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ submission.content }}</p>
                        </div>
                    </div>
                    <div v-if="submission.attachments && submission.attachments.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                        <div class="space-y-2">
                            <div
                                v-for="(attachment, index) in submission.attachments"
                                :key="index"
                                class="flex items-center justify-between p-2 bg-gray-50 rounded"
                            >
                                <span class="text-sm text-gray-700">{{ attachment.name }}</span>
                                <AppButton
                                    size="sm"
                                    variant="ghost"
                                    @click="downloadAttachment(attachment)"
                                >
                                    Download
                                </AppButton>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">Submitted on</p>
                            <p class="font-medium text-gray-900">{{ formatDate(submission.submitted_at) }}</p>
                        </div>
                        <div v-if="submission.score !== null">
                            <p class="text-sm text-gray-600">Score</p>
                            <p class="font-medium text-gray-900">
                                {{ submission.score }} / {{ assignment.max_points }}
                            </p>
                        </div>
                    </div>
                </div>
            </AppCard>

            <!-- Submit Modal -->
            <AppModal
                v-model="showSubmitModal"
                title="Submit Assignment"
            >
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <AppTextarea
                        v-model="submissionForm.content"
                        label="Your Submission"
                        :rows="8"
                        required
                        :error="errors.content"
                    />
                    
                    <AppFileInput
                        v-model="submissionForm.attachments"
                        label="Attachments (Optional)"
                        :multiple="true"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        hint="PDF, DOC, DOCX, Images up to 10MB"
                    />

                    <template #footer>
                        <AppButton variant="outline" @click="showSubmitModal = false">
                            Cancel
                        </AppButton>
                        <AppButton type="submit" :loading="submitting">
                            Submit
                        </AppButton>
                    </template>
                </form>
            </AppModal>
        </div>

        <!-- Error State -->
        <AppCard v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Assignment not found</h3>
            <router-link to="/dashboard/assignments" class="mt-6 inline-block">
                <AppButton>View All Assignments</AppButton>
            </router-link>
        </AppCard>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppTextarea from '@/components/ui/AppTextarea.vue';
import AppFileInput from '@/components/ui/AppFileInput.vue';
import AppModal from '@/components/ui/AppModal.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const route = useRoute();
const router = useRouter();
const { isStudent } = useAuth();

const loading = ref(true);
const assignment = ref(null);
const submission = ref(null);
const showSubmitModal = ref(false);
const submitting = ref(false);
const errors = ref({});

const submissionForm = ref({
    content: '',
    attachments: [],
});

const hasSubmitted = computed(() => !!submission.value);

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const downloadAttachment = (attachment) => {
    if (attachment.url) {
        window.open(attachment.url, '_blank');
    }
};

const handleSubmit = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        // TODO: Implement submission API endpoint
        // await api.post(`/assignments/${assignment.value.id}/submit`, submissionForm.value);
        
        showSubmitModal.value = false;
        // Refresh submission data
        await fetchSubmission();
    } catch (err) {
        if (err.response?.data?.errors) {
            errors.value = err.response.data.errors;
        }
    } finally {
        submitting.value = false;
    }
};

const fetchAssignment = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/assignments/${route.params.id}`);
        assignment.value = response.data.data;
        await fetchSubmission();
    } catch (error) {
        console.error('Failed to fetch assignment:', error);
    } finally {
        loading.value = false;
    }
};

const fetchSubmission = async () => {
    try {
        // TODO: Implement submission fetch endpoint
        // const response = await api.get(`/assignments/${route.params.id}/submission`);
        // submission.value = response.data.data;
    } catch (error) {
        // No submission yet
        submission.value = null;
    }
};

onMounted(() => {
    fetchAssignment();
});
</script>

<style scoped>
.prose {
    color: #374151;
}
.prose h1, .prose h2, .prose h3 {
    color: #111827;
    font-weight: 600;
}
.prose a {
    color: #4F46E5;
}
.prose a:hover {
    color: #4338CA;
}
</style>

