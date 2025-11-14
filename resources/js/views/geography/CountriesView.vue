<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <div class="flex flex-col gap-0">
                <h1 class="text-2xl font-bold text-gray-900 mb-0">Countries</h1>
                <p class="text-gray-500 text-sm mb-0">Manage the registered countries</p>
            </div>
            <button
                @click="showAddModal = true"
                class="flex items-center uppercase text-xs gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium shadow-sm hover:shadow"
            >
                <Icon icon="mdi:plus" :size="20" />
                Add Country
            </button>
        </div>

        <AddCountryModal
            :is-open="showAddModal"
            @close="showAddModal = false"
            @created="handleCountryCreated"
        />
        <div v-if="countriesStore.loading" class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">Loading countries...</p>
        </div>

        <div v-else-if="countriesStore.error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <p class="text-red-800">{{ countriesStore.error }}</p>
        </div>

        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ISO Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Regions
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Milk Centers
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Farmers
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="countries.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No countries found
                            </td>
                        </tr>
                        <tr 
                            v-for="country in countries" 
                            :key="country.id"
                            class="hover:bg-gray-50 transition-colors duration-150"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ country.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ country.iso_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ country.regions_count || 0 }} regions
                                </span>
                               
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"> {{country.milk_centers_count || 0 }} milk centers</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"> {{country.farmers_count || 0 }} farmers</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <router-link
                                    :to="`/admin/countries/${country.id}`"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900 mr-4"
                                >
                                    <Icon icon="mdi:eye" :size="16" />
                                    View
                                </router-link>
                                <button @click="handleDeleteCountry(country.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-900">
                                    <Icon icon="mdi:delete" :size="16" />
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useCountriesStore } from '../../stores/geographyStore';
import AddCountryModal from '../../components/geography/AddCountryModal.vue';
import Icon from '../../components/shared/Icon.vue';

const countriesStore = useCountriesStore();
const showAddModal = ref(false);

onMounted(() => {
    countriesStore.getCountries();
});

const countries = computed(() => countriesStore.countries);

const handleCountryCreated = () => {
    // Modal will refresh the list automatically
    showAddModal.value = false;
};

const handleDeleteCountry = (countryId: number) => {
    countriesStore.deleteCountry(countryId);
};
</script>

