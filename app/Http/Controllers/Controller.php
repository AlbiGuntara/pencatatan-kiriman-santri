<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Resolve a safe "per page" value from the request, clamped to an allow-list.
     */
    protected function perPage(Request $request, int $default = 10): int
    {
        $allowed = [10, 15, 20, 25, 50, 100];
        $perPage = $request->integer('per_page', $default);

        return in_array($perPage, $allowed, true) ? $perPage : $default;
    }

    /**
     * Redirect to the list page the user came from (via the "back" field or the
     * referer) so page number, sort and filters are preserved after a CRUD action.
     * Falls back to the named route when no safe previous URL points at the list.
     */
    protected function redirectToList(Request $request, string $listPath, string $fallbackRoute): RedirectResponse
    {
        $candidates = [$request->input('back'), $request->headers->get('referer')];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '' && $this->isInternalUrl($candidate)) {
                $path = (string) parse_url($candidate, PHP_URL_PATH);

                if ($path === $listPath || $path === $listPath.'/') {
                    return redirect($candidate);
                }
            }
        }

        return redirect()->route($fallbackRoute);
    }

    /**
     * Normalize a caller-supplied "back" URL, returning an empty string when the
     * value is missing or points at an external host.
     */
    protected function internalBackUrl(string $url): string
    {
        $url = trim($url);

        return $url !== '' && $this->isInternalUrl($url) ? $url : '';
    }

    protected function isInternalUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if ($host === null) {
            return true;
        }

        return strtolower((string) $host) === strtolower((string) request()->getHost());
    }
}
