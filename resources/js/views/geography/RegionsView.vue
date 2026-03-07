<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-md bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Geography Management
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Regions Overview</h1>
                    <p class="text-sm text-white/70">
                        Explore the regions configured under each country and monitor their connected districts, counties, and milk collection centers.
                    </p>
                </div>
                <button
                    class="inline-flex items-center gap-2 self-start rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                >
                    <Icon icon="mdi:plus" :size="20" />
                    Add Region
                </button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Regions</p>
                <p class="mt-2 text-2xl font-semibold text-surface-900">—</p>
                <p class="text-xs text-surface-500">Total regions detected</p>
            </div>
            <div class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Districts</p>
                <p class="mt-2 text-2xl font-semibold text-surface-900">—</p>
                <p class="text-xs text-surface-500">Loaded with each region</p>
            </div>
            <div class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Counties</p>
                <p class="mt-2 text-2xl font-semibold text-surface-900">—</p>
                <p class="text-xs text-surface-500">Geographical reach</p>
            </div>
            <div class="rounded-lg border border-white/70 bg-white/90 p-5 shadow-sm border border-surface-200 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-surface-500">Milk Centers</p>
                <p class="mt-2 text-2xl font-semibold text-surface-900">—</p>
                <p class="text-xs text-surface-500">Associated infrastructure</p>
            </div>
        </div>

        <div class="rounded-md border border-surface-100 bg-white/95 p-8 shadow-sm border border-surface-200 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-surface-900">Regions Management</h2>
                    <p class="text-sm text-surface-500">
                        Data will appear here once the API integration is complete. Use the add button above to seed initial records.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-surface-50 px-4 py-2 text-xs font-medium text-surface-600">
                    <Icon icon="mdi:clock-outline" :size="16" />
                    Awaiting integration
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-dashed border-surface-200 bg-surface-50/60 p-10 text-center text-surface-500">
                <p class="text-sm font-medium">No region data available yet.</p>
                <p class="mt-2 text-xs text-surface-400">
                    Connect to the geography endpoints to populate this view with live information.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '../../components/shared/Icon.vue';
</script>

