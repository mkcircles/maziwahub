<template>
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-6">User Details</h1>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600">User details will be displayed here</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router';

const route = useRoute();
const userId = route.params.id;
</script>

