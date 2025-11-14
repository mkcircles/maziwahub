import { RouteRecordRaw } from 'vue-router';

const milkOperationsRoutes: RouteRecordRaw[] = [
    {
        path: '/milk-deliveries',
        name: 'milk-deliveries',
        component: () => import('../views/milk-operations/MilkDeliveriesView.vue'),
    },
    {
        path: '/milk-deliveries/:id',
        name: 'milk-delivery-detail',
        component: () => import('../views/milk-operations/MilkDeliveryDetailView.vue'),
    },
    {
        path: '/cow-milk-productions',
        name: 'cow-milk-productions',
        component: () => import('../views/milk-operations/CowMilkProductionsView.vue'),
    },
    {
        path: '/cow-milk-productions/:id',
        name: 'cow-milk-production-detail',
        component: () => import('../views/milk-operations/CowMilkProductionDetailView.vue'),
    },
];

export default milkOperationsRoutes;

