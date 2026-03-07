<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <router-link
                    :to="backRoute"
                    class="inline-flex items-center gap-2 rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50 hover:text-surface-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to agents
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-surface-200">{{ agentName }}</h1>
                    <p class="text-sm text-surface-500">{{ agent?.email ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-50 hover:text-surface-900 transition"
                    @click="refresh"
                    :disabled="loading"
                >
                    <Icon icon="mdi:refresh" :size="18" />
                    Refresh
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm font-medium transition"
                    :class="agent?.is_active ? 'border border-surface-200 bg-surface-300 text-surface-700 hover:bg-surface-400' : 'bg-emerald-600 text-white hover:bg-emerald-700'"
                    @click="toggleActive"
                    :disabled="loading"
                >
                    <Icon :icon="agent?.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'" :size="18" />
                    {{ agent?.is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">Loading agent details...</div>
        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
        <div v-else-if="!agent" class="rounded-lg bg-white p-8 text-center text-surface-600 shadow">Agent not found.</div>
        <template v-else>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-4 rounded-lg bg-white p-6 shadow">
                    <h2 class="text-lg font-semibold text-surface-900">Profile</h2>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Full Name</dt>
                            <dd class="text-sm font-medium text-surface-900">{{ agentName }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Phone</dt>
                            <dd class="text-sm text-surface-700">{{ agent.phone ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Email</dt>
                            <dd class="text-sm text-surface-700">{{ agent.user?.email ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Address</dt>
                            <dd class="text-sm text-surface-700">{{ agent.address ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Assigned MCC</dt>
                            <dd class="text-sm text-surface-700">{{ agent.milkCollectionCenter?.name ?? 'Unassigned' }}</dd>
                        </div>
                         <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Partner Organization</dt>
                            <dd class="text-sm text-surface-700">{{ agent.partner?.name ?? 'Unassigned' }}</dd>
                        </div>
                    </dl>
                </div>

                 <div class="space-y-4 rounded-lg bg-white p-6 shadow">
                    <h2 class="text-lg font-semibold text-surface-900">System Info</h2>
                     <dl class="grid gap-4">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Registered</dt>
                            <dd class="text-sm text-surface-700">{{ formatDate(agent.created_at) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-surface-500">Status</dt>
                            <dd class="text-sm">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="agent.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-surface-200 text-surface-600'"
                                >
                                    {{ agent.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                     </dl>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import { useAgentStore } from '../../stores/agentStore';

const route = useRoute();
const agentStore = useAgentStore();

const agent = ref<any>(null);
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
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load agent.';
        agent.value = null;
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
