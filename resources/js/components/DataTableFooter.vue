<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

defineProps<{
    perPage: string;
    total: number;
    from: number | null;
    to: number | null;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    bordered?: boolean;
}>();

const emit = defineEmits<{
    'update:perPage': [value: string];
}>();

const options = [10, 15, 20, 25, 50, 100];
</script>

<template>
    <div
        :class="[
            'flex flex-col gap-3 px-4 py-3.5 sm:px-5',
            bordered
                ? 'border-t border-border bg-gradient-to-b from-surface-muted/70 to-surface-muted/30 dark:from-white/[0.04] dark:to-transparent'
                : '',
        ]"
    >
        <div
            class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"
        >
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                <div class="flex items-center gap-2">
                    <span
                        class="shrink-0 text-xs font-medium text-muted-foreground"
                    >
                        Tampilkan
                    </span>
                    <Select
                        :model-value="perPage"
                        @update:model-value="
                            emit('update:perPage', String($event))
                        "
                    >
                        <SelectTrigger
                            size="sm"
                            class="h-7 w-16 justify-center rounded-md border-input/80 bg-card px-1.5 font-semibold text-foreground"
                        >
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="opt in options"
                                :key="opt"
                                :value="String(opt)"
                            >
                                {{ opt }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span
                        class="shrink-0 text-xs font-medium text-muted-foreground"
                    >
                        per halaman
                    </span>
                </div>

                <span
                    aria-hidden="true"
                    class="hidden h-4 w-px bg-border md:block"
                ></span>

                <p
                    class="flex flex-wrap items-center gap-x-1 gap-y-0.5 text-xs text-muted-foreground"
                >
                    <span>Menampilkan</span>
                    <span class="font-semibold text-foreground tabular-nums">
                        {{ from ?? 0 }}
                    </span>
                    <span>&ndash;</span>
                    <span class="font-semibold text-foreground tabular-nums">
                        {{ to ?? 0 }}
                    </span>
                    <span>dari</span>
                    <span class="font-semibold text-foreground tabular-nums">
                        {{ total.toLocaleString('id-ID') }}
                    </span>
                    <span>data</span>
                </p>
            </div>

            <Pagination
                :links="links"
                :bordered="false"
                class="md:justify-end"
            />
        </div>
    </div>
</template>
