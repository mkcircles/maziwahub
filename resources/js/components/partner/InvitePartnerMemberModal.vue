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
                class="fixed inset-0 z-[10000] flex items-center justify-center bg-slate-900/40 px-4 py-8 backdrop-blur"
                role="dialog"
                aria-modal="true"
                @click.self="handleClose"
            >
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div
                        v-if="isOpen"
                        class="w-full max-w-lg overflow-hidden rounded-3xl border border-slate-200 bg-white text-slate-900 shadow-xl"
                    >
                        <form @submit.prevent="handleSubmit" class="flex flex-col">
                            <header class="flex items-start justify-between gap-3 border-b border-slate-200 px-6 py-5">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Invite partner member</h2>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Send an invitation email to add an administrator or an agent to your partner team.
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

                            <section class="space-y-5 px-6 py-6">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Email <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        v-model.trim="form.email"
                                        type="email"
                                        required
                                        placeholder="team.member@example.com"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                    />
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Full name
                                    </label>
                                    <input
                                        v-model.trim="form.name"
                                        type="text"
                                        placeholder="Optional — helps personalise the invite"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                    />
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Role <span class="text-rose-500">*</span>
                                        </label>
                                        <select
                                            v-model="form.role"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        >
                                            <option value="partner_admin">Partner admin</option>
                                            <option value="partner_agent">Partner agent</option>
                                        </select>
                                        <p class="text-xs text-slate-400">
                                            Admins can manage centers & invitations. Agents record field activity.
                                        </p>
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            Expires
                                        </label>
                                        <input
                                            v-model="form.expires_at"
                                            type="date"
                                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                        />
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Notes
                                    </label>
                                    <textarea
                                        v-model.trim="form.notes"
                                        rows="3"
                                        placeholder="Any extra context you'd like to share with the invitee."
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                                    ></textarea>
                                </div>

                                <p class="rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-500">
                                    Invitational links are single-use. Once accepted, the member receives a login and appears in
                                    your partner workspace immediately.
                                </p>

                                <div v-if="errorMessage" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                                    {{ errorMessage }}
                                </div>
                                <div v-if="successMessage" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                                    {{ successMessage }}
                                </div>
                            </section>

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
                                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="submitting || !partnerId"
                                >
                                    <Icon icon="mdi:email-send-outline" :size="18" />
                                    <span>{{ submitting ? 'Sending…' : 'Send invite' }}</span>
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
import { reactive, ref, watch } from 'vue';
import Icon from '../shared/Icon.vue';
import { usePartnerStore } from '../../stores/partnerStore';

const props = defineProps<{
    isOpen: boolean;
    partnerId: number | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const partnerStore = usePartnerStore();

const form = reactive({
    email: '',
    name: '',
    role: 'partner_admin' as 'partner_admin' | 'partner_agent',
    expires_at: '',
    notes: '',
});

const submitting = ref(false);
const errorMessage = ref<string | null>(null);
const successMessage = ref<string | null>(null);

const resetForm = () => {
    form.email = '';
    form.name = '';
    form.role = 'partner_admin';
    form.expires_at = '';
    form.notes = '';
    errorMessage.value = null;
    successMessage.value = null;
};

const handleClose = () => {
    if (submitting.value) return;
    resetForm();
    emit('close');
};

const handleSubmit = async () => {
    if (!props.partnerId) {
        errorMessage.value = 'Select a partner before sending an invitation.';
        return;
    }

    submitting.value = true;
    errorMessage.value = null;
    successMessage.value = null;

    try {
        await partnerStore.createInvitation(props.partnerId, {
            email: form.email,
            name: form.name || undefined,
            role: form.role,
            expires_at: form.expires_at || undefined,
            notes: form.notes || undefined,
        });

        successMessage.value = 'Invitation sent successfully.';
        emit('created');
        resetForm();
        emit('close');
    } catch (error: any) {
        errorMessage.value =
            error?.response?.data?.message || partnerStore.invitationsError || 'Failed to send invitation.';
    } finally {
        submitting.value = false;
    }
};

watch(
    () => props.isOpen,
    open => {
        if (!open) {
            resetForm();
        }
    },
);
</script>



