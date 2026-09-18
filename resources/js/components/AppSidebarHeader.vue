<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem } from '@/types';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user);
const pageTitle = computed(
    () => props.breadcrumbs[props.breadcrumbs.length - 1]?.title ?? '',
);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 bg-background/80 px-6 backdrop-blur-sm transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <div class="flex min-w-0 items-center gap-2 md:hidden">
                <AppLogoIcon class="size-6 shrink-0" />
                <span v-if="pageTitle" class="truncate text-sm font-semibold">
                    {{ pageTitle }}
                </span>
            </div>
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <div class="hidden items-center md:flex">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </div>
            </template>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <DropdownMenu>
                <DropdownMenuTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="relative size-9 rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                    >
                        <Avatar class="size-8 overflow-hidden rounded-full">
                            <AvatarImage
                                v-if="user?.avatar"
                                :src="user.avatar"
                                :alt="user.name"
                            />
                            <AvatarFallback
                                class="bg-primary-100 font-semibold text-primary-900"
                            >
                                {{ getInitials(user?.name) }}
                            </AvatarFallback>
                        </Avatar>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
