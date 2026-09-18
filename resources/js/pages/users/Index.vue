<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2, UserCog } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import EmptyState from '@/components/EmptyState.vue';
import SortableHeader from '@/components/SortableHeader.vue';
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
import { useOutOfRangePage } from '@/composables/useOutOfRangePage';
import { useTableSort } from '@/composables/useTableSort';
import {
    create as createUserRoute,
    destroy as destroyUserRoute,
    edit as editUserRoute,
    index as usersRoute,
} from '@/routes/users';
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

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    users: {
        data: User[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
        per_page: number;
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: {
        search?: string;
        per_page?: number;
        sort?: string;
        direction?: string;
    };
    roles: Record<string, string>;
}>();

const search = ref(props.filters.search ?? '');
const perPage = ref(
    String(props.filters.per_page ?? props.users.per_page ?? 10),
);
const { sort, cycle: cycleSort, reset: resetSort } = useTableSort({
    column: props.filters.sort ?? null,
    direction: props.filters.direction === 'desc' ? 'desc' : 'asc',
});

const hasActiveFilters = computed(
    () => Boolean(search.value) || sort.value.column !== null,
);

const buildParams = () => ({
    search: search.value || undefined,
    per_page: perPage.value,
    sort: sort.value.column ?? undefined,
    direction: sort.value.column ? sort.value.direction : undefined,
});

const page = usePage();

const backToList = () => page.url;

useOutOfRangePage(
    () => props.users,
    () => usersRoute().url,
    buildParams,
);

const applyFilters = () => {
    router.get(usersRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    resetSort();
    router.get(
        usersRoute(),
        { per_page: perPage.value },
        { preserveState: true, preserveScroll: true },
    );
};

const sortBy = (column: string) => {
    cycleSort(column);

    router.get(usersRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const changePerPage = (value: string) => {
    perPage.value = value;
    router.get(usersRoute(), buildParams(), {
        preserveState: true,
        preserveScroll: true,
    });
};

const rowIndex = (index: number) =>
    (props.users.current_page - 1) * props.users.per_page + index + 1;

const deleteUser = (user: User) => {
    router.delete(destroyUserRoute(user.id).url, { preserveScroll: true });
};
</script>

<template>
    <Head title="Pengguna" />

    <div class="flex flex-col gap-4 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h2 class="text-2xl font-semibold tracking-tight">Pengguna</h2>
                <p class="text-sm text-muted-foreground">
                    Kelola akun pengurus IKSASS daerah dan Super Admin pusat.
                </p>
            </div>
            <Button as-child>
                <Link
                    :href="createUserRoute({ query: { back: backToList() } })"
                >
                    <Plus />
                    Tambah Pengguna
                </Link>
            </Button>
        </div>

        <Card class="shadow-card">
            <CardHeader>
                <CardTitle>Pencarian</CardTitle>
                <CardDescription
                    >Cari berdasarkan nama atau email.</CardDescription
                >
            </CardHeader>
            <CardContent class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="grid flex-1 gap-2">
                    <Label for="search">Kata kunci</Label>
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
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
                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    class="text-muted-foreground"
                    @click="clearFilters"
                >
                    Reset
                </Button>
            </CardContent>
        </Card>

        <DataTable
            title="Daftar Pengguna"
            :description="`Menampilkan ${users.from ?? 0}–${users.to ?? 0} dari ${users.total} data.`"
            :total="users.total"
            :from="users.from"
            :to="users.to"
            :links="users.links"
            :per-page="perPage"
            :icon="UserCog"
            @update:per-page="changePerPage"
        >
            <table class="w-full text-sm">
                <thead
                    class="sticky top-0 z-10 bg-surface-muted/60 backdrop-blur-sm"
                >
                    <tr class="border-b border-border text-left">
                        <SortableHeader
                            label="No"
                            column="no"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Nama"
                            column="name"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Email"
                            column="email"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Peran"
                            column="role"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <SortableHeader
                            label="Wilayah"
                            column="region"
                            :sort="sort"
                            @sort="sortBy"
                        />
                        <th
                            class="px-3 py-3 text-right text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(user, index) in users.data"
                        :key="user.id"
                        class="border-b border-border/60 transition-colors last:border-0 even:bg-surface-muted/30 hover:bg-surface-muted/70"
                    >
                        <td
                            class="px-3 py-3 text-xs text-muted-foreground tabular-nums"
                        >
                            {{ rowIndex(index) }}
                        </td>
                        <td class="px-3 py-3 font-medium">
                            {{ user.name }}
                        </td>
                        <td class="px-3 py-3 text-muted-foreground">
                            {{ user.email }}
                        </td>
                        <td class="px-3 py-3">
                            <Badge
                                :variant="
                                    user.role === 'super_admin'
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{ roles[user.role] ?? user.role }}
                            </Badge>
                        </td>
                        <td class="px-3 py-3">
                            {{ user.region?.name ?? '-' }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <Button
                                    as-child
                                    variant="ghost"
                                    size="icon-sm"
                                    :aria-label="`Edit ${user.name}`"
                                >
                                    <Link
                                        :href="
                                            editUserRoute(user.id, {
                                                query: {
                                                    back: backToList(),
                                                },
                                            }).url
                                        "
                                    >
                                        <Pencil />
                                    </Link>
                                </Button>
                                <ConfirmDialog
                                    :title="`Hapus ${user.name}`"
                                    description="Yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan."
                                    @confirm="deleteUser(user)"
                                >
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        :aria-label="`Hapus ${user.name}`"
                                    >
                                        <Trash2 />
                                    </Button>
                                </ConfirmDialog>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="6" class="px-4 py-12">
                            <EmptyState
                                :icon="UserCog"
                                title="Belum ada pengguna"
                                description="Tidak ada pengguna yang cocok dengan pencarian Anda."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </DataTable>
    </div>
</template>
