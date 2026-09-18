# SIPENRI — Design System

Visual identity for SIPENRI ("Sistem Kiriman IKSASS"), built on Laravel + Inertia + Vue 3 + Tailwind CSS v4 + shadcn-vue (Reka UI).

## Direction

**Emerald elegan** — evolve the institutional green of IKSASS toward a refined emerald, paired with a subtle gold accent. Full visual refresh, elegant & airy, light-first with a purpose-built dark mode.

## Core ideas

- **Emerald is the voice, gold is the occasion.** Emerald carries structure, navigation, and primary actions. Gold is reserved for celebration: the "Input Kiriman" CTA, the value (nominal) metric, the Aksi Cepat card, delivery milestones.
- **Airy before dense.** Generous whitespace, soft 20px card radii, thin borders over heavy fills, and elevation via gentle shadow rather than outline.
- **Every state is legible in both themes.** Status colors are tuned separately for light and dark (e.g. `in_transit` is sky in light, a luminous `#67b3d3`-adjacent variant in dark).
- **Vanilla over framework.** Charts are hand-rolled SVG (area chart + donut) so they inherit theme tokens with zero dependency cost.

## Tokens (`resources/css/app.css`, Tailwind v4 `@theme`)

### Color — primary `#0f8050` emerald

| Step | Light | Dark |
| --- | --- | --- |
| `--color-primary-50` | `#eefbf4` | — |
| `--color-primary-400` | `#34c583` | — |
| `--color-primary-600` | `#0f8050` | — |
| `--color-primary-700` | `#0a6b42` | — |
| `--color-primary-950` | `#04291a` | — |

`--primary` resolves to `#0f8050` (light) / `#34c583` (dark).

### Color — accent `#d9ab33` gold

Used sparingly for celebration moments (primary CTA, nominal value, Aksi Cepat).

### Surfaces

| Token | Light | Dark |
| --- | --- | --- |
| `background` | `#f4f6f4` (warm grey-green) | `#0b120d` (deep green-black) |
| `foreground` | `#14201a` | `#e3eee6` |
| `card` | `#ffffff` | `#111b14` |
| `muted-foreground` | slate-500 | `#8da599` |
| `border` | `#dde5df` | `#203026` |
| `sidebar-background` | `#063223` | `#07170e` |

### Semantic

- `success` `#0f8050`, `info` `#0e7490`, `warning` `#c29628`, `danger` `#dc2626` (light) / `#f87171` (dark).
- Status palette: `created` = neutral (slate), `in_transit` = sky, `delivered` = emerald, each with explicit light + dark badge treatment.

### Chart sequence

`--chart-1..#5`: emerald `#0f8050` → `#17a368` → gold `#c29628` → `#34c583` → slate `#94a3b8`; dark theme re-tunes bright (`#34c583`, `#6bdfa8`, `#d9ab33`).

### Shape & elevation

- Radius: `sm` 8, `md` 10, `lg` 14, `xl` 20 px. Cards `rounded-2xl`, buttons `rounded-xl`.
- Shadows: `--shadow-card` (soft resting), `--shadow-elevated` (hover/modal/inset sidebar).

## Components

- **AppShell / Sidebar** (`ui/sidebar`): deep emerald rail, `rounded-2xl` inset/floating variant with `--shadow-elevated`; active nav item gets emerald pill + 2px emerald inset bar indicator; group labels uppercase `tracking-widest`.
- **AppSidebarHeader**: blurred translucent topbar with `border-border/70` hairline; avatar ring + emerald "online" status dot.
- **Buttons** (`ui/button`): `rounded-xl` semibold. Default = emerald w/ soft shadow + `active:scale-[0.98]`. `accent` = gold vertical gradient, dark-ink text.
- **Cards** (`ui/card`): `rounded-2xl shadow-card`.
- **Badges** (`ui/badge`, `ShipmentStatusBadge`): explicit border per status; dark-aware fills.
- **Charts**: `charts/AreaChart.vue` (catmull-rom smooth line + gradient area + endpoint emphasis) and an SVG donut for status composition. Both read `var(--chart-*)` so they theme-swap automatically.
- **Dashboard**: emerald gradient hero (date + greeting + actions), KPI row, "Aktivitas 7 Hari Terakhir" area chart with week summary, status donut, recent deliveries table, per-region bars (super admin) / status bars (region admin), Aksi Cepat card. Data served by `DashboardController::weeklyStats()`.

## Dark mode

Default light; `.dark` re-tunes every surface so nothing is pure-black/white-on-black — dark is a lamp-lit green-glass room (`#0b120d` bg, `#111b14` cards, `#34c583` emerald glow). Scrollbars and `::selection` are themed in both modes. Honors `prefers-reduced-motion`.

## Do

- Use tokens; never hardcode hex in components.
- Emerald for structure/primary, gold only for celebration moments.
- Test every status/state in both themes.

## Don't

- Don't reintroduce navy/blue default chakra styling; the old navy is gone.
- Don't add charting dependencies — keep SVG hand-rolled.
- Don't use pure black text on light or pure white on dark.