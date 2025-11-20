<template>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <router-link
                    to="/admin/vets"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                >
                    <Icon icon="mdi:arrow-left" :size="18" />
                    Back to vets
                </router-link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ vetName }}</h1>
                    <p class="text-sm text-gray-500">License {{ vet?.license_number ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition"
                    @click="refresh"
                    :disabled="loading"
                >
                    <Icon icon="mdi:refresh" :size="18" />
                    Refresh
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-lg"
                    :class="vet?.is_active ? 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50' : 'bg-emerald-600 text-white hover:bg-emerald-700'"
                    @click="toggleActive"
                    :disabled="loading"
                >
                    <Icon :icon="vet?.is_active ? 'mdi:pause-circle-outline' : 'mdi:play-circle-outline'" :size="18" />
                    {{ vet?.is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </div>
        </div>

        <div v-if="loading" class="rounded-lg bg-white p-8 text-center text-gray-600 shadow">Loading vet details...</div>
        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ error }}</div>
        <div v-else-if="!vet" class="rounded-lg bg-white p-8 text-center text-gray-600 shadow">Vet not found.</div>
        <template v-else>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <StatisticalCard icon="mdi:check-circle" icon-class="text-emerald-500">
                    <template #title>Status</template>
                    <template #default>{{ vet.is_active ? 'Active' : 'Inactive' }}</template>
                    <template #caption>Availability</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:calendar-clock" icon-class="text-amber-500">
                    <template #title>License Expiry</template>
                    <template #default>{{ formatDate(vet.license_expiry_date) }}</template>
                    <template #caption>Renewal deadline</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:medical-bag" icon-class="text-purple-500">
                    <template #title>Treatments Logged</template>
                    <template #default>{{ treatments.length }}</template>
                    <template #caption>Total records captured</template>
                </StatisticalCard>
                <StatisticalCard icon="mdi:currency-usd" icon-class="text-blue-500">
                    <template #title>Treatment Spend</template>
                    <template #default>{{ formatCurrency(totalTreatmentCost) }}</template>
                    <template #caption>Across all entries</template>
                </StatisticalCard>
            </div>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-4 rounded-lg bg-white p-6 shadow lg:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-900">Profile</h2>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Full Name</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ vetName }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">License Number</dt>
                            <dd class="text-sm text-gray-700">{{ vet.license_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Phone</dt>
                            <dd class="text-sm text-gray-700">{{ vet.phone_number ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Email</dt>
                            <dd class="text-sm text-gray-700">{{ vet.email ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Specialization</dt>
                            <dd class="text-sm text-gray-700">{{ vet.specialization ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Employer</dt>
                            <dd class="text-sm text-gray-700">{{ vet.employer ?? '—' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Assigned Milk Collection Center</dt>
                            <dd class="text-sm text-gray-700">{{ vet.milkCollectionCenter?.name ?? 'Not assigned' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Bio</dt>
                            <dd class="text-sm text-gray-700">{{ vet.bio?.trim() || 'No bio provided.' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="space-y-4 rounded-lg bg-white p-6 shadow">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Stats</h2>
                    <ul class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-center justify-between">
                            <span>Recent Activity</span>
                            <span>{{ formatDate(treatments[0]?.treatment_date) }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Upcoming Follow-ups</span>
                            <span>{{ upcomingFollowUps }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Average Treatment Cost</span>
                            <span>{{ formatCurrency(averageTreatmentCost) }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Registered</span>
                            <span>{{ formatDate(vet.created_at) }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="space-y-4 rounded-lg bg-white p-6 shadow">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Treatment History</h2>
                        <p class="text-sm text-gray-500">Recent treatment records attributed to this vet.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Cow</th>
                                <th class="px-6 py-3">Diagnosis</th>
                                <th class="px-6 py-3">Medication</th>
                                <th class="px-6 py-3">Outcome</th>
                                <th class="px-6 py-3">Cost</th>
                                <th class="px-6 py-3">Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            <tr v-if="treatments.length === 0">
                                <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">No treatment history found.</td>
                            </tr>
                            <tr v-for="record in treatments" :key="record.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ formatDate(record.treatment_date) }}</td>
                                <td class="px-6 py-4">
                                    <router-link
                                        v-if="record.cow_id"
                                        :to="`/admin/cows/${record.cow_id}`"
                                        class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                    >
                                        Cow #{{ record.cow_id }}
                                    </router-link>
                                    <span v-else>—</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ record.diagnosis ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ record.reason ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ record.medication ?? '—' }}</td>
                                <td class="px-6 py-4">{{ record.outcome ?? '—' }}</td>
                                <td class="px-6 py-4">{{ formatCurrency(record.cost) }}</td>
                                <td class="px-6 py-4">{{ record.recordedBy?.name ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </template>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import { useVetStore } from '../../stores/vetStore';
import type { CowTreatment, Vet } from '../../stores/geographyStore';

const route = useRoute();
const vetStore = useVetStore();

const vet = ref<Vet | null>(null);
const treatments = ref<CowTreatment[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

const vetName = computed(() => `${vet.value?.first_name ?? ''} ${vet.value?.last_name ?? ''}`.trim() || 'Vet');

const vetId = computed(() => Number(route.params.id));

const fetchVet = async () => {
    if (!vetId.value || Number.isNaN(vetId.value)) {
        error.value = 'Invalid vet identifier.';
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const result = await vetStore.getVet(vetId.value);
        vet.value = result;
        treatments.value = (result?.treatments ?? []) as CowTreatment[];
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to load vet.';
        vet.value = null;
        treatments.value = [];
    } finally {
        loading.value = false;
    }
};

const refresh = async () => {
    await fetchVet();
};

const toggleActive = async () => {
    if (!vet.value) return;
    try {
        loading.value = true;
        const result = await vetStore.toggleVetActive(vet.value);
        vet.value = result;
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Failed to update vet status.';
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

const formatCurrency = (value?: number | string | null) => {
    if (value === null || value === undefined) return '—';
    const numeric = Number(value);
    if (Number.isNaN(numeric)) return '—';
    return numeric.toLocaleString(undefined, { style: 'currency', currency: 'USD' });
};

const totalTreatmentCost = computed(() =>
    treatments.value.reduce((sum, record) => sum + Number(record.cost ?? 0), 0)
);

const averageTreatmentCost = computed(() => {
    if (!treatments.value.length) return 0;
    return totalTreatmentCost.value / treatments.value.length;
});

const upcomingFollowUps = computed(() => {
    const today = new Date();
    return treatments.value.filter(record => {
        if (!record.follow_up_date) return false;
        const followUp = new Date(record.follow_up_date);
        return !Number.isNaN(followUp.getTime()) && followUp >= today;
    }).length;
});

onMounted(async () => {
    await fetchVet();
});
</script>
