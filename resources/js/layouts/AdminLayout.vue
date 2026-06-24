<template>
    <div class="relative flex min-h-screen bg-surface-100 text-surface-800 font-sans">
        <!-- Desktop Sidebar (Ynex Dark Theme Sidebar) -->
        <aside
            class="relative z-30 hidden w-72 flex-col bg-[#111c43] text-surface-200 shadow-xl transition-all duration-300 xl:flex">
            <div class="flex h-16 items-center px-6 border-b border-white/10">
                <router-link to="/admin/dashboard"
                    class="flex items-center gap-3 text-xl font-bold tracking-tight text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded bg-primary-600 text-white shadow-md shadow-primary-600/30">
                        <Icon icon="mdi:chart-box-outline" :size="20" />
                    </span>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-primary-100 font-extrabold">YieldTech</span>
                </router-link>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin scrollbar-thumb-white/10">
                <p class="px-3 pb-2 pt-2 text-[10px] font-bold uppercase tracking-wider text-surface-400">
                    Main Menu
                </p>

                <router-link v-for="link in navLinks" :key="link.path" :to="link.path"
                    :class="[getLinkClasses(link.path)]" @click="handleNavClick">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded transition-colors duration-200"
                        :class="getIconWrapperClass(link.path)">
                        <Icon :icon="link.icon" :size="18" />
                    </span>
                    <span class="ml-3 text-sm font-medium transition-colors duration-200">
                        {{ link.label }}
                    </span>
                </router-link>
            </nav>
        </aside>

        <!-- Mobile Sidebar Overlay & Navigation -->
        <Transition enter-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="showMobileNav" class="fixed inset-0 z-40 bg-surface-900/60 backdrop-blur-sm xl:hidden"
                @click="showMobileNav = false"></div>
        </Transition>
        <Transition enter-active-class="transition-transform duration-300 ease-out" enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0" leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0" leave-to-class="-translate-x-full">
            <aside v-show="showMobileNav"
                class="fixed inset-y-0 left-0 z-50 w-72 flex-col bg-[#111c43] text-surface-200 shadow-2xl xl:hidden flex">
                <div class="flex h-16 items-center justify-between px-6 border-b border-white/10">
                    <router-link to="/admin/dashboard"
                        class="flex items-center gap-3 text-xl font-bold tracking-tight text-white">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded bg-primary-600 text-white">
                            <Icon icon="mdi:chart-box-outline" :size="20" />
                        </span>
                        YieldTech
                    </router-link>
                    <button class="p-2 text-surface-400 hover:text-white hover:bg-white/10 rounded"
                        @click="showMobileNav = false">
                        <Icon icon="mdi:close" :size="20" />
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto px-4 py-4">
                    <router-link v-for="link in navLinks" :key="link.path" :to="link.path"
                        :class="[getLinkClasses(link.path)]" @click="handleNavClick">
                        <span class="flex h-7 w-7 items-center justify-center rounded"
                            :class="getIconWrapperClass(link.path)">
                            <Icon :icon="link.icon" :size="18" />
                        </span>
                        <span class="ml-3 text-sm font-medium">
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
                        class="p-2 text-surface-500 hover:text-primary-600 hover:bg-surface-100 rounded xl:hidden transition-colors"
                        @click="showMobileNav = true">
                        <Icon icon="mdi:menu" :size="24" />
                    </button>
                    
                    <!-- Breadcrumbs component -->
                    <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-surface-400 uppercase tracking-wider">
                        <router-link to="/admin/dashboard" class="hover:text-primary-600 transition-colors">Admin</router-link>
                        <Icon icon="mdi:chevron-right" :size="14" />
                        <span class="text-surface-700 font-bold">{{ currentPageName }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-5">
                    <div class="hidden sm:flex items-center relative">
                        <Icon icon="mdi:magnify" class="absolute left-3 text-surface-400" :size="18" />
                        <input type="text" placeholder="Search..."
                            class="pl-9 pr-4 py-1.5 bg-surface-100 border-transparent rounded text-xs focus:bg-white focus:border-primary-300 focus:ring-2 focus:ring-primary-100 transition-all w-48" />
                    </div>

                    <button
                        class="relative p-2 text-surface-500 hover:text-primary-600 hover:bg-surface-100 rounded-full transition-colors">
                        <Icon icon="mdi:bell-outline" :size="20" />
                        <span
                            class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <div class="h-8 w-px bg-surface-200 mx-1"></div>

                    <!-- Profile Dropdown -->
                    <div class="flex items-center gap-3">
                        <router-link to="/admin/profile" class="hidden sm:flex flex-col items-end group cursor-pointer">
                            <span
                                class="text-xs font-bold text-surface-800 group-hover:text-primary-600 transition-colors">{{
                                    authStore.user?.name ?? 'Administrator' }}</span>
                            <span class="text-[10px] text-surface-400 font-semibold uppercase tracking-wider capitalize">{{ authStore.user?.user_type ?? 'user'
                                }}</span>
                        </router-link>
                        <router-link to="/admin/profile"
                            class="h-9 w-9 rounded bg-primary-100 text-primary-600 flex items-center justify-center font-bold shadow-sm hover:bg-primary-200 transition-colors">
                            {{ (authStore.user?.name ?? 'A').charAt(0).toUpperCase() }}
                        </router-link>
                        <button @click="handleLogout" title="Logout"
                            class="p-2 text-surface-400 hover:text-red-500 transition-colors rounded hover:bg-red-50">
                            <Icon icon="mdi:logout" :size="18" />
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-surface-100 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto w-full max-w-7xl">
                    <router-view v-slot="{ Component, route }">
                        <transition name="fade" mode="out-in">
                            <component :is="Component" :key="route.path" />
                        </transition>
                    </router-view>
                </div>
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
    tone: string;
    helper?: string;
    badge?: string;
};

