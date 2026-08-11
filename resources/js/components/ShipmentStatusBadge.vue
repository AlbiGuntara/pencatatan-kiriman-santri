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
            'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    },
    in_transit: {
        icon: Truck,
        label: 'Dalam Perjalanan',
        className:
            'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
    },
    delivered: {
        icon: CircleCheck,
        label: 'Diserahkan',
        className:
            'bg-primary-100 text-primary-800 dark:bg-primary-950 dark:text-primary-300',
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
