<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Form } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import UserController from '@/actions/App/Http/Controllers/UserController';
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
import { index as users } from '@/routes/users';
import type { Region } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengguna',
                href: users(),
            },
            {
                title: 'Tambah Pengguna',
                href: '/users/create',
            },
        ],
    },
});

defineProps<{
    roles: Record<string, string>;
    regions: Region[];
}>();
</script>

<template>
    <Head title="Tambah Pengguna" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="users()">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-semibold tracking-tight">Tambah Pengguna</h2>
            <p class="text-sm text-muted-foreground">
                Buat akun baru untuk pengurus IKSASS daerah atau Super Admin.
            </p>
        </div>

        <Card class="max-w-xl">
            <CardHeader>
                <CardTitle>Informasi Akun</CardTitle>
                <CardDescription>Akun dibuat oleh pusat; pengguna tidak dapat mendaftar mandiri.</CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="UserController.store.form()"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nama Lengkap</Label>
                        <Input id="name" name="name" required placeholder="Nama pengurus" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" required placeholder="email@contoh.com" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="role">Peran</Label>
                        <Select name="role">
                            <SelectTrigger id="role" class="w-full">
                                <SelectValue placeholder="Pilih peran" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="(label, value) in roles" :key="value" :value="value">
                                    {{ label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.role" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="region_iksass_id">Wilayah IKSASS</Label>
                        <Select name="region_iksass_id">
                            <SelectTrigger id="region_iksass_id" class="w-full">
                                <SelectValue placeholder="Wajib untuk Admin IKSASS Daerah" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="region in regions" :key="region.id" :value="String(region.id)">
                                    {{ region.name }} ({{ region.code }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.region_iksass_id" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input id="password" name="password" type="password" required autocomplete="new-password" />
                            <InputError :message="errors.password" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="password_confirmation">Konfirmasi Password</Label>
                            <Input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <Button type="submit" :disabled="processing">Simpan</Button>
                        <Button as-child variant="outline">
                            <Link :href="users()">Batal</Link>
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
