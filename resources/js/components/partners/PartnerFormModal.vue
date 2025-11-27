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
                        class="w-full max-w-3xl max-h-[calc(100vh-4rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white text-slate-900 shadow-xl sm:max-h-[90vh]"
                    >
                        <form @submit.prevent="handleSubmit" class="flex h-full flex-col">
                            <header class="flex items-start justify-between gap-3 border-b border-slate-200 px-6 py-5">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.4em] text-slate-400">
                                        {{ mode === 'create' ? 'Create Partner' : 'Edit Partner' }}
                                    </p>
                                    <h2 class="mt-1 text-xl font-semibold text-slate-900">
                                        {{ mode === 'create' ? 'New partner organisation' : form.name || partner?.name }}
                                    </h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Capture organisation contacts, registration details, and activation status.
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
                                <section class="grid gap-6 md:grid-cols-3">
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Organisation name <span class="text-rose-500">*</span>
                                        </label>
                                        <input
                                            v-model.trim="form.name"
                                            type="text"
                                            required
                                        placeholder="Enter partner organisation name"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Contact email <span class="text-rose-500">*</span>
                                        </label>
                                        <input
                                            v-model.trim="form.email"
                                            type="email"
                                            required
                                            placeholder="hello@partner.test"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Phone number
                                        </label>
                                        <input
                                            v-model.trim="form.phone"
                                            type="tel"
                                            placeholder="+256 700 123456"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Registration number
                                        </label>
                                        <input
                                            v-model.trim="form.registration_number"
                                            type="text"
                                            placeholder="REG-XXXX"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Website
                                        </label>
                                        <input
                                            v-model.trim="form.website"
                                            type="url"
                                            placeholder="https://partner.test"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Contact person
                                        </label>
                                        <input
                                            v-model.trim="form.contact_name"
                                            type="text"
                                            placeholder="Full name"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Contact title
                                        </label>
                                        <input
                                            v-model.trim="form.contact_title"
                                            type="text"
                                            placeholder="e.g. Operations Manager"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Country
                                        </label>
                                        <input
                                            v-model.trim="form.country"
                                            type="text"
                                            placeholder="Uganda"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            City / Town
                                        </label>
                                        <input
                                            v-model.trim="form.city"
                                            type="text"
                                            placeholder="Kampala"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Postal code
                                        </label>
                                        <input
                                            v-model.trim="form.postal_code"
                                            type="text"
                                            placeholder="25601"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Address
                                        </label>
                                        <textarea
                                            v-model.trim="form.address"
                                            rows="2"
                                            placeholder="Street, plot, or additional guidance"
                                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        ></textarea>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Description
                                        </label>
                                        <textarea
                                            v-model.trim="form.description"
                                            rows="3"
                                            placeholder="Brief summary of the partner's operations."
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
                                            Partner is active
                                        </label>
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
                                    <span>{{ submitting ? 'Saving…' : mode === 'create' ? 'Create partner' : 'Save changes' }}</span>
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
import { computed, reactive, watch, ref } from 'vue';
import Icon from '../shared/Icon.vue';
import { usePartnerStore, type Partner } from '../../stores/partnerStore';

const props = defineProps<{
    isOpen: boolean;
    partner?: Partner | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'saved', partner: Partner): void;
}>();

const partnerStore = usePartnerStore();

const mode = computed(() => (props.partner ? 'edit' : 'create'));
const submitting = ref(false);
const errorMessage = ref<string | null>(null);

const form = reactive({
    name: '',
    email: '',
    phone: '',
    registration_number: '',
    website: '',
    contact_name: '',
    contact_title: '',
    address: '',
    description: '',
    country: '',
    city: '',
    postal_code: '',
    is_active: true,
});

const resetForm = () => {
    form.name = props.partner?.name ?? '';
    form.email = props.partner?.email ?? '';
    form.phone = props.partner?.phone ?? '';
    form.registration_number = props.partner?.registration_number ?? '';
    form.website = props.partner?.website ?? '';
    form.contact_name = props.partner?.contact_name ?? '';
    form.contact_title = props.partner?.contact_title ?? '';
    form.address = props.partner?.address ?? '';
    form.description = props.partner?.description ?? '';
    form.country = props.partner?.country ?? '';
    form.city = props.partner?.city ?? '';
    form.postal_code = props.partner?.postal_code ?? '';
    form.is_active = props.partner?.is_active ?? true;
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

    const payload = {
        name: form.name.trim(),
        email: form.email.trim(),
        phone: toNullable(form.phone),
        registration_number: toNullable(form.registration_number),
        website: toNullable(form.website),
        contact_name: toNullable(form.contact_name),
        contact_title: toNullable(form.contact_title),
        address: toNullable(form.address),
        description: toNullable(form.description),
        country: toNullable(form.country),
        city: toNullable(form.city),
        postal_code: toNullable(form.postal_code),
        is_active: form.is_active,
    };

    try {
        let partner: Partner;
        if (props.partner) {
            partner = await partnerStore.updatePartner(props.partner.id, payload);
        } else {
            partner = await partnerStore.createPartner(payload);
        }
        emit('saved', partner);
        emit('close');
    } catch (error: any) {
        errorMessage.value =
            error?.response?.data?.message ||
            partnerStore.partnersError ||
            'Failed to save partner. Please review the details and try again.';
    } finally {
        submitting.value = false;
    }
};

watch(
    () => props.isOpen,
    isOpen => {
        if (isOpen) {
            resetForm();
        }
    },
    { immediate: true },
);

watch(
    () => props.partner,
    () => {
        if (props.isOpen) {
            resetForm();
        }
    },
);
</script>

