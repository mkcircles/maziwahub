<template>
    <div class="min-h-screen bg-surface-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow border border-surface-200">
            <div>
                <h2 class="text-center text-3xl font-extrabold text-surface-900">
                    Accept Invitation
                </h2>
                <p class="mt-2 text-center text-sm text-surface-500">
                    Join YieldTech Platform
                </p>
            </div>

            <!-- Loading State -->
            <div v-if="loadingDetails" class="flex flex-col items-center justify-center py-8 gap-3">
                <Icon icon="mdi:loading" :size="32" class="animate-spin text-primary-600" />
                <span class="text-sm text-surface-500 font-medium">Validating invitation link...</span>
            </div>

            <!-- Token Validation Error State -->
            <div v-else-if="validationError" class="space-y-4 py-4 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <Icon icon="mdi:alert-circle-outline" :size="24" />
                </div>
                <h3 class="text-base font-bold text-surface-900">Invalid Link</h3>
                <p class="text-sm text-surface-500 px-2">
                    {{ errorMessage || 'This invitation link is invalid, has expired, or has already been used.' }}
                </p>
                <div class="pt-4">
                    <router-link to="/login" class="inline-flex items-center gap-1.5 rounded-md bg-surface-100 border border-surface-200 hover:bg-surface-200 text-surface-700 font-bold py-2 px-4 text-sm transition">
                        Back to Login
                    </router-link>
                </div>
            </div>

            <!-- Invitation Success State -->
            <div v-else-if="success" class="space-y-4 py-4 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                    <Icon icon="mdi:check-circle-outline" :size="24" />
                </div>
                <h3 class="text-base font-bold text-surface-900">Welcome Aboard!</h3>
                <p class="text-sm text-surface-500">
                    {{ successMessage }}
                </p>
                <div class="pt-4">
                    <router-link to="/login" class="inline-flex items-center gap-1.5 rounded bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 text-sm transition">
                        Sign In Now
                    </router-link>
                </div>
            </div>

            <!-- Invitation Form -->
            <form v-else class="space-y-6" @submit.prevent="handleAccept">
                <!-- Invitation Banner Info -->
                <div class="rounded-lg bg-surface-50 border border-surface-150 p-4 space-y-2">
                    <p class="text-xs text-surface-600">
                        You have been invited to join <strong>{{ invitation?.partner?.name }}</strong> as a <span class="font-semibold">{{ formatRole(invitation?.role) }}</span>.
                    </p>
                    <p class="text-xs text-surface-500">
                        Email: <strong class="text-surface-700">{{ invitation?.email }}</strong>
                    </p>
                    <p v-if="invitation?.notes" class="text-xs italic text-surface-500 border-l-2 border-primary-500 pl-2 mt-2 bg-white/50 p-1.5 rounded">
                        "{{ invitation.notes }}"
                    </p>
                </div>

                <div v-if="errorMessage" class="rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-800">
                    {{ errorMessage }}
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wide text-surface-500">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model.trim="form.name"
                            type="text"
                            required
                            class="mt-1 w-full rounded-md border border-surface-300 px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="Enter your full name"
                        />
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wide text-surface-500">
                            Phone Number
                        </label>
                        <input
                            id="phone"
                            v-model.trim="form.phone"
                            type="tel"
                            class="mt-1 w-full rounded-md border border-surface-300 px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="e.g. +256700000000"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-surface-500">
                            Choose Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            minlength="8"
                            class="mt-1 w-full rounded-md border border-surface-300 px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="Minimum 8 characters"
                        />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wide text-surface-500">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            minlength="8"
                            class="mt-1 w-full rounded-md border border-surface-300 px-3 py-2 text-sm text-surface-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="Confirm your password"
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-semibold rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        <span>{{ submitting ? 'Accepting Invitation...' : 'Accept & Register' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Icon from '../../components/shared/Icon.vue';

const route = useRoute();
const token = route.params.token as string;

interface PartnerInvitation {
    id: number;
    email: string;
    name?: string | null;
    role: string;
    notes?: string | null;
    partner?: {
        name: string;
    };
}

const form = reactive({
    name: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const invitation = ref<PartnerInvitation | null>(null);
const loadingDetails = ref(true);
const validationError = ref(false);

const submitting = ref(false);
const success = ref(false);
const successMessage = ref('');
const errorMessage = ref<string | null>(null);

const fetchInvitationDetails = async () => {
    loadingDetails.value = true;
    validationError.value = false;
    errorMessage.value = null;
    try {
        const response = await axios.get<PartnerInvitation>(`/partner-invitations/${token}`);
        invitation.value = response.data;
        if (response.data.name) {
            form.name = response.data.name;
        }
    } catch (err: any) {
        validationError.value = true;
        errorMessage.value = err.response?.data?.message || 'The invitation link is invalid or has expired.';
    } finally {
        loadingDetails.value = false;
    }
};

const handleAccept = async () => {
    errorMessage.value = null;

    if (form.password !== form.password_confirmation) {
        errorMessage.value = 'Passwords do not match.';
        return;
    }

    submitting.value = true;
    try {
        const response = await axios.post(`/partner-invitations/${token}/accept`, {
            name: form.name,
            phone: form.phone || undefined,
            password: form.password,
            password_confirmation: form.password_confirmation,
        });

        successMessage.value = response.data?.message || 'Your invitation has been accepted successfully! You can now log in.';
        success.value = true;
    } catch (err: any) {
        errorMessage.value = err.response?.data?.message || 'Failed to accept invitation.';
    } finally {
        submitting.value = false;
    }
};

const formatRole = (role?: string) => {
    if (!role) return '';
    return role === 'partner_admin' ? 'Partner Administrator' : 'Partner Agent';
};

onMounted(() => {
    fetchInvitationDetails();
});
</script>
