import { router } from '@inertiajs/vue3';
import { watch } from 'vue';

type PaginatorLike = {
    data: unknown[];
    total: number;
    current_page: number;
    last_page: number;
};

/**
 * When the server returns an out-of-range page (e.g. after deleting the last
 * row of the last page and the referer still points at that page number), jump
 * back to the last valid page so the list never renders empty while data
 * exists. No-op when the empty result is caused by filters (total === 0).
 */
export function useOutOfRangePage(
    paginator: () => PaginatorLike,
    routeUrl: () => string,
    buildParams: () => Record<string, unknown>,
): void {
    watch(
        () => paginator().data,
        (data) => {
            const page = paginator();

            if (
                data.length === 0 &&
                page.total > 0 &&
                page.current_page > 1
            ) {
                router.get(
                    routeUrl(),
                    {
                        ...buildParams(),
                        page: Math.min(page.current_page, page.last_page),
                    },
                    { preserveScroll: true },
                );
            }
        },
        { immediate: true },
    );
}