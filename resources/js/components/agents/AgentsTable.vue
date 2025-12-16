<template>
    <div class="overflow-x-auto rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Contacts</th>
                    <th class="px-6 py-3">Assigned MCC</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                <tr v-if="loading">
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Loading agents...</td>
                </tr>
                <tr v-else-if="agents.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No agents found.</td>
                </tr>
                <tr v-else v-for="agent in agents" :key="agent.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900">{{ agent.user?.name ?? agent.name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div>{{ agent.phone ?? agent.user?.phone_number ?? '—' }}</div>
                        <div class="text-xs text-gray-500">{{ agent.user?.email ?? agent.email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        {{ agent.milk_collection_center?.name ?? 'Unassigned' }}
                    </td>
                    <td class="px-6 py-4">
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium"
                            :class="agent.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600'"
                        >
                            <Icon :icon="agent.is_active ? 'mdi:check-circle-outline' : 'mdi:pause-circle-outline'" :size="14" />
                            {{ agent.is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 inline-flex gap-2">

                        <button class="text-gray-400 hover:text-blue-600 mr-2" title="Edit" @click="$emit('edit', agent)">
                            <Icon icon="mdi:pencil-outline" :size="18" /> Edit
                        </button>
                        <router-link
                            :to="{ name: detailRouteName, params: { id: agent.id } }"
                            class="text-gray-400 hover:text-blue-600"
                            title="View Details"
                        >
                            <Icon icon="mdi:eye-outline" :size="18" /> View
                        </router-link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import type { Agent } from '../../stores/agentStore';

defineProps<{
    agents: Agent[];
    loading?: boolean;
}>();

defineEmits<{
    (e: 'edit', agent: Agent): void;
}>();

const route = useRoute();

const detailRouteName = computed(() => {
    return route.path.startsWith('/partner') ? 'partner-agent-detail' : 'admin-agent-detail';
});
</script>
