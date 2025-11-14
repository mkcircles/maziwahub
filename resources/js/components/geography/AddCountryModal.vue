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
                class="fixed inset-0 z-[9999] overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-0"
                    aria-hidden="true"
                    @click="close"
                ></div>

                <!-- Modal container -->
                <div class="relative flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0 z-10">
                    <!-- Center modal spacer -->
                    <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                    <!-- Modal panel -->
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
                            class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10"
                        >
                <form @submit.prevent="handleSubmit">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Add New Country
                            </h3>
                            <button
                                type="button"
                                @click="close"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <span class="sr-only">Close</span>
                                <Icon icon="mdi:close" :size="24" />
                            </button>
                        </div>

                        <div v-if="error" class="mb-4 rounded-md bg-red-50 p-4">
                            <div class="text-sm text-red-800">{{ error }}</div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Country Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter country name"
                                />
                            </div>

                            <div>
                                <label for="iso_code" class="block text-sm font-medium text-gray-700 mb-1">
                                    ISO Code <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="iso_code"
                                    v-model="form.iso_code"
                                    type="text"
                                    required
                                    maxlength="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 uppercase"
                                    placeholder="e.g., UGA"
                                    @input="form.iso_code = form.iso_code.toUpperCase()"
                                />
                                <p class="mt-1 text-xs text-gray-500">3-letter ISO code (e.g., UGA, USA)</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="loading">Creating...</span>
                            <span v-else>Create Country</span>
                        </button>
                        <button
                            type="button"
                            @click="close"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                        </div>
                    </form>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, Teleport, Transition } from 'vue';
import { useGeographyStore } from '../../stores/geographyStore';
import Icon from '../shared/Icon.vue';

interface Props {
    isOpen: boolean;
}

interface Emits {
    (e: 'close'): void;
    (e: 'created'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const geographyStore = useGeographyStore();

const form = ref({
    name: '',
    iso_code: '',
});

const error = ref<string | null>(null);
const loading = ref(false);

const close = () => {
    if (!loading.value) {
        form.value = { name: '', iso_code: '' };
        error.value = null;
        emit('close');
    }
};

const handleSubmit = async () => {
    error.value = null;
    loading.value = true;

    try {
        await geographyStore.createCountry({
            name: form.value.name,
            iso_code: form.value.iso_code.toUpperCase(),
        });

        emit('created');
        close();
    } catch (err: any) {
        error.value = geographyStore.error || 'Failed to create country';
    } finally {
        loading.value = false;
    }
};

// Reset form when modal closes
watch(() => props.isOpen, (newValue) => {
    if (!newValue) {
        form.value = { name: '', iso_code: '' };
        error.value = null;
    }
});
</script>

