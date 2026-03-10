<template>
    <div class="relative flex min-h-screen bg-surface-50 text-surface-800 font-sans">
        <!-- Desktop Sidebar -->
        <aside
            class="relative z-30 hidden w-72 flex-col border-r border-surface-200 bg-white shadow-sm transition-all duration-300 xl:flex">
            <div class="flex h-16 items-center px-6 border-b border-surface-100">
                <router-link to="/admin/dashboard"
                    class="flex items-center gap-3 text-xl font-bold tracking-tight text-primary-600">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                        <Icon icon="mdi:chart-box-outline" :size="20" />
                    </span>
                    Yield Tech
                </router-link>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin scrollbar-thumb-surface-200">
                <p class="px-2 pb-2 pt-2 text-xs font-semibold uppercase tracking-wider text-surface-400">
                    Main Menu
                </p>

                <router-link v-for="link in navLinks" :key="link.path" :to="link.path"
                    :class="[getLinkClasses(link.path)]" @click="handleNavClick">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-md shadow-sm transition-colors duration-200"
                        :class="getIconWrapperClass(link.path)">
                        <Icon :icon="link.icon" :size="16" />
                    </span>
                    <span class="ml-2.5 text-sm font-medium transition-colors duration-200"
                        :class="getTextClass(link.path)">
                        {{ link.label }}
                    </span>
                </router-link>
            </nav>
        </aside>

        <!-- Mobile Sidebar Overlay & Navigation -->
        <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="showMobileNav" class="fixed inset-0 z-40 bg-surface-900/50 backdrop-blur-sm xl:hidden"
                @click="showMobileNav = false"></div>
        </Transition>
        <Transition enter-active-class="transition-transform duration-300 ease-out" enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0" leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0" leave-to-class="-translate-x-full">
            <aside v-show="showMobileNav"
                class="fixed inset-y-0 left-0 z-50 w-72 flex-col bg-white shadow-xl xl:hidden flex">
                <div class="flex h-16 items-center justify-between px-6 border-b border-surface-100">
                    <router-link to="/admin/dashboard"
                        class="flex items-center gap-3 text-xl font-bold tracking-tight text-primary-600">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                            <Icon icon="mdi:chart-box-outline" :size="20" />
                        </span>
                        Maziwa Hub
                    </router-link>
                    <button class="p-2 text-surface-500 hover:text-surface-700 hover:bg-surface-100 rounded-lg"
                        @click="showMobileNav = false">
                        <Icon icon="mdi:close" :size="20" />
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto px-4 py-4">
                    <router-link v-for="link in navLinks" :key="link.path" :to="link.path"
                        :class="[getLinkClasses(link.path)]" @click="handleNavClick">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md shadow-sm"
                            :class="getIconWrapperClass(link.path)">
                            <Icon :icon="link.icon" :size="16" />
                        </span>
                        <span class="ml-2.5 text-sm font-medium" :class="getTextClass(link.path)">
                            {{ link.label }}
                        </span>
                    </router-link>
                </nav>
            </aside>
        </Transition>

        <!-- Main Content Wrapper -->
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header
                class="sticky top-0 z-20 flex h-16 items-center justify-between bg-white/80 backdrop-blur-md px-4 sm:px-6 lg:px-8 border-b border-surface-200 shadow-sm">
                <div class="flex items-center gap-4">
                    <button
                        class="p-2 text-surface-500 hover:text-primary-600 hover:bg-surface-100 rounded-lg xl:hidden transition-colors"
                        @click="showMobileNav = true">
                        <Icon icon="mdi:menu" :size="24" />
                    </button>
                    <!-- Search Bar (Placeholder for Modern Feel) -->
                    <div class="hidden sm:flex items-center relative">
                        <Icon icon="mdi:magnify" class="absolute left-3 text-surface-400" :size="20" />
                        <input type="text" placeholder="Search..."
                            class="pl-10 pr-4 py-2 bg-surface-100 border-transparent rounded-full text-sm focus:bg-white focus:border-primary-300 focus:ring-2 focus:ring-primary-100 transition-all w-64" />
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">
                    <button
                        class="relative p-2 text-surface-500 hover:text-primary-600 hover:bg-surface-100 rounded-full transition-colors">
                        <Icon icon="mdi:bell-outline" :size="22" />
                        <span
                            class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <div class="h-8 w-px bg-surface-200 mx-1"></div>

                    <!-- Profile Dropdown (Simplified) -->
                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div class="hidden sm:flex flex-col items-end">
                            <span
                                class="text-sm font-semibold text-surface-800 group-hover:text-primary-600 transition-colors">{{
                                    authStore.user?.name ?? 'Administrator' }}</span>
                            <span class="text-xs text-surface-500 capitalize">{{ authStore.user?.user_type ?? 'user'
                                }}</span>
                        </div>
                        <div
                            class="h-9 w-9 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold shadow-sm">
                            {{ (authStore.user?.name ?? 'A').charAt(0).toUpperCase() }}
                        </div>
                        <button @click="handleLogout" title="Logout"
                            class="p-2 text-surface-400 hover:text-red-500 transition-colors">
                            <Icon icon="mdi:logout" :size="20" />
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-surface-50 p-4 sm:p-6 lg:p-8">
                <router-view v-slot="{ Component, route }">
                    <transition name="fade" mode="out-in">
                        <component :is="Component" :key="route.path" />
                    </transition>
                </router-view>
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import { usePermissions } from '../composables/usePermissions';
import Icon from '../components/shared/Icon.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { isSuperAdmin, isAdmin, isPartner, isMcc, canManageUsers } = usePermissions();
const showMobileNav = ref(false);

