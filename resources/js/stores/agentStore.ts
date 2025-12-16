import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export interface Agent {
    id: number;
    name?: string; // Optional as it might come from user relation
    email?: string;
    phone?: string; // Agent specific phone
    phone_number?: string; // Legacy or alternative
    milk_collection_center_id?: number;
    milk_collection_center?: {
        id: number;
        name: string;
    };
    created_at: string;
    is_active: boolean;
    user?: {
        name: string;
        email: string;
        phone_number: string;
    };
}

export const useAgentStore = defineStore('agent', () => {
    const agents = ref<Agent[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    async function fetchAgents(params: Record<string, any> = {}) {
        loading.value = true;
        try {
            const response = await axios.get('/agents', { params });
            // Handle both paginated (response.data.data) and non-paginated (response.data) responses
            agents.value = Array.isArray(response.data) ? response.data : response.data.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch agents';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function fetchAgent(id: number) {
        loading.value = true;
        try {
            const response = await axios.get(`/agents/${id}`);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch agent details';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function createAgent(data: Partial<Agent>) {
        loading.value = true;
        try {
            const response = await axios.post('/agents', data);
            const newAgent = response.data; // API returns the object directly
            agents.value.unshift(newAgent);
            return newAgent;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to create agent';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function updateAgent(id: number, data: Partial<Agent>) {
        loading.value = true;
        try {
            const response = await axios.put(`/agents/${id}`, data);
            const updatedAgent = response.data; // API returns the object directly
            const index = agents.value.findIndex(a => a.id === id);
            if (index !== -1) {
                agents.value[index] = updatedAgent;
            }
            return updatedAgent;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to update agent';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    async function deleteAgent(id: number) {
        loading.value = true;
        try {
            await axios.delete(`/agents/${id}`);
            agents.value = agents.value.filter(a => a.id !== id);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to delete agent';
            throw err;
        } finally {
            loading.value = false;
        }
    }

    return {
        agents,
        loading,
        error,
        fetchAgents,
        fetchAgent,
        createAgent,
        updateAgent,
        deleteAgent,
    };
});
