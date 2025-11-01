import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import 'primeicons/primeicons.css';
import router from './router';
import App from './App.vue';

const app = createApp(App);

// Pinia for state management
app.use(createPinia());

// PrimeVue - using default theme (can be customized later)
app.use(PrimeVue);

// Vue Router
app.use(router);

// Vuelidate is used per-component (no global plugin needed in v2)
// Import { useVuelidate } from '@vuelidate/core' in components as needed

// Mount the app
app.mount('#app');
