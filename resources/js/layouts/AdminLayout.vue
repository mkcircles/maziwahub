<template>
    <div class="relative flex min-h-screen overflow-hidden bg-slate-950 text-slate-100">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-32 top-20 h-80 w-80 rounded-full bg-sky-500/30 blur-3xl"></div>
            <div class="absolute -right-16 top-64 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
        </div>

        <!-- Desktop Sidebar -->
        <aside class="relative z-30 hidden w-72 flex-col overflow-hidden border-r border-white/10 bg-gradient-to-b from-slate-950 via-slate-900/90 to-slate-950/95 md:flex">
            <div class="flex h-20 items-center px-6">
                <router-link
                    to="/admin/dashboard"
                    class="flex items-center gap-3 text-xl font-semibold tracking-tight text-white"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 via-indigo-500 to-purple-500 text-white">
                        <Icon icon="mdi:chart-box-outline" :size="22" />
                    </span>
                    Maziwa Hub
                </router-link>
            </div>
            <nav class="flex-1 overflow-y-auto px-4 pb-6">
                <p class="px-2 pb-2 pt-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-400">
                    Main Navigation
                </p>
                <router-link
                    v-for="link in navLinks"
                    :key="link.path"
                    :to="link.path"
                    :class="[
                        getLinkClasses(link.path),
                        toneClasses[link.tone] ?? toneClasses.default,
                    ]"
                    @click="handleNavClick"
                >
                    <span :class="['flex h-9 w-9 items-center justify-center rounded-xl text-white/90', toneIconClasses[link.tone] ?? toneIconClasses.default]">
                        <Icon :icon="link.icon" :size="18" />
                    </span>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold tracking-tight">
                            {{ link.label }}
                        </span>
                        <span v-if="link.helper" class="text-[11px] text-slate-300/70">
                            {{ link.helper }}
                        </span>
                    </div>
                    <span
                        v-if="link.badge"
                        :class="['ml-auto rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide', toneBadgeClasses[link.tone] ?? toneBadgeClasses.default]"
                    >
                        {{ link.badge }}
                    </span>
                </router-link>
            </nav>
            <div class="border-t border-white/10 px-6 py-6 text-sm text-slate-300/80">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500/20 via-indigo-500/20 to-purple-500/30 text-white">
                        <Icon icon="mdi:account-circle-outline" :size="22" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">
                            {{ authStore.user?.name ?? 'Administrator' }}
                        </p>
                        <p class="text-xs uppercase tracking-wide text-slate-400">
                            {{ authStore.user?.user_type ?? 'user' }}
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-slate-100 transition hover:border-white/20 hover:bg-white/10 hover:text-white"
                    @click="handleLogout"
                >
                    <Icon icon="mdi:logout-variant" :size="18" />
                    Logout
                </button>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showMobileNav" class="fixed inset-0 z-40 flex md:hidden">
                <div class="fixed inset-0 bg-slate-950/70 backdrop-blur" @click="showMobileNav = false"></div>
                <aside class="relative z-50 flex w-72 flex-col bg-gradient-to-b from-slate-950 to-slate-900/95">
                    <div class="flex h-16 items-center justify-between border-b border-white/10 px-5">
                        <router-link
                            to="/admin/dashboard"
                            class="flex items-center gap-3 text-base font-semibold text-white"
                            @click="handleNavClick"
                        >
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 via-indigo-500 to-purple-500 text-white">
                                <Icon icon="mdi:chart-box-outline" :size="20" />
                            </span>
                            Maziwa Hub
                        </router-link>
                        <button
                            type="button"
                            class="rounded-md p-2 text-slate-300 transition hover:bg-white/10 hover:text-white"
                            @click="showMobileNav = false"
                        >
                            <Icon icon="mdi:close" :size="20" />
                        </button>
                    </div>
                    <nav class="flex-1 overflow-y-auto px-5 py-4">
                        <router-link
                            v-for="link in navLinks"
                            :key="link.path"
                            :to="link.path"
                            :class="[
                                getLinkClasses(link.path, true),
                                toneClasses[link.tone] ?? toneClasses.default,
                            ]"
                            @click="handleNavClick"
                        >
                            <span :class="['flex h-9 w-9 items-center justify-center rounded-xl text-white/90', toneIconClasses[link.tone] ?? toneIconClasses.default]">
                                <Icon :icon="link.icon" :size="18" />
                            </span>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold tracking-tight">
                                    {{ link.label }}
                                </span>
                                <span v-if="link.helper" class="text-[11px] text-slate-300/70">
                                    {{ link.helper }}
                                </span>
                            </div>
                        </router-link>
                    </nav>
                    <div class="border-t border-white/10 px-5 py-5 text-sm text-slate-300/80">
                        <p class="font-semibold text-white">{{ authStore.user?.name ?? 'Administrator' }}</p>
                        <p class="text-xs uppercase tracking-wide text-slate-400">
                            {{ authStore.user?.user_type ?? 'user' }}
                        </p>
                        <button
                            type="button"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-sm font-medium text-white transition hover:border-white/25 hover:bg-white/15"
                            @click="handleLogout"
                        >
                            <Icon icon="mdi:logout-variant" :size="18" />
                            Logout
                        </button>
                    </div>
                </aside>
            </div>
        </Transition>

        <!-- Main Content -->
        <div class="relative z-20 flex min-h-screen flex-1 flex-col">
            <header class="flex items-center justify-between border-b border-white/10 bg-gradient-to-r from-slate-950 via-slate-900/80 to-slate-950/60 px-4 py-3 md:hidden">
                <button
                    type="button"
                    class="inline-flex items-center rounded-xl border border-white/10 bg-white/10 px-2 py-2 text-white transition hover:border-white/20 hover:bg-white/15"
                    @click="showMobileNav = true"
                >
                    <Icon icon="mdi:menu" :size="22" />
                </button>
                <router-link to="/admin/dashboard" class="text-base font-semibold text-white">
                    Maziwa Hub
                </router-link>
                <div class="flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-3 py-1 text-sm text-white/90">
                    <Icon icon="mdi:account-circle-outline" :size="20" />
                    <span>{{ authStore.user?.name ?? 'You' }}</span>
                </div>
            </header>

            <main class="relative flex-1 overflow-x-hidden px-4 py-8 sm:px-8 lg:px-14">
                <div class="pointer-events-none absolute -right-40 top-24 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl md:right-[-120px]"></div>
                <div class="pointer-events-none absolute bottom-20 left-1/3 hidden h-64 w-64 rounded-full bg-sky-500/10 blur-3xl lg:block"></div>
                <router-view />
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
            tone: 'cyan',
            helper: 'Logistics & payouts',
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

