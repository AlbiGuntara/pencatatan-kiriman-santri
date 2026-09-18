<script setup lang="ts">
import { ArrowDown, ArrowUp, ArrowUpDown } from '@lucide/vue';

export type SortState = {
    column: string | null;
    direction: 'asc' | 'desc';
};

const props = defineProps<{
    label: string;
    column: string;
    sort: SortState;
    align?: 'left' | 'right';
    class?: string;
}>();

const emit = defineEmits<{
    sort: [column: string];
}>();
</script>

<template>
    <th
        :class="[
            'whitespace-nowrap px-3 py-3',
            align === 'right' ? 'text-right' : 'text-left',
            props.class,
        ]"
    >
        <button
            type="button"
            class="-ml-1.5 inline-flex h-7 items-center gap-1.5 rounded-md px-1.5 text-[11px] font-semibold tracking-wider text-muted-foreground uppercase transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            @click="emit('sort', column)"
        >
            {{ label }}
            <ArrowDown
                v-if="sort.column === column && sort.direction === 'asc'"
                class="size-3.5"
            />
            <ArrowUp
                v-else-if="sort.column === column && sort.direction === 'desc'"
                class="size-3.5"
            />
            <ArrowUpDown v-else class="size-3.5 opacity-50" />
        </button>
    </th>
</template>
