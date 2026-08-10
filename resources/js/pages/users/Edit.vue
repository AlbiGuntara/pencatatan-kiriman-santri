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
import type { Region, User } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengguna',
                href: users(),
            },
            {
                title: 'Edit Pengguna',
                href: '/users',
            },
        ],
    },
});

defineProps<{
    user: User;
    roles: Record<string, string>;
    regions: Region[];
}>();

const update = UserController.update;
const destroy = UserController.destroy;
</script>

<template>
    <Head :title="`Edit Pengguna - ${user.name}`" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-2">
            <Button as-child variant="ghost" size="sm" class="w-fit">
                <Link :href="users()">
                    <ArrowLeft />
                    Kembali
                </Link>
            </Button>
            <h2 class="text-2xl font-semibold tracking-tight">Edit Pengguna</h2>
            <p class="text-sm text-muted-foreground">Perbarui akun {{ user.email }}.</p>
        </div>

        <Card class="max-w-xl">
            <CardHeader>
                <CardTitle>Informasi Akun</CardTitle>
                <CardDescription>Kosongkan password jika tidak ingin mengubahnya.</CardDescription>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="update.form(user.id)"
                    class="space-y-4"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nama Lengkap</Label>
                        <Input id="name" name="name" required :default-value="user.name" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="email" required :default-value="user.email" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="role">Peran</Label>
                        <Select name="role" :default-value="user.role">
                            <SelectTrigger id="role" class="w-full">
                                <SelectValue />
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
                        <Select
                            name="region_iksass_id"
                            :default-value="user.region_iksass_id ? String(user.region_iksass_id) : undefined"
                        >
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
                            <Label for="password">Password Baru</Label>
                            <Input id="password" name="password" type="password" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah" />
                            <InputError :message="errors.password" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="password_confirmation">Konfirmasi Password</Label>
                            <Input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <Button type="submit" :disabled="processing">Perbarui</Button>
                        <Button as-child variant="outline">
                            <Link :href="users()">Batal</Link>
                        </Button>
                    </div>
                </Form>

                <div class="mt-8 border-t pt-6">
                    <Form
                        v-bind="destroy.form(user.id)"
                        class="flex items-center justify-between"
                        v-slot="{ processing }"
                    >
                        <div>
                            <p class="text-sm font-medium text-destructive">Zona Bahaya</p>
                            <p class="text-xs text-muted-foreground">Hapus akun pengguna ini.</p>
                        </div>
                        <Button type="submit" variant="destructive" :disabled="processing">Hapus Akun</Button>
                    </Form>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
