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
                path: 'milk-centers/:id',
                name: 'partner-milk-center-detail',
                component: () => import('../views/milk-centers/MilkCollectionCenterDetailView.vue'),
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
            {
                path: 'farmers/:id',
                name: 'partner-farmer-detail',
                component: () => import('../views/farmers/FarmerDetailView.vue'),
                meta: {
                    requiresAuth: true,
                    requiresRole: ['partner'],
                    layout: 'partner',
                },
            },
            {
                path: 'agents',
                name: 'partner-agents',
                component: () => import('../views/admin/AgentsView.vue'),
                meta: {
                    requiresAuth: true,
                    requiresRole: ['partner'],
                    layout: 'partner',
                },
            },
            {
                path: 'agents/:id',
                name: 'partner-agent-detail',
                component: () => import('../views/admin/AgentDetailView.vue'),
                meta: {
                    requiresAuth: true,
                    requiresRole: ['partner'],
                    layout: 'partner',
                },
            },
            {
                path: 'profile',
                name: 'partner-profile',
                component: () => import('../views/profile/ProfileView.vue'),
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



