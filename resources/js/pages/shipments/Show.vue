<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Camera,
    Check,
    Circle,
    CircleCheck,
    Loader2,
    Printer,
    Truck,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import ShipmentController from '@/actions/App/Http/Controllers/ShipmentController';
import InputError from '@/components/InputError.vue';
import ShipmentStatusBadge from '@/components/ShipmentStatusBadge.vue';
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
import { formatCurrency, formatDateTime } from '@/lib/format';
import { index as shipments } from '@/routes/shipments';
import type { Shipment, ShipmentLog, ShipmentStatus } from '@/types';

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
    back?: string;
}>();

const backUrl = computed(() => props.back || shipments());

const logs = computed(() => props.shipment.logs ?? []);

const nextStatus = computed(() => {
    const current = props.shipment.status;

    if (current === 'created') {
        return {
            value: 'in_transit' as ShipmentStatus,
            label: 'Dalam Perjalanan',
        };
    }

    if (current === 'in_transit') {
        return {
            value: 'delivered' as ShipmentStatus,
            label: 'Tiba & Diserahkan',
        };
    }

    return null;
});

const order: Record<ShipmentStatus, number> = {
    created: 0,
    in_transit: 1,
    delivered: 2,
};

const timelineSteps = computed(() => {
    const steps: Array<{
        key: ShipmentStatus;
        label: string;
        description: string;
    }> = [
        {
            key: 'created',
            label: 'Paket Dibuat',
            description: 'Data kiriman dicatat dan stiker label dicetak.',
        },
        {
            key: 'in_transit',
            label: 'Dalam Perjalanan',
            description: 'Paket diberangkatkan dari daerah asal menuju pondok.',
        },
        {
            key: 'delivered',
            label: 'Tiba & Diserahkan',
            description:
                'Paket tiba di pondok dan diserahkan langsung kepada santri.',
        },
    ];

    return steps.map((step) => {
        const log = logs.value.find((l) => l.status_to === step.key);
        const reached = order[props.shipment.status] >= order[step.key];
        const isCurrent = props.shipment.status === step.key;

        return { ...step, log, reached, isCurrent };
    });
});

const evidence = computed(() =>
    logs.value.find(
        (l): l is ShipmentLog & { attachment_url: string } =>
            l.status_to === 'delivered' && Boolean(l.attachment_url),
    ),
);

const attachmentPreview = ref<string | null>(null);

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    attachmentPreview.value = file ? URL.createObjectURL(file) : null;
};
</script>

