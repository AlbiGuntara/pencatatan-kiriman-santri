<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { ArrowLeft, Check, Loader2, Printer } from '@lucide/vue';
import { computed, ref } from 'vue';
import ShipmentController from '@/actions/App/Http/Controllers/ShipmentController';
import InputError from '@/components/InputError.vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatCurrency, formatDateTime } from '@/lib/format';
import { index as shipments } from '@/routes/shipments';
import type { Shipment } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Kiriman',
                href: shipments(),
            },
            {
                title: 'Detail Kiriman',
                href: '/shipments',
            },
        ],
    },
});

const props = defineProps<{
    shipment: Shipment;
    canUpdateStatus: boolean;
    printUrl: string;
}>();

const logs = computed(() => props.shipment.logs ?? []);

const nextStatus = computed(() => {
    const current = props.shipment.status;

    if (current === 'created') {
return { value: 'in_transit', label: 'Dalam Perjalanan' };
}

    if (current === 'in_transit') {
return { value: 'delivered', label: 'Tiba & Diserahkan' };
}

    return null;
});

const attachmentPreview = ref<string | null>(null);

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    attachmentPreview.value = file ? URL.createObjectURL(file) : null;
};
</script>

<template>
    <Head :title="`Detail Kiriman ${shipment.tracking_code}`" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-2">
                <Button as-child variant="ghost" size="sm" class="w-fit">
                    <Link :href="shipments()">
                        <ArrowLeft />
                        Kembali
                    </Link>
                </Button>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-mono text-2xl font-semibold tracking-tight">{{ shipment.tracking_code }}</h2>
                    <ShipmentStatusBadge :status="shipment.status" />
                </div>
                <p class="text-sm text-muted-foreground">
                    Dicatat oleh {{ shipment.creator?.name }} &middot; {{ formatDateTime(shipment.created_at) }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <Link :href="printUrl">
                        <Printer />
                        Cetak Stiker
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            <div class="space-y-4 xl:col-span-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Santri</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 text-sm sm:grid-cols-2">
                            <div>
                                <div class="text-muted-foreground">Nama Santri</div>
                                <div class="font-medium">{{ shipment.student?.name }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">NIS</div>
                                <div class="font-mono">{{ shipment.student?.nis }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Asrama</div>
                                <div class="font-medium">{{ shipment.student?.dormitory }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Lokasi</div>
                                <Badge variant="secondary">{{ shipment.location_type || shipment.student?.location_type || '-' }}</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Kiriman</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 text-sm sm:grid-cols-2">
                            <div>
                                <div class="text-muted-foreground">Nama Pengirim</div>
                                <div class="font-medium">{{ shipment.sender_name }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Nomor HP Pengirim</div>
                                <div class="font-medium">{{ shipment.sender_phone || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Wilayah Asal (IKSASS)</div>
                                <div class="font-medium">{{ shipment.region?.name }}</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Kode Wilayah</div>
                                <Badge variant="outline">{{ shipment.region?.code }}</Badge>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Jumlah Paket</div>
                                <div class="font-medium">{{ shipment.package_count }} koli</div>
                            </div>
                            <div>
                                <div class="text-muted-foreground">Jumlah Uang</div>
                                <div class="font-medium">{{ formatCurrency(shipment.amount_money) }}</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Linimasa Pelacakan (Timeline)</CardTitle>
                        <CardDescription>Riwayat status dan bukti foto pengiriman.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ol v-if="logs.length > 0" class="relative space-y-6 border-l pl-6">
                            <li v-for="log in logs" :key="log.id" class="relative">
                                <span
                                    class="absolute top-1 -left-[30px] flex size-4 items-center justify-center rounded-full border bg-background"
                                >
                                    <span
                                        :class="
                                            log.status_to === 'delivered'
                                                ? 'bg-emerald-500'
                                                : log.status_to === 'in_transit'
                                                  ? 'bg-blue-500'
                                                  : 'bg-amber-500'
                                        "
                                        class="size-2 rounded-full"
                                    />
                                </span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium">{{ log.status_to_label }}</span>
                                    <Badge variant="secondary" class="text-xs">
                                        {{ formatDateTime(log.created_at) }}
                                    </Badge>
                                    <span v-if="log.logger" class="text-xs text-muted-foreground">oleh {{ log.logger }}</span>
                                </div>
                                <p v-if="log.notes" class="mt-1 text-sm text-muted-foreground">{{ log.notes }}</p>
                                <img
                                    v-if="log.attachment_url"
                                    :src="log.attachment_url"
                                    alt="Bukti foto"
                                    class="mt-2 max-h-56 rounded-md border object-cover"
                                />
                            </li>
                        </ol>
                        <p v-else class="text-sm text-muted-foreground">Belum ada riwayat.</p>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-4">
                <Card v-if="canUpdateStatus && nextStatus">
                    <CardHeader>
                        <CardTitle>Perbarui Status</CardTitle>
                        <CardDescription>
                            Tandai paket: {{ nextStatus.label }}.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            v-bind="ShipmentController.updateStatus.form(shipment.id)"
                            enctype="multipart/form-data"
                            class="space-y-4"
                            v-slot="{ errors, processing }"
                        >
                            <input type="hidden" name="status" :value="nextStatus.value" />

                            <div class="grid gap-2">
                                <Label for="notes">Catatan</Label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="3"
                                    class="border-input bg-transparent w-full rounded-md border px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                                    placeholder="Catatan opsional (mis. kondisi paket, waktu penyerahan...)"
                                ></textarea>
                                <InputError :message="errors.notes" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="attachment">Bukti Foto</Label>
                                <div class="flex items-center gap-3">
                                    <Input
                                        id="attachment"
                                        name="attachment"
                                        type="file"
                                        accept="image/*"
                                        class="file:text-sm"
                                        @change="onFileChange"
                                    />
                                </div>
                                <img
                                    v-if="attachmentPreview"
                                    :src="attachmentPreview"
                                    alt="Pratinjau bukti foto"
                                    class="mt-1 max-h-40 rounded-md border object-cover"
                                />
                                <InputError :message="errors.attachment" />
                            </div>

                            <Button type="submit" class="w-full" :disabled="processing">
                                <Loader2 v-if="processing" class="animate-spin" />
                                <Check v-else />
                                Tandai {{ nextStatus.label }}
                            </Button>
                        </Form>
                    </CardContent>
                </Card>

                <Card v-else class="border-emerald-200 bg-emerald-50/50 dark:border-emerald-900/40 dark:bg-emerald-900/20">
                    <CardContent class="flex items-center gap-2 text-sm text-emerald-700 dark:text-emerald-300">
                        <Check class="size-4" />
                        Kiriman telah tiba dan diserahkan kepada santri.
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
