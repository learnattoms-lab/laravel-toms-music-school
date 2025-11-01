<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900">Sign in to your account</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Or
                    <router-link
                        to="/register"
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        create a new account
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

            <!-- Login Form -->
            <AppCard class="p-8">
                <form @submit.prevent="handleLogin" class="space-y-6">
                    <!-- Email Input -->
                        <AppInput
                            v-model="form.email"
                            label="Email address"
                            type="email"
                            placeholder="Enter your email"
                            :error="errors.email"
                            autocomplete="email"
                            required
                        />

                    <!-- Password Input -->
                    <div>
                        <AppInput
                            v-model="form.password"
                            label="Password"
                            type="password"
                            placeholder="Enter your password"
                            :error="errors.password"
                            autocomplete="current-password"
                            required
                        />
                        <div class="mt-1 text-right">
                            <router-link
                                to="/forgot-password"
                                class="text-sm text-indigo-600 hover:text-indigo-500"
                            >
                                Forgot your password?
                            </router-link>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember-me"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <AppButton
                        type="submit"
                        :loading="loading"
                        :disabled="loading"
                        class="w-full"
                    >
                        Sign in
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
                            Sign in with Google
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
import AppAlert from '@/components/ui/AppAlert.vue';

const router = useRouter();
const { login } = useAuth();

const form = ref({
    email: '',
    password: '',
    remember: false,
});

const loading = ref(false);
const error = ref(null);
const errors = ref({});

const handleLogin = async () => {
    loading.value = true;
    error.value = null;
    errors.value = {};

    // Basic validation
    if (!form.value.email) {
        errors.value.email = 'Email is required';
        loading.value = false;
        return;
    }

    if (!form.value.password) {
        errors.value.password = 'Password is required';
        loading.value = false;
        return;
    }

    try {
        const result = await login({
            email: form.value.email,
            password: form.value.password,
        });

        if (result.success) {
            // Redirect to dashboard or intended route
            const redirect = router.currentRoute.value.query.redirect || '/dashboard';
            router.push(redirect);
        } else {
            error.value = result.error || 'Login failed. Please try again.';
        }
    } catch (err) {
        error.value = err.message || 'An error occurred during login.';
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
/* Login page styles */
</style>

