import { createRouter, createWebHistory } from 'vue-router';
import adminRoutes from './AdminRoutes';
import authRoutes from './AuthRoutes';
import { useAuthStore } from '../stores/authStore';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../views/Home.vue'),
        },
        ...authRoutes,
        ...adminRoutes,
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: () => import('../views/NotFoundView.vue'),
        },
    ],
});

// Navigation guards
router.beforeEach(async (to, _from, next) => {
    const authStore = useAuthStore();

    // If user is not loaded, try to fetch from storage/API
    if (!authStore.user && authStore.token) {
        await authStore.fetchUser();
    }

    // Check if route requires authentication
    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!authStore.isAuthenticated) {
            next({ name: 'login', query: { redirect: to.fullPath } });
            return;
        }

        // Check if route requires specific role
        if (to.matched.some(record => record.meta.requiresRole)) {
            const requiredRole = to.matched.find(record => record.meta.requiresRole)?.meta.requiresRole;
            if (requiredRole && authStore.user?.user_type !== requiredRole) {
                next({ name: 'admin-dashboard' });
                return;
            }
        }

        next();
    }
    // Check if route requires guest (not authenticated)
    else if (to.matched.some(record => record.meta.requiresGuest)) {
        if (authStore.isAuthenticated) {
            next({ name: 'admin-dashboard' });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;