import type { RouteRecordRaw } from 'vue-router';

const partnerRoutes: RouteRecordRaw[] = [
    {
        path: '/partner',
        component: () => import('../layouts/PartnerLayout.vue'),
        meta: {
            requiresAuth: true,
            requiresRole: ['partner'],
        },
        children: [
            {
                path: '',
                redirect: '/partner/dashboard',
            },
            {
                path: 'dashboard',
                name: 'partner-dashboard',
                component: () => import('../views/partner/PartnerDashboardView.vue'),
                meta: {
                    requiresAuth: true,
                    requiresRole: ['partner'],
                    layout: 'partner',
                },
            },
            {
                path: 'milk-centers',
                name: 'partner-milk-centers',
                component: () => import('../views/partner/PartnerMilkCentersView.vue'),
                meta: {
                    requiresAuth: true,
                    requiresRole: ['partner'],
                    layout: 'partner',
                },
            },
            {
                path: 'farmers',
                name: 'partner-farmers',
                component: () => import('../views/partner/PartnerFarmersView.vue'),
                meta: {
                    requiresAuth: true,
                    requiresRole: ['partner'],
                    layout: 'partner',
                },
            },
        ],
    },
];

export default partnerRoutes;



