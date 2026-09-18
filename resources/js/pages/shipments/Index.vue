<script setup lang="ts">
import { Form, Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowRightLeft,
    Boxes,
    Check,
    Download,
    Eye,
    FileSpreadsheet,
    FileUp,
    Loader2,
    Plus,
    Printer,
    Search,
    Trash2,
} from '@lucide/vue';
import type { CheckboxCheckedState } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import ShipmentController from '@/actions/App/Http/Controllers/ShipmentController';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import EmptyState from '@/components/EmptyState.vue';
import InputError from '@/components/InputError.vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
import SortableHeader from '@/components/SortableHeader.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
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
import { formatCurrency, formatDateTime } from '@/lib/format';
import {
    bulkStatus as bulkStatusRoute,
    create as createShipmentRoute,
    destroyMany as destroyManyShipmentsRoute,
    importTemplate as importTemplateRoute,
    index as shipmentsRoute,
    print as printShipmentsRoute,
    show as showShipmentRoute,
} from '@/routes/shipments';
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

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    shipments: {
        data: Shipment[];
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
        status?: string;
        region_id?: string;
        per_page?: number;
        sort?: string;
        direction?: string;
    };
    statuses: Record<string, string>;
    regions: Region[];
    isSuperAdmin: boolean;
    importErrors: string[];
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? 'all');
const regionId = ref(props.filters.region_id ?? 'all');
const perPage = ref(
    String(props.filters.per_page ?? props.shipments.per_page ?? 10),
);
const { sort, cycle: cycleSort, reset: resetSort } = useTableSort({
    column: props.filters.sort ?? null,
    direction: props.filters.direction === 'desc' ? 'desc' : 'asc',
});
const selectedIds = ref<number[]>([]);

const allSelected = computed(
    () =>
        props.shipments.data.length > 0 &&
        selectedIds.value.length === props.shipments.data.length,
);

const toggleAll = (checked: CheckboxCheckedState) => {
    selectedIds.value =
        checked === true ? props.shipments.data.map((s) => s.id) : [];
};

const isSelected = (id: number) => selectedIds.value.includes(id);

const toggle = (id: number, checked: CheckboxCheckedState) => {
    if (checked === true) {
        selectedIds.value.push(id);
    } else {
        selectedIds.value = selectedIds.value.filter((x) => x !== id);
    }
};

watch(
    () => props.shipments.data,
    (data) => {
        const ids = new Set(data.map((s) => s.id));
        selectedIds.value = selectedIds.value.filter((id) => ids.has(id));
    },
);

const hasActiveFilters = computed(
    () =>
        Boolean(search.value) ||
        status.value !== 'all' ||
        regionId.value !== 'all' ||
        sort.value.column !== null,
);

const buildParams = () => ({
    search: search.value || undefined,
    status: status.value === 'all' ? undefined : status.value,
    region_id: regionId.value === 'all' ? undefined : regionId.value,
    per_page: perPage.value,
    sort: sort.value.column ?? undefined,
    direction: sort.value.column ? sort.value.direction : undefined,
});

const page = usePage();

const backToList = () => page.url;

useOutOfRangePage(
    () => props.shipments,
    () => shipmentsRoute().url,
    buildParams,
);

