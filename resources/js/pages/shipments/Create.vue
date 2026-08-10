<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Search, X } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import ShipmentController from '@/actions/App/Http/Controllers/ShipmentController';
import InputError from '@/components/InputError.vue';
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
import { index as shipments } from '@/routes/shipments';
import type { Region, Student } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kiriman',
                href: shipments(),
            },
            {
                title: 'Input Kiriman',
                href: '/shipments/create',
            },
        ],
    },
});

const props = defineProps<{
    locations: string[];
    region: Region | null;
    regions: Region[];
}>();

const searchTerm = ref('');
const results = ref<Student[]>([]);
const selectedStudent = ref<Student | null>(null);
const loading = ref(false);
const searchOpen = ref(false);

const isSuperAdmin = computed(() => props.region === null);

let searchTimeout: ReturnType<typeof setTimeout> | undefined;

const doSearch = async (term: string) => {
    loading.value = true;

    try {
        const response = await fetch(`/students/search?search=${encodeURIComponent(term)}`);
        results.value = await response.json();
        searchOpen.value = true;
    } finally {
        loading.value = false;
    }
};

watch(searchTerm, (term) => {
    if (selectedStudent.value) {
return;
}

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => doSearch(term), 300);
});

const pickStudent = (student: Student) => {
    selectedStudent.value = student;
    searchTerm.value = student.name;
    searchOpen.value = false;
};

const clearStudent = () => {
    selectedStudent.value = null;
    searchTerm.value = '';
    searchOpen.value = false;
};
</script>

<template>
    <Head title="Input Kiriman" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="shipments()">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-semibold tracking-tight">Input Kiriman</h2>
            <p class="text-sm text-muted-foreground">
                Catat kiriman wali santri dari
                <span v-if="region" class="font-medium">{{ region.name }}</span>
                <span v-else class="font-medium">wilayah terpilih</span>.
            </p>
        </div>

        <Card class="max-w-3xl">
            <CardHeader>
                <CardTitle>Form Kiriman</CardTitle>
                <CardDescription>
                    Cari santri berdasarkan NIS atau nama, lalu lengkapi data pengiriman.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="ShipmentController.store.form()"
                    class="space-y-5"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="student-search">Santri Penerima *</Label>
                        <div class="relative">
                            <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="student-search"
                                :model-value="searchTerm"
                                :readonly="selectedStudent !== null"
                                class="pr-10 pl-9"
                                placeholder="Ketik NIS atau nama santri..."
                                @update:model-value="searchTerm = String($event)"
                            />
                            <Button
                                v-if="selectedStudent"
                                variant="ghost"
                                size="icon-sm"
                                class="absolute top-1/2 right-1 -translate-y-1/2"
                                type="button"
                                @click="clearStudent"
                            >
                                <X />
                            </Button>
                            <Loader2 v-else-if="loading" class="absolute top-1/2 right-3 size-4 -translate-y-1/2 animate-spin text-muted-foreground" />

                            <div
                                v-if="searchOpen && !selectedStudent"
                                class="absolute top-full left-0 z-10 mt-1 max-h-64 w-full overflow-auto rounded-md border bg-background shadow-lg"
                            >
                                <button
                                    v-for="student in results"
                                    :key="student.id"
                                    type="button"
                                    class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm hover:bg-accent"
                                    @click="pickStudent(student)"
                                >
                                    <span>
                                        <span class="font-medium">{{ student.name }}</span>
                                        <span class="block text-xs text-muted-foreground">{{ student.dormitory }}</span>
                                    </span>
                                    <span class="font-mono text-xs text-muted-foreground">{{ student.nis }}</span>
                                </button>
                                <div v-if="results.length === 0 && !loading" class="px-3 py-4 text-center text-sm text-muted-foreground">
                                    Tidak ada santri ditemukan.
                                </div>
                            </div>
                        </div>

                        <div v-if="selectedStudent" class="flex flex-wrap gap-2 pt-1">
                            <Badge variant="secondary">NIS {{ selectedStudent.nis }}</Badge>
                            <Badge variant="secondary">{{ selectedStudent.dormitory }}</Badge>
                            <Badge variant="outline">{{ selectedStudent.location_type }}</Badge>
                        </div>

                        <input type="hidden" name="student_id" :value="selectedStudent?.id" />

                        <InputError :message="errors.student_id" />
                    </div>

                    <div v-if="isSuperAdmin" class="grid gap-2">
                        <Label for="region_iksass_id">Wilayah IKSASS Asal *</Label>
                        <Select name="region_iksass_id">
                            <SelectTrigger id="region_iksass_id" class="w-full">
                                <SelectValue placeholder="Pilih wilayah IKSASS" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="r in regions" :key="r.id" :value="String(r.id)">
                                    {{ r.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.region_iksass_id" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="sender_name">Nama Pengirim *</Label>
                            <Input id="sender_name" name="sender_name" required placeholder="Nama wali santri / pengirim" />
                            <InputError :message="errors.sender_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sender_phone">Nomor HP Pengirim</Label>
                            <Input id="sender_phone" name="sender_phone" placeholder="08xxxx (opsional)" />
                            <InputError :message="errors.sender_phone" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="amount_money">Jumlah Uang (Rp)</Label>
                            <Input id="amount_money" name="amount_money" type="number" min="0" step="50000" placeholder="0" />
                            <InputError :message="errors.amount_money" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="package_count">Jumlah Paket (Koli) *</Label>
                            <Input id="package_count" name="package_count" type="number" min="1" max="99" :default-value="1" required />
                            <InputError :message="errors.package_count" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="location_type">Lokasi Santri</Label>
                        <Select name="location_type">
                            <SelectTrigger id="location_type" class="w-full">
                                <SelectValue placeholder="Pilih lokasi santri (opsional)" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="loc in locations" :key="loc" :value="loc">
                                    {{ loc }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.location_type" />
                    </div>

                    <div class="flex gap-2 pt-2">
                        <Button type="submit" :disabled="processing">Simpan Kiriman</Button>
                        <Button as-child variant="outline">
                            <Link :href="shipments()">Batal</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
