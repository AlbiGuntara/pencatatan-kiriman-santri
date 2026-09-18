import { ref } from 'vue';

export type SortState = {
    column: string | null;
    direction: 'asc' | 'desc';
};

export function useTableSort(initial?: Partial<SortState>) {
    const sort = ref<SortState>({
        column: initial?.column ?? null,
        direction: initial?.direction === 'desc' ? 'desc' : 'asc',
    });

    const cycle = (column: string) => {
        if (sort.value.column === column) {
            sort.value =
                sort.value.direction === 'asc'
                    ? { column, direction: 'desc' }
                    : { column: null, direction: 'asc' };
        } else {
            sort.value = { column, direction: 'asc' };
        }
    };

    const reset = () => {
        sort.value = { column: null, direction: 'asc' };
    };

    return { sort, cycle, reset };
}