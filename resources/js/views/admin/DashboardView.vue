<template>
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <router-link
                to="/admin/countries"
                class="bg-white p-6 rounded-lg shadow hover:shadow-md transition"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Countries</h3>
                <p class="text-gray-600">Manage countries</p>
            </router-link>
            <router-link
                to="/admin/milk-collection-centers"
                class="bg-white p-6 rounded-lg shadow hover:shadow-md transition"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Milk Centers</h3>
                <p class="text-gray-600">Manage collection centers</p>
            </router-link>
            <router-link
                to="/admin/farmers"
                class="bg-white p-6 rounded-lg shadow hover:shadow-md transition"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Farmers</h3>
                <p class="text-gray-600">Manage farmers</p>
            </router-link>
            <router-link
                to="/admin/cows"
                class="bg-white p-6 rounded-lg shadow hover:shadow-md transition"
            >
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Cows</h3>
                <p class="text-gray-600">Manage cows</p>
            </router-link>
        </div>
    </div>
</template>

<script setup lang="ts">
</script>

