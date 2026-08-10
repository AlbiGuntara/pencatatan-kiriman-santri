<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Printer, Search } from '@lucide/vue';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
import { formatCurrency } from '@/lib/format';
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
    router.get(shipmentsRoute(), {}, { preserveState: true, preserveScroll: true });
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

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Data Kiriman</h2>
                <p class="text-sm text-muted-foreground">
                    Kelola kiriman paket & dana wali santri dari setiap wilayah IKSASS.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    :disabled="selectedIds.length === 0"
                    @click="printSelected"
                >
                    <Printer />
                    Cetak Stiker ({{ selectedIds.length }})
                </Button>
                <Button as-child>
                    <Link :href="createShipmentRoute()">
                        <Plus />
                        Input Kiriman
                    </Link>
                </Button>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Filter Kiriman</CardTitle>
                <CardDescription>Filter berdasarkan kode, nama, status, atau wilayah.</CardDescription>
            </CardHeader>
            <CardContent class="grid gap-3 md:grid-cols-4">
                <div class="grid gap-2 md:col-span-2">
                    <Label for="search">Kata kunci</Label>
                    <div class="relative">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="search"
                            v-model="search"
                            class="pl-9"
                            placeholder="Kode / Pengirim / Nama Santri / NIS..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <Select v-model="status">
                        <SelectTrigger id="status" class="w-full">
                            <SelectValue placeholder="Semua Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem v-for="(label, key) in statuses" :key="key" :value="key">
                                {{ label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div v-if="isSuperAdmin" class="grid gap-2">
                    <Label for="region">Wilayah</Label>
                    <Select v-model="regionId">
                        <SelectTrigger id="region" class="w-full">
                            <SelectValue placeholder="Semua Wilayah" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Wilayah</SelectItem>
                            <SelectItem v-for="region in regions" :key="region.id" :value="String(region.id)">
                                {{ region.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex items-end gap-2">
                    <Button @click="applyFilters">Terapkan</Button>
                    <Button variant="ghost" @click="clearFilters">Reset</Button>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-muted-foreground">
                            <th class="w-10 pb-2 pr-2">
                                <Checkbox
                                    :checked="shipments.data.length > 0 && selectedIds.length === shipments.data.length"
                                    @update:checked="toggleAll"
                                />
                            </th>
                            <th class="pb-2 pr-4 font-medium">Kode</th>
                            <th class="pb-2 pr-4 font-medium">Santri</th>
                            <th class="pb-2 pr-4 font-medium">Pengirim</th>
                            <th v-if="isSuperAdmin" class="pb-2 pr-4 font-medium">Wilayah</th>
                            <th class="pb-2 pr-4 font-medium">Paket</th>
                            <th class="pb-2 pr-4 font-medium">Nominal</th>
                            <th class="pb-2 pr-4 font-medium">Status</th>
                            <th class="pb-2 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in shipments.data" :key="item.id" class="border-b last:border-0">
                            <td class="py-3 pr-2">
                                <Checkbox :checked="isSelected(item.id)" @update:checked="(v: boolean) => toggle(item.id, v)" />
                            </td>
                            <td class="py-3 pr-4">
                                <Link :href="`/shipments/${item.id}`" class="font-mono text-xs font-medium text-primary hover:underline">
                                    {{ item.tracking_code }}
                                </Link>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="font-medium">{{ item.student?.name }}</div>
                                <div class="text-xs text-muted-foreground">NIS {{ item.student?.nis }}</div>
                            </td>
                            <td class="py-3 pr-4">
                                <div>{{ item.sender_name }}</div>
                                <div class="text-xs text-muted-foreground">{{ item.sender_phone }}</div>
                            </td>
                            <td v-if="isSuperAdmin" class="py-3 pr-4">
                                <Badge variant="secondary">{{ item.region?.code }}</Badge>
                            </td>
                            <td class="py-3 pr-4">{{ item.package_count }} koli</td>
                            <td class="py-3 pr-4">{{ formatCurrency(item.amount_money) }}</td>
                            <td class="py-3 pr-4">
                                <ShipmentStatusBadge :status="item.status" />
                            </td>
                            <td class="py-3">
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="`/shipments/${item.id}`">Detail</Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="shipments.data.length === 0">
                            <td :colspan="isSuperAdmin ? 9 : 8" class="py-8 text-center text-muted-foreground">
                                Tidak ada kiriman yang cocok.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :links="shipments.links" />
            </CardContent>
        </Card>
    </div>
</template>
