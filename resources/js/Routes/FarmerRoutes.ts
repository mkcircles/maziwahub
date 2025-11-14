import { RouteRecordRaw } from 'vue-router';
import FarmersView from '../views/farmers/FarmersView.vue';
import FarmerDetailView from '../views/farmers/FarmerDetailView.vue';

const farmerRoutes: RouteRecordRaw[] = [
    {
        path: '/farmers',
        name: 'farmers',
        component: () => FarmersView,
    },
    {
        path: '/farmers/:id',
        name: 'farmer-detail',
        component: () => FarmerDetailView,
    },
];

export default farmerRoutes;

