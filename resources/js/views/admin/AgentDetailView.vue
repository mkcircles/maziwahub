<template>
    <div class="space-y-6">
        <div v-if="loading" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">Loading agent details...
        </div>
        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
        <div v-else-if="!agent" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">Agent not found.
        </div>

        <template v-else>
            <!-- Banner Section -->
            <div
                class="relative overflow-hidden rounded-xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 px-6 py-8 sm:px-8 sm:py-10 text-white shadow-lg mb-6">
                <div
                    class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(76,201,240,0.15),transparent_60%)] opacity-90">
                </div>
                <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex flex-col gap-4 flex-1">
                        <router-link :to="backRoute"
                            class="inline-flex items-center gap-1.5 self-start rounded-full bg-white/10 hover:bg-white/20 border border-white/10 px-3 py-1.5 text-xs font-semibold text-white transition duration-200">
                            <Icon icon="mdi:arrow-left" :size="14" />
                            Back to Agents
                        </router-link>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.4em] text-white/60">
                                Agent Profile
                            </p>
                            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl mt-1.5">
                                {{ agentName }}
                            </h1>
                            <p class="mt-2 text-xs text-white/70 flex flex-wrap gap-x-4 gap-y-1 items-center font-medium">
                                <span class="flex items-center gap-1">
                                    <Icon icon="mdi:email-outline" :size="14" class="text-white/60" />
                                    {{ agent.user?.email ?? '—' }}
                                </span>
                                <span class="hidden sm:inline text-white/30">|</span>
                                <span class="flex items-center gap-1">
                                    <Icon icon="mdi:phone-outline" :size="14" class="text-white/60" />
                                    {{ agent.phone ?? '—' }}
                                </span>
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[10px] sm:text-xs">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-semibold"
                                :class="agent.is_active ? 'bg-emerald-500/20 text-emerald-100 border border-emerald-500/30' : 'bg-white/10 text-white/80 border border-white/10'">
                                <Icon :icon="agent.is_active ? 'mdi:check-circle-outline' : 'mdi:close-circle-outline'"
                                    :size="14" />
                                {{ agent.is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span v-if="agent.milkCollectionCenter"
                                class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                <Icon icon="mdi:store-outline" :size="14" />
                                MCC: {{ agent.milkCollectionCenter.name }}
                            </span>
                            <span v-if="agent.partner"
                                class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                <Icon icon="mdi:domain" :size="14" />
                                Partner: {{ agent.partner.name }}
                            </span>
                            <span v-if="agent.address"
                                class="inline-flex items-center gap-1 rounded-full bg-white/10 border border-white/10 px-2.5 py-0.5 font-semibold text-white/80">
                                <Icon icon="mdi:map-marker-outline" :size="14" />
                                {{ agent.address }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 sm:items-end">
                        <div class="flex items-center gap-2">
                            <button
                                class="inline-flex items-center gap-1.5 rounded bg-white/10 hover:bg-white/20 border border-white/10 px-3.5 py-1.5 text-xs font-bold text-white transition duration-200"
                                @click="refresh" :disabled="loading">
                                <Icon icon="mdi:refresh" :size="14" />
                                Refresh
                            </button>
                            <button
                                class="inline-flex items-center gap-1.5 rounded px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition duration-200"
                                :class="agent?.is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                                @click="toggleActive" :disabled="loading">
                                <Icon :icon="agent?.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'"
                                    :size="14" />
                                {{ agent?.is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                        <div class="text-[10px] font-semibold text-white/50">
                            Registered {{ formatDate(agent.created_at) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats grid -->
            <div v-if="stats" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-4">
                <div class="rounded-lg bg-emerald-50/50 p-5 shadow border border-emerald-100">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Farmers Registered</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-900">{{ stats.total_farmers_registered }}</p>
                </div>
                <div class="rounded-lg bg-amber-50/50 p-5 shadow border border-amber-100">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Cows Registered</p>
                    <p class="mt-2 text-2xl font-bold text-amber-900">{{ stats.total_cows_registered }}</p>
                </div>
                <div class="rounded-lg bg-blue-50/50 p-5 shadow border border-blue-100">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Milk Productions</p>
                    <p class="mt-2 text-2xl font-bold text-blue-900">{{ stats.total_milk_productions_recorded }}</p>
                </div>
                <div class="rounded-lg bg-violet-50/50 p-5 shadow border border-violet-100">
                    <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Milk Deliveries</p>
                    <p class="mt-2 text-2xl font-bold text-violet-900">{{ stats.total_milk_deliveries_recorded }}</p>
                </div>
            </div>

            <AgentHistoricalChart v-if="stats?.historical_stats" :historical-data="stats.historical_stats"
                class="mb-4" />
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import AgentHistoricalChart from '../../components/admin/AgentHistoricalChart.vue';
import { useAgentStore } from '../../stores/agentStore';

const route = useRoute();
const agentStore = useAgentStore();

const agent = ref<any>(null);
const stats = ref<any>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const agentName = computed(() => agent.value?.user?.name ?? agent.value?.name ?? 'Agent');

const backRoute = computed(() => {
    return route.path.startsWith('/partner') ? '/partner/agents' : '/admin/agents';
});

const agentId = computed(() => Number(route.params.id));

const fetchAgent = async () => {
    if (!agentId.value || Number.isNaN(agentId.value)) {
        error.value = 'Invalid agent identifier.';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const result = await agentStore.fetchAgent(agentId.value);
        agent.value = result;
        const statsResult = await agentStore.fetchAgentStats(agentId.value);
        stats.value = statsResult;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load agent.';
        agent.value = null;
        stats.value = null;
    } finally {
        loading.value = false;
    }
};

const refresh = async () => {
    await fetchAgent();
};

const toggleActive = async () => {
    if (!agent.value) return;
    try {
        loading.value = true;
        // Assuming updateAgent logic handles boolean toggle for active status on the backend
        // If not, we might need a specific action. Using update for now.
        const updated = await agentStore.updateAgent(agent.value.id, { is_active: !agent.value.is_active } as any);
        agent.value = updated;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to update agent status.';
    } finally {
        loading.value = false;
    }
};

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '—';
    return date.toLocaleDateString();
};

onMounted(() => {
    fetchAgent();
});
</script>