const currentPageName = computed(() => {
    const matchedLink = navLinks.value.find(link => route.path.startsWith(link.path));
    return matchedLink ? matchedLink.label : 'Overview';
});

const navLinks = computed<NavLink[]>(() => {
    const links: NavLink[] = [
        { path: '/admin/dashboard', label: 'Dashboard', icon: 'mdi:view-dashboard-outline', tone: 'sky' },
    ];

    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/countries',
            label: 'Geography',
            icon: 'mdi:earth',
            tone: 'emerald',
        });
    }

    if (isSuperAdmin.value || isAdmin.value || isPartner.value) {
        links.push({
            path: '/admin/milk-collection-centers',
            label: 'Milk Centers',
            icon: 'mdi:storefront-outline',
            tone: 'amber',
        });
    }

    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/partners',
            label: 'Partners',
            icon: 'mdi:handshake',
            tone: 'cyan',
        });
    }

    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/partner-claims',
            label: 'MCC Claims',
            icon: 'mdi:handshake-outline',
            tone: 'cyan',
        });
    }

    if (!authStore.user?.user_type || authStore.user.user_type !== 'user') {
        links.push({
            path: '/admin/farmers',
            label: 'Farmers',
            icon: 'mdi:account-group-outline',
            tone: 'rose',
        });
    }

    links.push({
        path: '/admin/cows',
        label: 'Cows',
        icon: 'mdi:cow',
        tone: 'violet',
    });

    if (isSuperAdmin.value || isAdmin.value) {
        links.push({
            path: '/admin/vets',
            label: 'Vets',
            icon: 'mdi:medical-bag',
            tone: 'indigo',
        });
    }

    if (isSuperAdmin.value || isAdmin.value || isPartner.value || isMcc.value) {
        links.push({
            path: '/admin/milk-deliveries',
            label: 'Deliveries',
            icon: 'mdi:truck-delivery-outline',
            tone: 'sky',
        });
    }

    if (isSuperAdmin.value || isAdmin.value || isPartner.value || isMcc.value) {
        links.push({
            path: '/admin/agents',
            label: 'Agents',
            icon: 'mdi:account-tie-outline',
            tone: 'indigo',
        });
    }

    if (canManageUsers.value) {
        links.push({
            path: '/admin/users',
            label: 'Users',
            icon: 'mdi:shield-account-outline',
            tone: 'slate',
        });
    }

    return links;
});

const getLinkClasses = (path: string) => {
    const base = 'group mb-1.5 flex items-center rounded px-3 py-2 transition-all duration-200';
    const active = 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-600/20';
    const inactive = 'text-surface-300 hover:bg-white/5 hover:text-white';

    const matches = route.path === path || route.path.startsWith(`${path}/`);
    return `${base} ${matches ? active : inactive}`;
};

const getIconWrapperClass = (path: string) => {
    const matches = route.path === path || route.path.startsWith(`${path}/`);
    return matches ? 'text-white' : 'text-surface-400 group-hover:text-white';
};

const handleNavClick = () => {
    showMobileNav.value = false;
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

