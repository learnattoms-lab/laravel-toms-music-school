# Post-Login Flow Documentation

## What Happens After Login

### 1. **Login Process** (`LoginPage.vue`)

When a user submits the login form:

```178:181:laravel-toms-music-school/resources/js/pages/LoginPage.vue
        if (result.success) {
            // Redirect to dashboard or intended route
            const redirect = router.currentRoute.value.query.redirect || '/dashboard';
            router.push(redirect);
```

**Flow:**
1. User enters email and password
2. Form validation runs
3. `login()` method from `useAuth` is called
4. API request to `/api/v1/auth/login`

### 2. **Authentication Success** (`useAuth.js`)

```73:94:laravel-toms-music-school/resources/js/composables/useAuth.js
    const login = async (credentials) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await api.post('/auth/login', {
                email: credentials.email,
                password: credentials.password,
            });

            const { token: authToken, user: userData } = response.data;
            
            setAuth(authToken, userData);
            
            return { success: true, user: userData };
        } catch (err) {
            error.value = err.response?.data?.message || err.message || 'Login failed';
            return { success: false, error: error.value };
        } finally {
            loading.value = false;
        }
    };
```

**What happens:**
- Token and user data received from API
- `setAuth()` stores:
  - Authentication token in `localStorage` (key: `auth_token`)
  - User data in `localStorage` (key: `user`)
  - Updates reactive state (`token.value`, `user.value`)

### 3. **Redirect Logic**

**Default Redirect:** `/dashboard`

**Smart Redirect:**
- If user tried to access a protected route, redirects to that route
- Uses `redirect` query parameter (e.g., `/login?redirect=/courses/123`)
- Falls back to `/dashboard` if no redirect parameter

### 4. **Header Updates** (`AppHeader.vue`)

After login, the header automatically updates:

**Before Login:**
- Shows "Login" and "Sign Up" buttons

**After Login:**
- Shows user avatar/initials
- Shows user name (or email)
- Shows user dropdown menu with:
  - Profile link
  - Admin Dashboard (if admin)
  - Teacher Dashboard (if teacher)
  - Logout button

```62:100:laravel-toms-music-school/resources/js/components/layout/AppHeader.vue
                    <!-- Authenticated User Menu -->
                    <div v-if="isAuthenticated" class="relative">
                        <button
                            @click="toggleUserMenu"
                            class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100 focus:outline-none"
                        >
                            <img
                                v-if="user?.profile_picture"
                                :src="user.profile_picture"
                                :alt="user.display_name || user.email"
                                class="h-8 w-8 rounded-full object-cover"
                            />
                            <div
                                v-else
                                class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-medium"
                            >
                                {{ (user?.first_name?.[0] || user?.email?.[0] || 'U').toUpperCase() }}
                            </div>
                            <span class="hidden md:block text-gray-700 font-medium">
                                {{ user?.display_name || user?.email }}
```

### 5. **Dashboard Page** (`DashboardPage.vue`)

**URL:** `/dashboard`

**What User Sees:**

1. **Welcome Message**
   - Personalized greeting: "Welcome back, [First Name]!"
   
2. **Statistics Cards** (4 cards):
   - Enrolled Courses count
   - Completed Lessons count
   - Practice Hours
   - Current Level

3. **My Courses Section**:
   - List of enrolled courses
   - Progress bars for each course
   - "Continue" buttons
   - Empty state if no enrollments

4. **Upcoming Sessions**:
   - Next scheduled sessions
   - Join buttons for live sessions
   - Session details (date, time)

5. **Sidebar**:
   - Progress Overview
   - Recent Assignments
   - Quick Actions

### 6. **Router Guard Protection**

```97:116:laravel-toms-music-school/resources/js/router/index.js
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
```

**What this means:**
- If logged in user tries to access `/login` or `/register`, automatically redirected to `/dashboard`
- If logged out user tries to access protected routes, redirected to `/login` with return URL

### 7. **Available Routes After Login**

**Protected Routes (require authentication):**
- `/dashboard` - User dashboard
- `/sessions` - Session list
- `/sessions/:id` - Session details
- `/assignments/:id` - Assignment details
- `/quizzes/:id` - Quiz taking
- `/checkout/*` - Payment pages
- `/courses/:id` - Can enroll (if not enrolled)

**Public Routes (still accessible):**
- `/` - Home page
- `/courses` - Browse courses
- `/test-components` - Component testing

### 8. **API Request Headers**

After login, all API requests automatically include:

```javascript
Authorization: Bearer [token]
```

The token is stored and added to requests via Axios interceptors.

### 9. **Token Persistence**

**Storage:**
- Token stored in `localStorage` as `auth_token`
- User data stored in `localStorage` as `user`

**Session Handling:**
- Token persists across browser sessions
- User remains logged in until:
  - User clicks logout
  - Token expires (handled by backend)
  - User clears browser storage

### 10. **User State Management**

**Reactive State (Vue):**
- `user.value` - Current user object
- `token.value` - Authentication token
- `isAuthenticated.value` - Boolean computed property
- `isAdmin.value` - Role check
- `isTeacher.value` - Role check
- `isStudent.value` - Role check

**Available in:**
- All Vue components via `useAuth()` composable
- Router guards for route protection
- Header for user menu display

## Visual Flow Diagram

```
User Submits Login Form
    ↓
API Call: POST /api/v1/auth/login
    ↓
Success: Token + User Data Received
    ↓
Store in localStorage
    ↓
Update Reactive State (Vue)
    ↓
Header Updates (Login → User Menu)
    ↓
Redirect to Dashboard (or intended route)
    ↓
Dashboard Loads User Data
    ↓
Display: Stats, Courses, Sessions, Assignments
```

## Summary

**After successful login:**
1. ✅ User is authenticated
2. ✅ Token stored securely
3. ✅ Header shows user menu
4. ✅ Redirected to dashboard (or intended page)
5. ✅ Protected routes now accessible
6. ✅ User data available throughout app
7. ✅ API requests include auth token
8. ✅ Cannot access login/register pages (auto-redirect)

The login flow is smooth, secure, and provides a seamless user experience!

