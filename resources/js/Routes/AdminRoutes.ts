import { RouteRecordRaw } from 'vue-router';
import geographyRoutes from './GeographyRoutes';
import milkCenterRoutes from './MilkCenterRoutes';
import farmerRoutes from './FarmerRoutes';
import cowRoutes from './CowRoutes';
import milkOperationsRoutes from './MilkOperationsRoutes';
import userRoutes from './UserRoutes';

// Prefix all routes with /admin and wrap in AdminLayout
const prefixRoutes = (routes: RouteRecordRaw[], prefix: string): RouteRecordRaw[] => {
    return routes.map(route => ({
        ...route,
        path: route.path.startsWith('/') 
            ? `${prefix}${route.path}` 
            : `${prefix}/${route.path}`,
        name: route.name ? `admin-${String(route.name)}` : undefined,
        meta: { ...route.meta, requiresAuth: true },
    }));
};

const adminRoutes: RouteRecordRaw[] = [
    {
        path: '/admin',
        name: 'admin',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'admin-dashboard',
                component: () => import('../views/admin/DashboardView.vue'),
                meta: { requiresAuth: true },
            },
            ...prefixRoutes(geographyRoutes, '/admin'),
            ...prefixRoutes(milkCenterRoutes, '/admin'),
            ...prefixRoutes(farmerRoutes, '/admin'),
            ...prefixRoutes(cowRoutes, '/admin'),
            ...prefixRoutes(milkOperationsRoutes, '/admin'),
            ...prefixRoutes(userRoutes, '/admin'),
        ],
    },
];

export default adminRoutes;

