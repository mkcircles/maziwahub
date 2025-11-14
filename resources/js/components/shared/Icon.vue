<template>
    <IconifyIcon :icon="icon" :class="className" :width="size" :height="size" />
</template>

<script setup lang="ts">
import { Icon as IconifyIcon } from '@iconify/vue';

interface Props {
    icon: string;
    size?: string | number;
    className?: string;
}

withDefaults(defineProps<Props>(), {
    size: 20,
    className: '',
});
</script>

