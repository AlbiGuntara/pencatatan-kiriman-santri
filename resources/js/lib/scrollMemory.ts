const STORAGE_KEY = 'sipenri:list-scroll';

const LIST_PATHS = ['/shipments', '/students', '/regions', '/users'];

type ScrollEntry = {
    url: string;
    y: number;
};

/**
 * Remember the vertical scroll offset of a list page right before the user
 * navigates away to a create/edit/detail page. Restored when they come back to
 * the exact same list URL (after a CRUD redirect that preserves the query
 * string). The entry is cleared as soon as it is consumed.
 */
export const saveListScroll = (): void => {
    if (!LIST_PATHS.includes(window.location.pathname)) {
        return;
    }

    const entry: ScrollEntry = {
        url: window.location.href,
        y: window.scrollY,
    };

    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(entry));
};

export const restoreListScroll = (): void => {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);

        if (!raw) {
            return;
        }

        const entry = JSON.parse(raw) as ScrollEntry;

        if (entry.url !== window.location.href) {
            return;
        }

        sessionStorage.removeItem(STORAGE_KEY);

        requestAnimationFrame(() => window.scrollTo(0, entry.y));
    } catch {
        sessionStorage.removeItem(STORAGE_KEY);
    }
};