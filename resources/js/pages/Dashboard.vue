<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Boxes,
    CircleCheck,
    PackageOpen,
    Plus,
    Send,
    Wallet,
} from '@lucide/vue';
import { computed } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatCurrency, formatDateTime } from '@/lib/format';
import { dashboard } from '@/routes';
import {
    create as createShipment,
    index as shipments,
} from '@/routes/shipments';
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

const props = defineProps<{
    stats: {
        total_shipments: number;
        in_transit: number;
        delivered: number;
        total_money: number;
        total_students: number | null;
    };
    regionStats: Array<{
        id: number;
        name: string;
        code: string;
        shipments_count: number;
    }>;
    recentShipments: RecentShipment[];
    isSuperAdmin: boolean;
}>();

const page = usePage<{
    auth: {
        user?: { name?: string; role_label?: string; region_name?: string };
    };
}>();
const user = computed(() => page.props.auth.user);

const greeting = computed(() => {
    const name = user.value?.name?.split(' ')[0] || 'Admin';

    if (props.isSuperAdmin) {
        return {
            title: `Selamat datang, ${name}`,
            sub: 'Pantau seluruh kiriman santri dari semua wilayah IKSASS.',
        };
    }

    const region = user.value?.region_name || 'wilayah Anda';

    return {
        title: `Selamat datang, ${name}`,
        sub: `Kelola dan pantau pengiriman santri dari ${region} dengan mudah.`,
    };
});

const deliveryRate = computed(() => {
    if (props.stats.total_shipments === 0) {
        return 0;
    }

    return Math.round(
        (props.stats.delivered / props.stats.total_shipments) * 100,
    );
});

const maxRegion = computed(() =>
    Math.max(1, ...props.regionStats.map((r) => r.shipments_count)),
);

