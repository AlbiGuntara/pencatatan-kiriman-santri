<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search } from '@lucide/vue';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { index as studentsRoute } from '@/routes/students';
import { create as createStudentRoute } from '@/routes/students';
import type { Student } from '@/types';

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

const props = defineProps<{
    students: {
        data: Student[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string; location_type?: string };
    locations: string[];
}>();

const search = ref(props.filters.search ?? '');
const locationType = ref(props.filters.location_type ?? 'all');

const applyFilters = () => {
    router.get(
        studentsRoute(),
        {
            search: search.value || undefined,
            location_type: locationType.value === 'all' ? undefined : locationType.value,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const clearFilters = () => {
    search.value = '';
    locationType.value = 'all';
    router.get(studentsRoute(), {}, { preserveState: true, preserveScroll: true });
};
</script>

<template>
    <Head title="Data Santri" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Data Santri</h2>
                <p class="text-sm text-muted-foreground">
                    Master data santri Pondok Pesantren Salafiyah Syafi'iyah Sukorejo.
                </p>
            </div>
            <Button as-child>
                <Link :href="createStudentRoute()">
                    <Plus />
                    Tambah Santri
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Pencarian & Filter</CardTitle>
                <CardDescription>Cari berdasarkan NIS, nama, atau asrama.</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-3 md:flex-row">
                <div class="grid flex-1 gap-2">
                    <Label for="search">Kata kunci</Label>
                    <div class="relative">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
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
                            <SelectItem v-for="loc in locations" :key="loc" :value="loc">
                                {{ loc }}
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
                            <th class="pb-2 pr-4 font-medium">NIS</th>
                            <th class="pb-2 pr-4 font-medium">Nama</th>
                            <th class="pb-2 pr-4 font-medium">Asrama</th>
                            <th class="pb-2 pr-4 font-medium">Lokasi</th>
                            <th class="pb-2 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="student in students.data" :key="student.id" class="border-b last:border-0">
                            <td class="py-3 pr-4 font-mono text-xs">{{ student.nis }}</td>
                            <td class="py-3 pr-4 font-medium">{{ student.name }}</td>
                            <td class="py-3 pr-4">{{ student.dormitory }}</td>
                            <td class="py-3 pr-4">
                                <Badge variant="secondary">{{ student.location_type }}</Badge>
                            </td>
                            <td class="py-3">
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="`/students/${student.id}/edit`">Edit</Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td colspan="5" class="py-8 text-center text-muted-foreground">
                                Tidak ada data santri yang cocok.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :links="students.links" />
            </CardContent>
        </Card>
    </div>
</template>
