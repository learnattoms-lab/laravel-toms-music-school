<template>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white py-20 sm:py-32">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6">
                    Learn Music from Expert Teachers
                </h1>
                <p class="text-xl sm:text-2xl text-indigo-100 mb-8">
                    Join thousands of students improving their musical skills every day
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <router-link
                        to="/register"
                        class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition-colors text-lg"
                    >
                        Get Started
                    </router-link>
                    <router-link
                        to="/courses"
                        class="bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-800 transition-colors text-lg border-2 border-white/30"
                    >
                        Browse Courses
                    </router-link>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 sm:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    Why Choose Tom's Music School?
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Learn music with the best teachers, flexible schedules, and a comprehensive curriculum
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <AppCard class="text-center hoverable">
                    <div class="text-indigo-600 mb-4">
                        <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Expert Teachers</h3>
                    <p class="text-gray-600">
                        Learn from professional musicians and experienced educators
                    </p>
                </AppCard>

                <!-- Feature 2 -->
                <AppCard class="text-center hoverable">
                    <div class="text-indigo-600 mb-4">
                        <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Flexible Schedule</h3>
                    <p class="text-gray-600">
                        Book sessions that fit your busy lifestyle
                    </p>
                </AppCard>

                <!-- Feature 3 -->
                <AppCard class="text-center hoverable">
                    <div class="text-indigo-600 mb-4">
                        <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Track Progress</h3>
                    <p class="text-gray-600">
                        Monitor your improvement with detailed progress tracking
                    </p>
                </AppCard>

                <!-- Feature 4 -->
                <AppCard class="text-center hoverable">
                    <div class="text-indigo-600 mb-4">
                        <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Live Sessions</h3>
                    <p class="text-gray-600">
                        Attend interactive live sessions with Google Meet integration
                    </p>
                </AppCard>

                <!-- Feature 5 -->
                <AppCard class="text-center hoverable">
                    <div class="text-indigo-600 mb-4">
                        <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Comprehensive Courses</h3>
                    <p class="text-gray-600">
                        Access structured courses with lessons, assignments, and quizzes
                    </p>
                </AppCard>

                <!-- Feature 6 -->
                <AppCard class="text-center hoverable">
                    <div class="text-indigo-600 mb-4">
                        <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Secure Platform</h3>
                    <p class="text-gray-600">
                        Your data is safe with our secure payment and data handling
                    </p>
                </AppCard>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 sm:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    What Our Students Say
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <AppCard>
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                            JD
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">John Doe</h4>
                            <p class="text-sm text-gray-600">Piano Student</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">
                        "The quality of instruction is outstanding. My teacher is patient and knowledgeable, and I've improved so much in just a few months!"
                    </p>
                    <div class="mt-4 flex text-yellow-400">
                        <span v-for="i in 5" :key="i">★</span>
                    </div>
                </AppCard>

                <!-- Testimonial 2 -->
                <AppCard>
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold text-lg">
                            SJ
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Sarah Johnson</h4>
                            <p class="text-sm text-gray-600">Guitar Student</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">
                        "Flexible scheduling makes it perfect for my busy work schedule. The live sessions are really engaging and helpful."
                    </p>
                    <div class="mt-4 flex text-yellow-400">
                        <span v-for="i in 5" :key="i">★</span>
                    </div>
                </AppCard>

                <!-- Testimonial 3 -->
                <AppCard>
                    <div class="flex items-center mb-4">
                        <div class="h-12 w-12 rounded-full bg-pink-600 flex items-center justify-center text-white font-bold text-lg">
                            MW
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Mike Williams</h4>
                            <p class="text-sm text-gray-600">Voice Student</p>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">
                        "The course structure is well-organized and the assignments really help reinforce what I learn. Highly recommended!"
                    </p>
                    <div class="mt-4 flex text-yellow-400">
                        <span v-for="i in 5" :key="i">★</span>
                    </div>
                </AppCard>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 sm:py-24 bg-indigo-600 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-bold mb-6">
                    Ready to Start Your Musical Journey?
                </h2>
                <p class="text-xl text-indigo-100 mb-8">
                    Join thousands of students learning music online with expert teachers
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <router-link
                        to="/register"
                        class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition-colors text-lg"
                    >
                        Get Started Today
                    </router-link>
                    <router-link
                        to="/courses"
                        class="bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-800 transition-colors text-lg border-2 border-white/30"
                    >
                        View Courses
                    </router-link>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import AppCard from '@/components/ui/AppCard.vue';
</script>

<style scoped>
/* Home page styles */
</style>

