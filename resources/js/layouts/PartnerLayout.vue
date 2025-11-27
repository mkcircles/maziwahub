<template>
    <div class="flex min-h-screen bg-slate-50 text-slate-900">
        <aside
            class="hidden w-72 flex-shrink-0 flex-col border-r border-slate-200/70 bg-[#0F172A] px-6 py-8 text-white lg:flex"
        >
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-500/90">
                    <Icon icon="mdi:handshake-outline" :size="22" />
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-200/70">Partner Hub</p>
                    <p class="text-sm font-semibold text-white">
                        {{ partnerTitle }}
                    </p>
                </div>
            </div>

            <nav class="mt-10 space-y-2 text-sm">
                <router-link
                    v-for="item in navigation"
                    :key="item.path"
                    :to="item.path"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 transition"
                    :class="isActive(item.path) ? 'bg-white text-[#0F172A]' : 'text-slate-200 hover:bg-white/10'"
                >
                    <Icon :icon="item.icon" :size="18" />
                    <span class="font-medium">{{ item.label }}</span>
                    <span
                        v-if="item.badge"
                        class="ml-auto rounded-full bg-white/10 px-2 py-0.5 text-xs font-semibold text-white/80"
                    >
                        {{ item.badge }}
                    </span>
                </router-link>
            </nav>

            <div class="mt-auto rounded-2xl border border-white/10 bg-white/5 p-4 text-xs text-slate-200/80">
                <p class="font-semibold text-white">Need a hand?</p>
                <p class="mt-2 leading-relaxed">
                    Invite team members, manage your collection centers, and stay aligned with the admin team from this
                    workspace.
                </p>
            </div>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col">
            <header class="border-b border-slate-200/70 bg-white/80 backdrop-blur">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-slate-500">
                            Partner Workspace
                        </p>
                        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                            {{ pageTitle }}
                        </h1>
                        <p class="text-sm text-slate-500">
                            {{ pageSubtitle }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-500/10 text-sky-600">
                            <Icon icon="mdi:account-tie" :size="20" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ authStore.user?.name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ partnerTitle }}
                            </p>
                        </div>
                        <button
                            class="ml-4 inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-800"
                            @click="handleLogout"
                        >
                            <Icon icon="mdi:logout" :size="16" />
                            Sign out
                        </button>
                    </div>
                </div>
            </header>

            <main class="flex-1 bg-slate-50">
                <div class="mx-auto w-full max-w-6xl px-6 py-10">
                    <router-view />
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



