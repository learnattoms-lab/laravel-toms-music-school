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
    // Admin routes
    {
        path: '/admin',
        name: 'admin-dashboard',
        component: () => import('../pages/admin/AdminDashboardPage.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/users',
        name: 'admin-users',
        component: () => import('../pages/admin/UserManagementPage.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/teachers',
        name: 'admin-teachers',
        component: () => import('../pages/admin/TeacherManagementPage.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: '/admin/analytics',
        name: 'admin-analytics',
        component: () => import('../pages/admin/AnalyticsPage.vue'),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    // Teacher routes
    {
        path: '/teacher',
        name: 'teacher-dashboard',
        component: () => import('../pages/teacher/TeacherDashboardPage.vue'),
        meta: { requiresAuth: true, requiresTeacher: true },
    },
    {
        path: '/teacher/courses',
        name: 'teacher-courses',
        component: () => import('../pages/teacher/CourseManagementPage.vue'),
        meta: { requiresAuth: true, requiresTeacher: true },
    },
    {
        path: '/teacher/courses/create',
        name: 'teacher-course-create',
        component: () => import('../pages/teacher/CourseManagementPage.vue'), // Will be separate create page later
        meta: { requiresAuth: true, requiresTeacher: true },
    },
    {
        path: '/teacher/sessions',
        name: 'teacher-sessions',
        component: () => import('../pages/teacher/SessionManagementPage.vue'),
        meta: { requiresAuth: true, requiresTeacher: true },
    },
    {
        path: '/teacher/sessions/create',
        name: 'teacher-session-create',
        component: () => import('../pages/teacher/SessionManagementPage.vue'), // Will be separate create page later
        meta: { requiresAuth: true, requiresTeacher: true },
    },
    {
        path: '/teacher/students',
        name: 'teacher-students',
        component: () => import('../pages/teacher/StudentManagementPage.vue'),
        meta: { requiresAuth: true, requiresTeacher: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const { isAuthenticated, isAdmin, isTeacher } = useAuth();

    // Check if route requires authentication
    if (to.meta.requiresAuth !== false && to.meta.requiresAuth && !isAuthenticated.value) {
        next({
            name: 'login',
            query: { redirect: to.fullPath },
        });
        return;
    }

    // Check if route requires admin
    if (to.meta.requiresAdmin && !isAdmin.value) {
        next({ name: 'dashboard' });
        return;
    }

    // Check if route requires teacher
    if (to.meta.requiresTeacher && !isTeacher.value) {
        next({ name: 'dashboard' });
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

