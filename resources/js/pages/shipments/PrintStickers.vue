<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import QRCode from 'qrcode';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';

type PrintShipment = {
    id: number;
    tracking_code: string;
    package_count: number;
    student_name: string;
    nis: string;
    dormitory: string;
    sender_name: string;
    region_name: string;
};

const props = defineProps<{
    shipments: PrintShipment[];
}>();

const qrDataUrls = ref<Record<string, string>>({});

const APP_URL = `${window.location.origin}`;

const doPrint = () => window.print();

const goBack = () => window.history.back();

onMounted(async () => {
    for (const shipment of props.shipments) {
        const url = `${APP_URL}/shipments/${shipment.id}`;
        qrDataUrls.value[shipment.id] = await QRCode.toDataURL(url, {
            width: 120,
            margin: 1,
            errorCorrectionLevel: 'M',
        });
    }

    setTimeout(() => window.print(), 500);
});
</script>

<template>
    <Head title="Cetak Stiker" />

    <div class="no-print fixed inset-x-0 top-0 z-50 flex items-center justify-between border-b bg-background px-4 py-2">
        <div class="text-sm font-medium">
            Cetak Stiker &middot; {{ shipments.length }} kiriman
            <span class="text-muted-foreground">(ukuran label thermal 57x40mm)</span>
        </div>
        <div class="flex gap-2">
            <Button @click="doPrint">Cetak</Button>
            <Button variant="outline" @click="goBack">Kembali</Button>
        </div>
    </div>

    <div class="mt-14 grid grid-cols-2 gap-3 p-4 print:mt-0 print:p-0">
        <template v-for="shipment in shipments" :key="shipment.id">
            <div
                v-for="index in shipment.package_count"
                :key="`${shipment.id}-${index}`"
                class="sticker break-inside-avoid rounded border p-2 text-center shadow-sm"
            >
                <div class="text-[9px] leading-tight font-semibold tracking-wide uppercase">
                    Pondok Pesantren Salafiyah Syafi'iyah
                </div>
                <div class="text-[8px] leading-tight text-muted-foreground">Sukorejo &middot; Situbondo</div>

                <img
                    v-if="qrDataUrls[shipment.id]"
                    :src="qrDataUrls[shipment.id]"
                    alt="QR"
                    class="mx-auto my-1 h-14 w-14"
                />

                <div class="font-mono text-[8px] text-muted-foreground">{{ shipment.tracking_code }}</div>
                <div class="mt-0.5 truncate text-xs font-bold leading-tight">{{ shipment.student_name }}</div>
                <div class="font-mono text-[8px]">NIS: {{ shipment.nis }}</div>
                <div class="truncate text-[8px] leading-tight">{{ shipment.dormitory }}</div>
                <div class="mt-1 text-[9px] font-semibold">
                    Paket {{ index }} dari {{ shipment.package_count }}
                </div>
                <div class="truncate text-[8px]">Pengirim: {{ shipment.sender_name }}</div>
                <div class="text-[8px] font-medium">{{ shipment.region_name }}</div>
            </div>
        </template>
    </div>
</template>

<style>
@media print {
    .no-print {
        display: none !important;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .sticker {
        width: 57mm;
        min-height: 40mm;
        page-break-inside: avoid;
        break-inside: avoid;
        box-shadow: none !important;
    }
}
</style>
