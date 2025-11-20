import { RouteRecordRaw } from 'vue-router';

const vetRoutes: RouteRecordRaw[] = [
    {
        path: '/vets',
        name: 'vets',
        component: () => import('../views/vets/VetsView.vue'),
        meta: { requiresRole: ['super_admin', 'admin'] },
    },
    {
        path: '/vets/:id',
        name: 'vet-detail',
        component: () => import('../views/vets/VetDetailView.vue'),
        meta: { requiresRole: ['super_admin', 'admin'] },
    },
];

export default vetRoutes;
