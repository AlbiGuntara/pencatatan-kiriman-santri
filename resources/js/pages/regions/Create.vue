<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import RegionController from '@/actions/App/Http/Controllers/RegionController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as regions } from '@/routes/regions';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Wilayah IKSASS',
                href: regions(),
            },
            {
                title: 'Tambah Wilayah',
                href: '/regions/create',
            },
        ],
    },
});
</script>

<template>
    <Head title="Tambah Wilayah IKSASS" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="regions()">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-semibold tracking-tight">Tambah Wilayah IKSASS</h2>
            <p class="text-sm text-muted-foreground">
                Tambahkan wilayah baru (contoh: IKSASS Bali, kode BALI).
            </p>
        </div>

        <Card class="max-w-xl">
            <CardHeader>
                <CardTitle>Informasi Wilayah</CardTitle>
                <CardDescription>Kode akan diubah menjadi huruf kapital otomatis.</CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="RegionController.store.form()"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nama Wilayah</Label>
                        <Input id="name" name="name" required placeholder="Contoh: IKSASS Bali" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="code">Kode Wilayah</Label>
                        <Input id="code" name="code" required maxlength="10" placeholder="Contoh: BALI" />
                        <InputError :message="errors.code" />
                    </div>

                    <div class="flex gap-2 pt-2">
                        <Button type="submit" :disabled="processing">Simpan</Button>
                        <Button as-child variant="outline">
                            <Link :href="regions()">Batal</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
