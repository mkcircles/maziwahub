<template>
    <div class="relative flex min-h-screen bg-surface-50 text-surface-800 font-sans">
        <!-- Desktop Sidebar -->
        <aside
            class="relative z-30 hidden w-72 flex-col border-r border-surface-200 bg-white shadow-sm transition-all duration-300 lg:flex">
            <div class="flex h-16 items-center px-6 border-b border-surface-100">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                        <Icon icon="mdi:handshake-outline" :size="20" />
                    </span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-surface-400">Partner Hub</p>
                        <p class="text-sm font-bold text-primary-700 truncate w-40" :title="partnerTitle">
                            {{ partnerTitle }}
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-4 scrollbar-thin scrollbar-thumb-surface-200">
                <p class="px-2 pb-2 pt-2 text-xs font-semibold uppercase tracking-wider text-surface-400">
                    Workspace Menu
                </p>

                <router-link v-for="item in navigation" :key="item.path" :to="item.path"
                    class="group mb-0.5 flex items-center rounded-md px-2.5 py-1.5 transition-all duration-200"
                    :class="isActive(item.path) ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-surface-600 hover:bg-surface-50 hover:text-surface-900'">
                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-md shadow-sm transition-colors duration-200"
                        :class="isActive(item.path) ? 'bg-primary-100 text-primary-600' : 'bg-transparent text-surface-400 group-hover:bg-surface-100 group-hover:text-surface-600'">
                        <Icon :icon="item.icon" :size="16" />
                    </span>
                    <span class="ml-2.5 text-sm font-medium transition-colors duration-200">
                        {{ item.label }}
                    </span>
                    <span v-if="item.badge"
                        class="ml-auto rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-bold text-primary-700">
                        {{ item.badge }}
                    </span>
                </router-link>
            </nav>

            <div class="m-4 rounded-2xl border border-surface-200 bg-surface-50 p-4 text-xs shadow-sm">
                <p class="font-bold text-surface-800">Need a hand?</p>
                <p class="mt-1 text-surface-500 leading-relaxed">
                    Invite team members, manage centers, and coordinate from this workspace.
                </p>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header class="sticky top-0 z-20 border-b border-surface-200 bg-white/80 backdrop-blur-md shadow-sm">
                <div class="flex h-16 w-full items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <div class="space-y-0.5 hidden sm:block">
                            <h1 class="text-xl font-bold tracking-tight text-surface-900">
                                {{ pageTitle }}
                            </h1>
                            <p class="text-xs text-surface-500">
                                {{ pageSubtitle }}
                            </p>
                        </div>
                        <div class="sm:hidden flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                                <Icon icon="mdi:handshake-outline" :size="20" />
                            </div>
                            <span class="font-bold text-surface-800 truncate w-32">{{ pageTitle }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">
                        <!-- Profile Dropdown (Simplified) -->
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex flex-col items-end">
                                <span class="text-sm font-semibold text-surface-800">{{ authStore.user?.name }}</span>
                                <span class="text-xs text-surface-500 truncate max-w-[120px]">{{ partnerTitle }}</span>
                            </div>
                            <div
                                class="h-9 w-9 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold shadow-sm">
                                {{ (authStore.user?.name ?? 'P').charAt(0).toUpperCase() }}
                            </div>
                            <div class="h-6 w-px bg-surface-200 mx-1"></div>
                            <button @click="handleLogout" title="Logout"
                                class="p-2 text-surface-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                                <Icon icon="mdi:logout" :size="20" />
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-surface-50 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto w-full max-w-6xl">
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
    if (route.name === 'partner-farmers') return 'Farmer Profiles';
    return 'Partner Workspace';
});

const pageSubtitle = computed(() => {
    if (route.name === 'partner-dashboard') {
        return 'Monitor performance, deliveries, and the health of your collection network.';
    }
    if (route.name === 'partner-milk-centers') {
        return 'Manage owned centers or claim existing ones to consolidate operations.';
    }
    if (route.name === 'partner-farmers') {
        return 'Review farmer records registered to your collection centers.';
    }
    return 'Navigate your partner workspace.';
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