type NavLink = {
    path: string;
    label: string;
    icon: string;
    tone: keyof typeof toneClasses;
    helper?: string;
    badge?: string;
};

const navLinks = computed<NavLink[]>(() => {
    const links: NavLink[] = [
        { path: '/admin/dashboard', label: 'Dashboard', icon: 'mdi:view-dashboard-outline', tone: 'sky', helper: 'Operations pulse', badge: 'Home' },
    ];

    // Geography - visible to all
    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/countries',
            label: 'Geography',
            icon: 'mdi:earth',
            tone: 'emerald',
            helper: 'Regions & coverage',
        });
    }

    // Milk Centers
    if (isSuperAdmin.value || isAdmin.value || isPartner.value) {
        links.push({
            path: '/admin/milk-collection-centers',
            label: 'Milk Centers',
            icon: 'mdi:storefront-outline',
            tone: 'amber',
            helper: 'Infrastructure',
        });
    }

    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/partners',
            label: 'Partners',
            icon: 'mdi:handshake',
            tone: 'cyan',
            helper: 'Onboard & monitor',
        });
    }

    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/partner-claims',
            label: 'MCC Claims',
            icon: 'mdi:handshake-outline',
            tone: 'cyan',
            helper: 'Review requests',
        });
    }

    // Farmers - visible to all except basic users
    if (!authStore.user?.user_type || authStore.user.user_type !== 'user') {
        links.push({
            path: '/admin/farmers',
            label: 'Farmers',
            icon: 'mdi:account-group-outline',
            tone: 'rose',
            helper: 'Community registry',
        });
    }

    // Cows
    links.push({
        path: '/admin/cows',
        label: 'Cows',
        icon: 'mdi:cow',
        tone: 'violet',
        helper: 'Livestock data',
    });

    // Vets
    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/vets',
            label: 'Vets',
            icon: 'mdi:medical-bag',
            tone: 'indigo',
            helper: 'Care network',
        });
    }

    // Milk Deliveries
    if (isSuperAdmin.value || isAdmin.value || isPartner.value || isMcc.value) {
        links.push({
            path: '/admin/milk-deliveries',
            label: 'Deliveries',
            icon: 'mdi:truck-delivery-outline',
            tone: 'sky',
            helper: 'Logistics & payouts',
        });
    }

    // Agents
    if (isSuperAdmin.value || isAdmin.value || isPartner.value || isMcc.value) {
        links.push({
            path: '/admin/agents',
            label: 'Agents',
            icon: 'mdi:account-tie-outline',
            tone: 'indigo',
            helper: 'Field agents',
        });
    }

    // User Management - super_admin only
    if (canManageUsers.value) {
        links.push({
            path: '/admin/users',
            label: 'Users',
            icon: 'mdi:shield-account-outline',
            tone: 'slate',
            helper: 'Access controls',
        });
    }

    return links;
});

const getLinkClasses = (path: string) => {
    const base = 'group mb-0.5 flex items-center rounded-md px-2.5 py-1.5 transition-all duration-200';
    const active = 'bg-primary-50 text-primary-700 font-semibold';
    const inactive = 'text-surface-600 hover:bg-surface-50 hover:text-surface-900';

    const matches = route.path === path || route.path.startsWith(`${path}/`);
    return `${base} ${matches ? active : inactive}`;
};

const getIconWrapperClass = (path: string) => {
    const matches = route.path === path || route.path.startsWith(`${path}/`);
    return matches ? 'bg-primary-100 text-primary-600' : 'bg-transparent text-surface-400 group-hover:bg-surface-100 group-hover:text-surface-600';
};

const getTextClass = (path: string) => {
    const matches = route.path === path || route.path.startsWith(`${path}/`);
    return matches ? 'text-primary-700' : 'text-surface-600 group-hover:text-surface-900';
};

const handleNavClick = () => {
    showMobileNav.value = false;
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};

const toneClasses: Record<string, string> = {
    sky: 'text-sky-500',
    emerald: 'text-emerald-500',
    amber: 'text-amber-500',
    rose: 'text-rose-500',
    violet: 'text-violet-500',
    indigo: 'text-indigo-500',
    cyan: 'text-cyan-500',
    slate: 'text-slate-500',
    default: 'text-surface-500',
};
</script>
