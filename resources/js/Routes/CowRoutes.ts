import { RouteRecordRaw } from 'vue-router';

const cowRoutes: RouteRecordRaw[] = [
    {
        path: '/cows',
        name: 'cows',
        component: () => import('../views/cows/CowsView.vue'),
    },
    {
        path: '/cows/:id',
        name: 'cow-detail',
        component: () => import('../views/cows/CowDetailView.vue'),
    },
];

export default cowRoutes;

