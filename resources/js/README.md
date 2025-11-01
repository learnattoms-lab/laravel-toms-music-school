# Vue.js Frontend Setup

This directory contains the Vue.js frontend application for Tom's Music School.

## Structure

```
resources/js/
├── app.js              # Main application entry point
├── App.vue             # Root Vue component
├── bootstrap.js        # Axios configuration
├── components/         # Reusable Vue components
│   └── ExampleComponent.vue
├── pages/              # Page components (route views)
│   └── Home.vue
├── router/             # Vue Router configuration
│   └── index.js
├── stores/             # Pinia stores (state management)
│   └── index.js
└── plugins/            # Vue plugins and configurations
    ├── axios.js
    └── vuelidate.js
```

## Installed Packages

- **Vue.js 3.5.22** - Progressive JavaScript framework (Composition API)
- **Vue Router 4.6.3** - Official router for Vue.js
- **Pinia 3.0.3** - State management library for Vue
- **PrimeVue 4.4.1** - UI component library
- **PrimeIcons 7.0.0** - Icon library for PrimeVue
- **Vuelidate 0.7.7** - Form validation library
- **Axios 1.13.1** - HTTP client
- **Vite 7.0.7** - Build tool and dev server
- **Tailwind CSS 4.0.0** - Utility-first CSS framework

## Development

### Start Dev Server
```bash
# Using Docker
docker compose up -d node
docker compose exec node npm run dev

# Or using Makefile
make npm dev
```

### Build for Production
```bash
# Using Docker
docker compose exec node npm run build

# Or using Makefile
make npm build
```

## Architecture

### Single Page Application (SPA)
The application is configured as a full SPA where Vue Router handles all routing. 
Laravel serves the initial HTML and then Vue takes over for all navigation.

### Component Organization
- **Pages**: Top-level route components
- **Components**: Reusable UI components
- **Stores**: Pinia stores for state management
- **Router**: Route definitions and navigation guards

## Usage Examples

### Using Vue Component
```vue
<template>
    <div class="p-4">
        <h1>{{ title }}</h1>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const title = ref('Hello Vue!');
</script>
```

### Using Pinia Store
```javascript
// stores/user.js
import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
    }),
    actions: {
        setUser(user) {
            this.user = user;
        },
    },
});
```

### Using Vue Router
```javascript
import { useRouter } from 'vue-router';

const router = useRouter();
router.push('/dashboard');
```

### Using Vuelidate
```vue
<script setup>
import { useVuelidate } from '@vuelidate/core';
import { required, email } from '@vuelidate/validators';

const rules = {
    email: { required, email },
};

const v$ = useVuelidate(rules, { email: '' });
</script>
```

## API Integration

Axios is configured to automatically include:
- CSRF token from meta tag
- `X-Requested-With` header
- Base URL: `/api`

Example API call:
```javascript
import axios from 'axios';

// GET request
const response = await axios.get('/api/users');

// POST request
const response = await axios.post('/api/users', { name: 'John' });
```

