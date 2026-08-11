<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Boxes, MapPin, Printer, ScanLine } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import { dashboard, login } from '@/routes';
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <header class="w-full border-b border-border">
            <nav
                class="mx-auto flex max-w-4xl items-center justify-between px-6 py-5"
            >
                <div class="flex items-center gap-2">
                    <AppLogo />
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-lg bg-primary-700 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary-800"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-lg border border-border bg-white px-4 py-2 text-sm font-medium transition-colors hover:bg-primary-50"
                        >
                            Masuk
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <main
            class="mx-auto flex w-full max-w-4xl flex-1 flex-col items-center justify-center gap-10 px-6 py-16"
        >
            <div class="text-center">
                <div
                    class="mb-4 flex items-center justify-center gap-2 text-sm font-semibold text-primary-700 dark:text-primary-400"
                >
                    <span
                        class="inline-block h-2 w-2 rounded-full bg-primary-600"
                    />
                    SIPENRI &middot; Pondok Pesantren Salafiyah Syafi'iyah
                    Sukorejo
                </div>
                <h1
                    class="mb-4 text-4xl leading-tight font-bold tracking-tight lg:text-5xl"
                >
                    Sistem Pendataan &amp; Pelacakan Kiriman Santri
                </h1>
                <p
                    class="mx-auto mb-10 max-w-2xl text-[15px] leading-relaxed text-muted-foreground"
                >
                    Kelola, catat, dan lacak pengiriman paket serta dana dari
                    wali santri melalui jaringan IKSASS se-Nusantara. Cetak
                    stiker label termal, unggah bukti foto, dan pantau
                    perjalanan kiriman hingga diserahkan langsung kepada santri.
                </p>

                <div class="grid gap-3 text-left sm:grid-cols-2">
                    <div
                        class="rounded-xl border border-border bg-white p-5 shadow-sm"
                    >
                        <Boxes class="mb-2 size-5 text-primary-600" />
                        <div class="text-sm font-semibold">
                            Pencatatan Terpusat
                        </div>
                        <p
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            Data santri dan kiriman terstruktur dalam basis data
                            relasional.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-border bg-white p-5 shadow-sm"
                    >
                        <MapPin class="mb-2 size-5 text-primary-600" />
                        <div class="text-sm font-semibold">
                            Isolasi Multi-Wilayah
                        </div>
                        <p
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            Setiap Admin IKSASS daerah hanya mengelola data
                            daerahnya sendiri.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-border bg-white p-5 shadow-sm"
                    >
                        <Printer class="mb-2 size-5 text-accent-600" />
                        <div class="text-sm font-semibold">
                            Stiker Label Standar
                        </div>
                        <p
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            Cetak stiker massal maupun satuan untuk thermal
                            printer.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-border bg-white p-5 shadow-sm"
                    >
                        <ScanLine class="mb-2 size-5 text-accent-600" />
                        <div class="text-sm font-semibold">
                            Tracking &amp; Bukti Foto
                        </div>
                        <p
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            Linimasa status lengkap dengan QR code dan
                            dokumentasi foto.
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <footer
            class="border-t border-border py-6 text-center text-xs text-muted-foreground"
        >
            &copy; 2026 Pondok Pesantren Salafiyah Syafi'iyah Sukorejo &middot;
            SIPENRI
        </footer>
    </div>
</template>
