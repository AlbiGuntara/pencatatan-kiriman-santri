<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Boxes, MapPin, Printer, ScanLine } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import { dashboard, login } from '@/routes';
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <header class="w-full">
            <nav class="mx-auto flex max-w-4xl items-center justify-between px-6 py-5">
                <div class="flex items-center gap-2">
                    <AppLogo />
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-md bg-[#1b1b18] px-4 py-1.5 text-sm text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-md border border-[#19140035] px-4 py-1.5 text-sm hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                        >
                            Masuk
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <main class="flex flex-1 flex-col items-center justify-center gap-10 px-6 py-16 lg:flex-row lg:gap-20">
            <div class="max-w-xl">
                <div class="mb-4 flex items-center gap-2 text-sm font-semibold text-[#f53003] dark:text-[#FF4433]">
                    <span class="inline-block h-2 w-2 rounded-full bg-current" />
                    SIPENRI &middot; Pondok Pesantren Salafiyah Syafi'iyah Sukorejo
                </div>
                <h1 class="mb-4 text-4xl font-semibold leading-tight tracking-tight lg:text-5xl">
                    Sistem Pendataan &amp; Pelacakan Kiriman Santri
                </h1>
                <p class="mb-8 text-[15px] leading-relaxed text-[#706f6c] dark:text-[#A1A09A]">
                    Kelola, catat, dan lacak pengiriman paket serta dana dari wali santri melalui
                    jaringan IKSASS se-Nusantara. Cetak stiker label termal, unggah bukti foto,
                    dan pantau perjalanan kiriman hingga diserahkan langsung kepada santri.
                </p>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border p-4">
                        <Boxes class="mb-2 size-5 text-[#f53003] dark:text-[#FF4433]" />
                        <div class="text-sm font-semibold">Pencatatan Terpusat</div>
                        <p class="mt-1 text-xs leading-relaxed text-muted-foreground">
                            Data santri dan kiriman terstruktur dalam basis data relasional.
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <MapPin class="mb-2 size-5 text-[#f53003] dark:text-[#FF4433]" />
                        <div class="text-sm font-semibold">Isolasi Multi-Wilayah</div>
                        <p class="mt-1 text-xs leading-relaxed text-muted-foreground">
                            Setiap Admin IKSASS daerah hanya mengelola data daerahnya sendiri.
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <Printer class="mb-2 size-5 text-[#f53003] dark:text-[#FF4433]" />
                        <div class="text-sm font-semibold">Stiker Label Standar</div>
                        <p class="mt-1 text-xs leading-relaxed text-muted-foreground">
                            Cetak stiker massal maupun satuan untuk thermal printer.
                        </p>
                    </div>
                    <div class="rounded-lg border p-4">
                        <ScanLine class="mb-2 size-5 text-[#f53003] dark:text-[#FF4433]" />
                        <div class="text-sm font-semibold">Tracking &amp; Bukti Foto</div>
                        <p class="mt-1 text-xs leading-relaxed text-muted-foreground">
                            Linimasa status lengkap dengan QR code dan dokumentasi foto.
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-t py-6 text-center text-xs text-[#706f6c] dark:text-[#A1A09A]">
            &copy; 2026 Pondok Pesantren Salafiyah Syafi'iyah Sukorejo &middot; SIPENRI
        </footer>
    </div>
</template>