const applyFilters = () => {
    router.get(shipmentsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = 'all';
    regionId.value = 'all';
    resetSort();
    router.get(
        shipmentsRoute(),
        { per_page: perPage.value },
        { preserveState: true, preserveScroll: true },
    );
};

const sortBy = (column: string) => {
    cycleSort(column);

    router.get(shipmentsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const changePerPage = (value: string) => {
    perPage.value = value;
    router.get(shipmentsRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const rowIndex = (index: number) =>
    (props.shipments.current_page - 1) * props.shipments.per_page + index + 1;

const printSelected = () => {
    if (selectedIds.value.length === 0) {
        return;
    }

    window.location.href = printShipmentsRoute({
        query: { shipments: selectedIds.value },
    }).url;
};

const bulkStatusOpen = ref(false);
const bulkTarget = ref<string>('');
const bulkAttachmentPreview = ref<string | null>(null);

const bulkTargets = computed(() => {
    const targets = new Set<string>();
    const selected = new Set(selectedIds.value);

    for (const item of props.shipments.data) {
        if (!selected.has(item.id)) {
            continue;
        }

        if (item.status === 'created') {
            targets.add('in_transit');
        } else if (item.status === 'in_transit') {
            targets.add('delivered');
        }
    }

    return Object.entries(props.statuses).filter(([key]) => targets.has(key));
});

const canBulkStatus = computed(() => {
    if (selectedIds.value.length === 0) {
        return false;
    }

    const selected = new Set(selectedIds.value);
    const statuses = new Set<string>();

    for (const item of props.shipments.data) {
        if (selected.has(item.id)) {
            statuses.add(item.status);
        }
    }

    if (statuses.size !== 1) {
        return false;
    }

    const status = statuses.values().next().value as string | undefined;

    return status === 'created' || status === 'in_transit';
});

const openBulkStatus = () => {
    bulkTarget.value = bulkTargets.value[0]?.[0] ?? '';
    bulkAttachmentPreview.value = null;
    bulkStatusOpen.value = true;
};

const onBulkFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    bulkAttachmentPreview.value = file ? URL.createObjectURL(file) : null;
};

const clearSelection = () => {
    selectedIds.value = [];
};

const deleteSelected = () => {
    if (selectedIds.value.length === 0) {
        return;
    }

    router.delete(destroyManyShipmentsRoute().url, {
        data: { ids: selectedIds.value },
        preserveScroll: true,
    });
};

const importDialogOpen = ref(false);
const importFile = ref<File | null>(null);
const importFileName = ref('');
const importErrors = ref<string[]>(props.importErrors);

watch(
    () => props.importErrors,
    (errors) => {
        importErrors.value = errors;
    },
);

const dismissImportErrors = () => {
    importErrors.value = [];
};

const onImportFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    importFile.value = target.files?.[0] ?? null;
    importFileName.value = importFile.value?.name ?? '';
};

const importTemplateUrl = importTemplateRoute().url;
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
            <div class="flex flex-wrap items-center gap-2">
                <Dialog v-model:open="importDialogOpen">
                    <DialogTrigger as-child>
                        <Button variant="outline">
                            <FileUp />
                            Import Excel
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader class="space-y-3">
                            <DialogTitle class="flex items-center gap-2">
                                <FileSpreadsheet class="size-5 text-primary" />
                                Import Data Kiriman
                            </DialogTitle>
                            <DialogDescription>
                                Tambahkan banyak kiriman sekaligus dengan
                                mengunggah file Excel. Ikuti format pada file
                                template, lalu periksa hasil impornya.
                            </DialogDescription>
                        </DialogHeader>

                        <Form
                            v-bind="ShipmentController.import.form()"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            v-slot="{ errors, processing }"
                            @success="importDialogOpen = false"
                        >
                            <div
                                v-if="isSuperAdmin"
                                class="grid gap-2"
                            >
                                <Label for="import-region"
                                    >Wilayah IKSASS *</Label
                                >
                                <Select name="region_iksass_id">
                                    <SelectTrigger
                                        id="import-region"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Pilih wilayah IKSASS"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="r in regions"
                                            :key="r.id"
                                            :value="String(r.id)"
                                        >
                                            {{ r.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="errors.region_iksass_id"
                                />
                            </div>

                            <div class="grid gap-2">
                                <Label for="import-file">File Excel *</Label>
                                <Input
                                    id="import-file"
                                    name="file"
                                    type="file"
                                    accept=".xlsx,.xls,.csv"
                                    required
                                    class="file:text-sm"
                                    @change="onImportFileChange"
                                />
                                <p class="mt-1 text-center font-mono text-xs text-muted-foreground break-all">
                                    {{ importFileName || 'Belum ada file dipilih.' }}
                                </p>
                                <InputError :message="errors.file" />
                            </div>

                            <Button
                                as-child
                                variant="ghost"
                                size="sm"
                                class="w-fit -m-1 text-xs"
                            >
                                <a
                                    :href="importTemplateUrl"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    <Download />
                                    Unduh template Excel
                                </a>
                            </Button>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button
                                        type="button"
                                        variant="secondary"
                                    >
                                        Batal
                                    </Button>
                                </DialogClose>
                                <Button type="submit" :disabled="processing">
                                    <Loader2
                                        v-if="processing"
                                        class="animate-spin"
                                    />
                                    <FileUp v-else />
                                    Import Sekarang
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
                <Button as-child>
                    <Link
                        :href="
                            createShipmentRoute({
                                query: { back: backToList() },
                            })
                        "
                    >
                        <Plus />
                        Tambah Kiriman
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Import result -->
        <Alert
            v-if="importErrors.length > 0"
            variant="destructive"
        >
            <FileSpreadsheet />
            <AlertTitle class="font-semibold">
                {{ importErrors.length }} baris dilewati saat impor
            </AlertTitle>
            <AlertDescription class="mt-2">
                <ul
                    class="max-h-56 list-disc space-y-1 overflow-auto pl-5 text-xs"
                >
                    <li
                        v-for="(error, index) in importErrors"
                        :key="index"
                    >
                        {{ error }}
                    </li>
                </ul>
                <Button
                    variant="ghost"
                    size="sm"
                    class="mt-3 h-auto text-xs"
                    @click="dismissImportErrors"
                >
                    Tutup
                </Button>
            </AlertDescription>
        </Alert>

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

        <!-- Bulk action bar -->
        <div
            v-if="selectedIds.length > 0"
            class="flex flex-wrap items-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-3 py-2 shadow-card dark:border-primary-800 dark:bg-primary-950/60"
        >
            <div class="flex items-center gap-1">
                <span
                    class="text-sm font-semibold text-primary-900 dark:text-primary-100"
                >
                    {{ selectedIds.length }} kiriman dipilih
                </span>
                <Button
                    variant="ghost"
                    size="sm"
                    class="text-muted-foreground"
                    @click="clearSelection"
                >
                    Batal
                </Button>
            </div>
            <div class="ml-auto flex flex-wrap items-center gap-2">
                <Button variant="outline" size="sm" @click="printSelected">
                    <Printer />
                    Cetak Stiker
                </Button>
                <Dialog v-model:open="bulkStatusOpen">
                    <DialogTrigger as-child>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="!canBulkStatus"
                            @click="openBulkStatus"
                        >
                            <ArrowRightLeft />
                            Ubah Status
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader class="space-y-3">
                            <DialogTitle>Perbarui Status Kiriman</DialogTitle>
                            <DialogDescription>
                                Tandai
                                {{ selectedIds.length }} kiriman terpilih.
                                Lengkapi catatan dan bukti foto sebelum
                                mengirim.
                            </DialogDescription>
                        </DialogHeader>

                        <Form
                            v-bind="bulkStatusRoute.form()"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            v-slot="{ errors, processing }"
                            @success="bulkStatusOpen = false"
                        >
                            <input
                                v-for="id in selectedIds"
                                :key="id"
                                type="hidden"
                                name="ids[]"
                                :value="id"
                            />

                            <div v-if="bulkTargets.length > 1" class="grid gap-2">
                                <Label
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    Status Tujuan
                                </Label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        v-for="[key, label] in bulkTargets"
                                        :key="key"
                                        type="button"
                                        class="rounded-lg border px-3 py-2 text-sm font-medium transition-colors"
                                        :class="
                                            bulkTarget === key
                                                ? 'border-primary-600 bg-primary-600 text-white'
                                                : 'border-input text-foreground hover:bg-surface-muted dark:hover:bg-white/10'
                                        "
                                        @click="bulkTarget = key"
                                    >
                                        {{ label }}
                                    </button>
                                </div>
                                <input
                                    type="hidden"
                                    name="status"
                                    :value="bulkTarget"
                                />
                            </div>
                            <input
                                v-else
                                type="hidden"
                                name="status"
                                :value="bulkTarget"
                            />

                            <div class="grid gap-2">
                                <Label
                                    for="bulk-notes"
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    Catatan
                                </Label>
                                <textarea
                                    id="bulk-notes"
                                    name="notes"
                                    rows="3"
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                                    placeholder="Catatan opsional (mis. kondisi paket, waktu penyerahan...)"
                                ></textarea>
                                <InputError :message="errors.notes" />
                            </div>

                            <div class="grid gap-2">
                                <Label
                                    for="bulk-attachment"
                                    class="text-xs font-medium text-muted-foreground"
                                >
                                    Bukti Foto
                                </Label>
                                <Input
                                    id="bulk-attachment"
                                    name="attachment"
                                    type="file"
                                    accept="image/*"
                                    class="file:text-sm"
                                    @change="onBulkFileChange"
                                />
                                <img
                                    v-if="bulkAttachmentPreview"
                                    :src="bulkAttachmentPreview"
                                    alt="Pratinjau bukti foto"
                                    class="mt-1 max-h-40 rounded-lg border object-cover"
                                />
                                <InputError :message="errors.attachment" />
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button type="button" variant="secondary">
                                        Batal
                                    </Button>
                                </DialogClose>
                                <Button
                                    type="submit"
                                    :disabled="processing"
                                >
                                    <Loader2
                                        v-if="processing"
                                        class="animate-spin"
                                    />
                                    <Check v-else />
                                    Tandai
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
                <ConfirmDialog
                    title="Hapus Kiriman Terpilih"
                    :description="`Yakin ingin menghapus ${selectedIds.length} kiriman terpilih? Tindakan ini tidak dapat dibatalkan.`"
                    confirm-text="Hapus Semua"
                    @confirm="deleteSelected"
                >
                    <Button variant="destructive" size="sm">
                        <Trash2 />
                        Hapus
                    </Button>
                </ConfirmDialog>
            </div>
        </div>

        <!-- Datatable -->
        <DataTable
            title="Daftar Kiriman"
            :description="`Menampilkan ${shipments.from ?? 0}–${shipments.to ?? 0} dari ${shipments.total} kiriman.`"
            :total="shipments.total"
            :from="shipments.from"
            :to="shipments.to"
            :links="shipments.links"
            :per-page="perPage"
            :icon="Boxes"
            @update:per-page="changePerPage"
        >
            <table class="w-full text-sm">
                    <thead
                        class="sticky top-0 z-10 bg-surface-muted/60 backdrop-blur-sm dark:bg-surface-muted"
                    >
                        <tr class="border-b border-border text-left">
                            <th class="w-12 px-3 py-3">
                                <Checkbox
                                    :model-value="allSelected"
                                    @update:model-value="toggleAll"
                                    aria-label="Pilih semua"
                                />
                            </th>
                            <SortableHeader
                                label="No"
                                column="no"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Tracking"
                                column="tracking_code"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Santri"
                                column="student"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Pengirim"
                                column="sender_name"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                v-if="isSuperAdmin"
                                label="Wilayah"
                                column="region"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Paket"
                                column="package_count"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Nominal"
                                column="amount_money"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Status"
                                column="status"
                                :sort="sort"
                                @sort="sortBy"
                            />
                            <SortableHeader
                                label="Update"
                                column="updated_at"
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
                            v-for="(item, index) in shipments.data"
                            :key="item.id"
                            class="border-b border-border/60 transition-colors last:border-0 even:bg-surface-muted/30 hover:bg-surface-muted/70 dark:even:bg-white/[0.03] dark:hover:bg-white/[0.06]"
                            :class="{
                                'bg-primary-50/60 hover:bg-primary-50/80 dark:bg-primary-950/50 dark:hover:bg-primary-950/60':
                                    isSelected(item.id),
                            }"
                        >
                            <td class="px-3 py-3">
                                <Checkbox
                                    :model-value="isSelected(item.id)"
                                    @update:model-value="
                                        (v: CheckboxCheckedState) =>
                                            toggle(item.id, v)
                                    "
                                    :aria-label="`Pilih ${item.tracking_code}`"
                                />
                            </td>
                            <td
                                class="px-3 py-3 text-xs text-muted-foreground tabular-nums"
                            >
                                {{ rowIndex(index) }}
                            </td>
                            <td class="px-3 py-3">
                                <Link
                                    :href="
                                        showShipmentRoute(item.id, {
                                            query: { back: backToList() },
                                        }).url
                                    "
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
                            <td class="px-3 py-3">
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
                            <td class="px-3 py-3 tabular-nums">
                                {{ item.package_count }} koli
                            </td>
                            <td class="px-3 py-3 tabular-nums">
                                {{ formatCurrency(item.amount_money) }}
                            </td>
                            <td class="px-3 py-3">
                                <ShipmentStatusBadge
                                    :status="item.status"
                                    size="sm"
                                />
                            </td>
                            <td class="px-3 py-3 text-xs text-muted-foreground">
                                {{ formatDateTime(item.updated_at) }}
                            </td>
                            <td class="px-3 py-3">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        as-child
                                        variant="ghost"
                                        size="icon-sm"
                                        :aria-label="`Lihat detail ${item.tracking_code}`"
                                    >
                                        <Link
                                            :href="
                                                showShipmentRoute(item.id, {
                                                    query: {
                                                        back: backToList(),
                                                    },
                                                }).url
                                            "
                                        >
                                            <Eye />
                                        </Link>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="shipments.data.length === 0" class="px-4 py-12">
                    <EmptyState
                        :icon="Boxes"
                        title="Belum ada kiriman"
                        :description="
                            hasActiveFilters
                                ? 'Tidak ada kiriman yang cocok dengan filter Anda.'
                                : 'Belum ada data kiriman untuk ditampilkan.'
                        "
                    />
                </div>
        </DataTable>
    </div>
</template>
