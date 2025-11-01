<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Welcome to Tom's Music School
            </h1>
            <p class="text-lg text-gray-600 mb-6">
                Vue.js + Laravel Frontend Setup Complete!
            </p>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-semibold mb-4">Installed Packages</h2>
                <ul class="space-y-2">
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        Vue.js {{ vueVersion }}
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        Vue Router 4.x
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        Pinia (State Management)
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        PrimeVue Components
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        Tailwind CSS 4.x
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        Vuelidate (Form Validation)
                    </li>
                    <li class="flex items-center">
                        <span class="text-green-600 mr-2">✓</span>
                        Axios (HTTP Client)
                    </li>
                </ul>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-blue-900 mb-2">
                    Frontend Development Environment
                </h3>
                <p class="text-blue-700">
                    Your Vue.js application is now ready for development. 
                    Hot Module Replacement (HMR) is enabled for a seamless development experience.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { version } from 'vue';

const vueVersion = version;
</script>

<style scoped>
/* Component-specific styles */
</style>

