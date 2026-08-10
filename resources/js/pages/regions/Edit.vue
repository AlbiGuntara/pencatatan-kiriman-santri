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
import type { Region } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Wilayah IKSASS',
                href: regions(),
            },
            {
                title: 'Edit Wilayah',
                href: '/regions',
            },
        ],
    },
});

defineProps<{
    region: Region;
}>();

const update = RegionController.update;
const destroy = RegionController.destroy;
</script>

<template>
    <Head :title="`Edit Wilayah - ${region.name}`" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="regions()">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-semibold tracking-tight">Edit Wilayah IKSASS</h2>
            <p class="text-sm text-muted-foreground">Perbarui informasi wilayah {{ region.code }}.</p>
        </div>

        <Card class="max-w-xl">
            <CardHeader>
                <CardTitle>Informasi Wilayah</CardTitle>
                <CardDescription>Perbarui nama dan kode wilayah IKSASS.</CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="update.form(region.id)"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nama Wilayah</Label>
                        <Input id="name" name="name" required :default-value="region.name" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="code">Kode Wilayah</Label>
                        <Input id="code" name="code" required maxlength="10" :default-value="region.code" />
                        <InputError :message="errors.code" />
                    </div>

                    <div class="flex gap-2 pt-2">
                        <Button type="submit" :disabled="processing">Perbarui</Button>
                        <Button as-child variant="outline">
                            <Link :href="regions()">Batal</Link>
                        </Button>
                    </div>
                </Form>

                <div class="mt-8 border-t pt-6">
                    <Form
                        v-bind="destroy.form(region.id)"
                        class="flex items-center justify-between"
                        v-slot="{ processing }"
                    >
                        <div>
                            <p class="text-sm font-medium text-destructive">Zona Bahaya</p>
                            <p class="text-xs text-muted-foreground">
                                Menghapus wilayah ini juga memengaruhi kiriman terkait.
                            </p>
                        </div>
                        <Button type="submit" variant="destructive" :disabled="processing">Hapus</Button>
                    </Form>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