const kpis = computed(() => [
    {
        label: 'Total Kiriman',
        value: props.stats.total_shipments.toLocaleString('id-ID'),
        hint: 'Seluruh entri kiriman',
        icon: Boxes,
        iconClass:
            'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400',
    },
    {
        label: 'Dalam Perjalanan',
        value: props.stats.in_transit.toLocaleString('id-ID'),
        hint: 'Sedang menuju pondok',
        icon: Send,
        iconClass:
            'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400',
    },
    {
        label: 'Diserahkan',
        value: props.stats.delivered.toLocaleString('id-ID'),
        hint: `${deliveryRate.value}% dari total`,
        icon: CircleCheck,
        iconClass:
            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400',
    },
    {
        label: 'Nominal Kiriman',
        value: formatCurrency(props.stats.total_money),
        hint: 'Total dana tercatat',
        icon: Wallet,
        iconClass:
            'bg-accent-100 text-accent-700 dark:bg-accent-950/60 dark:text-accent-400',
    },
]);
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex min-h-full w-full flex-col gap-6 overflow-x-hidden p-4 md:p-6"
    >
        <!-- Hero / Greeting -->
        <div
            class="flex flex-col gap-4 rounded-2xl border border-primary-700/30 bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 p-6 shadow-sm dark:border-primary-400/20 dark:from-primary-950 dark:via-primary-900 dark:to-primary-800"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex flex-col gap-1">
                    <h2 class="text-2xl font-bold tracking-tight text-white">
                        {{ greeting.title }}
                    </h2>
                    <p class="text-sm text-primary-50/90">
                        {{ greeting.sub }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Button as-child variant="outline" class="shrink-0">
                        <Link :href="shipments()">
                            Semua Kiriman
                            <ArrowRight />
                        </Link>
                    </Button>
                    <Button as-child variant="accent" class="shrink-0">
                        <Link :href="createShipment()">
                            <Plus />
                            Input Kiriman
                        </Link>
                    </Button>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <Card
                v-for="kpi in kpis"
                :key="kpi.label"
                class="gap-0 overflow-hidden py-5"
            >
                <CardContent class="flex items-start justify-between px-5">
                    <div class="space-y-1">
                        <div class="text-sm font-medium text-muted-foreground">
                            {{ kpi.label }}
                        </div>
                        <div class="text-2xl font-bold tracking-tight">
                            {{ kpi.value }}
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ kpi.hint }}
                        </div>
                    </div>
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl"
                        :class="kpi.iconClass"
                    >
                        <component :is="kpi.icon" class="size-5" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid flex-1 grid-cols-1 gap-4 xl:grid-cols-3">
            <!-- Recent Shipments -->
            <Card class="min-w-0 xl:col-span-2">
                <CardHeader class="flex-row flex-wrap items-center justify-between">
                    <div>
                        <CardTitle>Kiriman Terbaru</CardTitle>
                        <CardDescription>
                            Riwayat kiriman terakhir yang tercatat.
                        </CardDescription>
                    </div>
                    <Button as-child variant="ghost" size="sm" class="-mr-2">
                        <Link :href="shipments()">
                            Lihat semua
                            <ArrowRight class="size-3.5" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent class="overflow-x-auto px-0 pb-0">
                    <table class="w-full text-sm">
                        <thead>
                            <tr
                                class="border-y border-border bg-surface-muted/60 text-left"
                            >
                                <th
                                    class="px-5 py-2.5 font-medium text-muted-foreground"
                                >
                                    Tracking
                                </th>
                                <th
                                    class="px-3 py-2.5 font-medium text-muted-foreground"
                                >
                                    Santri
                                </th>
                                <th
                                    class="hidden px-3 py-2.5 font-medium text-muted-foreground md:table-cell"
                                >
                                    Paket
                                </th>
                                <th
                                    class="px-3 py-2.5 font-medium text-muted-foreground"
                                >
                                    Status
                                </th>
                                <th
                                    class="hidden px-3 py-2.5 font-medium text-muted-foreground lg:table-cell"
                                >
                                    Update
                                </th>
                                <th
                                    class="px-3 py-2.5 font-medium text-muted-foreground"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in recentShipments"
                                :key="item.id"
                                class="border-b border-border last:border-0 hover:bg-surface-muted/50"
                            >
                                <td class="px-5 py-3 font-mono text-xs">
                                    <Link
                                        :href="`/shipments/${item.id}`"
                                        class="font-medium text-primary-700 hover:underline dark:text-primary-400"
                                    >
                                        {{ item.tracking_code }}
                                    </Link>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-medium">
                                        {{ item.student_name }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        NIS {{ item.student_nis }}
                                    </div>
                                </td>
                                <td class="hidden px-3 py-3 md:table-cell">
                                    {{ item.package_count }} koli
                                </td>
                                <td class="px-3 py-3">
                                    <ShipmentStatusBadge
                                        :status="item.status"
                                        size="sm"
                                    />
                                </td>
                                <td
                                    class="hidden px-3 py-3 text-xs text-muted-foreground lg:table-cell"
                                >
                                    {{ formatDateTime(item.created_at) }}
                                </td>
                                <td class="px-3 py-3">
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="sm"
                                        class="text-primary-700 hover:text-primary-800 dark:text-primary-400"
                                    >
                                        <Link :href="`/shipments/${item.id}`">
                                            Detail
                                        </Link>
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <EmptyState
                        v-if="recentShipments.length === 0"
                        :icon="PackageOpen"
                        title="Belum ada kiriman"
                        description="Belum ada data kiriman untuk ditampilkan."
                    />
                </CardContent>
            </Card>

            <!-- Right column -->
            <div class="flex min-w-0 flex-col gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            {{
                                isSuperAdmin
                                    ? 'Kiriman per Wilayah'
                                    : 'Ringkasan Kiriman'
                            }}
                        </CardTitle>
                        <CardDescription>
                            {{
                                isSuperAdmin
                                    ? 'Distribusi kiriman dari setiap IKSASS daerah.'
                                    : 'Distribusi status kiriman wilayah Anda.'
                            }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <template v-if="isSuperAdmin">
                            <div
                                v-for="region in regionStats"
                                :key="region.id"
                                class="space-y-1.5"
                            >
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="font-medium">{{
                                        region.name
                                    }}</span>
                                    <span class="text-xs text-muted-foreground">
                                        {{ region.shipments_count }}
                                    </span>
                                </div>
                                <div
                                    class="h-1.5 overflow-hidden rounded-full bg-surface-muted"
                                >
                                    <div
                                        class="h-full rounded-full bg-primary-600"
                                        :style="{
                                            width: `${Math.round((region.shipments_count / maxRegion) * 100)}%`,
                                        }"
                                    />
                                </div>
                            </div>
                            <p
                                v-if="regionStats.length === 0"
                                class="text-sm text-muted-foreground"
                            >
                                Belum ada wilayah IKSASS.
                            </p>
                        </template>
                        <template v-else>
                            <div class="space-y-1.5">
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span>Dalam Perjalanan</span>
                                    <span class="text-xs text-muted-foreground">
                                        {{ stats.in_transit }}
                                    </span>
                                </div>
                                <div
                                    class="h-1.5 overflow-hidden rounded-full bg-surface-muted"
                                >
                                    <div
                                        class="h-full rounded-full bg-info"
                                        :style="{
                                            width: `${(stats.in_transit / Math.max(1, stats.total_shipments)) * 100}%`,
                                        }"
                                    />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <div
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span>Diserahkan</span>
                                    <span class="text-xs text-muted-foreground">
                                        {{ stats.delivered }}
                                    </span>
                                </div>
                                <div
                                    class="h-1.5 overflow-hidden rounded-full bg-surface-muted"
                                >
                                    <div
                                        class="h-full rounded-full bg-success"
                                        :style="{
                                            width: `${(stats.delivered / Math.max(1, stats.total_shipments)) * 100}%`,
                                        }"
                                    />
                                </div>
                            </div>
                            <Button as-child variant="outline" class="w-full">
                                <Link :href="shipments()"
                                    >Lihat Kiriman Saya</Link
                                >
                            </Button>
                        </template>
                    </CardContent>
                </Card>

                <Card
                    class="border-primary-100 bg-primary-50/60 dark:border-primary-900/50 dark:bg-primary-950/30"
                >
                    <CardHeader>
                        <CardTitle
                            class="text-sm font-semibold text-primary-900 dark:text-primary-200"
                        >
                            Aksi Cepat
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-2">
                        <Button as-child class="w-full justify-start">
                            <Link :href="createShipment()">
                                <Plus />
                                Input Kiriman Baru
                            </Link>
                        </Button>
                        <Button
                            as-child
                            variant="outline"
                            class="w-full justify-start"
                        >
                            <Link :href="shipments()">
                                <Boxes />
                                Kelola Kiriman
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
