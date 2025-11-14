import { RouteRecordRaw } from 'vue-router';
import UsersView from '../views/users/UsersView.vue';
import UserDetailView from '../views/users/UserDetailView.vue';

const userRoutes: RouteRecordRaw[] = [
    {
        path: '/users',
        name: 'users',
        component: UsersView,
        meta: { requiresAuth: true, requiresRole: 'super_admin' },
    },
    {
        path: '/users/:id',
        name: 'user-detail',
        component: UserDetailView,
        meta: { requiresAuth: true, requiresRole: 'super_admin' },
    },
];

export default userRoutes;

