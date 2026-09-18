<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import { computed } from 'vue';
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

const props = defineProps<{
    region: Region;
    back?: string;
}>();

const update = RegionController.update;
const destroy = RegionController.destroy;

const backUrl = computed(() => props.back || regions());
</script>

<template>
    <Head :title="`Edit Wilayah - ${region.name}`" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="backUrl">
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
                    :options="{ preserveScroll: true }"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <input
                        type="hidden"
                        name="back"
                        :value="props.back"
                    />
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
                            <Link :href="backUrl">Batal</Link>
                        </Button>
                    </div>
                </Form>

                <div class="mt-8 border-t pt-6">
                    <Form
                        v-bind="destroy.form(region.id)"
                        :options="{ preserveScroll: true }"
                        class="flex items-center justify-between"
                        v-slot="{ processing }"
                    >
                        <input
                            type="hidden"
                            name="back"
                            :value="props.back"
                        />
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
