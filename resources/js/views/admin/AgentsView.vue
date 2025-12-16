<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-300">Agents</h1>
                <p class="text-sm text-gray-500">Manage registered agents and their assignments.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    :disabled="loading"
                    @click="fetchAgents"
                >
                    <Icon icon="mdi:refresh" :size="18" />
                    Refresh
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="openCreateModal"
                >
                    <Icon icon="mdi:account-plus" :size="18" />
                    Add Agent
                </button>
            </div>
        </div>

        <div v-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
        
        <AgentsTable 
            :agents="agents" 
            :loading="loading" 
            @edit="openEditModal" 
        />
        
        <CreateAgentModal 
            :is-open="showCreateModal" 
            @close="closeCreateModal" 
            @created="handleAgentCreated" 
        />
    </div>

</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { storeToRefs } from 'pinia';
import Icon from '../../components/shared/Icon.vue';
import { useAgentStore } from '../../stores/agentStore';
import AgentsTable from '../../components/agents/AgentsTable.vue';
import CreateAgentModal from '../../components/agents/CreateAgentModal.vue';

const agentStore = useAgentStore();
const { agents, loading, error } = storeToRefs(agentStore);

const showCreateModal = ref(false);

const fetchAgents = async () => {
    await agentStore.fetchAgents();
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const handleAgentCreated = async () => {
    await fetchAgents();
};

const openEditModal = (agent: any) => {
    console.log('Open edit modal', agent);
};

onMounted(() => {
    fetchAgents();
});
</script>
