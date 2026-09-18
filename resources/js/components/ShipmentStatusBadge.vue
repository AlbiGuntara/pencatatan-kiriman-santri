<script setup lang="ts">
import { CircleCheck, CircleDashed, Truck } from '@lucide/vue';
import { computed } from 'vue';
import type { Component } from 'vue';
import { Badge } from '@/components/ui/badge';
import type { ShipmentStatus } from '@/types';

const props = defineProps<{
    status: ShipmentStatus;
    size?: 'sm' | 'md';
}>();

const config: Record<
    ShipmentStatus,
    { icon: Component; label: string; className: string }
> = {
    created: {
        icon: CircleDashed,
        label: 'Dibuat',
        className:
            'bg-slate-100 text-slate-600 dark:bg-slate-800/80 dark:text-slate-300 border-slate-200 dark:border-slate-700',
    },
    in_transit: {
        icon: Truck,
        label: 'Dalam Perjalanan',
        className:
            'bg-sky-100 text-sky-700 dark:bg-sky-950/70 dark:text-sky-300 border-sky-200 dark:border-sky-900',
    },
    delivered: {
        icon: CircleCheck,
        label: 'Diserahkan',
        className:
            'bg-primary-100 text-primary-800 dark:bg-primary-950/70 dark:text-primary-300 border-primary-200 dark:border-primary-900',
    },
};

const current = computed(
    () =>
        config[props.status] ?? {
            icon: CircleDashed,
            label: props.status,
            className: 'bg-muted text-muted-foreground',
        },
);
</script>

<template>
    <Badge
        :class="[
            current.className,
            size === 'sm' ? 'px-2 py-0 text-[11px]' : 'px-2.5 py-0.5 text-xs',
        ]"
        variant="outline"
    >
        <component :is="current.icon" class="size-3" />
        {{ current.label }}
    </Badge>
</template>
