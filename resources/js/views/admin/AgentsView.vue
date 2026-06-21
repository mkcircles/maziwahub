<template>
    <div class="space-y-6">
        <div class="ynex-page-header">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-surface-900 dark:text-white">Agents</h1>
                <p class="text-xs text-surface-500 font-medium mt-1">Manage registered agents and their assignments.</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="ynex-btn-secondary py-1.5 px-3.5 text-xs flex items-center gap-1.5"
                    :disabled="loading"
                    @click="fetchAgents"
                >
                    <Icon icon="mdi:refresh" :size="16" />
                    Refresh
                </button>
                <button
                    class="ynex-btn-primary py-1.5 px-3.5 text-xs flex items-center gap-1.5 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="openCreateModal"
                >
                    <Icon icon="mdi:account-plus" :size="16" />
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
            :agent-to-edit="agentToEdit"
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
const agentToEdit = ref<any>(null);

const fetchAgents = async () => {
    await agentStore.fetchAgents();
};

const openCreateModal = () => {
    agentToEdit.value = null;
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    agentToEdit.value = null;
};

const handleAgentCreated = async () => {
    await fetchAgents();
};

const openEditModal = (agent: any) => {
    agentToEdit.value = agent;
    showCreateModal.value = true;
};

onMounted(() => {
    fetchAgents();
});
</script>
