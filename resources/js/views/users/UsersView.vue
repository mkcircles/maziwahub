<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-surface-900">Users</h1>
            <button
                @click="showCreateModal = true"
                class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700"
            >
                Add User
            </button>
        </div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <p class="text-surface-600">Users list will be displayed here</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const showCreateModal = ref(false);
</script>

