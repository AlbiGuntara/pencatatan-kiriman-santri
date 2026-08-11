<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Boxes, Plus, Printer, Search } from '@lucide/vue';
import { computed, ref } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import Pagination from '@/components/Pagination.vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { formatCurrency, formatDateTime } from '@/lib/format';
import { index as shipmentsRoute } from '@/routes/shipments';
import { create as createShipmentRoute } from '@/routes/shipments';
import type { Region, Shipment } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kiriman',
                href: shipmentsRoute(),
            },
        ],
    },
});

const props = defineProps<{
    shipments: {
        data: Shipment[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string; status?: string; region_id?: string };
    statuses: Record<string, string>;
    regions: Region[];
    isSuperAdmin: boolean;
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? 'all');
const regionId = ref(props.filters.region_id ?? 'all');
const selectedIds = ref<number[]>([]);

const allSelected = computed(
    () =>
        props.shipments.data.length > 0 &&
        selectedIds.value.length === props.shipments.data.length,
);

const toggleAll = (checked: boolean) => {
    selectedIds.value = checked ? props.shipments.data.map((s) => s.id) : [];
};

const isSelected = (id: number) => selectedIds.value.includes(id);

const toggle = (id: number, checked: boolean) => {
    if (checked) {
        selectedIds.value.push(id);
    } else {
        selectedIds.value = selectedIds.value.filter((x) => x !== id);
    }
};

const hasActiveFilters = computed(
    () =>
        Boolean(search.value) ||
        status.value !== 'all' ||
        regionId.value !== 'all',
);

const applyFilters = () => {
    router.get(
        shipmentsRoute(),
        {
            search: search.value || undefined,
            status: status.value === 'all' ? undefined : status.value,
            region_id: regionId.value === 'all' ? undefined : regionId.value,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const clearFilters = () => {
    search.value = '';
    status.value = 'all';
    regionId.value = 'all';
    router.get(
        shipmentsRoute(),
        {},
        { preserveState: true, preserveScroll: true },
    );
};

const printSelected = () => {
    if (selectedIds.value.length === 0) {
        return;
    }

    window.location.href = `/shipments/print?shipments=${selectedIds.value.join(',')}`;
};
</script>

<template>
    <Head title="Kiriman" />

    <div class="flex flex-col gap-5 p-4 md:p-6">
        <!-- Page header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-bold tracking-tight">Kiriman</h2>
                <p class="text-sm text-muted-foreground">
                    Kelola dan pantau seluruh data kiriman.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    :disabled="selectedIds.length === 0"
                    @click="printSelected"
                >
                    <Printer />
                    Cetak Stiker
                    <span
                        v-if="selectedIds.length > 0"
                        class="rounded-full bg-primary-100 px-1.5 py-0.5 text-[11px] font-semibold text-primary-800"
                    >
                        {{ selectedIds.length }}
                    </span>
                </Button>
                <Button as-child>
                    <Link :href="createShipmentRoute()">
                        <Plus />
                        Tambah Kiriman
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Filter bar -->
        <Card class="gap-0 py-4">
            <CardContent class="grid gap-3 md:grid-cols-12 md:items-end">
                <div class="grid gap-1.5 md:col-span-4">
                    <Label
                        for="search"
                        class="text-xs font-medium text-muted-foreground"
                    >
                        Cari kiriman
                    </Label>
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            id="search"
                            v-model="search"
                            class="pl-9"
                            placeholder="Kode / Pengirim / NIS / Nama santri..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                </div>
                <div class="grid gap-1.5 md:col-span-2">
                    <Label
                        for="status"
                        class="text-xs font-medium text-muted-foreground"
                    >
                        Status
                    </Label>
                    <Select v-model="status">
                        <SelectTrigger id="status" class="w-full">
                            <SelectValue placeholder="Semua Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem
                                v-for="(label, key) in statuses"
                                :key="key"
                                :value="key"
                            >
                                {{ label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div v-if="isSuperAdmin" class="grid gap-1.5 md:col-span-2">
                    <Label
                        for="region"
                        class="text-xs font-medium text-muted-foreground"
                    >
                        Wilayah
                    </Label>
                    <Select v-model="regionId">
                        <SelectTrigger id="region" class="w-full">
                            <SelectValue placeholder="Semua Wilayah" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Wilayah</SelectItem>
                            <SelectItem
                                v-for="region in regions"
                                :key="region.id"
                                :value="String(region.id)"
                            >
                                {{ region.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div
                    class="flex items-center gap-2 md:col-span-4 md:justify-end"
                >
                    <Button @click="applyFilters">Terapkan</Button>
                    <Button
                        v-if="hasActiveFilters"
                        variant="ghost"
                        class="text-muted-foreground"
                        @click="clearFilters"
                    >
                        Reset
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Desktop table -->
        <Card class="hidden gap-0 overflow-hidden py-0 md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead
                        class="sticky top-0 z-10 bg-surface-muted/80 backdrop-blur-sm"
                    >
                        <tr class="border-b border-border text-left">
                            <th class="w-10 px-4 py-3">
                                <Checkbox
                                    :checked="allSelected"
                                    @update:checked="toggleAll"
                                    aria-label="Pilih semua"
                                />
                            </th>
                            <th
                                class="px-3 py-3 font-medium text-muted-foreground"
                            >
                                Tracking
                            </th>
                            <th
                                class="px-3 py-3 font-medium text-muted-foreground"
                            >
                                Santri
                            </th>
                            <th
                                class="hidden px-3 py-3 font-medium text-muted-foreground lg:table-cell"
                            >
                                Pengirim
                            </th>
                            <th
                                v-if="isSuperAdmin"
                                class="px-3 py-3 font-medium text-muted-foreground"
                            >
                                Wilayah
                            </th>
                            <th
                                class="px-3 py-3 font-medium text-muted-foreground"
                            >
                                Paket
                            </th>
                            <th
                                class="hidden px-3 py-3 font-medium text-muted-foreground xl:table-cell"
                            >
                                Nominal
                            </th>
                            <th
                                class="px-3 py-3 font-medium text-muted-foreground"
                            >
                                Status
                            </th>
                            <th
                                class="hidden px-3 py-3 font-medium text-muted-foreground xl:table-cell"
                            >
                                Update
                            </th>
                            <th
                                class="px-3 py-3 text-right font-medium text-muted-foreground"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in shipments.data"
                            :key="item.id"
                            class="border-b border-border last:border-0 hover:bg-surface-muted/50"
                            :class="{
                                'bg-primary-50/60 dark:bg-primary-950/20':
                                    isSelected(item.id),
                            }"
                        >
                            <td class="px-4 py-3">
                                <Checkbox
                                    :checked="isSelected(item.id)"
                                    @update:checked="
                                        (v: boolean) => toggle(item.id, v)
                                    "
                                    :aria-label="`Pilih ${item.tracking_code}`"
                                />
                            </td>
                            <td class="px-3 py-3">
                                <Link
                                    :href="`/shipments/${item.id}`"
                                    class="font-mono text-xs font-semibold text-primary-700 hover:underline dark:text-primary-400"
                                >
                                    {{ item.tracking_code }}
                                </Link>
                            </td>
                            <td class="px-3 py-3">
                                <div class="font-medium">
                                    {{ item.student?.name }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    NIS {{ item.student?.nis }}
                                </div>
                            </td>
                            <td class="hidden px-3 py-3 lg:table-cell">
                                <div>{{ item.sender_name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ item.sender_phone }}
                                </div>
                            </td>
                            <td v-if="isSuperAdmin" class="px-3 py-3">
                                <Badge variant="outline" class="font-mono">
                                    {{ item.region?.code }}
                                </Badge>
                            </td>
                            <td class="px-3 py-3">
                                {{ item.package_count }} koli
                            </td>
                            <td class="hidden px-3 py-3 xl:table-cell">
                                {{ formatCurrency(item.amount_money) }}
                            </td>
                            <td class="px-3 py-3">
                                <ShipmentStatusBadge
                                    :status="item.status"
                                    size="sm"
                                />
                            </td>
                            <td
                                class="hidden px-3 py-3 text-xs text-muted-foreground xl:table-cell"
                            >
                                {{ formatDateTime(item.updated_at) }}
                            </td>
                            <td class="px-3 py-3 text-right">
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="`/shipments/${item.id}`"
                                        >Detail</Link
                                    >
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <EmptyState
                    v-if="shipments.data.length === 0"
                    :icon="Boxes"
                    title="Belum ada kiriman"
                    :description="
                        hasActiveFilters
                            ? 'Tidak ada kiriman yang cocok dengan filter Anda.'
                            : 'Belum ada data kiriman untuk ditampilkan.'
                    "
                />
            </div>
            <div
                v-if="shipments.data.length > 0"
                class="border-t border-border px-4 py-3"
            >
                <Pagination :links="shipments.links" />
            </div>
        </Card>

        <!-- Mobile cards -->
        <div v-if="shipments.data.length === 0" class="md:hidden">
            <Card>
                <CardContent>
                    <EmptyState
                        :icon="Boxes"
                        title="Belum ada kiriman"
                        :description="
                            hasActiveFilters
                                ? 'Tidak ada kiriman yang cocok dengan filter Anda.'
                                : 'Belum ada data kiriman untuk ditampilkan.'
                        "
                    />
                </CardContent>
            </Card>
        </div>
        <div v-else class="flex flex-col gap-3 md:hidden">
            <div
                v-for="item in shipments.data"
                :key="item.id"
                class="rounded-xl border border-border bg-card p-4 shadow-sm"
            >
                <div class="flex items-center justify-between gap-2">
                    <Link
                        :href="`/shipments/${item.id}`"
                        class="font-mono text-sm font-semibold text-primary-700 dark:text-primary-400"
                    >
                        {{ item.tracking_code }}
                    </Link>
                    <ShipmentStatusBadge :status="item.status" size="sm" />
                </div>
                <div class="mt-3 space-y-1">
                    <div class="font-medium">{{ item.student?.name }}</div>
                    <div class="text-xs text-muted-foreground">
                        NIS {{ item.student?.nis }}
                    </div>
                    <div class="text-xs text-muted-foreground">
                        {{ item.student?.dormitory }}
                    </div>
                </div>
                <div
                    class="mt-3 flex items-center justify-between border-t border-border pt-3 text-sm"
                >
                    <span class="text-muted-foreground">
                        {{ item.package_count }} koli
                        <span
                            v-if="item.amount_money > 0"
                            class="text-foreground"
                        >
                            &middot; {{ formatCurrency(item.amount_money) }}
                        </span>
                    </span>
                    <span class="text-xs text-muted-foreground">
                        {{ formatDateTime(item.updated_at) }}
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <Button as-child variant="outline" size="sm" class="flex-1">
                        <Link :href="`/shipments/${item.id}`"
                            >Lihat Detail</Link
                        >
                    </Button>
                    <label
                        class="flex items-center gap-1.5 text-xs text-muted-foreground"
                    >
                        <Checkbox
                            :checked="isSelected(item.id)"
                            @update:checked="(v: boolean) => toggle(item.id, v)"
                        />
                        Pilih
                    </label>
                </div>
            </div>
        </div>
        <Pagination
            v-if="shipments.data.length > 0"
            :links="shipments.links"
            class="md:hidden"
        />
    </div>
</template>
