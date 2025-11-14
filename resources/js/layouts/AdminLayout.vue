<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <router-link to="/admin/dashboard" class="flex items-center px-4 text-gray-900 font-semibold">
                            Maziwa Hub
                        </router-link>
                    </div>
                    <div class="flex items-center space-x-4">
                        <router-link
                            v-for="link in navLinks"
                            :key="link.path"
                            :to="link.path"
                            class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md"
                        >
                            {{ link.label }}
                        </router-link>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-700">{{ authStore.user?.name }}</span>
                            <button
                                @click="handleLogout"
                                class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <router-view />
        </main>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import { usePermissions } from '../composables/usePermissions';

const router = useRouter();
const authStore = useAuthStore();
const { isSuperAdmin, isAdmin, isPartner, isMcc, canManageUsers } = usePermissions();

const navLinks = computed(() => {
    const links: Array<{ path: string; label: string }> = [
        { path: '/admin/dashboard', label: 'Dashboard' },
    ];

    // Geography - visible to all
    if (isSuperAdmin.value || isAdmin.value) {
        links.push({ path: '/admin/countries', label: 'Geography' });
    }

    // Milk Centers
    if (isSuperAdmin.value || isAdmin.value || isPartner.value) {
        links.push({ path: '/admin/milk-collection-centers', label: 'Milk Centers' });
    }

    // Farmers - visible to all except basic users
    if (!authStore.user?.user_type || authStore.user.user_type !== 'user') {
        links.push({ path: '/admin/farmers', label: 'Farmers' });
    }

    // Cows
    links.push({ path: '/admin/cows', label: 'Cows' });

    // Milk Deliveries
    if (isSuperAdmin.value || isAdmin.value || isPartner.value || isMcc.value) {
        links.push({ path: '/admin/milk-deliveries', label: 'Deliveries' });
    }

    // User Management - super_admin only
    if (canManageUsers.value) {
        links.push({ path: '/admin/users', label: 'Users' });
    }

    return links;
});

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

