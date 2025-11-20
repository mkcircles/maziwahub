<template>
    <div class="rounded-lg bg-white p-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <slot name="title">{{ title }}</slot>
                </h3>
                <p v-if="$slots.subtitle || subtitle" class="text-[11px] text-gray-400">
                    <slot name="subtitle">{{ subtitle }}</slot>
                </p>
            </div>
            <slot name="icon">
                <Icon :icon="icon" :size="20" :class="iconClass" v-if="icon" />
            </slot>
        </div>
        <p class="mt-3 text-xl font-semibold text-gray-900">
            <slot>{{ value }}</slot>
        </p>
        <p v-if="$slots.caption || caption" class="mt-1 text-[10px] text-gray-500">
            <slot name="caption">{{ caption }}</slot>
        </p>
    </div>
</template>

<script setup lang="ts">
import Icon from '../shared/Icon.vue';

withDefaults(
    defineProps<{
        title?: string;
        subtitle?: string;
        caption?: string;
        value?: string | number;
        icon?: string;
        iconClass?: string;
    }>(),
    {
        title: '',
        subtitle: '',
        caption: '',
        value: '',
        icon: '',
        iconClass: 'text-gray-400',
    }
);
</script>

