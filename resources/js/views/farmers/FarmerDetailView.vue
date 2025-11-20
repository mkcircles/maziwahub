<template>
    <div class="space-y-10 pb-16">
        <div
            class="relative overflow-hidden rounded-3xl bg-[#0F172A] px-6 py-10 text-white shadow-xl shadow-blue-900/30 sm:px-10"
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.12),transparent_60%)] opacity-80"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-col gap-4">
                    <router-link
                        to="/admin/farmers"
                        class="inline-flex items-center gap-2 self-start rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25"
                    >
                        <Icon icon="mdi:arrow-left" :size="18" />
                        Back to Farmers
                    </router-link>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.45em] text-white/60">
                            Farmer Profile
                        </p>
                        <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                            {{ fullName }}
                        </h1>
                        <p class="mt-2 text-xs text-white/70 sm:text-sm">
                            Farmer ID:
                            <span class="font-semibold tracking-wide text-white">
                                {{ farmer?.farmer_id ?? 'N/A' }}
                            </span>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-[11px] sm:text-xs">
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-white/15 px-3 py-1 font-medium text-white backdrop-blur"
                            :class="statusChipClass(farmer?.status)"
                        >
                            <Icon :icon="statusChipIcon(farmer?.status)" :size="14" />
                            {{ farmer?.status ?? 'Unknown' }}
                        </span>
                        <span
                            v-if="farmer?.reg_type"
                            class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 font-medium uppercase text-white/85 backdrop-blur"
                        >
                            {{ farmer?.reg_type }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:items-end">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-white/15 px-5 py-2 text-sm font-medium text-white backdrop-blur transition hover:bg-white/25 disabled:cursor-not-allowed disabled:opacity-60"
                        @click="openEditModal"
                        :disabled="detailLoading || !farmer"
                    >
                        <Icon icon="mdi:pencil-outline" :size="16" />
                        Edit Profile
                    </button>
                    <div class="text-xs text-white/60">
                        Last updated {{ formatDate(farmer?.updated_at) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white/90 p-2 shadow-lg shadow-slate-100">
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 focus-visible:ring-offset-2"
                    :class="[
                        activeTab === tab.id
                            ? 'bg-slate-900 text-white shadow-lg shadow-slate-400/40'
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900',
                    ]"
                    @click="selectTab(tab.id)"
                >
                    <span>{{ tab.label }}</span>
                </button>
            </div>
        </div>

        <div v-if="detailLoading" class="rounded-3xl border border-slate-200 bg-white p-8 text-slate-600 shadow-lg shadow-slate-100">
            Loading farmer details…
        </div>
        <div v-else-if="detailError" class="rounded-3xl border border-red-200 bg-red-50/80 p-6 text-red-700 shadow-lg shadow-red-100">
            {{ detailError }}
        </div>

        <template v-else-if="farmer">
            
            <div v-if="activeTab === 'overview'" class="space-y-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <!-- <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="openEditModal"
                        :disabled="detailLoading || !farmer"
                    >
                        <Icon icon="mdi:pencil-outline" :size="16" />
                        Edit Profile
                    </button> -->
            </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <StatisticalCard
                        icon="mdi:cow"
                        icon-class="text-rose-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                    <template #title>Herd Size</template>
                    <template #default>{{ farmer.herd_size ?? '-' }}</template>
                    <template #caption><b>{{ cows.length }}</b> Cows registered on system</template>
                </StatisticalCard>

                <StatisticalCard
                        icon="mdi:nature"
                        icon-class="text-lime-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                    <template #title>Farm Size</template>
                    <template #default>{{ farmer.farm_size ?? 0 }} acres</template>
                    <template #caption>Land registered by farmer</template>
                </StatisticalCard>
                <StatisticalCard
                        icon="mdi:account-outline"
                        icon-class="text-sky-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                    <template #title>Household Members</template>
                    <template #default>{{ farmer.family_size ?? '—' }}</template>
                    <template #caption>Total family size</template>
                </StatisticalCard>

                <StatisticalCard
                        icon="mdi:human-male-female-child"
                        icon-class="text-emerald-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                    <template #title>Adults</template>
                    <template #default>{{ farmer.above_18 ?? 0 }}</template>
                    <template #caption>Household members above 18</template>
                </StatisticalCard>

                <StatisticalCard
                        icon="mdi:baby-face-outline"
                        icon-class="text-amber-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                    <template #title>Children</template>
                    <template #default>{{ farmer.below_5 ?? 0 }}</template>
                    <template #caption>Children under 5 years</template>
                </StatisticalCard>


            </div>

                <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100 space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">Personal Information</h2>
                    <div class="grid gap-4 sm:grid-cols-2 text-slate-700">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">First Name</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.first_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Last Name</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.last_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Gender</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.gender ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Date of Birth</p>
                            <p class="text-sm font-medium text-slate-900">
                                {{ formatDate(farmer.dob) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Phone Number</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.phone_number ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">National ID</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.id_number ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Marital Status</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.marital_status ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Education Level</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.educational_level ?? '—' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100 space-y-4">
                    <h2 class="text-lg font-semibold text-slate-900">Household &amp; Next of Kin</h2>
                    <div class="grid gap-4 sm:grid-cols-2 text-slate-700">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Household Head</p>
                            <p class="text-sm font-medium text-slate-900">
                                {{ farmer.household_head ? 'Yes' : 'No' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Registered By</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.registered_by ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Next of Kin</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.next_of_kin ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Next of Kin Contact</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.next_of_kin_contact ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Relationship</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.next_of_kin_relationship ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Financial Instrument</p>
                            <p class="text-sm font-medium text-slate-900">{{ farmer.financial_instrument ?? '—' }}</p>
                        </div>
                        
                    </div>
                </section>
            </div>

            <section class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100 space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Milk Collection &amp; Location</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 text-slate-700">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Milk Collection Center</h3>
                        <p class="mt-2 text-sm font-medium text-slate-900">
                            {{ farmer.milkCollectionCenter?.name ?? 'Not assigned' }}
                        </p>
                        <p v-if="farmer.milkCollectionCenter?.physical_address" class="text-xs text-slate-500">
                            {{ farmer.milkCollectionCenter?.physical_address }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Location Trail</h3>
                        <p class="mt-2 text-sm text-slate-900">{{ formatFarmerLocation(farmer) }}</p>
                    </div>
                    <div v-if="farmer.coordinates" class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Coordinates</h3>
                        <p class="mt-2 text-sm text-slate-900">
                            Lat: {{ farmer.coordinates?.latitude ?? '—' }}<br />
                            Lng: {{ farmer.coordinates?.longitude ?? '—' }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Herd Size Range</h3>
                        <p class="mt-2 text-sm font-medium text-slate-900">
                            {{ farmer.herd_size ?? 'Not specified' }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Grazing Type</h3>
                        <p class="mt-2 text-sm font-medium text-slate-900">
                            {{ farmer.grazing_type ?? 'Not specified' }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Water Source</h3>
                        <p class="mt-2 text-sm font-medium text-slate-900">
                            {{ farmer.water_source ?? 'Not specified' }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Primary Feeding</h3>
                                <p class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ primaryFeedingMethod?.name ?? 'Not assigned' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ feedingLastChangedAt ? `Updated ${formatDateTime(feedingLastChangedAt)}` : 'No recent updates recorded' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                @click="activeTab = 'feeding'"
                            >
                                Manage
                                <Icon icon="mdi:arrow-right" :size="14" />
                            </button>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Supplemental Feeding</h3>
                                <p class="mt-2 text-sm font-semibold text-slate-900">
                                    {{ supplementalFeedingMethod?.name ?? 'Not assigned' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ supplementalFeedingMethod ? 'Supplemental feeding in place' : 'No supplemental feeding recorded' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                                @click="activeTab = 'feeding'"
                            >
                                View
                                <Icon icon="mdi:arrow-right" :size="14" />
                            </button>
                        </div>
                    </div>
                    <div v-if="feedingNotes" class="rounded-2xl border border-slate-100 bg-slate-50/80 p-5 shadow-sm sm:col-span-2">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Feeding Notes</h3>
                        <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">
                            {{ feedingNotes }}
                        </p>
                    </div>
                </div>
            </section>

            <section v-if="farmer.support_needed" class="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg shadow-slate-100 space-y-4">
                <h2 class="text-lg font-semibold text-slate-900">Support Needed</h2>
                <p class="text-sm text-slate-700">
                    {{ farmer.support_needed }}
                </p>
            </section>
            </div>

            <div v-else-if="activeTab === 'feeding'" class="space-y-6">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Primary Feeding</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">
                                    {{ primaryFeedingMethod?.name ?? 'Not assigned' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ feedingLastChangedAt ? `Updated ${formatDateTime(feedingLastChangedAt)}` : 'No updates recorded' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-60"
                                @click="openFeedingModal('primary')"
                                :disabled="detailLoading"
                            >
                                <Icon icon="mdi:plus" :size="16" />
                                Record
                            </button>
                        </div>
                        <p v-if="primaryFeedingMethod?.description" class="text-sm text-slate-600">
                            {{ primaryFeedingMethod.description }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Supplemental Feeding</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">
                                    {{ supplementalFeedingMethod?.name ?? 'Not assigned' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ supplementalFeedingMethod ? 'Supplemental feeding in use' : 'No supplemental schedule' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-sky-700 disabled:opacity-60"
                                @click="openFeedingModal('supplemental')"
                                :disabled="detailLoading"
                            >
                                <Icon icon="mdi:plus" :size="16" />
                                Record
                            </button>
                        </div>
                        <p v-if="supplementalFeedingMethod?.description" class="text-sm text-slate-600">
                            {{ supplementalFeedingMethod.description }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Feeding Notes</p>
                                <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">
                                    {{ feedingNotes || 'No notes recorded yet.' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 disabled:opacity-60"
                                @click="openFeedingModal()"
                                :disabled="detailLoading"
                            >
                                <Icon icon="mdi:pencil-outline" :size="16" />
                                Update
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-white/95 p-6 shadow-lg shadow-slate-100 backdrop-blur space-y-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Feeding Timeline</h2>
                            <p class="text-sm text-slate-500">
                                Track updates to the farmer’s feeding practices over time.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                v-for="option in feedingFilterOptions"
                                :key="option.value"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium transition"
                                :class="isFeedingFilterActive(option.value)
                                    ? 'border-slate-900 bg-slate-900 text-white shadow-sm shadow-slate-400/40'
                                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                                @click="changeFeedingFilter(option.value)"
                            >
                                {{ option.label }}
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:opacity-60"
                                @click="openFeedingModal()"
                                :disabled="detailLoading || feedingMethodsLoading"
                            >
                                <Icon icon="mdi:plus" :size="18" />
                                Record Feeding Update
                            </button>
                        </div>
                    </div>

                    <div v-if="feedingMethodsError" class="rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                        {{ feedingMethodsError }}
                    </div>

                    <div v-if="feedingHistoryLoading" class="rounded-md border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                        Loading feeding history…
                    </div>
                    <div v-else-if="feedingHistoryError" class="rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-600 flex items-center justify-between gap-4">
                        <span>{{ feedingHistoryError }}</span>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-white px-3 py-1 text-xs font-semibold text-red-600 hover:bg-red-100"
                            @click="ensureFeedingHistory()"
                        >
                            <Icon icon="mdi:refresh" :size="14" />
                            Retry
                        </button>
                    </div>
                    <div v-else>
                        <div v-if="!feedingHistory.length" class="rounded-md border border-slate-200 border-dashed bg-slate-50 p-6 text-sm text-slate-500 text-center">
                            No feeding history recorded yet. Start by recording a feeding update.
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="entry in feedingHistory"
                                :key="entry.id"
                                class="relative overflow-hidden rounded-2xl border border-slate-100 bg-white/90 p-5 shadow-md shadow-slate-100"
                            >
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ feedingMethodName(entry) }}
                                        </p>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            {{ feedingTypeLabel(entry.feeding_type) }}
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="isActiveFeedingEntry(entry)
                                            ? 'border border-emerald-200 bg-emerald-50 text-emerald-600'
                                            : 'border border-slate-200 bg-slate-50 text-slate-500'"
                                    >
                                        <Icon :icon="isActiveFeedingEntry(entry) ? 'mdi:leaf' : 'mdi:calendar-check'" :size="14" />
                                        {{ isActiveFeedingEntry(entry) ? 'Active' : `Ended ${formatDate(entry.ended_at)}` }}
                                    </span>
                                </div>
                                <div class="mt-3 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                                    <div class="flex items-center gap-2">
                                        <Icon icon="mdi:calendar-start" :size="16" class="text-slate-400" />
                                        <span>Started {{ formatDate(entry.started_at) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Icon icon="mdi:calendar-end" :size="16" class="text-slate-400" />
                                        <span>{{ entry.ended_at ? `Ended ${formatDate(entry.ended_at)}` : 'Ongoing' }}</span>
                                    </div>
                                    <div v-if="entry.recordedBy" class="flex items-center gap-2 sm:col-span-2">
                                        <Icon icon="mdi:account-outline" :size="16" class="text-slate-400" />
                                        <span>Recorded by {{ entry.recordedBy.name }}</span>
                                    </div>
                                    <div v-if="entry.notes" class="sm:col-span-2">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Notes</p>
                                        <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">
                                            {{ entry.notes }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="feedingHistoryPagination.last_page > 1"
                        class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p>
                            Page
                            <span class="font-semibold text-slate-800">{{ feedingHistoryPagination.current_page }}</span>
                            of
                            <span class="font-semibold text-slate-800">{{ feedingHistoryPagination.last_page }}</span>
                        </p>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-medium text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="feedingHistoryPagination.current_page <= 1 || feedingHistoryLoading"
                                @click="changeFeedingPage(feedingHistoryPagination.current_page - 1)"
                            >
                                <Icon icon="mdi:chevron-left" :size="18" />
                                Previous
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm font-medium text-slate-600 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="feedingHistoryPagination.current_page >= feedingHistoryPagination.last_page || feedingHistoryLoading"
                                @click="changeFeedingPage(feedingHistoryPagination.current_page + 1)"
                            >
                                Next
                                <Icon icon="mdi:chevron-right" :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else-if="activeTab === 'cows'" class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Registered Cows</h2>
                        <p class="text-sm text-slate-500">
                            Manage the herd associated with this farmer.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="openAddCowModal"
                        :disabled="detailLoading"
                    >
                        <Icon icon="mdi:plus" :size="18" />
                        Add Cow
                    </button>
                </div>
                <FarmerCowsTable
                    :cows="cows"
                    :format-date="formatDate"
                    :format-liters="formatLiters"
                />
            </div>

            <div v-else-if="activeTab === 'milk'" class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Milk Production Records</h2>
                        <p class="text-sm text-slate-500">
                            Daily production entries for cows owned by this farmer.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="openAddMilkModal"
                        :disabled="detailLoading || !cows.length"
                    >
                        <Icon icon="mdi:plus" :size="18" />
                        Record Milk
                    </button>
                </div>
                <FarmerMilkProductionTable
                    :records="milkProductionsSorted"
                    :format-date="formatDate"
                    :format-liters="formatLiters"
                    :get-cow-tag="getCowTag"
                />
            </div>

            <div v-else-if="activeTab === 'deliveries'" class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-3">
                    <StatisticalCard
                        icon="mdi:bucket-outline"
                        icon-class="text-sky-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                        <template #title>Total Volume</template>
                        <template #default>{{ formatLiters(milkDeliverySummary.totalVolume) }}</template>
                        <template #caption>Sum across deliveries</template>
                    </StatisticalCard>
                    <StatisticalCard
                        icon="mdi:currency-usd"
                        icon-class="text-emerald-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                        <template #title>Total Value</template>
                        <template #default>{{ formatCurrency(milkDeliverySummary.totalAmount) }}</template>
                        <template #caption>Aggregate amount earned</template>
                    </StatisticalCard>
                    <StatisticalCard
                        icon="mdi:counter"
                        icon-class="text-amber-500"
                        class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-lg shadow-slate-100 backdrop-blur"
                    >
                        <template #title>Average Volume</template>
                        <template #default>{{ formatLiters(milkDeliverySummary.averageVolume) }}</template>
                        <template #caption>Per delivery average</template>
                    </StatisticalCard>
                </div>
                <FarmerMilkDeliveryTrend :deliveries="milkDeliveries" />
                <FarmerMilkDeliveriesTable
                    :deliveries="milkDeliveries"
                    :format-date="formatDate"
                    :format-liters="formatLiters"
                    :format-currency="formatCurrency"
                />
            </div>
        </template>

        <EditFarmerModal
            :is-open="showEditModal"
            :farmer="farmer"
            :milk-centers="milkCenters"
            @close="handleModalClose"
            @updated="handleFarmerUpdated"
        />
        <AddCowModal
            :is-open="showAddCowModal"
            :farmer-id="farmer?.id ?? null"
            @close="handleCowModalClose"
            @created="handleCowCreated"
        />
        <AddMilkProductionModal
            :is-open="showAddMilkModal"
            :farmer-id="farmer?.id ?? null"
            :cows="cows"
            @close="handleMilkModalClose"
            @created="handleMilkRecordCreated"
        />
        <RecordFeedingModal
            :is-open="showFeedingModal"
            :farmer-id="farmer?.id ?? null"
            :feeding-methods="feedingMethods"
            :default-type="feedingModalType"
            :current-method-id="feedingModalMethodId"
            :loading="feedingMethodsLoading"
            @close="handleFeedingModalClose"
            @created="handleFeedingRecorded"
        />
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useRoute } from 'vue-router';
import Icon from '../../components/shared/Icon.vue';
import StatisticalCard from '../../components/shared/StatisticalCard.vue';
import EditFarmerModal from '../../components/farmers/EditFarmerModal.vue';
import FarmerCowsTable from '../../components/farmers/FarmerCowsTable.vue';
import FarmerMilkProductionTable from '../../components/farmers/FarmerMilkProductionTable.vue';
import AddCowModal from '../../components/farmers/AddCowModal.vue';
import AddMilkProductionModal from '../../components/farmers/AddMilkProductionModal.vue';
import FarmerMilkDeliveriesTable from '../../components/farmers/FarmerMilkDeliveriesTable.vue';
import FarmerMilkDeliveryTrend from '../../components/farmers/FarmerMilkDeliveryTrend.vue';
import RecordFeedingModal from '../../components/farmers/RecordFeedingModal.vue';
import { useFarmerStore } from '../../stores/farmerStore';
import { useGeographyStore } from '../../stores/geographyStore';
import type { Farmer, Cow, CowMilkProduction, FarmerFeedingHistory } from '../../stores/geographyStore';

const route = useRoute();
const farmerStore = useFarmerStore();
const geographyStore = useGeographyStore();
const { milkCenters } = storeToRefs(geographyStore);

const {
    feedingMethods,
    feedingMethodsLoading,
    feedingMethodsError,
    feedingHistory,
    feedingHistoryLoading,
    feedingHistoryError,
} = storeToRefs(farmerStore);

const feedingHistoryPagination = farmerStore.feedingHistoryPagination;
const feedingHistoryFilters = farmerStore.feedingHistoryFilters;
const feedingFilterOptions = [
    { label: 'All', value: '' },
    { label: 'Primary', value: 'primary' },
    { label: 'Supplemental', value: 'supplemental' },
    { label: 'Other', value: 'other' },
];

const farmerId = computed(() => Number(route.params.id));

type TabKey = 'overview' | 'feeding' | 'cows' | 'milk' | 'deliveries';

const farmer = computed(() => farmerStore.selectedFarmer as Farmer | null);
const detailLoading = computed(() => farmerStore.detailLoading);
const detailError = computed(() => farmerStore.detailError);
const showEditModal = ref(false);
const showAddCowModal = ref(false);
const showAddMilkModal = ref(false);
const showFeedingModal = ref(false);
const feedingModalType = ref<'primary' | 'supplemental' | 'other'>('primary');
const feedingModalMethodId = ref<number | null>(null);
const feedingDataLoaded = ref(false);
const activeTab = ref<TabKey>('overview');

const fullName = computed(() => {
    if (!farmer.value) return 'Farmer';
    return `${farmer.value.first_name} ${farmer.value.last_name}`.trim();
});

const cows = computed<Cow[]>(() => {
    const value = farmer.value as Farmer | null;
    const list =
        (value?.cows as Cow[] | undefined) ??
        ((value as any)?.cows ?? []);
    return Array.isArray(list) ? list : [];
});

const milkProductions = computed<CowMilkProduction[]>(() => {
    const value = farmer.value as any;
    const list =
        value?.cow_milk_productions ??
        value?.cowMilkProductions ??
        [];
    return Array.isArray(list) ? list : [];
});

const milkDeliveries = computed(() => {
    const value = farmer.value as any;
    const list = value?.milk_deliveries ?? value?.milkDeliveries ?? [];
    return Array.isArray(list) ? list : [];
});

const primaryFeedingMethod = computed(() => {
    const value = farmer.value as any;
    return value?.primaryFeedingMethod ?? null;
});

const supplementalFeedingMethod = computed(() => {
    const value = farmer.value as any;
    return value?.supplementalFeedingMethod ?? null;
});

const feedingNotes = computed(() => farmer.value?.feeding_notes ?? '');

const feedingLastChangedAt = computed(() => farmer.value?.feeding_last_changed_at ?? null);

const milkDeliverySummary = computed(() => {
    const list = milkDeliveries.value;
    if (!list.length) {
        return {
            totalVolume: 0,
            totalAmount: 0,
            averageVolume: 0,
        };
    }

    const totalVolume = list.reduce(
        (sum: number, delivery: any) => sum + Number(delivery.volume_liters ?? 0),
        0
    );
    const totalAmount = list.reduce(
        (sum: number, delivery: any) => sum + Number(delivery.total_amount ?? 0),
        0
    );

    return {
        totalVolume,
        totalAmount,
        averageVolume: totalVolume / list.length,
    };
});

const milkProductionsSorted = computed(() =>
    [...milkProductions.value].sort((a, b) => {
        const dateA = new Date(a.recorded_date ?? '').getTime();
        const dateB = new Date(b.recorded_date ?? '').getTime();
        return dateB - dateA;
    })
);

const cowLookup = computed(() => {
    const map = new Map<number, Cow>();
    cows.value.forEach((cow) => {
        map.set(cow.id, cow);
    });
    return map;
});

const tabs = computed<Array<{ id: TabKey; label: string }>>(() => [
    { id: 'overview', label: 'Overview' },
    { id: 'feeding', label: `Feeding${feedingHistory.value.length ? ` (${feedingHistory.value.length})` : ''}` },
    { id: 'cows', label: `Cows (${cows.value.length})` },
    { id: 'milk', label: `Milk Production (${milkProductions.value.length})` },
    { id: 'deliveries', label: `Deliveries (${milkDeliveries.value.length})` },
]);

const formatDate = (value?: string | null) => {
    if (!value) return '—';
    try {
        return new Date(value).toLocaleDateString();
    } catch {
        return value;
    }
};

const formatDateTime = (value?: string | null) => {
    if (!value) return '—';
    try {
        return new Date(value).toLocaleString();
    } catch {
        return value;
    }
};

const formatLiters = (value?: number | string | null) => {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const numberValue = Number(value);

    if (Number.isNaN(numberValue)) {
        return '—';
    }

    return numberValue.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatCurrency = (value?: number | null) => {
    if (value === null || value === undefined) return '—';
    return value.toLocaleString(undefined, { style: 'currency', currency: 'UGX' });
};

const getCowTag = (cowId?: number | null) => {
    if (!cowId) return `Cow #${cowId ?? '—'}`;
    return cowLookup.value.get(cowId)?.tag_number ?? `Cow #${cowId}`;
};

const formatFarmerLocation = (farmer: Farmer) => {
    const location = (farmer as any)?.location ?? {};
    const segments = [
        location.country ?? farmer.district,
        location.region,
        location.district ?? farmer.district,
        location.county ?? farmer.county,
        location.sub_county ?? farmer.sub_county,
        location.parish ?? farmer.parish,
        location.village ?? farmer.village,
    ].filter((segment): segment is string => Boolean(segment));
    return segments.length ? segments.join(' > ') : 'Location details unavailable';
};

const feedingTypeLabel = (type: string) => {
    if (type === 'primary') return 'Primary';
    if (type === 'supplemental') return 'Supplemental';
    return 'Other';
};

const feedingMethodName = (entry: FarmerFeedingHistory) => {
    if (entry.feedingMethod?.name) {
        return entry.feedingMethod.name;
    }

    if (entry.feeding_type === 'other') {
        return entry.notes ? entry.notes.split('\n')[0] : 'Custom feeding practice';
    }

    return 'Feeding method removed';
};

const isActiveFeedingEntry = (entry: FarmerFeedingHistory) => !entry.ended_at;

const ensureFeedingMethods = async () => {
    try {
        await farmerStore.fetchFeedingMethods();
    } catch {
        // error state handled in store
    }
};

const ensureFeedingHistory = async ({ force = false }: { force?: boolean } = {}) => {
    if (!farmerId.value || Number.isNaN(farmerId.value)) return;

    const params: Record<string, any> = {
        feeding_type: feedingHistoryFilters.feeding_type,
        per_page: feedingHistoryFilters.per_page,
        page: feedingHistoryFilters.page,
    };

    if (force) {
        params.page = 1;
    }

    try {
        await farmerStore.fetchFeedingHistory(farmerId.value, params);
        feedingDataLoaded.value = true;
    } catch {
        // errors handled via store state
    }
};

const ensureFeedingData = async (force = false) => {
    await ensureFeedingMethods();
    await ensureFeedingHistory({ force });
};

const openFeedingModal = async (type: 'primary' | 'supplemental' | 'other' = 'primary') => {
    await ensureFeedingMethods();
    feedingModalType.value = type;
    feedingModalMethodId.value =
        type === 'primary'
            ? farmer.value?.primary_feeding_method_id ?? null
            : type === 'supplemental'
              ? farmer.value?.supplemental_feeding_method_id ?? null
              : null;
    showFeedingModal.value = true;
};

const handleFeedingModalClose = () => {
    showFeedingModal.value = false;
};

const handleFeedingRecorded = async () => {
    showFeedingModal.value = false;
    await loadFarmer(farmerId.value);
    await ensureFeedingHistory({ force: true });
};

const changeFeedingFilter = async (type: string) => {
    if (feedingHistoryFilters.feeding_type === type) return;
    feedingHistoryFilters.feeding_type = type;
    feedingHistoryFilters.page = 1;
    await ensureFeedingHistory({ force: true });
};

const isFeedingFilterActive = (value: string) => feedingHistoryFilters.feeding_type === value;

const changeFeedingPage = async (page: number) => {
    if (
        page < 1 ||
        page > feedingHistoryPagination.last_page ||
        page === feedingHistoryPagination.current_page
    ) {
        return;
    }

    await farmerStore.fetchFeedingHistory(farmerId.value, {
        page,
        feeding_type: feedingHistoryFilters.feeding_type,
    });
};

const statusChipClass = (status?: string | null) => {
    if (status === 'active') return 'bg-green-100 text-green-700';
    if (status === 'pending') return 'bg-yellow-100 text-yellow-700';
    if (status === 'inactive') return 'bg-gray-100 text-gray-600';
    return 'bg-gray-100 text-gray-600';
};

const statusChipIcon = (status?: string | null) => {
    if (status === 'active') return 'mdi:check-circle-outline';
    if (status === 'pending') return 'mdi:clock-outline';
    if (status === 'inactive') return 'mdi:alert-circle-outline';
    return 'mdi:help-circle-outline';
};

const ensureMilkCenters = async () => {
    if (!milkCenters.value.length) {
        try {
            await geographyStore.getMilkCollectionCenters();
        } catch {
            // error handled in store
        }
    }
};

const loadFarmer = async (id: number) => {
    if (Number.isNaN(id)) {
        farmerStore.detailError = 'Invalid farmer identifier.';
        return;
    }

    try {
        await farmerStore.fetchFarmer(id);
    } catch {
        // error already handled in store
    }
};

const openEditModal = async () => {
    await ensureMilkCenters();
    showEditModal.value = true;
};

const openAddCowModal = () => {
    showAddCowModal.value = true;
};

const handleCowModalClose = () => {
    showAddCowModal.value = false;
};

const openAddMilkModal = () => {
    showAddMilkModal.value = true;
};

const handleMilkModalClose = () => {
    showAddMilkModal.value = false;
};

const selectTab = (tab: TabKey) => {
    activeTab.value = tab;
};

const handleModalClose = () => {
    showEditModal.value = false;
};

const handleFarmerUpdated = async () => {
    showEditModal.value = false;
    await loadFarmer(farmerId.value);
};

const handleCowCreated = async () => {
    await loadFarmer(farmerId.value);
};

const handleMilkRecordCreated = async () => {
    showAddMilkModal.value = false;
    await loadFarmer(farmerId.value);
};

watch(activeTab, async (tab) => {
    if (tab === 'feeding' && !feedingDataLoaded.value) {
        await ensureFeedingData();
    }
});

onMounted(async () => {
    await Promise.all([loadFarmer(farmerId.value), ensureMilkCenters()]);
});

watch(
    farmerId,
    async (newId) => {
        feedingDataLoaded.value = false;
        feedingHistory.value = [];
        await loadFarmer(newId);

        if (activeTab.value === 'feeding') {
            await ensureFeedingData(true);
        }
    }
);
</script>

