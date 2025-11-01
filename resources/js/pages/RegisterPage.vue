<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Already have an account?
                    <router-link
                        to="/login"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        Sign in
                    </router-link>
                </p>
            </div>

            <!-- Alert for errors -->
            <AppAlert
                v-if="error"
                variant="error"
                :title="error"
                :dismissible="true"
                @dismiss="error = null"
            />

            <!-- Alert for success -->
            <AppAlert
                v-if="success"
                variant="success"
                title="Registration successful!"
                message="Please check your email to verify your account."
                :dismissible="true"
                @dismiss="success = false"
            />

            <!-- Register Form -->
            <AppCard class="p-8">
                <form @submit.prevent="handleRegister" class="space-y-6">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <AppInput
                            v-model="form.first_name"
                            label="First name"
                            placeholder="John"
                            :error="errors.first_name"
                        />
                        <AppInput
                            v-model="form.last_name"
                            label="Last name"
                            placeholder="Doe"
                            :error="errors.last_name"
                        />
                    </div>

                    <!-- Email Input -->
                    <AppInput
                        v-model="form.email"
                        label="Email address"
                        type="email"
                        placeholder="Enter your email"
                        :error="errors.email"
                        required
                    />

                    <!-- Password Inputs -->
                    <AppInput
                        v-model="form.password"
                        label="Password"
                        type="password"
                        placeholder="Create a password"
                        :error="errors.password"
                        hint="Must be at least 8 characters"
                        required
                    />

                    <AppInput
                        v-model="form.password_confirmation"
                        label="Confirm password"
                        type="password"
                        placeholder="Confirm your password"
                        :error="errors.password_confirmation"
                        required
                    />

                    <!-- Optional Fields -->
                    <div class="space-y-4">
                        <AppInput
                            v-model="form.phone"
                            label="Phone (optional)"
                            type="tel"
                            placeholder="+1 (555) 123-4567"
                            :error="errors.phone"
                        />

                        <AppSelect
                            v-model="form.instrument"
                            :options="instruments"
                            label="Primary instrument (optional)"
                            placeholder="Select an instrument"
                            :error="errors.instrument"
                        />

                        <AppSelect
                            v-model="form.skill_level"
                            :options="skillLevels"
                            label="Skill level (optional)"
                            placeholder="Select your skill level"
                            :error="errors.skill_level"
                        />
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <input
                            id="terms"
                            v-model="form.accept_terms"
                            type="checkbox"
                            class="h-4 w-4 mt-1 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            required
                        />
                        <label for="terms" class="ml-2 block text-sm text-gray-900">
                            I agree to the
                            <router-link
                                to="/terms"
                                class="text-indigo-600 hover:text-indigo-500"
                            >
                                Terms of Service
                            </router-link>
                            and
                            <router-link
                                to="/privacy"
                                class="text-indigo-600 hover:text-indigo-500"
                            >
                                Privacy Policy
                            </router-link>
                        </label>
                    </div>
                    <p v-if="errors.accept_terms" class="text-sm text-red-600">
                        {{ errors.accept_terms }}
                    </p>

                    <!-- Teacher Checkbox -->
                    <div class="flex items-center">
                        <input
                            id="is_teacher"
                            v-model="form.is_teacher"
                            type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label for="is_teacher" class="ml-2 block text-sm text-gray-900">
                            I want to become a teacher
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <AppButton
                        type="submit"
                        :loading="loading"
                        :disabled="loading"
                        class="w-full"
                    >
                        Create account
                    </AppButton>
                </form>

                <!-- OAuth Divider -->
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300" />
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <!-- OAuth Buttons -->
                    <div class="mt-6 grid grid-cols-1 gap-3">
                        <AppButton
                            variant="outline"
                            class="w-full"
                            @click="handleGoogleLogin"
                            :disabled="loading"
                        >
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path
                                    fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                />
                            </svg>
                            Sign up with Google
                        </AppButton>
                    </div>
                </div>
            </AppCard>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import AppCard from '@/components/ui/AppCard.vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppInput from '@/components/ui/AppInput.vue';
import AppSelect from '@/components/ui/AppSelect.vue';
import AppAlert from '@/components/ui/AppAlert.vue';

const router = useRouter();
const { register } = useAuth();

const form = ref({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    instrument: '',
    skill_level: '',
    is_teacher: false,
    accept_terms: false,
});

const loading = ref(false);
const error = ref(null);
const success = ref(false);
const errors = ref({});

const instruments = [
    { value: 'piano', label: 'Piano' },
    { value: 'guitar', label: 'Guitar' },
    { value: 'violin', label: 'Violin' },
    { value: 'drums', label: 'Drums' },
    { value: 'voice', label: 'Voice' },
    { value: 'bass', label: 'Bass' },
    { value: 'saxophone', label: 'Saxophone' },
    { value: 'trumpet', label: 'Trumpet' },
    { value: 'other', label: 'Other' },
];

const skillLevels = [
    { value: 'beginner', label: 'Beginner' },
    { value: 'intermediate', label: 'Intermediate' },
    { value: 'advanced', label: 'Advanced' },
];

const validateForm = () => {
    errors.value = {};

    if (!form.value.email) {
        errors.value.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(form.value.email)) {
        errors.value.email = 'Email is invalid';
    }

    if (!form.value.password) {
        errors.value.password = 'Password is required';
    } else if (form.value.password.length < 8) {
        errors.value.password = 'Password must be at least 8 characters';
    }

    if (!form.value.password_confirmation) {
        errors.value.password_confirmation = 'Password confirmation is required';
    } else if (form.value.password !== form.value.password_confirmation) {
        errors.value.password_confirmation = 'Passwords do not match';
    }

    if (!form.value.accept_terms) {
        errors.value.accept_terms = 'You must accept the terms and conditions';
    }

    return Object.keys(errors.value).length === 0;
};

const handleRegister = async () => {
    if (!validateForm()) {
        return;
    }

    loading.value = true;
    error.value = null;
    success.value = false;

    try {
        const result = await register({
            ...form.value,
            // Remove empty optional fields
            phone: form.value.phone || undefined,
            instrument: form.value.instrument || undefined,
            skill_level: form.value.skill_level || undefined,
        });

        if (result.success) {
            success.value = true;
            // Redirect to dashboard after a short delay
            setTimeout(() => {
                router.push('/dashboard');
            }, 2000);
        } else {
            // Handle validation errors
            if (typeof result.error === 'object' && result.error.errors) {
                errors.value = result.error.errors;
                error.value = result.error.message || 'Please correct the errors below.';
            } else {
                error.value = result.error || 'Registration failed. Please try again.';
            }
        }
    } catch (err) {
        error.value = err.message || 'An error occurred during registration.';
    } finally {
        loading.value = false;
    }
};

const handleGoogleLogin = () => {
    // Redirect to Google OAuth endpoint
    window.location.href = '/api/v1/oauth/google/redirect';
};
</script>

<style scoped>
/* Register page styles */
</style>

