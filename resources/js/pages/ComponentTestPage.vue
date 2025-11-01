<template>
    <div class="space-y-8 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Component Test Page</h1>

            <!-- Buttons -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Buttons</h2>
                <div class="flex flex-wrap gap-4">
                    <AppButton variant="primary">Primary Button</AppButton>
                    <AppButton variant="secondary">Secondary</AppButton>
                    <AppButton variant="outline">Outline</AppButton>
                    <AppButton variant="danger">Danger</AppButton>
                    <AppButton variant="ghost">Ghost</AppButton>
                    <AppButton :loading="true">Loading</AppButton>
                    <AppButton size="sm">Small</AppButton>
                    <AppButton size="lg">Large</AppButton>
                </div>
            </section>

            <!-- Cards -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Cards</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <AppCard>
                        <p>Basic Card Content</p>
                    </AppCard>
                    <AppCard hoverable>
                        <template #header>
                            <h3 class="font-semibold">Card with Header</h3>
                        </template>
                        <p>Card body content</p>
                        <template #footer>
                            <AppButton size="sm">Action</AppButton>
                        </template>
                    </AppCard>
                    <AppCard padding="sm">
                        <p>Small Padding Card</p>
                    </AppCard>
                </div>
            </section>

            <!-- Badges -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Badges</h2>
                <div class="flex flex-wrap gap-2">
                    <AppBadge variant="primary" text="Primary" />
                    <AppBadge variant="success" text="Success" />
                    <AppBadge variant="error" text="Error" />
                    <AppBadge variant="warning" text="Warning" />
                    <AppBadge variant="info" text="Info" />
                    <AppBadge size="sm" text="Small" />
                    <AppBadge size="lg" text="Large" />
                </div>
            </section>

            <!-- Alerts -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Alerts</h2>
                <div class="space-y-4">
                    <AppAlert variant="success" title="Success!" message="Operation completed successfully." />
                    <AppAlert variant="error" title="Error!" message="Something went wrong." />
                    <AppAlert variant="warning" title="Warning!" message="Please check your input." />
                    <AppAlert variant="info" title="Info" message="This is an informational message." />
                    <AppAlert variant="success" message="Alert without title" :dismissible="true" />
                </div>
            </section>

            <!-- Form Inputs -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Form Inputs</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <AppInput
                        v-model="formData.email"
                        label="Email"
                        type="email"
                        placeholder="Enter your email"
                        hint="We'll never share your email"
                    />
                    <AppInput
                        v-model="formData.name"
                        label="Name"
                        required
                        error="This field is required"
                    />
                    <AppSelect
                        v-model="formData.country"
                        :options="countries"
                        label="Country"
                        placeholder="Select a country"
                    />
                    <AppTextarea
                        v-model="formData.message"
                        label="Message"
                        :rows="4"
                        hint="Enter your message here"
                        :max-length="200"
                    />
                    <AppFileInput
                        v-model="formData.file"
                        label="Upload File"
                        hint="PDF, DOC, DOCX up to 5MB"
                        accept=".pdf,.doc,.docx"
                    />
                </div>
            </section>

            <!-- Loading -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Loading States</h2>
                <div class="relative h-64 border border-gray-200 rounded-lg">
                    <AppLoading message="Loading content..." />
                </div>
            </section>

            <!-- Modal -->
            <section class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Modal</h2>
                <AppButton @click="showModal = true">Open Modal</AppButton>
                <AppModal
                    v-model="showModal"
                    title="Example Modal"
                >
                    <p class="mb-4">This is a modal dialog example.</p>
                    <p>You can put any content here.</p>
                    <template #footer>
                        <AppButton variant="outline" @click="showModal = false">Cancel</AppButton>
                        <AppButton @click="showModal = false">Confirm</AppButton>
                    </template>
                </AppModal>
            </section>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import AppButton from '@/components/ui/AppButton.vue';
import AppCard from '@/components/ui/AppCard.vue';
import AppBadge from '@/components/ui/AppBadge.vue';
import AppAlert from '@/components/ui/AppAlert.vue';
import AppInput from '@/components/ui/AppInput.vue';
import AppTextarea from '@/components/ui/AppTextarea.vue';
import AppSelect from '@/components/ui/AppSelect.vue';
import AppFileInput from '@/components/ui/AppFileInput.vue';
import AppLoading from '@/components/ui/AppLoading.vue';
import AppModal from '@/components/ui/AppModal.vue';

const showModal = ref(false);
const formData = ref({
    email: '',
    name: '',
    country: '',
    message: '',
    file: null,
});

const countries = [
    { value: 'us', label: 'United States' },
    { value: 'uk', label: 'United Kingdom' },
    { value: 'ca', label: 'Canada' },
    { value: 'au', label: 'Australia' },
];
</script>

<style scoped>
/* Test page styles */
</style>

