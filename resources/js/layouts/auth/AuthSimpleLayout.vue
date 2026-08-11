<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh items-center justify-center overflow-hidden bg-primary-950 p-6 md:p-10"
    >
        <!-- Subtle Islamic geometric pattern -->
        <div
            class="absolute inset-0 opacity-[0.07]"
            aria-hidden="true"
            style="
                background-image: url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2256%22%20height%3D%2256%22%20viewBox%3D%220%200%2056%2056%22%3E%3Cg%20fill%3D%22none%22%20stroke%3D%22%23FFFFFF%22%20stroke-width%3D%221%22%3E%3Cpath%20d%3D%22M28%200l7%2021%2021%207-21%207-7%2021-7-21-21-7%2021-7z%22%2F%3E%3Ccircle%20cx%3D%2228%22%20cy%3D%2228%22%20r%3D%224%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E');
                background-size: 56px 56px;
            "
        />

        <!-- Soft glows -->
        <div
            class="pointer-events-none absolute -top-32 right-0 size-96 rounded-full bg-primary-500/20 blur-3xl"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute bottom-0 -left-32 size-96 rounded-full bg-accent-500/10 blur-3xl"
            aria-hidden="true"
        />

        <div class="relative z-10 w-full max-w-sm">
            <div class="flex flex-col items-center gap-3">
                <div
                    class="flex size-14 items-center justify-center overflow-hidden rounded-2xl bg-white/10 ring-1 ring-white/20 backdrop-blur"
                >
                    <AppLogoIcon class="size-10" />
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    {{ title }}
                </h1>
                <p
                    v-if="description"
                    class="max-w-xs text-center text-sm text-primary-100/80"
                >
                    {{ description }}
                </p>
                <span
                    class="mt-1 h-1 w-14 rounded-full bg-accent-500"
                    aria-hidden="true"
                />
            </div>

            <div class="mt-7">
                <div
                    class="rounded-2xl border border-white/10 bg-white/95 p-6 shadow-elevated backdrop-blur-sm"
                >
                    <slot />
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-primary-100/60">
                SIPENRI &middot; Pondok Pesantren Salafiyah Syafi'iyah Sukorejo
            </p>
        </div>
    </div>
</template>
