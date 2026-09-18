<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { GraduationCap, Pencil, Plus, Search, Trash2 } from '@lucide/vue';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useOutOfRangePage } from '@/composables/useOutOfRangePage';
import { useTableSort } from '@/composables/useTableSort';
import {
    create as createStudentRoute,
    destroy as destroyStudentRoute,
    edit as editStudentRoute,
    index as studentsRoute,
} from '@/routes/students';
import type { Region, Student } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Data Santri',
                href: studentsRoute(),
            },
        ],
    },
});

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    students: {
        data: Student[];
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
        location_type?: string;
        region_iksass_id?: number;
        per_page?: number;
        sort?: string;
        direction?: string;
    };
    locations: string[];
    regions: Region[];
}>();

const search = ref(props.filters.search ?? '');
const locationType = ref(props.filters.location_type ?? 'all');
const regionId = ref(
    props.filters.region_iksass_id ? String(props.filters.region_iksass_id) : 'all',
);
const perPage = ref(
    String(props.filters.per_page ?? props.students.per_page ?? 10),
);
const { sort, cycle: cycleSort, reset: resetSort } = useTableSort({
    column: props.filters.sort ?? null,
    direction: props.filters.direction === 'desc' ? 'desc' : 'asc',
});

const hasActiveFilters = computed(
    () =>
        Boolean(search.value) ||
        locationType.value !== 'all' ||
        regionId.value !== 'all' ||
        sort.value.column !== null,
);

const buildParams = () => ({
    search: search.value || undefined,
    location_type:
        locationType.value === 'all' ? undefined : locationType.value,
    region_iksass_id:
        regionId.value === 'all' ? undefined : Number(regionId.value),
    per_page: perPage.value,
    sort: sort.value.column ?? undefined,
    direction: sort.value.column ? sort.value.direction : undefined,
});

const page = usePage();

const backToList = () => page.url;

const isSuperAdmin = computed(() => page.props.auth.user.role === 'super_admin');

useOutOfRangePage(
    () => props.students,
    () => studentsRoute().url,
    buildParams,
);

const applyFilters = () => {
    router.get(studentsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    locationType.value = 'all';
    regionId.value = 'all';
    resetSort();
    router.get(
        studentsRoute(),
        { per_page: perPage.value },
        { preserveState: true, preserveScroll: true },
    );
};

const sortBy = (column: string) => {
    cycleSort(column);

    router.get(studentsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const changePerPage = (value: string) => {
    perPage.value = value;
    router.get(studentsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const rowIndex = (index: number) =>
    (props.students.current_page - 1) * props.students.per_page + index + 1;

const deleteStudent = (student: Student) => {
    router.delete(destroyStudentRoute(student.id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Data Santri" />

    <div class="flex flex-col gap-4 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">
                    Data Santri
                </h2>
                <p class="text-sm text-muted-foreground">
                    Master data santri Pondok Pesantren Salafiyah Syafi'iyah
                    Sukorejo.
                </p>
            </div>
            <Button as-child>
                <Link
                    :href="
                        createStudentRoute({ query: { back: backToList() } })
                    "
                >
                    <Plus />
                    Tambah Santri
                </Link>
            </Button>
        </div>

        <Card class="shadow-card">
            <CardHeader>
                <CardTitle>Pencarian &amp; Filter</CardTitle>
                <CardDescription
                    >Cari berdasarkan NIS, nama, atau asrama.</CardDescription
                >
            </CardHeader>
            <CardContent class="flex flex-col gap-3 md:flex-row">
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
                            placeholder="NIS / Nama / Asrama..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="location">Lokasi Santri</Label>
                    <Select v-model="locationType">
                        <SelectTrigger id="location" class="w-full md:w-56">
                            <SelectValue placeholder="Semua Lokasi" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Lokasi</SelectItem>
                            <SelectItem
                                v-for="loc in locations"
                                :key="loc"
                                :value="loc"
                            >
                                {{ loc }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div v-if="isSuperAdmin" class="grid gap-2">
                    <Label for="region">Wilayah IKSASS</Label>
                    <Select v-model="regionId">
                        <SelectTrigger id="region" class="w-full md:w-56">
                            <SelectValue placeholder="Semua Wilayah" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Semua Wilayah</SelectItem>
                            <SelectItem
                                v-for="region in regions"
                                :key="region.id"
                                :value="String(region.id)"
                            >
                                {{ region.name }} ({{ region.code }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex items-end gap-2">
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

        <DataTable
            title="Daftar Santri"
            :description="`Menampilkan ${students.from ?? 0}–${students.to ?? 0} dari ${students.total} data.`"
            :total="students.total"
            :from="students.from"
            :to="students.to"
            :links="students.links"
            :per-page="perPage"
            :icon="GraduationCap"
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
                            label="NIS"
                            column="nis"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Nama"
                            column="name"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Asrama"
                            column="dormitory"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Lokasi"
                            column="location_type"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="IKSASS"
                            column="region"
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
                        v-for="(student, index) in students.data"
                        :key="student.id"
                        class="border-b border-border/60 transition-colors last:border-0 even:bg-surface-muted/30 hover:bg-surface-muted/70"
                    >
                        <td
                            class="px-3 py-3 text-xs text-muted-foreground tabular-nums"
                        >
                            {{ rowIndex(index) }}
                        </td>
                        <td
                            class="px-3 py-3 font-mono text-xs font-medium tabular-nums"
                        >
                            {{ student.nis }}
                        </td>
                        <td class="px-3 py-3 font-medium">
                            {{ student.name }}
                        </td>
                        <td class="px-3 py-3">{{ student.dormitory }}</td>
                        <td class="px-3 py-3">
                            <Badge variant="secondary">{{
                                student.location_type
                            }}</Badge>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex flex-wrap gap-1">
                                <Badge
                                    v-for="region in student.regions"
                                    :key="region.id"
                                    variant="outline"
                                    class="font-normal"
                                >
                                    {{ region.name }}
                                </Badge>
                                <span
                                    v-if="student.regions.length === 0"
                                    class="text-xs text-muted-foreground"
                                >
                                    —
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <Button
                                    as-child
                                    variant="ghost"
                                    size="icon-sm"
                                    :aria-label="`Edit ${student.name}`"
                                >
                                    <Link
                                        :href="
                                            editStudentRoute(student.id, {
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
                                    :title="`Hapus ${student.name}`"
                                    description="Yakin ingin menghapus data santri ini? Tindakan ini tidak dapat dibatalkan."
                                    @confirm="deleteStudent(student)"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        :aria-label="`Hapus ${student.name}`"
                                    >
                                        <Trash2 />
                                    </Button>
                                </ConfirmDialog>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="students.data.length === 0">
                        <td colspan="7" class="px-4 py-12">
                            <EmptyState
                                :icon="GraduationCap"
                                title="Belum ada data santri"
                                description="Tidak ada data santri yang cocok dengan pencarian Anda."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTable>
    </div>
</template>
