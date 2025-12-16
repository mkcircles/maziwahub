<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-[10000] flex min-h-screen items-end justify-center overflow-y-auto bg-slate-900/50 px-4 py-8 sm:items-center"
                role="dialog"
                aria-modal="true"
                @click.self="handleClose"
            >
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-if="isOpen"
                        class="w-full max-w-2xl max-h-[calc(100vh-4rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white text-slate-900 shadow-xl sm:max-h-[90vh]"
                    >
                        <form @submit.prevent="handleSubmit" class="flex h-full flex-col">
                            <header class="flex items-start justify-between gap-3 border-b border-slate-200 px-6 py-5">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.4em] text-slate-400">
                                        New Agent
                                    </p>
                                    <h2 class="mt-1 text-xl font-semibold text-slate-900">
                                        Register Agent
                                    </h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Create a new agent account and assign them to a center or partner.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                    @click="handleClose"
                                >
                                    <Icon icon="mdi:close" :size="20" />
                                </button>
                            </header>

                            <div class="flex-1 overflow-y-auto px-6 py-6 max-h-[calc(100vh-10rem)] pb-10">
                                <section class="space-y-6">
                                    <!-- Context Selection -->
                                    <div v-if="!fixedContext" class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3 block">
                                            Assignment Context
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    v-model="context" 
                                                    value="mcc" 
                                                    class="text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="text-sm font-medium text-slate-700">Milk Collection Center</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    v-model="context" 
                                                    value="partner" 
                                                    class="text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="text-sm font-medium text-slate-700">Partner Organization</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="grid gap-6 md:grid-cols-2">
                                        <!-- Assignment Dropdowns -->
                                        <div v-if="context === 'mcc' && !preselectedMccId" class="md:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Select Milk Collection Center <span class="text-rose-500">*</span>
                                            </label>
                                            <select
                                                v-model="form.milk_collection_center_id"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                                required
                                            >
                                                <option value="" disabled>Select a center</option>
                                                <option v-for="center in milkCenters" :key="center.id" :value="center.id">
                                                    {{ center.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div v-if="context === 'partner' && !preselectedPartnerId" class="md:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Select Partner <span class="text-rose-500">*</span>
                                            </label>
                                            <select
                                                v-model="form.partner_id"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                                required
                                            >
                                                <option value="" disabled>Select a partner</option>
                                                <option v-for="partner in partners" :key="partner.id" :value="partner.id">
                                                    {{ partner.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Personal Details -->
                                        <div class="md:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Full Name <span class="text-rose-500">*</span>
                                            </label>
                                            <input
                                                v-model.trim="form.name"
                                                type="text"
                                                required
                                                placeholder="Enter full name"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                            />
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Email Address <span class="text-rose-500">*</span>
                                            </label>
                                            <input
                                                v-model.trim="form.email"
                                                type="email"
                                                required
                                                placeholder="agent@example.com"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                            />
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Initial Password <span class="text-rose-500">*</span>
                                            </label>
                                            <input
                                                v-model="form.password"
                                                type="password"
                                                required
                                                minlength="8"
                                                placeholder="Minimum 8 characters"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                            />
                                        </div>

                                        <div>
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Phone Number
                                            </label>
                                            <input
                                                v-model.trim="form.phone"
                                                type="tel"
                                                placeholder="+256..."
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                            />
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                                Address
                                            </label>
                                            <textarea
                                                v-model.trim="form.address"
                                                rows="2"
                                                placeholder="Physical address"
                                                class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                            ></textarea>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                                                <input
                                                    v-model="form.is_active"
                                                    type="checkbox"
                                                    class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                                                />
                                                Agent is active
                                            </label>
                                        </div>
                                    </div>
                                </section>

                                <section v-if="errorMessage" class="mt-4">
                                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                                        {{ errorMessage }}
                                    </div>
                                </section>
                            </div>

                            <footer class="flex flex-col gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row sm:justify-end">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-800"
                                    :disabled="submitting"
                                    @click="handleClose"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-70"
                                    :disabled="submitting"
                                >
                                    <Icon icon="mdi:content-save-outline" :size="18" />
                                    <span>{{ submitting ? 'Saving...' : 'Create Agent' }}</span>
                                </button>
                            </footer>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { reactive, watch, ref, computed } from 'vue';
import Icon from '../shared/Icon.vue';
import { useAgentStore } from '../../stores/agentStore';
import { useGeographyStore } from '../../stores/geographyStore';
import { usePartnerStore } from '../../stores/partnerStore';
import { storeToRefs } from 'pinia';

const props = defineProps<{
    isOpen: boolean;
    preselectedMccId?: number | null;
    preselectedPartnerId?: number | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const agentStore = useAgentStore();
const geographyStore = useGeographyStore();
const partnerStore = usePartnerStore();

const { milkCenters } = storeToRefs(geographyStore);
const { partners } = storeToRefs(partnerStore);

const submitting = ref(false);
const errorMessage = ref<string | null>(null);
const context = ref<'mcc' | 'partner'>('mcc');

const fixedContext = computed(() => {
    if (props.preselectedMccId) return true;
    if (props.preselectedPartnerId) return true;
    return false;
});

const form = reactive({
    name: '',
    email: '',
    password: '',
    phone: '',
    address: '',
    milk_collection_center_id: '' as number | '',
    partner_id: '' as number | '',
    is_active: true,
});

const resetForm = () => {
    form.name = '';
    form.email = '';
    form.password = '';
    form.phone = '';
    form.address = '';
    form.is_active = true;
    
    if (props.preselectedMccId) {
        context.value = 'mcc';
        form.milk_collection_center_id = props.preselectedMccId;
        form.partner_id = '';
    } else if (props.preselectedPartnerId) {
        context.value = 'partner';
        form.partner_id = props.preselectedPartnerId;
        form.milk_collection_center_id = '';
    } else {
        context.value = 'mcc';
        form.milk_collection_center_id = '';
        form.partner_id = '';
    }
    
    errorMessage.value = null;
};

const handleClose = () => {
    if (submitting.value) return;
    emit('close');
};

const toNullable = (value: string) => (value?.trim() ? value.trim() : null);

const handleSubmit = async () => {
    submitting.value = true;
    errorMessage.value = null;

    const payload: any = {
        name: form.name.trim(),
        email: form.email.trim(),
        password: form.password,
        phone: toNullable(form.phone),
        address: toNullable(form.address),
        is_active: form.is_active,
    };

    if (context.value === 'mcc') {
        payload.milk_collection_center_id = form.milk_collection_center_id;
        payload.partner_id = null;
    } else {
        payload.partner_id = form.partner_id;
        payload.milk_collection_center_id = null;
    }

    try {
        await agentStore.createAgent(payload);
        emit('created');
        emit('close');
    } catch (error: any) {
        errorMessage.value = error?.response?.data?.message || 'Failed to create agent.';
    } finally {
        submitting.value = false;
    }
};

// Fetch data when modal opens if not already available
const loadData = async () => {
    if (!milkCenters.value.length) {
        await geographyStore.getMilkCollectionCenters();
    }
    if (!partners.value.length) {
        await partnerStore.fetchPartners();
    }
};

watch(
    () => props.isOpen,
    (isOpen) => {
        if (isOpen) {
            resetForm();
            loadData();
        }
    }
);
</script>
