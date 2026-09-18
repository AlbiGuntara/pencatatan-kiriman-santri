<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { MapPinned, Pencil, Plus, Search, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import EmptyState from '@/components/EmptyState.vue';
import SortableHeader from '@/components/SortableHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useOutOfRangePage } from '@/composables/useOutOfRangePage';
import { useTableSort } from '@/composables/useTableSort';
import {
    create as createRegionRoute,
    destroy as destroyRegionRoute,
    edit as editRegionRoute,
    index as regionsRoute,
} from '@/routes/regions';
import type { Region } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Wilayah IKSASS',
                href: regionsRoute(),
            },
        ],
    },
});

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    regions: {
        data: (Region & { shipments_count: number; users_count: number })[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        per_page: number;
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: {
        search?: string;
        per_page?: number;
        sort?: string;
        direction?: string;
    };
}>();

const search = ref(props.filters.search ?? '');
const perPage = ref(
    String(props.filters.per_page ?? props.regions.per_page ?? 10),
);
const { sort, cycle: cycleSort, reset: resetSort } = useTableSort({
    column: props.filters.sort ?? null,
    direction: props.filters.direction === 'desc' ? 'desc' : 'asc',
});

const hasActiveFilters = computed(
    () => Boolean(search.value) || sort.value.column !== null,
);

const buildParams = () => ({
    search: search.value || undefined,
    per_page: perPage.value,
    sort: sort.value.column ?? undefined,
    direction: sort.value.column ? sort.value.direction : undefined,
});

const page = usePage();

const backToList = () => page.url;

useOutOfRangePage(
    () => props.regions,
    () => regionsRoute().url,
    buildParams,
);

const applyFilters = () => {
    router.get(regionsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    resetSort();
    router.get(
        regionsRoute(),
        { per_page: perPage.value },
        { preserveState: true, preserveScroll: true },
    );
};

const sortBy = (column: string) => {
    cycleSort(column);

    router.get(regionsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const changePerPage = (value: string) => {
    perPage.value = value;
    router.get(regionsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const rowIndex = (index: number) =>
    (props.regions.current_page - 1) * props.regions.per_page + index + 1;

const deleteRegion = (region: Region) => {
    router.delete(destroyRegionRoute(region.id).url, { preserveScroll: true });
};
</script>

<template>
    <Head title="Wilayah IKSASS" />

    <div class="flex flex-col gap-4 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">
                    Wilayah IKSASS
                </h2>
                <p class="text-sm text-muted-foreground">
                    Daftar wilayah IKSASS dan jumlah kiriman masing-masing.
                </p>
            </div>
            <Button as-child>
                <Link
                    :href="createRegionRoute({ query: { back: backToList() } })"
                >
                    <Plus />
                    Tambah Wilayah
                </Link>
            </Button>
        </div>

        <Card class="shadow-card">
            <CardHeader>
                <CardTitle>Pencarian</CardTitle>
                <CardDescription
                    >Cari berdasarkan nama atau kode wilayah.</CardDescription
                >
            </CardHeader>
            <CardContent class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="grid flex-1 gap-2">
                    <Label for="search">Kata kunci</Label>
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            id="search"
                            v-model="search"
                            class="pl-9"
                            placeholder="Nama / kode wilayah..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                </div>
                <Button @click="applyFilters">Cari</Button>
                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    class="text-muted-foreground"
                    @click="clearFilters"
                >
                    Reset
                </Button>
            </CardContent>
        </Card>

        <DataTable
            title="Daftar Wilayah IKSASS"
            :description="`Menampilkan ${regions.from ?? 0}–${regions.to ?? 0} dari ${regions.total} data.`"
            :total="regions.total"
            :from="regions.from"
            :to="regions.to"
            :links="regions.links"
            :per-page="perPage"
            :icon="MapPinned"
            @update:per-page="changePerPage"
        >
            <table class="w-full text-sm">
                <thead
                    class="sticky top-0 z-10 bg-surface-muted/60 backdrop-blur-sm"
                >
                    <tr class="border-b border-border text-left">
                        <SortableHeader
                            label="No"
                            column="no"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Kode"
                            column="code"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Nama Wilayah"
                            column="name"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Pengurus"
                            column="users_count"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Kiriman"
                            column="shipments_count"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <th
                            class="px-3 py-3 text-right text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(region, index) in regions.data"
                        :key="region.id"
                        class="border-b border-border/60 transition-colors last:border-0 even:bg-surface-muted/30 hover:bg-surface-muted/70"
                    >
                        <td
                            class="px-3 py-3 text-xs text-muted-foreground tabular-nums"
                        >
                            {{ rowIndex(index) }}
                        </td>
                        <td class="px-3 py-3">
                            <Badge variant="outline" class="font-mono">
                                {{ region.code }}
                            </Badge>
                        </td>
                        <td class="px-3 py-3 font-medium">
                            {{ region.name }}
                        </td>
                        <td class="px-3 py-3 tabular-nums">
                            <span class="font-medium">{{
                                region.users_count
                            }}</span>
                            <span class="text-xs text-muted-foreground">
                                orang</span
                            >
                        </td>
                        <td class="px-3 py-3 tabular-nums">
                            <span class="font-medium">{{
                                region.shipments_count
                            }}</span>
                            <span class="text-xs text-muted-foreground">
                                kiriman</span
                            >
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <Button
                                    as-child
                                    variant="ghost"
                                    size="icon-sm"
                                    :aria-label="`Edit ${region.name}`"
                                >
                                    <Link
                                        :href="
                                            editRegionRoute(region.id, {
                                                query: {
                                                    back: backToList(),
                                                },
                                            }).url
                                        "
                                    >
                                        <Pencil />
                                    </Link>
                                </Button>
                                <ConfirmDialog
                                    :title="`Hapus ${region.name}`"
                                    description="Yakin ingin menghapus wilayah IKSASS ini? Tindakan ini tidak dapat dibatalkan."
                                    @confirm="deleteRegion(region)"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        :aria-label="`Hapus ${region.name}`"
                                    >
                                        <Trash2 />
                                    </Button>
                                </ConfirmDialog>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="regions.data.length === 0">
                        <td colspan="6" class="px-4 py-12">
                            <EmptyState
                                :icon="MapPinned"
                                title="Belum ada wilayah IKSASS"
                                description="Belum ada data wilayah IKSASS untuk ditampilkan."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTable>
    </div>
</template>
