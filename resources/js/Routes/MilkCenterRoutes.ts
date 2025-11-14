import { RouteRecordRaw } from 'vue-router';
import MilkCollectionCentersView from '../views/milk-centers/MilkCollectionCentersView.vue';
import MilkCollectionCenterDetailView from '../views/milk-centers/MilkCollectionCenterDetailView.vue';

const milkCenterRoutes: RouteRecordRaw[] = [
    {
        path: '/milk-collection-centers',
        name: 'milk-collection-centers',
        component: () => MilkCollectionCentersView,
    },
    {
        path: '/milk-collection-centers/:id',
        name: 'milk-collection-center-detail',
        component: () => MilkCollectionCenterDetailView,
    },
];

export default milkCenterRoutes;

