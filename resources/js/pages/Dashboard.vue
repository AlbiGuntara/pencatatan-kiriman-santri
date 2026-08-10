<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { Boxes, CheckCircle2, DollarSign, Send, Users } from '@lucide/vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { formatCurrency } from '@/lib/format';
import { dashboard } from '@/routes';
import { index as shipments } from '@/routes/shipments';
import type { RecentShipment } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

defineProps<{
    stats: {
        total_shipments: number;
        in_transit: number;
        delivered: number;
        total_money: number;
        total_students: number | null;
    };
    regionStats: Array<{ id: number; name: string; code: string; shipments_count: number }>;
    recentShipments: RecentShipment[];
    isSuperAdmin: boolean;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-hidden p-4">
        <div class="flex flex-col gap-2">
            <h2 class="text-2xl font-semibold tracking-tight">Dashboard SIPENRI</h2>
            <p class="text-sm text-muted-foreground">
                Rekapitulasi kiriman santri Pondok Pesantren Salafiyah Syafi'iyah Sukorejo.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-sm font-medium text-muted-foreground">
                        Total Kiriman
                        <Boxes class="size-4" />
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.total_shipments }}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-sm font-medium text-muted-foreground">
                        Dalam Perjalanan
                        <Send class="size-4" />
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.in_transit }}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-sm font-medium text-muted-foreground">
                        Tiba & Diserahkan
                        <CheckCircle2 class="size-4" />
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.delivered }}</div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center justify-between text-sm font-medium text-muted-foreground">
                        Nominal Kiriman
                        <DollarSign class="size-4" />
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ formatCurrency(stats.total_money) }}</div>
                </CardContent>
            </Card>
        </div>

        <div class="grid flex-1 gap-4 xl:grid-cols-3">
            <Card class="xl:col-span-2">
                <CardHeader>
                    <CardTitle>Kiriman Terbaru</CardTitle>
                    <CardDescription>Riwayat kiriman terakhir yang tercatat.</CardDescription>
                </CardHeader>
                <CardContent class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-muted-foreground">
                                <th class="pb-2 pr-4 font-medium">Kode</th>
                                <th class="pb-2 pr-4 font-medium">Santri</th>
                                <th class="pb-2 pr-4 font-medium">Asal</th>
                                <th class="pb-2 pr-4 font-medium">Paket</th>
                                <th class="pb-2 pr-4 font-medium">Status</th>
                                <th class="pb-2 font-medium">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in recentShipments" :key="item.id" class="border-b last:border-0">
                                <td class="py-3 pr-4 font-mono text-xs">
                                    <Link :href="`/shipments/${item.id}`" class="font-medium text-primary hover:underline">
                                        {{ item.tracking_code }}
                                    </Link>
                                </td>
                                <td class="py-3 pr-4">
                                    <div class="font-medium">{{ item.student_name }}</div>
                                    <div class="text-xs text-muted-foreground">NIS {{ item.student_nis }}</div>
                                </td>
                                <td class="py-3 pr-4 text-muted-foreground">{{ item.region }}</td>
                                <td class="py-3 pr-4">{{ item.package_count }} koli</td>
                                <td class="py-3 pr-4">
                                    <ShipmentStatusBadge :status="item.status" />
                                </td>
                                <td class="py-3">{{ formatCurrency(item.amount_money) }}</td>
                            </tr>
                            <tr v-if="recentShipments.length === 0">
                                <td colspan="6" class="py-8 text-center text-muted-foreground">
                                    Belum ada kiriman tercatat.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users v-if="isSuperAdmin" class="size-4" />
                        {{ isSuperAdmin ? 'Rekapitulasi per Wilayah' : 'Lokasi Santri' }}
                    </CardTitle>
                    <CardDescription>
                        {{ isSuperAdmin ? 'Jumlah kiriman dari setiap IKSASS daerah.' : 'Cakupan akses wilayah Anda.' }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <template v-if="isSuperAdmin">
                        <div
                            v-for="region in regionStats"
                            :key="region.id"
                            class="flex items-center justify-between rounded-lg border px-3 py-2"
                        >
                            <div>
                                <div class="text-sm font-medium">{{ region.name }}</div>
                                <div class="text-xs text-muted-foreground">{{ region.code }}</div>
                            </div>
                            <Badge variant="secondary">{{ region.shipments_count }} kiriman</Badge>
                        </div>
                        <div v-if="regionStats.length === 0" class="text-sm text-muted-foreground">
                            Belum ada wilayah IKSASS.
                        </div>
                    </template>
                    <template v-else>
                        <div class="rounded-lg border px-3 py-2 text-sm text-muted-foreground">
                            Anda mengelola kiriman untuk wilayah
                            <span class="font-medium text-foreground">{{ $page.props.auth.user.region_name }}</span>.
                        </div>
                        <Button as-child variant="outline" class="w-full">
                            <Link :href="shipments()">Lihat Kiriman Saya</Link>
                        </Button>
                    </template>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