const getLinkClasses = (path: string, isMobile = false) => {
    const base =
        'group mb-2 flex items-center gap-3 rounded-2xl px-3.5 py-2.5 text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-white/10';
    const active =
        'border border-white/15 bg-white/10 text-white shadow-lg shadow-slate-900/50 backdrop-blur';
    const inactive =
        'border border-transparent text-slate-300 hover:border-white/10 hover:bg-white/5 hover:text-white/90';

    const matches = route.path === path || route.path.startsWith(`${path}/`);

    const spacing = isMobile ? '' : ' last:mb-0';

    return `${base} ${matches ? active : inactive}${spacing}`;
};

const handleNavClick = () => {
    showMobileNav.value = false;
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};

const toneClasses: Record<
    string,
    string
> = {
    sky: 'bg-gradient-to-r from-sky-500/10 via-slate-900/40 to-slate-900/20',
    emerald: 'bg-gradient-to-r from-emerald-500/10 via-slate-900/40 to-slate-900/25',
    amber: 'bg-gradient-to-r from-amber-500/10 via-slate-900/40 to-slate-900/25',
    rose: 'bg-gradient-to-r from-rose-500/10 via-slate-900/40 to-slate-900/25',
    violet: 'bg-gradient-to-r from-violet-500/10 via-slate-900/40 to-slate-900/25',
    indigo: 'bg-gradient-to-r from-indigo-500/10 via-slate-900/40 to-slate-900/25',
    cyan: 'bg-gradient-to-r from-cyan-500/10 via-slate-900/40 to-slate-900/25',
    slate: 'bg-gradient-to-r from-slate-500/10 via-slate-900/40 to-slate-900/25',
    default: 'bg-gradient-to-r from-white/5 via-slate-900/40 to-slate-900/25',
};

const toneIconClasses: Record<string, string> = {
    sky: 'bg-sky-500/20 shadow-sky-500/30',
    emerald: 'bg-emerald-500/20 shadow-emerald-500/30',
    amber: 'bg-amber-500/20 shadow-amber-500/30',
    rose: 'bg-rose-500/20 shadow-rose-500/30',
    violet: 'bg-violet-500/20 shadow-violet-500/30',
    indigo: 'bg-indigo-500/20 shadow-indigo-500/30',
    cyan: 'bg-cyan-500/20 shadow-cyan-500/30',
    slate: 'bg-slate-500/20 shadow-slate-500/30',
    default: 'bg-white/10 shadow-slate-500/20',
};

const toneBadgeClasses: Record<string, string> = {
    sky: 'bg-sky-500/20 text-sky-50 border border-sky-500/30',
    emerald: 'bg-emerald-500/20 text-emerald-50 border border-emerald-500/30',
    amber: 'bg-amber-500/20 text-amber-50 border border-amber-500/30',
    rose: 'bg-rose-500/20 text-rose-50 border border-rose-500/30',
    violet: 'bg-violet-500/20 text-violet-50 border border-violet-500/30',
    indigo: 'bg-indigo-500/20 text-indigo-50 border border-indigo-500/30',
    cyan: 'bg-cyan-500/20 text-cyan-50 border border-cyan-500/30',
    slate: 'bg-slate-500/20 text-slate-50 border border-slate-500/30',
    default: 'bg-white/10 text-slate-100 border border-white/15',
};
</script>

