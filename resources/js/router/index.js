import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import HomePage from '../pages/HomePage.vue';
import LoginPage from '../pages/LoginPage.vue';
import RegisterPage from '../pages/RegisterPage.vue';
import ComponentTestPage from '../pages/ComponentTestPage.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomePage,
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage,
        meta: { requiresGuest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterPage,
        meta: { requiresGuest: true },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../pages/DashboardPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/courses',
        name: 'courses',
        component: () => import('../pages/CourseListPage.vue'),
    },
    {
        path: '/courses/:id',
        name: 'course-detail',
        component: () => import('../pages/CourseDetailPage.vue'),
    },
    {
        path: '/sessions',
        name: 'sessions',
        component: () => import('../pages/SessionListPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/sessions/:id',
        name: 'session-detail',
        component: () => import('../pages/SessionDetailPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/checkout/course/:courseId',
        name: 'checkout',
        component: () => import('../pages/CheckoutPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/checkout/success',
        name: 'checkout-success',
        component: () => import('../pages/CheckoutSuccessPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/checkout/cancel',
        name: 'checkout-cancel',
        component: () => import('../pages/CheckoutCancelPage.vue'),
    },
    {
        path: '/assignments/:id',
        name: 'assignment-detail',
        component: () => import('../pages/AssignmentDetailPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/quizzes/:id',
        name: 'quiz-detail',
        component: () => import('../pages/QuizDetailPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/test-components',
        name: 'test-components',
        component: ComponentTestPage,
        meta: { requiresAuth: false }, // Allow test page without auth
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const { isAuthenticated } = useAuth();

    // Check if route requires authentication
    if (to.meta.requiresAuth !== false && to.meta.requiresAuth && !isAuthenticated.value) {
        next({
            name: 'login',
            query: { redirect: to.fullPath },
        });
        return;
    }

    // Check if route requires guest (logged out)
    if (to.meta.requiresGuest && isAuthenticated.value) {
        next({ name: 'dashboard' });
        return;
    }

    next();
});

export default router;

