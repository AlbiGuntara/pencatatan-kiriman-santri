<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Boxes,
    CalendarDays,
    CircleCheck,
    PackageOpen,
    PiggyBank,
    Plus,
    TrendingUp,
    Truck,
    Wallet,
} from '@lucide/vue';
import { computed } from 'vue';
import AreaChart from '@/components/charts/AreaChart.vue';
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
        created: number;
        total_money: number;
        total_students: number | null;
    };
    weeklyStats: Array<{
        label: string;
        short_label: string;
        count: number;
        money: number;
    }>;
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
        user?: {
            name?: string;
            role_label?: string;
            region_name?: string;
            avatar?: string | null;
        };
    };
}>();
const user = computed(() => page.props.auth.user);

const today = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
}).format(new Date());

const greeting = computed(() => {
    const name = user.value?.name?.split(' ')[0] || 'Admin';
    const hour = new Date().getHours();
    const part =
        hour < 11 ? 'Selamat pagi' : hour < 15 ? 'Selamat siang' : hour < 19 ? 'Selamat sore' : 'Selamat malam';

    if (props.isSuperAdmin) {
        return {
            title: `${part}, ${name}`,
            sub: 'Pantau seluruh kiriman santri dari semua wilayah IKSASS.',
        };
    }

    const region = user.value?.region_name || 'wilayah Anda';

    return {
        title: `${part}, ${name}`,
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

const weekTotal = computed(() =>
    props.weeklyStats.reduce((acc, d) => acc + d.count, 0),
);

const weekAverage = computed(() =>
    props.weeklyStats.length ? Math.round(weekTotal.value / props.weeklyStats.length) : 0,
);

const peakDay = computed(() =>
    [...props.weeklyStats].sort((a, b) => b.count - a.count)[0],
);

const chartData = computed(() =>
    props.weeklyStats.map((d) => ({ label: d.short_label, value: d.count })),
);

const statusSegments = computed(() => {
    const total = Math.max(
        1,
        props.stats.total_shipments ||
            props.stats.created + props.stats.in_transit + props.stats.delivered,
    );

    return [
        {
            label: 'Dibuat',
            value: props.stats.created,
            pct: (props.stats.created / total) * 100,
            color: 'var(--chart-5)',
        },
        {
            label: 'Dalam Perjalanan',
            value: props.stats.in_transit,
            pct: (props.stats.in_transit / total) * 100,
            color: 'var(--chart-4)',
        },
        {
            label: 'Diserahkan',
            value: props.stats.delivered,
            pct: (props.stats.delivered / total) * 100,
            color: 'var(--chart-1)',
        },
    ];
});

const donutSegments = computed(() => {
    let offset = 0;
    const circumference = 2 * Math.PI * 42;

    return statusSegments.value.map((seg) => {
        const dash = (seg.pct / 100) * circumference;

        const segWithOffset = { ...seg, dash, offset };
        offset += dash;

        return segWithOffset;
    });
});

const kpis = computed(() => [
    {
        label: 'Total Kiriman',
        value: props.stats.total_shipments.toLocaleString('id-ID'),
        hint: 'Seluruh entri kiriman',
        icon: Boxes,
        iconClass: 'bg-primary text-white shadow-sm',
        accentClass: 'text-primary',
    },
    {
        label: 'Dalam Perjalanan',
        value: props.stats.in_transit.toLocaleString('id-ID'),
        hint: 'Sedang menuju pondok',
        icon: Truck,
        iconClass: 'bg-sky-500 text-white shadow-sm',
        accentClass: 'text-sky-600 dark:text-sky-400',
    },
    {
        label: 'Diserahkan',
        value: props.stats.delivered.toLocaleString('id-ID'),
        hint: `${deliveryRate.value}% dari total`,
        icon: CircleCheck,
        iconClass: 'bg-emerald-500 text-white shadow-sm',
        accentClass: 'text-emerald-600 dark:text-emerald-400',
    },
    {
        label: 'Nominal Kiriman',
        value: formatCurrency(props.stats.total_money),
        hint: 'Total dana tercatat',
        icon: Wallet,
        iconClass:
            'bg-[linear-gradient(135deg,#a67e1f,#d9ab33)] text-white shadow-sm',
        accentClass: 'text-accent-700 dark:text-accent-400',
    },
]);
</script>

<template>
    <Head title="Dashboard" />

    <div class="dashboard-page flex min-h-full w-full flex-col gap-6 p-4 md:p-6 lg:p-8">
        <!-- Hero / Greeting -->
        <div
            class="relative overflow-hidden rounded-3xl border border-primary-800/40 bg-gradient-to-br from-emerald-950 via-primary-900 to-primary-700 p-6 shadow-elevated md:p-8 dark:from-emerald-950 dark:via-emerald-900 dark:to-primary-800"
        >
            <!-- Decorative glow -->
            <div
                class="pointer-events-none absolute -right-20 -top-24 size-72 rounded-full bg-emerald-400/20 blur-3xl"
            />
            <div
                class="pointer-events-none absolute -left-16 bottom-0 size-56 rounded-full bg-primary-300/10 blur-3xl"
            />
            <div
                class="pointer-events-none absolute right-24 top-0 size-40 rounded-full bg-accent-400/10 blur-3xl"
            />

            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-1.5">
                    <div
                        class="flex items-center gap-2 text-[13px] font-medium text-primary-100/80"
                    >
                        <CalendarDays class="size-4" />
                        <span>{{ today }}</span>
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight text-white">
                        {{ greeting.title }}
                    </h2>
                    <p class="max-w-xl text-sm text-primary-50/90">
                        {{ greeting.sub }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Button
                        as-child
                        variant="ghost"
                        class="border border-white/20 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20 hover:text-white"
                    >
                        <Link :href="shipments()">
                            Semua Kiriman
                            <ArrowRight />
                        </Link>
                    </Button>
                    <Button
                        as-child
                        variant="accent"
                        class="shadow-lg shadow-black/20"
                    >
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
                class="group/kpi gap-0 overflow-hidden border-transparent py-5 shadow-card transition-shadow duration-300 hover:shadow-elevated dark:border-border"
            >
                <CardContent class="flex items-start justify-between px-5">
                    <div class="space-y-1.5">
                        <div
                            class="text-[13px] font-medium text-muted-foreground"
                        >
                            {{ kpi.label }}
                        </div>
                        <div
                            class="text-[26px] font-bold tracking-tight tabular-nums"
                        >
                            <span :class="kpi.accentClass">
                                {{ kpi.value }}
                            </span>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ kpi.hint }}
                        </div>
                    </div>
                    <div
                        class="flex size-11 shrink-0 items-center justify-center rounded-2xl"
                        :class="kpi.iconClass"
                    >
                        <component :is="kpi.icon" class="size-5" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Charts row -->
        <div class="grid flex-1 grid-cols-1 gap-4 xl:grid-cols-3">
            <!-- Weekly activity chart -->
            <Card class="min-w-0 xl:col-span-2">
                <CardHeader
                    class="flex-row flex-wrap items-center justify-between"
                >
                    <div>
                        <CardTitle
                            class="flex items-center gap-2 text-base font-semibold"
                        >
                            <TrendingUp class="size-4 text-primary" />
                            Aktivitas 7 Hari Terakhir
                        </CardTitle>
                        <CardDescription>
                            Jumlah kiriman tercatat per hari.
                        </CardDescription>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-2.5 py-1 font-medium text-primary-700 dark:bg-primary-950/60 dark:text-primary-300"
                        >
                            {{ weekTotal }} total
                        </span>
                        <span
                            class="text-muted-foreground"
                        >
                            ±{{ weekAverage }} / hari
                        </span>
                    </div>
                </CardHeader>
                <CardContent class="pt-2">
                    <AreaChart
                        :data="chartData"
                        :height="200"
                        class="h-48"
                    />
                    <p
                        v-if="peakDay && peakDay.count > 0"
                        class="mt-1 text-center text-xs text-muted-foreground"
                    >
                        Puncak aktivitas pada hari
                        <span class="font-semibold text-foreground">{{
                            peakDay.short_label
                        }}</span>
                        dengan {{ peakDay.count }} kiriman.
                    </p>
                </CardContent>
            </Card>

            <!-- Status donut -->
            <Card class="min-w-0">
                <CardHeader>
                    <CardTitle class="text-base font-semibold">
                        Status Kiriman
                    </CardTitle>
                    <CardDescription>
                        Distribusi status seluruh kiriman.
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col items-center gap-5 pt-2">
                    <div class="relative size-40">
                        <svg
                            viewBox="0 0 100 100"
                            class="size-full -rotate-90"
                            role="img"
                            aria-label="Distribusi status kiriman"
                        >
                            <circle
                                cx="50"
                                cy="50"
                                r="42"
                                fill="none"
                                stroke-width="10"
                                class="stroke-muted"
                            />
                            <circle
                                v-for="seg in donutSegments"
                                :key="seg.label"
                                cx="50"
                                cy="50"
                                r="42"
                                fill="none"
                                :stroke="seg.color"
                                stroke-width="10"
                                stroke-linecap="butt"
                                :stroke-dasharray="`${seg.dash} 999`"
                                :stroke-dashoffset="-seg.offset"
                                class="transition-all duration-700 ease-out"
                            />
                        </svg>
                        <div
                            class="absolute inset-0 flex flex-col items-center justify-center"
                        >
                            <span
                                class="text-3xl font-bold tracking-tight tabular-nums"
                                >{{ stats.total_shipments }}</span
                            >
                            <span class="text-xs text-muted-foreground"
                                >total</span
                            >
                        </div>
                    </div>
                    <div class="grid w-full grid-cols-1 gap-2">
                        <div
                            v-for="seg in statusSegments"
                            :key="seg.label"
                            class="flex items-center justify-between rounded-lg border border-transparent bg-surface-muted/70 px-3 py-2 dark:border-border/50 dark:bg-white/[0.04]"
                        >
                            <span
                                class="flex items-center gap-2 text-sm"
                            >
                                <span
                                    class="size-2.5 rounded-full"
                                    :style="{ backgroundColor: seg.color }"
                                />
                                {{ seg.label }}
                            </span>
                            <span
                                class="text-sm font-semibold tabular-nums"
                                >{{ seg.value }}</span
                            >
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid flex-1 grid-cols-1 gap-4 xl:grid-cols-3">
            <!-- Recent Shipments -->
            <Card class="min-w-0 xl:col-span-2">
                <CardHeader
                    class="flex-row flex-wrap items-center justify-between"
                >
                    <div>
                        <CardTitle class="text-base font-semibold">
                            Kiriman Terbaru
                        </CardTitle>
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
                                class="border-y border-border bg-surface-muted/60 text-left dark:bg-surface-muted"
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
                                class="border-b border-border last:border-0 hover:bg-surface-muted/50 dark:hover:bg-white/[0.06]"
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
                                        <Link
                                            :href="`/shipments/${item.id}`"
                                        >
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
                        <CardTitle class="flex items-center gap-2 text-base font-semibold">
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
                                    <span class="text-xs text-muted-foreground tabular-nums">
                                        {{ region.shipments_count }}
                                    </span>
                                </div>
                                <div
                                    class="h-2 overflow-hidden rounded-full bg-surface-muted dark:bg-white/[0.08]"
                                >
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-primary-600 to-primary-400"
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
                                    <span class="text-xs text-muted-foreground tabular-nums">
                                        {{ stats.in_transit }}
                                    </span>
                                </div>
                                <div
                                    class="h-2 overflow-hidden rounded-full bg-surface-muted dark:bg-white/[0.08]"
                                >
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-sky-500 to-info"
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
                                    <span class="text-xs text-muted-foreground tabular-nums">
                                        {{ stats.delivered }}
                                    </span>
                                </div>
                                <div
                                    class="h-2 overflow-hidden rounded-full bg-surface-muted dark:bg-white/[0.08]"
                                >
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-success"
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

                <Card class="relative overflow-hidden">
                    <div
                        class="pointer-events-none absolute inset-x-0 -top-px h-px bg-gradient-to-r from-transparent via-accent-500/70 to-transparent"
                    />
                    <CardHeader>
                        <CardTitle
                            class="flex items-center gap-2 text-sm font-semibold text-primary"
                        >
                            <PiggyBank class="size-4" />
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