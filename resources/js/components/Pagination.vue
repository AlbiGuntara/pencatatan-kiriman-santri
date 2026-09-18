<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PageItem =
    | { type: 'page'; page: number; url: string | null }
    | { type: 'ellipsis' };

const props = withDefaults(
    defineProps<{
        links: PaginationLink[];
        bordered?: boolean;
        class?: string;
    }>(),
    {
        bordered: true,
    },
);

const numericLinks = computed(() =>
    props.links.filter((link) => /^\d+$/.test(link.label)),
);

const lastPage = computed(() => numericLinks.value.length);

const currentPage = computed(() => {
    const active = numericLinks.value.find((link) => link.active);

    return active ? Number(active.label) : 1;
});

const urlForPage = (page: number): string | null =>
    numericLinks.value.find((link) => Number(link.label) === page)?.url ?? null;

const prevLink = computed(
    () => props.links.find((link) => link.label.includes('Previous')) ?? null,
);

const nextLink = computed(
    () => props.links.find((link) => link.label.includes('Next')) ?? null,
);

const pageItems = computed<PageItem[]>(() => {
    const current = Math.min(Math.max(currentPage.value, 1), lastPage.value);
    const last = lastPage.value;

    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => ({
            type: 'page' as const,
            page: i + 1,
            url: urlForPage(i + 1),
        }));
    }

    const start = Math.max(2, current - 1);
    const end = Math.min(last - 1, current + 1);
    const items: PageItem[] = [{ type: 'page', page: 1, url: urlForPage(1) }];

    if (start > 2) {
        items.push({ type: 'ellipsis' });
    }

    for (let page = start; page <= end; page++) {
        items.push({ type: 'page', page, url: urlForPage(page) });
    }

    if (end < last - 1) {
        items.push({ type: 'ellipsis' });
    }

    items.push({ type: 'page', page: last, url: urlForPage(last) });

    return items;
});
</script>

<template>
    <nav
        v-if="links.length > 0"
        :class="[
            'flex flex-wrap items-center gap-1',
            bordered ? 'border-t pt-4' : '',
            props.class,
        ]"
    >
        <Button
            v-if="prevLink?.url"
            variant="outline"
            size="icon-sm"
            as-child
        >
            <Link :href="prevLink.url" aria-label="Halaman sebelumnya">
                <ChevronLeft />
            </Link>
        </Button>
        <Button v-else variant="outline" size="icon-sm" disabled aria-label="Halaman sebelumnya">
            <ChevronLeft />
        </Button>

        <template v-for="(item, index) in pageItems" :key="index">
            <span
                v-if="item.type === 'ellipsis'"
                class="inline-flex h-8 w-8 items-center justify-center rounded-md text-sm text-muted-foreground"
            >
                &hellip;
            </span>
            <Button
                v-else-if="item.url"
                :variant="item.page === currentPage ? 'default' : 'outline'"
                :class="item.page === currentPage ? 'shadow-sm' : ''"
                size="sm"
                as-child
            >
                <Link :href="item.url">{{ item.page }}</Link>
            </Button>
            <Button v-else variant="outline" size="sm" disabled>
                {{ item.page }}
            </Button>
        </template>

        <Button
            v-if="nextLink?.url"
            variant="outline"
            size="icon-sm"
            as-child
        >
            <Link :href="nextLink.url" aria-label="Halaman berikutnya">
                <ChevronRight />
            </Link>
        </Button>
        <Button v-else variant="outline" size="icon-sm" disabled aria-label="Halaman berikutnya">
            <ChevronRight />
        </Button>
    </nav>
</template>
