<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { Boxes, LayoutGrid, MapPin, Users, UserRoundCog } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import RegionIndicator from '@/components/RegionIndicator.vue';
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
const isSuperAdmin = computed(
    () => page.props.auth.user?.role === 'super_admin',
);

const navGroups = computed(() => {
    const main: NavItem[] = [
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
        },
    ];

    const admin: NavItem[] = [
        {
            title: 'Wilayah IKSASS',
            href: regions(),
            icon: MapPin,
        },
        {
            title: 'Pengguna',
            href: users(),
            icon: UserRoundCog,
        },
    ];

    return isSuperAdmin.value
        ? [
              { label: 'Utama', items: main },
              { label: 'Administrasi', items: admin },
          ]
        : [{ label: 'Utama', items: main }];
});
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
            <div class="px-3 pb-3 group-data-[collapsible=icon]:hidden">
                <RegionIndicator />
            </div>

            <NavMain :groups="navGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
