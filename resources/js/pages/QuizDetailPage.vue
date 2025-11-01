<template>
    <AppLayout :show-sidebar="true">
        <AppLoading v-if="loading" message="Loading quiz..." />
        
        <div v-else-if="quiz" class="space-y-6">
            <!-- Quiz Header -->
            <AppCard>
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">Quiz</h1>
                        <p v-if="quiz.instructions" class="text-gray-700 mb-4">
                            {{ quiz.instructions }}
                        </p>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pass Mark: {{ quiz.pass_mark || 70 }}%
                            </div>
                            <div v-if="quiz.time_limit" class="flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Time Limit: {{ quiz.time_limit }} minutes
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ quiz.questions?.length || 0 }} Questions
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-6">
                        <AppButton
                            v-if="!hasAttempted && canAttempt"
                            @click="startQuiz"
                            class="w-full sm:w-auto"
                        >
                            Start Quiz
                        </AppButton>
                        <AppBadge
                            v-else-if="hasAttempted"
                            variant="success"
                            text="Completed"
                        />
                    </div>
                </div>
            </AppCard>

            <!-- Quiz Attempt -->
            <AppCard v-if="showQuiz">
                <template #header>
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Quiz Questions</h2>
                        <div v-if="timeLimit && timeRemaining > 0" class="flex items-center text-lg font-semibold">
                            <svg class="h-5 w-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span :class="timeRemaining < 300 ? 'text-red-600' : 'text-gray-900'">
                                {{ formatTime(timeRemaining) }}
                            </span>
                        </div>
                    </div>
                </template>

                <form @submit.prevent="submitQuiz" class="space-y-8">
                    <div
                        v-for="(question, index) in quizQuestions"
                        :key="index"
                        class="border-b border-gray-200 pb-6 last:border-b-0"
                    >
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                Question {{ index + 1 }}
                            </h3>
                            <p class="text-gray-700">{{ question.question }}</p>
                            <p v-if="question.points" class="text-sm text-gray-500 mt-1">
                                {{ question.points }} point{{ question.points !== 1 ? 's' : '' }}
                            </p>
                        </div>

                        <!-- Multiple Choice -->
                        <div v-if="question.type === 'multiple_choice'" class="space-y-2">
                            <label
                                v-for="(option, optIndex) in question.options"
                                :key="optIndex"
                                class="flex items-start p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors"
                                :class="answers[index] === option ? 'border-indigo-500 bg-indigo-50' : ''"
                            >
                                <input
                                    type="radio"
                                    :name="`question-${index}`"
                                    :value="option"
                                    v-model="answers[index]"
                                    class="mt-1 mr-3"
                                />
                                <span class="flex-1 text-gray-900">{{ option }}</span>
                            </label>
                        </div>

                        <!-- True/False -->
                        <div v-else-if="question.type === 'true_false'" class="space-y-2">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors" :class="answers[index] === 'true' ? 'border-indigo-500 bg-indigo-50' : ''">
                                <input type="radio" :name="`question-${index}`" value="true" v-model="answers[index]" class="mr-3" />
                                <span class="text-gray-900">True</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500 transition-colors" :class="answers[index] === 'false' ? 'border-indigo-500 bg-indigo-50' : ''">
                                <input type="radio" :name="`question-${index}`" value="false" v-model="answers[index]" class="mr-3" />
                                <span class="text-gray-900">False</span>
                            </label>
                        </div>

                        <!-- Short Answer -->
                        <div v-else-if="question.type === 'short_answer'">
                            <AppTextarea
                                v-model="answers[index]"
                                :rows="3"
                                placeholder="Enter your answer..."
                            />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <AppButton type="submit" :loading="submitting">
                            Submit Quiz
                        </AppButton>
                    </div>
                </form>
            </AppCard>

            <!-- Results -->
            <AppCard v-if="quizResult">
                <template #header>
                    <h2 class="text-xl font-semibold text-gray-900">Quiz Results</h2>
                </template>
                <div class="text-center py-8">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-4"
                         :class="quizResult.passed ? 'bg-green-100' : 'bg-red-100'">
                        <svg
                            v-if="quizResult.passed"
                            class="h-10 w-10 text-green-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg
                            v-else
                            class="h-10 w-10 text-red-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ quizResult.passed ? 'Congratulations!' : 'Try Again' }}
                    </h3>
                    <p class="text-lg text-gray-600 mb-4">
                        You scored {{ quizResult.score }}% ({{ quizResult.correct }}/{{ quizResult.total }} correct)
                    </p>
                    <p v-if="!quizResult.passed && quiz.allow_retakes" class="text-gray-600 mb-6">
                        Pass mark is {{ quiz.pass_mark }}%. You can retake this quiz.
                    </p>
                    <div class="flex justify-center gap-4">
                        <router-link
                            v-if="quizResult.passed"
                            :to="`/courses/${route.query.course_id}`"
                        >
                            <AppButton>Continue Learning</AppButton>
                        </router-link>
                        <AppButton
                            v-else-if="quiz.allow_retakes && canRetake"
                            @click="startQuiz"
                        >
                            Retake Quiz
                        </AppButton>
                    </div>
                </div>
            </AppCard>
        </div>

        <!-- Error State -->
        <AppCard v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Quiz not found</h3>
        </AppCard>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import api from '@/utils/api';
import AppLayout from '@/components/layout/AppLayout.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppTextarea from '@/components/ui/AppTextarea.vue';
import AppLoading from '@/components/ui/AppLoading.vue';

const route = useRoute();
const router = useRouter();
const { isStudent } = useAuth();

const loading = ref(true);
const quiz = ref(null);
const quizQuestions = ref([]);
const showQuiz = ref(false);
const submitting = ref(false);
const answers = ref({});
const attemptId = ref(null);
const quizResult = ref(null);
const timeRemaining = ref(0);
const timeLimit = ref(null);
let timer = null;

const hasAttempted = computed(() => !!quizResult.value);
const canAttempt = computed(() => {
    if (!quiz.value) return false;
    if (quiz.value.allow_retakes) return true;
    return !hasAttempted.value;
});
const canRetake = computed(() => {
    return quiz.value?.allow_retakes && quizResult.value && !quizResult.value.passed;
});

const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

const startQuiz = async () => {
    try {
        const response = await api.post(`/quizzes/${route.params.id}/attempt`);
        attemptId.value = response.data.attempt_id;
        quizQuestions.value = quiz.value.questions || [];
        showQuiz.value = true;
        quizResult.value = null;
        answers.value = {};

        // Start timer if time limit exists
        if (quiz.value.time_limit) {
            timeLimit.value = quiz.value.time_limit;
            timeRemaining.value = timeLimit.value * 60; // Convert to seconds
            startTimer();
        }
    } catch (error) {
        console.error('Failed to start quiz:', error);
    }
};

const startTimer = () => {
    timer = setInterval(() => {
        if (timeRemaining.value > 0) {
            timeRemaining.value--;
        } else {
            // Auto-submit when time runs out
            clearInterval(timer);
            submitQuiz();
        }
    }, 1000);
};

const submitQuiz = async () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }

    submitting.value = true;
    try {
        const response = await api.post(`/quizzes/${route.params.id}/submit`, {
            attempt_id: attemptId.value,
            answers: answers.value,
        });

        quizResult.value = response.data;
        showQuiz.value = false;
    } catch (error) {
        console.error('Failed to submit quiz:', error);
    } finally {
        submitting.value = false;
    }
};

const fetchQuiz = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/quizzes/${route.params.id}`);
        quiz.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch quiz:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchQuiz();
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>

<style scoped>
/* Quiz page styles */
</style>

