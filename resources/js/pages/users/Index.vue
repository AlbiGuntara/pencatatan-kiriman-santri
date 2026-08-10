<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search } from '@lucide/vue';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as usersRoute } from '@/routes/users';
import { create as createUserRoute } from '@/routes/users';
import type { User } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pengguna',
                href: usersRoute(),
            },
        ],
    },
});

const props = defineProps<{
    users: {
        data: User[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
    roles: Record<string, string>;
}>();

const search = ref(props.filters.search ?? '');

const applyFilters = () => {
    router.get(
        usersRoute(),
        { search: search.value || undefined },
        { preserveState: true, preserveScroll: true },
    );
};
</script>

<template>
    <Head title="Pengguna" />

    <div class="flex flex-col gap-4 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Pengguna</h2>
                <p class="text-sm text-muted-foreground">
                    Kelola akun pengurus IKSASS daerah dan Super Admin pusat.
                </p>
            </div>
            <Button as-child>
                <Link :href="createUserRoute()">
                    <Plus />
                    Tambah Pengguna
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Pencarian</CardTitle>
                <CardDescription>Cari berdasarkan nama atau email.</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="grid flex-1 gap-2">
                    <Label for="search">Kata kunci</Label>
                    <div class="relative">
                        <Search class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            id="search"
                            v-model="search"
                            class="pl-9"
                            placeholder="Nama / email..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                </div>
                <Button @click="applyFilters">Cari</Button>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-muted-foreground">
                            <th class="pb-2 pr-4 font-medium">Nama</th>
                            <th class="pb-2 pr-4 font-medium">Email</th>
                            <th class="pb-2 pr-4 font-medium">Peran</th>
                            <th class="pb-2 pr-4 font-medium">Wilayah</th>
                            <th class="pb-2 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id" class="border-b last:border-0">
                            <td class="py-3 pr-4 font-medium">{{ user.name }}</td>
                            <td class="py-3 pr-4 text-muted-foreground">{{ user.email }}</td>
                            <td class="py-3 pr-4">
                                <Badge :variant="user.role === 'super_admin' ? 'default' : 'secondary'">
                                    {{ roles[user.role] ?? user.role }}
                                </Badge>
                            </td>
                            <td class="py-3 pr-4">{{ user.region?.name ?? '-' }}</td>
                            <td class="py-3">
                                <Button as-child variant="outline" size="sm">
                                    <Link :href="`/users/${user.id}/edit`">Edit</Link>
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="py-8 text-center text-muted-foreground">
                                Tidak ada pengguna.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <Pagination :links="users.links" />
            </CardContent>
        </Card>
    </div>
</template>
