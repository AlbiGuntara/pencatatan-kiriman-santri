<script setup lang="ts">
import { Boxes, MapPin, Printer, ScanLine } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh overflow-hidden bg-primary-950">
        <!-- Full-page subtle Islamic geometric pattern -->
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
            class="pointer-events-none absolute -top-32 right-1/3 size-96 rounded-full bg-primary-500/20 blur-3xl"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute bottom-0 -left-32 size-96 rounded-full bg-accent-500/10 blur-3xl"
            aria-hidden="true"
        />

        <!-- Hero panel (left) -->
        <div
            class="relative z-10 hidden flex-1 flex-col justify-between p-12 lg:flex"
        >
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <div
                    class="flex size-12 items-center justify-center overflow-hidden rounded-xl bg-white/10 ring-1 ring-white/20 backdrop-blur"
                >
                    <AppLogoIcon class="size-9" />
                </div>
                <div class="leading-tight">
                    <div class="font-semibold tracking-tight text-white">
                        SIPENRI
                    </div>
                    <div class="text-xs text-primary-200/80">
                        IKSASS &middot; Sukorejo
                    </div>
                </div>
            </div>

            <!-- Value proposition -->
            <div class="space-y-8">
                <div class="space-y-4">
                    <h1
                        class="max-w-md text-3xl leading-tight font-bold text-white"
                    >
                        Sistem Pendataan &amp; Pelacakan Kiriman Santri
                    </h1>
                    <p
                        class="max-w-md text-sm leading-relaxed text-primary-100/85"
                    >
                        Kelola, catat, dan lacak pengiriman paket serta dana
                        dari wali santri melalui jaringan IKSASS se-Nusantara,
                        hingga diserahkan langsung kepada santri.
                    </p>
                </div>

                <div class="grid max-w-lg gap-3 sm:grid-cols-2">
                    <div
                        class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur"
                    >
                        <Boxes class="mb-2 size-5 text-primary-300" />
                        <div class="text-sm font-semibold text-white">
                            Pencatatan Terpusat
                        </div>
                        <p class="mt-1 text-xs text-primary-100/70">
                            Data santri &amp; kiriman terstruktur dan rapi.
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur"
                    >
                        <MapPin class="mb-2 size-5 text-primary-300" />
                        <div class="text-sm font-semibold text-white">
                            Isolasi Multi-Wilayah
                        </div>
                        <p class="mt-1 text-xs text-primary-100/70">
                            Setiap daerah hanya melihat data daerahnya.
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur"
                    >
                        <Printer class="mb-2 size-5 text-accent-400" />
                        <div class="text-sm font-semibold text-white">
                            Stiker Label Standar
                        </div>
                        <p class="mt-1 text-xs text-primary-100/70">
                            Cetak stiker termal massal maupun satuan.
                        </p>
                    </div>
                    <div
                        class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10 backdrop-blur"
                    >
                        <ScanLine class="mb-2 size-5 text-accent-400" />
                        <div class="text-sm font-semibold text-white">
                            Tracking &amp; Bukti Foto
                        </div>
                        <p class="mt-1 text-xs text-primary-100/70">
                            Linimasa status lengkap dengan dokumentasi.
                        </p>
                    </div>
                </div>
            </div>

            <p class="text-xs text-primary-100/60">
                &copy; 2026 Pondok Pesantren Salafiyah Syafi'iyah Sukorejo
            </p>
        </div>

        <!-- Login card (right) — enlarged panel touching top/bottom/right -->
        <div
            class="relative z-10 flex flex-1 items-center justify-center p-6 md:p-10 lg:items-stretch lg:justify-start lg:py-2.5 lg:pr-2.5 lg:pl-0"
        >
            <div
                class="relative flex w-full max-w-md flex-col justify-center overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-elevated lg:w-full lg:max-w-none lg:rounded-3xl lg:px-14 lg:py-10 lg:shadow-none"
            >
                <!-- Corner ornamental star (desktop) -->
                <svg
                    class="pointer-events-none absolute -top-10 -right-10 hidden size-44 text-primary-950/10 lg:block"
                    viewBox="0 0 100 100"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1"
                    aria-hidden="true"
                >
                    <path
                        d="M50 2l13 35 35 13-35 13-13 35-13-35-35-13 35-13z"
                        opacity="0.6"
                    />
                    <circle cx="50" cy="50" r="5" />
                    <path
                        d="M50 20v60M20 50h60M29 29l42 42M71 29L29 71"
                        opacity="0.5"
                    />
                </svg>

                <!-- Subtle pattern watermark (desktop) -->
                <div
                    class="pointer-events-none absolute right-0 bottom-0 hidden size-72 opacity-[0.05] lg:block"
                    aria-hidden="true"
                    style="
                        background-image: url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2248%22%20height%3D%2248%22%20viewBox%3D%220%200%2048%2048%22%3E%3Cg%20fill%3D%22none%22%20stroke%3D%22%23063B25%22%20stroke-width%3D%221%22%3E%3Cpath%20d%3D%22M24%200l6%2018%2018%206-18%206-6%2018-6-18-18-6%2018-6z%22%2F%3E%3Ccircle%20cx%3D%2224%22%20cy%3D%2224%22%20r%3D%224%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E');
                        background-size: 48px 48px;
                    "
                />

                <!-- Mobile brand header -->
                <div class="mb-6 flex items-center gap-3 lg:hidden">
                    <div
                        class="flex size-11 items-center justify-center overflow-hidden rounded-xl bg-primary-950"
                    >
                        <AppLogoIcon class="size-8" />
                    </div>
                    <div>
                        <div
                            class="font-bold text-primary-950 dark:text-primary-100"
                        >
                            SIPENRI
                        </div>
                        <div class="text-xs text-muted-foreground">
                            Pondok Pesantren Sukorejo
                        </div>
                    </div>
                </div>

                <!-- Heading -->
                <div class="mb-6 flex flex-col gap-1.5">
                    <h1 class="text-2xl font-bold tracking-tight">
                        {{ title }}
                    </h1>
                    <p v-if="description" class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                    <div
                        class="mt-2 h-0.5 w-16 rounded-full bg-gradient-to-r from-primary-600 to-accent-500"
                        aria-hidden="true"
                    />
                </div>

                <slot />

                <p
                    class="mt-8 text-center text-xs text-muted-foreground lg:hidden"
                >
                    SIPENRI &middot; Pondok Pesantren Salafiyah Syafi'iyah
                    Sukorejo
                </p>
            </div>
        </div>
    </div>
</template>
