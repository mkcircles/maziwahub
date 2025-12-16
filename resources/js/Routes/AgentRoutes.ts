import { RouteRecordRaw } from 'vue-router';

const agentRoutes: RouteRecordRaw[] = [
    {
        path: '/agents',
        name: 'agents',
        component: () => import('../views/admin/AgentsView.vue'),
        meta: { requiresRole: ['super_admin', 'admin', 'partner', 'mcc'] },
    },
    {
        path: '/agents/:id',
        name: 'agent-detail',
        component: () => import('../views/admin/AgentDetailView.vue'),
        meta: { requiresRole: ['super_admin', 'admin', 'partner', 'mcc'] },
    },
];

export default agentRoutes;
