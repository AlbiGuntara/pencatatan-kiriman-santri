<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name;
</script>

<template>
    <div
        class="flex aspect-square size-9 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-sidebar-primary/20 ring-1 ring-sidebar-primary/40 transition-all duration-200 ease-linear group-data-[collapsible=icon]:size-8 group-data-[collapsible=icon]:rounded-md"
    >
        <AppLogoIcon class="size-full p-1" />
    </div>
    <div
        class="ml-1 grid flex-1 text-left text-sm group-data-[collapsible=icon]:hidden"
    >
        <span class="mb-0.5 truncate leading-tight font-semibold">{{
            name
        }}</span>
    </div>
</template>