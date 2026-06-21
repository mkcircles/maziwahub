import { RouteRecordRaw } from 'vue-router';
import LoginView from '../views/auth/LoginView.vue';
import RegisterView from '../views/auth/RegisterView.vue';
import AcceptInvitationView from '../views/auth/AcceptInvitationView.vue';

const authRoutes: RouteRecordRaw[] = [
    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: { requiresGuest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: RegisterView,
        meta: { requiresGuest: true },
    },
    {
        path: '/accept-invitation/:token',
        name: 'accept-invitation',
        component: AcceptInvitationView,
        meta: { requiresGuest: true },
    },
];

export default authRoutes;

