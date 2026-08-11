<script setup lang="ts">
import { PackageOpen } from '@lucide/vue';
import type { Component } from 'vue';

type Props = {
    icon?: Component;
    title: string;
    description?: string;
    action?: Component;
};

withDefaults(defineProps<Props>(), {
    icon: PackageOpen,
    description: '',
});
</script>

<template>
    <div
        class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
    >
        <div
            class="flex size-14 items-center justify-center rounded-full bg-primary-50 text-primary-600 ring-1 ring-primary-100 dark:bg-primary-950/50 dark:text-primary-400 dark:ring-primary-900"
        >
            <component :is="icon" class="size-7" />
        </div>
        <div class="space-y-1">
            <p class="text-sm font-semibold text-foreground">{{ title }}</p>
            <p v-if="description" class="text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>
        <component v-if="action" :is="action" class="mt-1" />
    </div>
</template>
