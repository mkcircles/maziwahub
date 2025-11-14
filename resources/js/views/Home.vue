<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Maziwa Hub</h1>
            <p class="text-gray-600 mb-8">Milk Collection Management System</p>
            <div class="space-x-4">
                <router-link
                    to="/login"
                    class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
                >
                    Sign In
                </router-link>
                <router-link
                    to="/register"
                    class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition"
                >
                    Register
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
</script>

