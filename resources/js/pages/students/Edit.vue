<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import StudentController from '@/actions/App/Http/Controllers/StudentController';
import InputError from '@/components/InputError.vue';
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
import { index as students } from '@/routes/students';
import type { Student } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Data Santri',
                href: students(),
            },
            {
                title: 'Edit Santri',
                href: '/students',
            },
        ],
    },
});

defineProps<{
    student: Student;
    locations: string[];
}>();

const update = StudentController.update;
</script>

<template>
    <Head :title="`Edit Santri - ${student.name}`" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="students()">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-semibold tracking-tight">Edit Santri</h2>
            <p class="text-sm text-muted-foreground">
                Perbarui data santri ({{ student.nis }}).
            </p>
        </div>

        <Card class="max-w-2xl">
            <CardHeader>
                <CardTitle>Informasi Santri</CardTitle>
                <CardDescription>Perbarui informasi data santri.</CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="update.form(student.id)"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="nis">NIS</Label>
                        <Input id="nis" name="nis" required :default-value="student.nis" />
                        <InputError :message="errors.nis" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Nama Lengkap</Label>
                        <Input id="name" name="name" required :default-value="student.name" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="dormitory">Asrama</Label>
                        <Input id="dormitory" name="dormitory" required :default-value="student.dormitory" />
                        <InputError :message="errors.dormitory" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="location_type">Lokasi Santri</Label>
                        <Select name="location_type" :default-value="student.location_type">
                            <SelectTrigger id="location_type" class="w-full">
                                <SelectValue />
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
                        <Button type="submit" :disabled="processing">Perbarui</Button>
                        <Button as-child variant="outline">
                            <Link :href="students()">Batal</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
