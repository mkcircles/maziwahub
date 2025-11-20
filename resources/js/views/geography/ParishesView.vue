<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-3xl bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-xl space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                        Geography Management
                    </p>
                    <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Parish Explorer</h1>
                    <p class="text-sm text-white/70">
                        Manage parishes linked to subcounties and ensure every village and farmer is anchored to the right location.
                    </p>
                </div>
                <button
                    class="inline-flex items-center gap-2 self-start rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                >
                    <Icon icon="mdi:plus" :size="20" />
                    Add Parish
                </button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Parishes</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">—</p>
                <p class="text-xs text-slate-500">Total configured</p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Villages</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">—</p>
                <p class="text-xs text-slate-500">Nested within parishes</p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Farmers</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">—</p>
                <p class="text-xs text-slate-500">Mapped to this level</p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Milk Centers</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">—</p>
                <p class="text-xs text-slate-500">Nearby facilities</p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white/95 p-8 shadow-lg shadow-slate-100 backdrop-blur">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Parish Management</h2>
                    <p class="text-sm text-slate-500">
                        Hook up the parish endpoints to view and manage parishes, their villages, and key farmer insights.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-xs font-medium text-slate-600">
                    <Icon icon="mdi:map-marker-path" :size="16" />
                    Sync pending
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 p-10 text-center text-slate-500">
                <p class="text-sm font-medium">No parish data available.</p>
                <p class="mt-2 text-xs text-slate-400">
                    Once connected, parishes will appear with quick stats, filter controls, and navigation links to details.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '../../components/shared/Icon.vue';
</script>

