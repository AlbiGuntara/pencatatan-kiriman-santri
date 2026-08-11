<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { MapPinned, Plus, Search } from '@lucide/vue';
import { ref } from 'vue';
import EmptyState from '@/components/EmptyState.vue';
import Pagination from '@/components/Pagination.vue';
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
import { index as regionsRoute } from '@/routes/regions';
import { create as createRegionRoute } from '@/routes/regions';
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

const props = defineProps<{
    regions: {
        data: (Region & { shipments_count: number; users_count: number })[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search ?? '');

const applyFilters = () => {
    router.get(
        regionsRoute(),
        { search: search.value || undefined },
        { preserveState: true, preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Wilayah IKSASS" />

    <div class="flex flex-col gap-4 p-4">
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
                <Link :href="createRegionRoute()">
                    <Plus />
                    Tambah Wilayah
                </Link>
            </Button>
        </div>

        <Card>
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
            </CardContent>
        </Card>

        <Card>
            <CardContent class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-muted-foreground">
                            <th class="pr-4 pb-2 font-medium">Kode</th>
                            <th class="pr-4 pb-2 font-medium">Nama Wilayah</th>
                            <th class="pr-4 pb-2 font-medium">Pengurus</th>
                            <th class="pr-4 pb-2 font-medium">Kiriman</th>
                            <th class="pb-2 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="region in regions.data"
                            :key="region.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 pr-4">
                                <Badge variant="outline">{{
                                    region.code
                                }}</Badge>
                            </td>
                            <td class="py-3 pr-4 font-medium">
                                {{ region.name }}
                            </td>
                            <td class="py-3 pr-4">
                                {{ region.users_count }} orang
                            </td>
                            <td class="py-3 pr-4">
                                {{ region.shipments_count }} kiriman
                            </td>
                            <td class="py-3">
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="`/regions/${region.id}/edit`"
                                        >Edit</Link
                                    >
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="regions.data.length === 0">
                            <td colspan="5">
                                <EmptyState
                                    :icon="MapPinned"
                                    title="Belum ada wilayah IKSASS"
                                    description="Belum ada data wilayah IKSASS untuk ditampilkan."
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :links="regions.links" />
            </CardContent>
        </Card>
    </div>
</template>
