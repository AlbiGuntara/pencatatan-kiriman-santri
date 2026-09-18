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
        class="flex h-16 shrink-0 items-center gap-2 border-b border-border/70 bg-background/85 px-6 backdrop-blur-md transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4 lg:px-8"
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
                        class="relative size-10 rounded-full p-0.5 ring-1 ring-primary-200 transition-shadow hover:ring-primary-400 focus-within:ring-2 focus-within:ring-primary dark:ring-primary-800"
                    >
                        <Avatar
                            class="size-9 overflow-hidden rounded-full bg-primary-50"
                        >
                            <AvatarImage
                                v-if="user?.avatar"
                                :src="user.avatar"
                                :alt="user.name"
                            />
                            <AvatarFallback
                                class="bg-primary-600 font-semibold text-white"
                            >
                                {{ getInitials(user?.name) }}
                            </AvatarFallback>
                        </Avatar>
                        <span
                            class="absolute bottom-0 right-0 size-2.5 rounded-full bg-emerald-500 ring-2 ring-background"
                            title="Online"
                        />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
