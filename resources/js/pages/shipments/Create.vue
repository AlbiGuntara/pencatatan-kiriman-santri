<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Boxes,
    Loader2,
    MapPin,
    Phone,
    Search,
    Send,
    UserRound,
    Wallet,
    X,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import ShipmentController from '@/actions/App/Http/Controllers/ShipmentController';
import InputError from '@/components/InputError.vue';
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
    back?: string;
}>();

const backUrl = computed(() => props.back || shipments());

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
        const response = await fetch(
            `/students/search?search=${encodeURIComponent(term)}`,
        );
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

    <div class="mx-auto flex max-w-3xl flex-col gap-5 p-4 md:p-6">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="backUrl">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-bold tracking-tight">Input Kiriman</h2>
            <p class="text-sm text-muted-foreground">
                Catat kiriman wali santri dari
                <span v-if="region" class="font-medium text-foreground">
                    {{ region.name }}
                </span>
                <span v-else class="font-medium text-foreground">
                    wilayah terpilih </span
                >.
            </p>
        </div>

        <Form
            v-bind="ShipmentController.store.form()"
            :options="{ preserveScroll: true }"
            class="space-y-4"
            v-slot="{ errors, processing }"
        >
            <input
                type="hidden"
                name="back"
                :value="props.back"
            />
            <input
                type="hidden"
                name="student_id"
                :value="selectedStudent?.id"
            />

            <!-- Section 1 · Santri -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <span
                            class="flex size-7 items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400"
                        >
                            <UserRound class="size-4" />
                        </span>
                        Data Santri Penerima
                    </CardTitle>
                    <CardDescription>
                        Cari santri berdasarkan NIS atau nama untuk memastikan
                        paket diterima orang yang tepat.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="student-search">Santri Penerima *</Label>
                        <div class="relative">
                            <Search
                                class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="student-search"
                                :model-value="searchTerm"
                                :readonly="selectedStudent !== null"
                                class="pr-10 pl-9"
                                placeholder="Ketik NIS atau nama santri..."
                                @update:model-value="
                                    searchTerm = String($event)
                                "
                            />
                            <Button
                                v-if="selectedStudent"
                                variant="ghost"
                                size="icon-sm"
                                class="absolute top-1/2 right-1 -translate-y-1/2"
                                type="button"
                                @click="clearStudent"
                                aria-label="Hapus pilihan santri"
                            >
                                <X />
                            </Button>
                            <Loader2
                                v-else-if="loading"
                                class="absolute top-1/2 right-3 size-4 -translate-y-1/2 animate-spin text-muted-foreground"
                            />

                            <div
                                v-if="searchOpen && !selectedStudent"
                                class="absolute top-full left-0 z-20 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-border bg-popover shadow-elevated"
                            >
                                <button
                                    v-for="student in results"
                                    :key="student.id"
                                    type="button"
                                    class="flex w-full items-center justify-between gap-2 px-3 py-2.5 text-left text-sm hover:bg-accent"
                                    @click="pickStudent(student)"
                                >
                                    <span>
                                        <span class="font-medium">{{
                                            student.name
                                        }}</span>
                                        <span
                                            class="block text-xs text-muted-foreground"
                                        >
                                            {{ student.dormitory }}
                                        </span>
                                    </span>
                                    <span
                                        class="font-mono text-xs text-muted-foreground"
                                    >
                                        {{ student.nis }}
                                    </span>
                                </button>
                                <div
                                    v-if="results.length === 0 && !loading"
                                    class="px-3 py-4 text-center text-sm text-muted-foreground"
                                >
                                    Tidak ada santri ditemukan.
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="selectedStudent"
                            class="rounded-xl border border-primary-100 bg-primary-50/70 p-4 dark:border-primary-900/50 dark:bg-primary-950/30"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <span
                                    class="text-xs font-semibold tracking-wide text-primary-800 uppercase dark:text-primary-300"
                                >
                                    Konfirmasi Santri
                                </span>
                                <Badge variant="outline" class="bg-background">
                                    NIS {{ selectedStudent.nis }}
                                </Badge>
                            </div>
                            <dl class="grid gap-3 text-sm sm:grid-cols-3">
                                <div>
                                    <dt class="text-xs text-muted-foreground">
                                        Nama
                                    </dt>
                                    <dd class="font-semibold">
                                        {{ selectedStudent.name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-muted-foreground">
                                        Asrama
                                    </dt>
                                    <dd class="font-medium">
                                        {{ selectedStudent.dormitory }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-muted-foreground">
                                        Lokasi
                                    </dt>
                                    <dd class="font-medium">
                                        {{ selectedStudent.location_type }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <InputError :message="errors.student_id" />
                    </div>
                </CardContent>
            </Card>

            <!-- Section 2 · Pengirim -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <span
                            class="flex size-7 items-center justify-center rounded-lg bg-accent-100 text-accent-700 dark:bg-accent-950/60 dark:text-accent-400"
                        >
                            <Phone class="size-4" />
                        </span>
                        Data Pengirim
                    </CardTitle>
                    <CardDescription>
                        Informasi wali santri atau pihak pengirim di daerah
                        asal.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="sender_name">Nama Pengirim *</Label>
                        <Input
                            id="sender_name"
                            name="sender_name"
                            required
                            placeholder="Nama wali santri / pengirim"
                        />
                        <InputError :message="errors.sender_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="sender_phone">Nomor HP Pengirim</Label>
                        <Input
                            id="sender_phone"
                            name="sender_phone"
                            placeholder="08xxxx (opsional)"
                        />
                        <InputError :message="errors.sender_phone" />
                    </div>
                </CardContent>
            </Card>

            <!-- Section 3 · Kiriman -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <span
                            class="flex size-7 items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400"
                        >
                            <Boxes class="size-4" />
                        </span>
                        Detail Kiriman
                    </CardTitle>
                    <CardDescription>
                        Jumlah uang, paket, dan wilayah asal pengiriman.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-if="isSuperAdmin" class="grid gap-2">
                        <Label for="region_iksass_id"
                            >Wilayah IKSASS Asal *</Label
                        >
                        <Select name="region_iksass_id">
                            <SelectTrigger id="region_iksass_id" class="w-full">
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
                        <InputError :message="errors.region_iksass_id" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="amount_money">Jumlah Uang (Rp)</Label>
                            <div class="relative">
                                <Wallet
                                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input
                                    id="amount_money"
                                    name="amount_money"
                                    type="number"
                                    min="0"
                                    step="50000"
                                    class="pl-9"
                                    placeholder="0"
                                />
                            </div>
                            <InputError :message="errors.amount_money" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="package_count"
                                >Jumlah Paket (Koli) *</Label
                            >
                            <div class="relative">
                                <Boxes
                                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input
                                    id="package_count"
                                    name="package_count"
                                    type="number"
                                    min="1"
                                    max="99"
                                    :default-value="1"
                                    class="pl-9"
                                    required
                                />
                            </div>
                            <InputError :message="errors.package_count" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="location_type">Lokasi Santri</Label>
                        <div class="relative">
                            <MapPin
                                class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Select name="location_type">
                                <SelectTrigger
                                    id="location_type"
                                    class="w-full pl-9"
                                >
                                    <SelectValue
                                        placeholder="Pilih lokasi santri (opsional)"
                                    />
                                </SelectTrigger>
                                <SelectContent>
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
                        <InputError :message="errors.location_type" />
                    </div>
                </CardContent>
            </Card>

            <div class="flex items-center gap-2 pt-2 pb-4">
                <Button
                    type="submit"
                    class="flex-1 sm:min-w-44 sm:flex-none"
                    :disabled="processing"
                >
                    <Loader2 v-if="processing" class="animate-spin" />
                    <Send v-else />
                    Simpan Kiriman
                </Button>
<Button as-child variant="outline">
                            <Link :href="backUrl">Batal</Link>
                        </Button>
            </div>
        </Form>
    </div>
</template>
