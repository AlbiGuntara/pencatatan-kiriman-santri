<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { BookOpen, Boxes, FolderGit2, LayoutGrid, MapPin, Users } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as regions } from '@/routes/regions';
import { index as shipments } from '@/routes/shipments';
import { index as students } from '@/routes/students';
import { index as users } from '@/routes/users';
import type { NavItem } from '@/types';

const page = usePage<{ auth: { user?: { role?: string } } }>();
const isSuperAdmin = computed(() => page.props.auth.user?.role === 'super_admin');

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Kiriman',
        href: shipments(),
        icon: Boxes,
    },
    {
        title: 'Data Santri',
        href: students(),
        icon: Users,
        isActive: false,
    },
    {
        title: 'Wilayah IKSASS',
        href: regions(),
        icon: MapPin,
        isActive: false,
    },
    {
        title: 'Pengguna',
        href: users(),
        icon: Users,
        isActive: false,
    },
]);

const visibleNavItems = computed(() =>
    isSuperAdmin.value
        ? mainNavItems.value
        : mainNavItems.value.filter(
              (item) => !['Data Santri', 'Wilayah IKSASS', 'Pengguna'].includes(item.title),
          ),
);

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="visibleNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
