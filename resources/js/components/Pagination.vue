<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <nav v-if="links.length > 0" class="flex flex-wrap items-center gap-1 border-t pt-4">
        <template v-for="(link, index) in links" :key="index">
            <Button v-if="link.url" :variant="link.active ? 'default' : 'outline'" size="sm" as-child>
                <Link :href="link.url">
                    <span v-html="link.label"></span>
                </Link>
            </Button>
            <span
                v-else
                v-html="link.label"
                class="inline-flex h-8 items-center justify-center rounded-md px-3 text-sm text-muted-foreground"
            />
        </template>
    </nav>
</template>
