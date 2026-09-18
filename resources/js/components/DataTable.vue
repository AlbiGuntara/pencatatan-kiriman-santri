<script setup lang="ts">
import type { Component } from 'vue';
import DataTableFooter from '@/components/DataTableFooter.vue';
import { Badge } from '@/components/ui/badge';
import { cn } from '@/lib/utils';

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    title: string;
    description?: string;
    icon?: Component;
    class?: string;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
    perPage: string;
}>();

const emit = defineEmits<{
    'update:perPage': [value: string];
}>();
</script>

<template>
    <div
        :class="
            cn(
                'overflow-hidden rounded-xl border border-border bg-card shadow-card',
                props.class,
            )
        "
    >
        <div
            class="flex flex-wrap items-center justify-between gap-3 border-b border-border bg-surface-muted/70 px-4 py-3 dark:bg-surface-muted/90"
        >
            <div class="flex min-w-0 items-center gap-2.5">
                <span
                    v-if="icon"
                    class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400"
                >
                    <component :is="icon" class="size-4" />
                </span>
                <div class="min-w-0">
                    <h3 class="truncate text-sm font-semibold tracking-tight">
                        {{ title }}
                    </h3>
                    <p
                        v-if="description"
                        class="truncate text-xs text-muted-foreground"
                    >
                        {{ description }}
                    </p>
                </div>
            </div>
            <Badge
                variant="secondary"
                class="rounded-md px-2 py-1 font-mono text-[11px] font-semibold"
            >
                {{ total.toLocaleString('id-ID') }} data
            </Badge>
        </div>

        <div class="overflow-x-auto">
            <slot />
        </div>

        <DataTableFooter
            v-if="total > 0"
            bordered
            :per-page="perPage"
            :total="total"
            :from="from"
            :to="to"
            :links="links"
            @update:per-page="emit('update:perPage', $event)"
        />
    </div>
</template>
