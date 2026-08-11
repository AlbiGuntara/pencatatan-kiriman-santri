<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Globe2, MapPin } from '@lucide/vue';
import { computed } from 'vue';

withDefaults(
    defineProps<{
        variant?: 'sidebar' | 'topbar';
    }>(),
    {
        variant: 'sidebar',
    },
);

const page = usePage<{
    auth: {
        user?: {
            role?: string;
            region_name?: string | null;
            region_code?: string | null;
        };
    };
}>();

const user = computed(() => page.props.auth.user);
const isSuperAdmin = computed(() => user.value?.role === 'super_admin');
const regionName = computed(() =>
    isSuperAdmin.value ? 'Semua Wilayah' : user.value?.region_name || 'Wilayah',
);
const regionCode = computed(() =>
    isSuperAdmin.value ? 'NASIONAL' : user.value?.region_code || 'IKSASS',
);
</script>

<template>
    <div
        v-if="variant === 'sidebar'"
        class="inline-flex items-center gap-2 rounded-full border border-sidebar-border bg-white/5 py-1 pr-3 pl-1.5 text-sidebar-foreground shadow-sm"
        role="status"
        aria-label="Wilayah aktif"
    >
        <span
            class="flex size-5 items-center justify-center rounded-full bg-sidebar-primary text-sidebar-primary-foreground"
        >
            <MapPin v-if="!isSuperAdmin" class="size-3" />
            <Globe2 v-else class="size-3" />
        </span>
        <span class="flex flex-col leading-tight">
            <span class="text-xs font-semibold text-sidebar-accent-foreground">
                {{ regionName }}
            </span>
            <span class="text-[10px] text-sidebar-foreground/70">
                {{ isSuperAdmin ? 'Seluruh wilayah' : 'Wilayah aktif' }}
                <span v-if="regionCode" class="font-medium">
                    &middot; {{ regionCode }}
                </span>
            </span>
        </span>
    </div>

    <div
        v-else
        class="inline-flex items-center gap-2 rounded-full border border-border bg-background py-1 pr-3 pl-1.5 shadow-sm"
        role="status"
        aria-label="Wilayah aktif"
    >
        <span
            class="flex size-5 items-center justify-center rounded-full bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400"
        >
            <MapPin v-if="!isSuperAdmin" class="size-3" />
            <Globe2 v-else class="size-3" />
        </span>
        <span class="flex flex-col leading-tight">
            <span class="text-xs font-semibold text-foreground">
                {{ regionName }}
            </span>
            <span class="text-[10px] text-muted-foreground">
                {{ isSuperAdmin ? 'Seluruh wilayah' : 'Wilayah aktif' }}
                <span v-if="regionCode" class="font-medium">
                    &middot; {{ regionCode }}
                </span>
            </span>
        </span>
    </div>
</template>