<template>
    <Head :title="`Detail Kiriman ${shipment.tracking_code}`" />

    <div class="mx-auto flex max-w-6xl flex-col gap-5 p-4 md:p-6">
        <!-- Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <div class="flex flex-col gap-2">
                <Button as-child variant="ghost" size="sm" class="w-fit">
                    <Link :href="backUrl">
                        <ArrowLeft />
                        Kembali
                    </Link>
                </Button>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-mono text-2xl font-bold tracking-tight">
                        {{ shipment.tracking_code }}
                    </h2>
                    <ShipmentStatusBadge :status="shipment.status" />
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ shipment.student?.name }}
                    <span class="font-mono"
                        >· NIS {{ shipment.student?.nis }}</span
                    >
                </p>
                <p class="text-xs text-muted-foreground">
                    Dicatat oleh {{ shipment.creator?.name }} &middot;
                    {{ formatDateTime(shipment.created_at) }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button as-child variant="accent">
                    <Link :href="printUrl">
                        <Printer />
                        Cetak Stiker
                    </Link>
                </Button>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-3">
            <!-- Main column -->
            <div class="flex flex-col gap-5 xl:col-span-2">
                <!-- Timeline -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-base">
                            <Truck class="size-4 text-primary-600" />
                            Lacak Kiriman
                        </CardTitle>
                        <CardDescription>
                            Perjalanan paket hingga diserahkan kepada santri.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <ol class="relative">
                            <li
                                v-for="(step, index) in timelineSteps"
                                :key="step.key"
                                class="relative flex gap-4 pb-8 last:pb-0"
                            >
                                <div class="flex flex-col items-center">
                                    <span
                                        class="flex size-8 shrink-0 items-center justify-center rounded-full border-2"
                                        :class="
                                            step.reached
                                                ? 'border-primary-600 bg-primary-600 text-white'
                                                : 'border-border bg-background text-muted-foreground'
                                        "
                                    >
                                        <CircleCheck
                                            v-if="step.reached"
                                            class="size-4"
                                        />
                                        <Circle v-else class="size-4" />
                                    </span>
                                    <span
                                        v-if="index < timelineSteps.length - 1"
                                        class="w-0.5 flex-1"
                                        :class="
                                            step.reached
                                                ? 'bg-primary-200 dark:bg-primary-800'
                                                : 'bg-border'
                                        "
                                    />
                                </div>
                                <div class="flex-1 pb-1">
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <span
                                            class="text-sm font-semibold"
                                            :class="
                                                step.reached
                                                    ? 'text-foreground'
                                                    : 'text-muted-foreground'
                                            "
                                        >
                                            {{ step.label }}
                                        </span>
                                        <span
                                            v-if="step.isCurrent"
                                            class="inline-flex items-center gap-1.5 rounded-full bg-info/10 px-2 py-0.5 text-[11px] font-medium text-info"
                                        >
                                            <span
                                                class="size-1.5 animate-pulse rounded-full bg-info"
                                            />
                                            Status saat ini
                                        </span>
                                    </div>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        {{ step.description }}
                                    </p>
                                    <p
                                        v-if="step.log"
                                        class="mt-1 text-xs font-medium text-primary-700 dark:text-primary-400"
                                    >
                                        {{
                                            formatDateTime(step.log.created_at)
                                        }}
                                        <span
                                            v-if="step.log.logger"
                                            class="font-normal text-muted-foreground"
                                        >
                                            · oleh {{ step.log.logger }}
                                        </span>
                                    </p>
                                    <p
                                        v-else-if="
                                            !step.reached &&
                                            step.key === 'created'
                                        "
                                        class="mt-1 text-xs font-medium"
                                    >
                                        {{
                                            formatDateTime(shipment.created_at)
                                        }}
                                    </p>
                                    <p
                                        v-if="step.log?.notes"
                                        class="mt-1 text-xs text-muted-foreground"
                                    >
                                        {{ step.log.notes }}
                                    </p>
                                </div>
                            </li>
                        </ol>
                    </CardContent>
                </Card>

                <div class="grid gap-5 lg:grid-cols-2">
                    <!-- Student info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base"
                                >Informasi Santri</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    Nama Santri
                                </div>
                                <div class="font-medium">
                                    {{ shipment.student?.name }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    NIS
                                </div>
                                <div class="font-mono">
                                    {{ shipment.student?.nis }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    Asrama
                                </div>
                                <div class="font-medium">
                                    {{ shipment.student?.dormitory }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    Lokasi
                                </div>
                                <Badge variant="secondary">
                                    {{
                                        shipment.location_type ||
                                        shipment.student?.location_type ||
                                        '-'
                                    }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Shipment info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-base"
                                >Informasi Kiriman</CardTitle
                            >
                        </CardHeader>
                        <CardContent class="space-y-3 text-sm">
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    Nama Pengirim
                                </div>
                                <div class="font-medium">
                                    {{ shipment.sender_name }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    Nomor HP Pengirim
                                </div>
                                <div class="font-medium">
                                    {{ shipment.sender_phone || '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    Wilayah Asal (IKSASS)
                                </div>
                                <div class="font-medium">
                                    {{ shipment.region?.name }}
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <div class="text-xs text-muted-foreground">
                                        Jumlah Paket
                                    </div>
                                    <div class="font-medium">
                                        {{ shipment.package_count }} koli
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-muted-foreground">
                                        Jumlah Uang
                                    </div>
                                    <div class="font-medium">
                                        {{
                                            formatCurrency(
                                                shipment.amount_money,
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Sidebar column -->
            <div class="flex flex-col gap-5">
                <!-- Status update -->
                <Card v-if="canUpdateStatus && nextStatus">
                    <CardHeader>
                        <CardTitle class="text-base">Perbarui Status</CardTitle>
                        <CardDescription>
                            Tandai paket sebagai
                            <span class="font-medium text-foreground">{{
                                nextStatus.label
                            }}</span
                            >.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            v-bind="
                                ShipmentController.updateStatus.form(
                                    shipment.id,
                                )
                            "
                            enctype="multipart/form-data"
                            class="space-y-4"
                            v-slot="{ errors, processing }"
                        >
                            <input
                                type="hidden"
                                name="status"
                                :value="nextStatus.value"
                            />

                            <div class="grid gap-2">
                                <Label for="notes">Catatan</Label>
                                <textarea
                                    id="notes"
                                    name="notes"
                                    rows="3"
                                    class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
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
                                    class="mt-1 max-h-40 rounded-lg border object-cover"
                                />
                                <InputError :message="errors.attachment" />
                            </div>

                            <Button
                                type="submit"
                                class="w-full"
                                :disabled="processing"
                            >
                                <Loader2
                                    v-if="processing"
                                    class="animate-spin"
                                />
                                <Check v-else />
                                Tandai {{ nextStatus.label }}
                            </Button>
                        </Form>
                    </CardContent>
                </Card>

                <Card
                    v-else-if="shipment.status === 'delivered'"
                    class="border-primary-200 bg-primary-50/70 dark:border-primary-900/50 dark:bg-primary-950/30"
                >
                    <CardContent class="flex items-center gap-3 py-4 text-sm">
                        <span
                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white"
                        >
                            <Check class="size-4" />
                        </span>
                        <div>
                            <p
                                class="font-semibold text-primary-900 dark:text-primary-200"
                            >
                                Kiriman telah diserahkan
                            </p>
                            <p
                                class="text-xs text-primary-800/70 dark:text-primary-300/70"
                            >
                                Paket telah sampai di pondok dan diserahkan
                                kepada santri.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Evidence -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2 text-base">
                            <Camera class="size-4 text-primary-600" />
                            Bukti Penyerahan
                        </CardTitle>
                        <CardDescription>
                            Dokumentasi foto saat paket tiba & diserahkan.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="evidence" class="space-y-3">
                            <img
                                :src="evidence.attachment_url"
                                alt="Bukti foto penyerahan paket"
                                class="w-full rounded-lg border border-border object-cover"
                            />
                            <div class="text-xs text-muted-foreground">
                                {{ formatDateTime(evidence.created_at) }}
                                <span v-if="evidence.logger">
                                    · Diunggah oleh {{ evidence.logger }}
                                </span>
                            </div>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center gap-2 rounded-lg border border-dashed border-border py-8 text-center"
                        >
                            <Camera class="size-6 text-muted-foreground" />
                            <p class="text-sm text-muted-foreground">
                                {{
                                    shipment.status === 'delivered'
                                        ? 'Belum ada bukti foto diunggah.'
                                        : 'Bukti foto akan tampil setelah paket diserahkan.'
                                }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
