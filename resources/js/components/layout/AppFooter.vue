<template>
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="container mx-auto px-4 lg:px-8 py-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Tom's Music School
                    </h3>
                    <p class="text-gray-600 text-sm">
                        Learn music from expert teachers. Join thousands of students
                        improving their musical skills every day.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        Quick Links
                    </h4>
                    <ul class="space-y-2">
                        <li>
                            <router-link
                                to="/courses"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Courses
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/about"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                About Us
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/contact"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Contact
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/faq"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                FAQ
                            </router-link>
                        </li>
                    </ul>
                </div>

                <!-- For Students -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        For Students
                    </h4>
                    <ul class="space-y-2">
                        <li>
                            <router-link
                                to="/register"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Sign Up
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/dashboard"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Dashboard
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/pricing"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Pricing
                            </router-link>
                        </li>
                    </ul>
                </div>

                <!-- For Teachers -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
                        For Teachers
                    </h4>
                    <ul class="space-y-2">
                        <li>
                            <router-link
                                to="/teacher/register"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Become a Teacher
                            </router-link>
                        </li>
                        <li>
                            <router-link
                                to="/teacher/dashboard"
                                class="text-gray-600 hover:text-indigo-600 text-sm"
                            >
                                Teacher Portal
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-600 text-sm">
                        &copy; {{ currentYear }} Tom's Music School. All rights reserved.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a
                            href="#"
                            class="text-gray-600 hover:text-indigo-600 text-sm"
                        >
                            Privacy Policy
                        </a>
                        <a
                            href="#"
                            class="text-gray-600 hover:text-indigo-600 text-sm"
                        >
                            Terms of Service
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script>

<style scoped>
/* Footer-specific styles */
</style>

