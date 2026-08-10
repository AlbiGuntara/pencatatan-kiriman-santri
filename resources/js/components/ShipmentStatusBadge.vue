<script setup lang="ts">
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import type { ShipmentStatus } from '@/types';

const props = defineProps<{
    status: ShipmentStatus;
}>();

const styles: Record<ShipmentStatus, string> = {
    created: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    in_transit:
        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    delivered:
        'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
};

const labels: Record<ShipmentStatus, string> = {
    created: 'Draft / Dibuat',
    in_transit: 'Dalam Perjalanan',
    delivered: 'Tiba & Diserahkan',
};

const computedClass = computed(() => styles[props.status] ?? 'bg-muted text-muted-foreground');
const computedLabel = computed(() => labels[props.status] ?? props.status);
</script>

<template>
    <Badge :class="computedClass" variant="outline">{{ computedLabel }}</Badge>
</template>
