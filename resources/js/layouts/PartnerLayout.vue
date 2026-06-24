<template>
    <div class="relative flex min-h-screen bg-surface-100 text-surface-800 font-sans">
        <!-- Desktop Sidebar (Ynex Dark Theme Sidebar) -->
        <aside
            class="relative z-30 hidden w-72 flex-col bg-[#111c43] text-surface-200 shadow-xl transition-all duration-300 lg:flex">
            <div class="flex h-16 items-center px-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded bg-primary-600 text-white shadow-md shadow-primary-600/30">
                        <Icon icon="mdi:handshake-outline" :size="20" />
                    </span>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-surface-400">Partner Hub</p>
                        <p class="text-xs font-bold text-white truncate w-40" :title="partnerTitle">
                            {{ partnerTitle }}
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin scrollbar-thumb-white/10">
                <p class="px-3 pb-2 pt-2 text-[10px] font-bold uppercase tracking-wider text-surface-400">
                    Workspace Menu
                </p>

                <router-link v-for="item in navigation" :key="item.path" :to="item.path"
                    class="group mb-1.5 flex items-center rounded px-3 py-2 transition-all duration-200"
                    :class="isActive(item.path) ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-600/20' : 'text-surface-300 hover:bg-white/5 hover:text-white'">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded transition-colors duration-200"
                        :class="isActive(item.path) ? 'text-white' : 'text-surface-400 group-hover:text-white'">
                        <Icon :icon="item.icon" :size="18" />
                    </span>
                    <span class="ml-3 text-sm font-medium transition-colors duration-200">
                        {{ item.label }}
                    </span>
                    <span v-if="item.badge"
                        class="ml-auto rounded-full bg-primary-500 px-2 py-0.5 text-[10px] font-bold text-white shadow-sm">
                        {{ item.badge }}
                    </span>
                </router-link>
            </nav>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header class="sticky top-0 z-20 border-b border-surface-200 bg-white/80 backdrop-blur-md shadow-sm">
                <div class="flex h-16 w-full items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <!-- Breadcrumbs component -->
                        <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-surface-400 uppercase tracking-wider">
                            <router-link to="/partner/dashboard" class="hover:text-primary-600 transition-colors">Partner</router-link>
                            <Icon icon="mdi:chevron-right" :size="14" />
                            <span class="text-surface-700 font-bold">{{ pageTitle }}</span>
                        </div>
                        <div class="md:hidden flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded bg-primary-600 text-white">
                                <Icon icon="mdi:handshake-outline" :size="18" />
                            </div>
                            <span class="font-bold text-surface-800 truncate w-32 text-sm">{{ pageTitle }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">
                        <!-- Profile Dropdown -->
                        <div class="flex items-center gap-3">
                            <router-link to="/partner/profile" class="hidden sm:flex flex-col items-end group cursor-pointer">
                                <span class="text-xs font-bold text-surface-800 group-hover:text-primary-600 transition-colors">{{ authStore.user?.name }}</span>
                                <span class="text-[10px] text-surface-400 font-semibold uppercase tracking-wider truncate max-w-[120px]">{{ partnerTitle }}</span>
                            </router-link>
                            <router-link to="/partner/profile"
                                class="h-9 w-9 rounded bg-primary-100 text-primary-600 flex items-center justify-center font-bold shadow-sm hover:bg-primary-200 transition-colors">
                                {{ (authStore.user?.name ?? 'P').charAt(0).toUpperCase() }}
                            </router-link>
                            <div class="h-6 w-px bg-surface-200 mx-1"></div>
                            <button @click="handleLogout" title="Logout"
                                class="p-2 text-surface-400 hover:text-red-500 transition-colors rounded hover:bg-red-50">
                                <Icon icon="mdi:logout" :size="18" />
                            </button>
                        </div>
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
import { computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Icon from '../components/shared/Icon.vue';
import { useAuthStore } from '../stores/authStore';
import { usePartnerStore } from '../stores/partnerStore';

const authStore = useAuthStore();
const partnerStore = usePartnerStore();
const route = useRoute();
const router = useRouter();

const navigation = computed(() =>
    [
        {
            path: '/partner/dashboard',
            label: 'Overview',
            icon: 'mdi:view-dashboard-outline',
        },
        {
            path: '/partner/milk-centers',
            label: 'Milk Centers',
            icon: 'mdi:storefront-outline',
            badge: partnerStore.activePartner?.milk_collection_centers_count
                ? partnerStore.activePartner.milk_collection_centers_count.toString()
                : undefined,
        },
        {
            path: '/partner/agents',
            label: 'Agents',
            icon: 'mdi:account-tie-outline',
        },
        {
            path: '/partner/farmers',
            label: 'Farmers',
            icon: 'mdi:account-group-outline',
        },
    ].map(item => ({
        ...item,
        badge: item.badge,
    })),
);

const partnerTitle = computed(() => partnerStore.activePartner?.name ?? authStore.user?.partner?.name ?? 'Partner');

const pageTitle = computed(() => {
    if (route.name === 'partner-dashboard') return 'Daily Insight Board';
    if (route.name === 'partner-milk-centers') return 'Milk Collection Centers';
    if (route.name === 'partner-milk-center-detail') return 'Collection Center Details';
    if (route.name === 'partner-farmers') return 'Farmer Profiles';
    if (route.name === 'partner-profile') return 'Account Settings';
    return 'Partner Workspace';
});

const isActive = (path: string) => route.path.startsWith(path);

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

onMounted(() => {
    if (!partnerStore.activePartner && authStore.user?.partner_id) {
        partnerStore.fetchPartner(authStore.user.partner_id).catch(() => {
            // Silently ignore for layout load; individual views will surface errors if needed.
        });
    }
});
</script>

