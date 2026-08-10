# DESIGN.md — SIPENRI Design System

> **SIPENRI — Sistem Pendataan & Pelacakan Kiriman Santri**  
> Pondok Pesantren Salafiyah Syafi'iyah Sukorejo, Situbondo  
> Design direction: **Modern Islamic Logistics Dashboard**

---

## 1. Design Vision

SIPENRI is an operational logistics and shipment-tracking application, not a marketing website. The interface must prioritize:

- Fast information scanning
- Clear shipment status
- Efficient data entry
- Reliable tracking
- Strong regional context
- Data privacy awareness
- Easy label printing
- Responsive usage on desktop, tablet, and mobile

The visual identity combines:

**Modern Institutional + Minimalism + Swiss Grid + Subtle Islamic Identity + Logistics Dashboard**

The overall impression should be:

> **Terpercaya · Modern · Tenang · Efisien · Institusional**

Avoid making the interface overly decorative. Visual design must support operational work first.

---

## 2. Design Principles

### 2.1 Clarity First

Every screen should make the primary action obvious.

Prefer:
- Clear headings
- Short labels
- Strong hierarchy
- Consistent spacing
- Visible status
- Predictable navigation

Avoid:
- Excessive decoration
- Ambiguous icons
- Long blocks of instructional text
- Excessive gradients
- Dense visual noise

### 2.2 Minimalism

Use whitespace deliberately.

Cards should not exist merely to wrap content. Use cards when they create meaningful grouping or hierarchy.

### 2.3 Institutional Trust

SIPENRI represents an official operational system. The UI should feel stable, secure, and professional rather than experimental.

### 2.4 Operational Efficiency

Frequent tasks such as searching students, entering shipments, updating status, viewing history, and printing labels should require minimal interaction.

### 2.5 Context Awareness

Users must always understand which IKSASS region they are currently operating in.

Regional context should be visible in the application shell, especially for `iksass_admin`.

### 2.6 Responsive by Default

The interface must work well on:
- Desktop
- Laptop
- Tablet
- Mobile

Do not simply shrink desktop layouts. Reflow tables, forms, navigation, and cards appropriately.

---

## 3. Visual Style

### Primary Style

**Modern Institutional + Clean Minimalism**

### Supporting Styles

- Swiss / Editorial Grid
- Modern Dashboard UI
- Subtle Islamic geometric accents
- Utility / logistics visual language

### Limited Use

Glassmorphism may be used only as an accent for:
- Login surfaces
- Modal overlays
- Floating notifications
- Profile menus

It must not become the dominant application style.

### Do Not Use as Main Style

- Cyberpunk
- Heavy Neumorphism
- Neo-Brutalism
- Excessive 3D
- Excessive glass effects
- Decorative Islamic ornamentation throughout the UI

---

# 4. Color System

The primary brand color is **green**, supported by **yellow** as an accent.

The palette should feel mature and institutional rather than overly saturated.

## 4.1 Brand Colors

| Token | Hex | Usage |
|---|---|---|
| `--color-primary-950` | `#12351F` | Deepest green, high-emphasis text/background |
| `--color-primary-900` | `#164B2A` | Dark green |
| `--color-primary-800` | `#176B38` | Strong brand green |
| `--color-primary-700` | `#198754` | Primary action |
| `--color-primary-600` | `#229A5B` | Hover / interactive green |
| `--color-primary-500` | `#2EAA68` | Supporting green |
| `--color-primary-100` | `#DDF4E5` | Soft green surface |
| `--color-primary-50` | `#F0FAF3` | Very subtle green background |

## 4.2 Yellow Accent

Yellow is an accent, not a second primary brand.

| Token | Hex | Usage |
|---|---|---|
| `--color-accent-700` | `#A66B00` | Accessible dark yellow text |
| `--color-accent-600` | `#C58A00` | Accent |
| `--color-accent-500` | `#E0A800` | Main yellow accent |
| `--color-accent-400` | `#F2C94C` | Highlight |
| `--color-accent-100` | `#FFF3C4` | Soft yellow surface |
| `--color-accent-50` | `#FFFAE8` | Very subtle yellow background |

Use yellow for:
- Important highlights
- Selected indicators
- Secondary CTA
- Attention states
- Small decorative accents
- Brand details

Do not use yellow for large blocks of body text or primary navigation.

## 4.3 Neutral Colors

| Token | Hex | Usage |
|---|---|---|
| `--color-background` | `#F7F9F7` | Main application background |
| `--color-surface` | `#FFFFFF` | Cards and panels |
| `--color-surface-muted` | `#F1F4F2` | Secondary surfaces |
| `--color-border` | `#E1E7E3` | Borders/dividers |
| `--color-text` | `#17211B` | Primary text |
| `--color-text-secondary` | `#526057` | Secondary text |
| `--color-text-muted` | `#7A857E` | Muted text |

---

# 5. Semantic Status Colors

Shipment status must be recognizable through **color + icon + text**, never color alone.

## Created

- Color: Neutral
- Icon: `CircleDashed`
- Label: `Dibuat`

## In Transit

- Color: Blue
- Icon: `Truck`
- Label: `Dalam Perjalanan`

## Delivered

- Color: Green
- Icon: `CircleCheck`
- Label: `Diserahkan`

## Warning

- Color: Amber
- Icon: `TriangleAlert`

## Error

- Color: Red
- Icon: `CircleX`

Recommended semantic tokens:

```text
success: #198754
info:    #2563EB
warning: #D97706
danger:  #DC2626
neutral: #64748B
```

---

# 6. Typography

Typography should be modern, highly readable, and appropriate for a formal institutional application.

## Recommended Font

**Inter**

Fallback:

```text
Inter, ui-sans-serif, system-ui, sans-serif
```

Use a secondary serif font only for optional branding or editorial moments. The operational interface itself should remain sans-serif.

## Type Scale

| Element | Size | Weight |
|---|---:|---:|
| Display | 36–48px | 700 |
| Page title | 28–32px | 700 |
| Section title | 20–24px | 600 |
| Card title | 16–18px | 600 |
| Body | 14–16px | 400 |
| Small | 12–13px | 400/500 |
| Caption | 11–12px | 500 |

Use `line-height` generously for readability.

---

# 7. Spacing System

Use a consistent 4px base scale.

```text
4px   — micro spacing
8px   — icon/text spacing
12px  — compact controls
16px  — standard component spacing
20px  — card spacing
24px  — section spacing
32px  — major section spacing
40px  — page spacing
48px  — large layout spacing
64px  — hero / major separation
```

Prefer consistent spacing over arbitrary values.

---

# 8. Border Radius

Use moderate rounding.

```text
sm: 6px
md: 8px
lg: 12px
xl: 16px
```

Recommended:
- Inputs: `8px`
- Buttons: `8px`
- Cards: `12px`
- Large feature panels: `16px`
- Pills/badges: `9999px`

Avoid making every component excessively rounded.

---

# 9. Shadows

Use subtle shadows only.

```text
shadow-sm:
0 1px 2px rgba(16, 24, 40, 0.04)

shadow-card:
0 2px 8px rgba(16, 24, 40, 0.05)

shadow-elevated:
0 8px 24px rgba(16, 24, 40, 0.08)
```

Do not use large, dramatic shadows.

---

# 10. Layout Architecture

## Desktop

Use a persistent sidebar + topbar.

```text
┌───────────────┬─────────────────────────────────────────┐
│               │ Topbar                                  │
│     LOGO      ├─────────────────────────────────────────┤
│               │                                         │
│ Dashboard     │                                         │
│               │                 CONTENT                 │
│ Kiriman       │                                         │
│               │                                         │
│ Santri        │                                         │
│               │                                         │
│ Tracking      │                                         │
│               │                                         │
│ Laporan       │                                         │
│               │                                         │
│ Cetak Label   │                                         │
│               │                                         │
│ Pengguna      │                                         │
│               │                                         │
└───────────────┴─────────────────────────────────────────┘
```

### Sidebar

Recommended width:

```text
240px – 264px
```

The sidebar should contain:
- SIPENRI logo/wordmark
- Navigation
- Region context
- User section

## Mobile

Use:
- Top navigation
- Drawer navigation
- Bottom actions only when useful
- Horizontally scrollable data tables where unavoidable
- Stacked cards for dense desktop layouts

---

# 11. Application Shell

## Topbar

The topbar should show:

- Page context
- Region
- Notifications
- User profile

Example:

```text
Dashboard                         🔔   IKSASS BALI   [AG]
```

For Super Admin:

```text
Dashboard                         🔔   ALL REGIONS   [AG]
```

## Region Indicator

For `iksass_admin`, show the active region prominently:

```text
IKSASS BALI
● Wilayah aktif
```

The region should be visually clear and, where appropriate, read-only.

This reinforces the multi-region data isolation model.

---

# 12. Navigation

Recommended primary navigation:

1. Dashboard
2. Kiriman
3. Santri
4. Tracking
5. Laporan
6. Cetak Label

Super Admin additional navigation:

7. Pengguna
8. Wilayah IKSASS
9. Pengaturan

Keep navigation concise.

---

# 13. Dashboard

The dashboard is the primary operational overview.

## Hero / Greeting

Keep it compact:

```text
Selamat datang, Admin IKSASS Bali
Kelola dan pantau pengiriman santri dengan mudah.
```

Do not use a large marketing-style hero.

## KPI Cards

Recommended:

```text
┌────────────────┐ ┌────────────────┐ ┌────────────────┐
│ Total Kiriman  │ │ Dalam Perjalanan│ │ Diserahkan    │
│     1,248      │ │       84       │ │      71       │
│ +12% bulan ini │ │                 │ │                 │
└────────────────┘ └────────────────┘ └────────────────┘
```

Possible fourth KPI:

```text
Total Paket
```

## Recent Shipments

Use a compact table.

Columns:

- Tracking Code
- Santri
- NIS
- Region
- Package Count
- Status
- Updated
- Action

For `iksass_admin`, region is already constrained and does not need excessive visual repetition.

---

# 14. Bento Grid

Bento Grid may be used selectively on the dashboard.

Recommended dashboard structure:

```text
┌──────────────────────────────┬───────────────────────┐
│ Total Kiriman                │ Dalam Perjalanan      │
│                              │                       │
├──────────────────────┬───────┴───────────────────────┤
│ Diserahkan           │ Aktivitas Terbaru             │
│                      │                               │
└──────────────────────┴───────────────────────────────┘
```

Do not use Bento Grid for every page.

Tables and forms should remain conventional.

---

# 15. Shipment List

The shipment list is a core operational page.

## Header

```text
Kiriman
Kelola dan pantau seluruh data kiriman.

[ + Tambah Kiriman ]
```

## Toolbar

Include:

- Search by tracking code / NIS / student name
- Status filter
- Date filter
- Region filter (Super Admin)
- Export if implemented
- Batch selection

## Table

Recommended columns:

```text
[ ] Tracking | Santri | NIS | Paket | Pengirim | Status | Updated | Action
```

Use sticky table headers where useful.

---

# 16. Shipment Detail

The shipment detail page should make tracking the central visual element.

## Header

```text
SIP-2026-00124
Ahmad Fauzan
NIS 2024123456

[ Status: Diserahkan ]
```

## Timeline

```text
✓  Paket Dibuat
│   10 Agustus 2026 · 08:21
│
●  Dalam Perjalanan
│   10 Agustus 2026 · 14:32
│
●  Tiba & Diserahkan
    11 Agustus 2026 · 09:12

    📷 Bukti Foto
```

The timeline is a signature visual component of SIPENRI.

---

# 17. Shipment Status UX

The lifecycle is:

```text
Dibuat
   ↓
Dalam Perjalanan
   ↓
Tiba & Diserahkan
```

Use clear status transitions.

The final state should clearly communicate:

> Paket telah sampai di pondok dan diserahkan kepada santri.

Do not introduce a separate logistics-pesantren receiver role in the interface because the PRD explicitly states that such a role does not exist.

---

# 18. Student Search & Selection

Student selection is a high-frequency workflow.

Search should support:
- NIS
- Nama santri

Use an autocomplete / command-style search:

```text
Cari santri berdasarkan NIS atau nama
┌─────────────────────────────────────────────┐
│ 🔍  2024123456                              │
├─────────────────────────────────────────────┤
│ Ahmad Fauzan                                │
│ NIS 2024123456 · Al-Khuzaimah              │
│ Pusat Putra                                 │
└─────────────────────────────────────────────┘
```

Once selected, show a confirmation summary:

```text
Santri
Ahmad Fauzan

NIS
2024123456

Asrama
Al-Khuzaimah

Lokasi
Pusat Putra
```

This reduces wrong-recipient errors.

---

# 19. Shipment Form

Organize the form into logical sections.

## Section 1 — Santri

- Search student
- NIS
- Student name
- Dormitory
- Location

## Section 2 — Pengirim

- Sender name
- Sender phone

## Section 3 — Kiriman

- Amount of money
- Package count
- Region

For regional admin, the region must be automatically derived from the logged-in account and should not be freely editable.

---

# 20. Form UX

Rules:

- Labels must always be visible
- Required fields must be obvious
- Use inline validation
- Preserve entered data when validation fails
- Use appropriate input types
- Format currency as Rupiah
- Provide clear success/error feedback

Primary CTA:

```text
Simpan Kiriman
```

Secondary:

```text
Batal
```

After successful creation, offer:

```text
✓ Kiriman berhasil disimpan

[ Cetak Label ] [ Lihat Detail ]
```

---

# 21. Label Printing UI

Printing is an operational feature, not a decorative screen.

Provide:

```text
Cetak Label

[ Preview ]
[ Pilih ukuran printer ]
[ Pilih data ]
[ Cetak ]
```

Support:
- Single printing
- Batch printing

The label must prioritize:
1. Student name
2. NIS
3. Dormitory
4. Package number
5. Sender
6. IKSASS region
7. QR Code / Barcode

Use high contrast.

Avoid gradients and low-contrast colors in the actual thermal label.

---

# 22. Tracking & Evidence

The final shipment stage may include a photo attachment.

Use a clear evidence panel:

```text
Bukti Penyerahan

┌──────────────────────────────┐
│                              │
│          FOTO                │
│                              │
└──────────────────────────────┘

11 Agustus 2026 · 09:12
Diunggah oleh: Admin IKSASS Bali
```

Allow previewing the image without navigating away.

---

# 23. Tables

Tables are a primary component in SIPENRI.

Rules:
- Keep rows compact but readable
- Use consistent column alignment
- Numeric values right-aligned where appropriate
- Status uses badges
- Actions remain predictable
- Use pagination for large datasets
- Provide empty states
- Support responsive overflow

Example:

```text
┌─────────────────────────────────────────────────────────────┐
│ Tracking       Santri          Paket     Status      Action │
├─────────────────────────────────────────────────────────────┤
│ SIP-00124      Ahmad Fauzan    2         Delivered     ⋮   │
│ SIP-00125      Fulan           1         Transit       ⋮   │
│ SIP-00126      Yusuf           3         Created       ⋮   │
└─────────────────────────────────────────────────────────────┘
```

---

# 24. Cards

Use cards for:
- KPI
- Summary
- Shipment overview
- Evidence
- Important grouped information

Do not put every individual field inside a separate card.

Recommended:

```text
background: #FFFFFF
border: 1px solid #E1E7E3
radius: 12px
shadow: subtle
padding: 20px–24px
```

---

# 25. Buttons

## Primary

Green filled:

```text
[ + Tambah Kiriman ]
```

Use for the main action.

## Secondary

White / neutral with border:

```text
[ Filter ]
```

## Accent

Yellow may be used sparingly:

```text
[ Cetak Label ]
```

Do not use yellow for every CTA.

## Destructive

Red:

```text
[ Hapus ]
```

Always require confirmation for destructive actions.

---

# 26. Badges

Use compact pill badges.

Examples:

```text
[ DIBUAT ]
[ DALAM PERJALANAN ]
[ DISERAHKAN ]
```

Recommended style:

```text
font-size: 12px
font-weight: 600
padding: 4px 8px
border-radius: 9999px
```

---

# 27. Icons

Use a single consistent icon family.

Recommended:

**Lucide Icons**

Rules:
- Do not mix icon styles
- Default size: 18–20px
- Navigation icons: 18–20px
- Button icons: 16–18px
- Large empty-state icons: 32–48px

Icons should support labels, not replace important text.

---

# 28. Islamic Visual Identity

Use subtle geometric Islamic inspiration.

Good uses:
- Login background pattern
- Empty states
- Decorative line
- Small section accent
- Brand mark background
- Landing/login illustration

Bad uses:
- Full-page mosque background
- Repeating ornaments behind tables
- Heavy gold decoration
- Decorative Arabic text everywhere

The interface should communicate:

**Pesantren modern**, not a traditional poster.

---

# 29. Login Page

The login page can be more expressive than the internal dashboard.

Recommended composition:

```text
┌────────────────────────────────────────────────────┐
│                                                    │
│       subtle green geometric background           │
│                                                    │
│              ┌───────────────────┐                 │
│              │      LOGO         │                 │
│              │                   │                 │
│              │     SIPENRI       │                 │
│              │                   │                 │
│              │ Email             │                 │
│              │ [_______________] │                 │
│              │                   │                 │
│              │ Password          │                 │
│              │ [_______________] │                 │
│              │                   │                 │
│              │ [     MASUK     ] │                 │
│              └───────────────────┘                 │
│                                                    │
└────────────────────────────────────────────────────┘
```

Green dominates.

Yellow appears as a small accent.

---

# 30. Empty States

Do not show blank tables.

Example:

```text
             📦

Belum ada kiriman

Belum ada data kiriman untuk ditampilkan.

[ + Tambah Kiriman ]
```

Use subtle green/yellow accents.

---

# 31. Loading States

Prefer skeleton loading for tables and dashboard cards.

Example:

```text
████████████
████████
████████████████
```

Avoid full-screen spinners unless the operation genuinely blocks the whole interface.

---

# 32. Notifications

Use toast notifications for completed actions.

Success:

```text
✓ Kiriman berhasil disimpan
```

Error:

```text
× Kiriman gagal disimpan
Periksa kembali data yang dimasukkan.
```

Keep messages short and actionable.

---

# 33. Accessibility

Minimum requirements:

- WCAG-conscious contrast
- Visible keyboard focus
- Labels for form controls
- Keyboard-accessible interactive elements
- Do not communicate status through color alone
- Alt text for meaningful images
- Adequate touch target size
- Clear error messages
- Respect reduced-motion preferences

Recommended minimum touch target:

```text
44 × 44px
```

---

# 34. Motion

Motion should be subtle and functional.

Recommended:
- 150–200ms button transitions
- 200–300ms dropdown/modal transitions
- Soft page transitions
- Timeline reveal
- Toast entrance/exit

Avoid:
- Excessive parallax
- Large bouncing animations
- Decorative animation on every card
- Long transitions that slow operations

Motion must never interfere with data entry.

---

# 35. Responsive Rules

## Desktop

Prioritize:
- Dense data tables
- Sidebar
- Multi-column forms
- Dashboard overview

## Tablet

Prioritize:
- Collapsible sidebar
- Two-column layouts where possible
- Horizontal table scrolling

## Mobile

Prioritize:
- Search
- Shipment status
- Shipment details
- Add shipment
- Evidence upload
- Tracking

Forms should become one column.

KPI cards can become a 2-column grid.

Tables may transform into stacked shipment cards where appropriate.

---

# 36. Mobile Shipment Card

Example:

```text
┌──────────────────────────────┐
│ SIP-00124        [Delivered] │
│                              │
│ Ahmad Fauzan                 │
│ NIS 2024123456               │
│ Al-Khuzaimah                 │
│                              │
│ 2 Paket                      │
│                              │
│ Updated 10 min ago           │
│                              │
│ [ Lihat Detail ]             │
└──────────────────────────────┘
```

---

# 37. Role-Based UI

## Super Admin

Can see:
- All regions
- National dashboard
- Master students
- IKSASS users
- Regional summaries
- Audit information

The UI may expose a region filter:

```text
Region
[ Semua Wilayah ▼ ]
```

## Admin IKSASS Daerah

Can see:
- Their region only
- Their shipments
- Relevant student data
- Printing
- Tracking
- Regional history

Show:

```text
IKSASS BALI
Wilayah aktif
```

Do not expose other regions through normal UI controls.

---

# 38. Data Privacy UX

Regional isolation is not only a backend concern. The UI should reinforce it.

For regional admins:
- Region is visible
- Region is read-only
- No cross-region filter
- No cross-region links
- No global shipment count
- No other-region data in autocomplete

The interface should never imply that regional admins can browse another region.

---

# 39. Dashboard Information Hierarchy

Recommended priority:

```text
1. Current region / role
2. Shipment KPIs
3. Current shipment status
4. Recent activity
5. Quick actions
6. Secondary reports
```

Do not overwhelm the dashboard with charts.

Charts should only exist when they answer a useful operational question.

---

# 40. Data Visualization

Use simple visualizations.

Good:
- Shipment count by status
- Shipment trend over time
- Regional comparison for Super Admin
- Delivered percentage

Avoid:
- 3D charts
- Decorative charts
- Excessive pie charts
- Charts without actionable meaning

---

# 41. Design Tokens Summary

```css
:root {
  --color-primary-950: #12351F;
  --color-primary-900: #164B2A;
  --color-primary-800: #176B38;
  --color-primary-700: #198754;
  --color-primary-600: #229A5B;
  --color-primary-500: #2EAA68;
  --color-primary-100: #DDF4E5;
  --color-primary-50: #F0FAF3;

  --color-accent-700: #A66B00;
  --color-accent-600: #C58A00;
  --color-accent-500: #E0A800;
  --color-accent-400: #F2C94C;
  --color-accent-100: #FFF3C4;
  --color-accent-50: #FFFAE8;

  --color-background: #F7F9F7;
  --color-surface: #FFFFFF;
  --color-surface-muted: #F1F4F2;
  --color-border: #E1E7E3;

  --color-text: #17211B;
  --color-text-secondary: #526057;
  --color-text-muted: #7A857E;

  --color-success: #198754;
  --color-info: #2563EB;
  --color-warning: #D97706;
  --color-danger: #DC2626;

  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-full: 9999px;
}
```

---

# 42. Tailwind CSS Direction

The project uses Tailwind CSS.

Prefer semantic design tokens and reusable component classes rather than scattering arbitrary color values throughout Vue components.

Example conceptual usage:

```html
<button class="bg-primary-700 hover:bg-primary-800 text-white">
  Tambah Kiriman
</button>
```

For surfaces:

```html
<div class="rounded-xl border border-border bg-white shadow-sm">
  ...
</div>
```

For status:

```html
<span class="rounded-full bg-primary-100 px-2 py-1 text-xs font-semibold text-primary-800">
  Diserahkan
</span>
```

The exact Tailwind configuration may adapt these tokens to the project's existing setup.

---

# 43. Component Priorities

Build the design system around these reusable components:

### Core
- Button
- Input
- Select
- Textarea
- Checkbox
- Radio
- Badge
- Avatar
- Tooltip

### Layout
- AppShell
- Sidebar
- Topbar
- PageHeader
- Section
- Card

### Data
- DataTable
- Pagination
- Search
- FilterBar
- EmptyState
- Skeleton

### SIPENRI-specific
- RegionIndicator
- ShipmentStatusBadge
- ShipmentTimeline
- ShipmentCard
- StudentSearch
- ShipmentForm
- EvidencePhoto
- LabelPreview
- TrackingCode
- BatchPrintSelector

---

# 44. Page-by-Page Visual Direction

| Page | Primary Visual Approach |
|---|---|
| Login | Modern Islamic + subtle glass/accent |
| Dashboard | Clean dashboard + selective Bento |
| Kiriman | Dense professional data table |
| Tambah Kiriman | Clean multi-section form |
| Detail Kiriman | Timeline-first |
| Tracking | Timeline + evidence |
| Santri | Search + data table |
| Laporan | Data visualization + table |
| Cetak Label | Utility / high-contrast |
| Pengguna | Administrative table |
| Wilayah | Administrative cards/table |
| Pengaturan | Simple form |

---

# 45. Do / Don't

## DO

- Use green as the dominant brand color
- Use yellow as a restrained accent
- Use whitespace
- Use strong typography
- Use clear shipment status
- Make region context visible
- Use timeline as a signature component
- Keep tables efficient
- Keep forms simple
- Use subtle Islamic geometry
- Optimize for mobile
- Use consistent icons
- Use accessible contrast

## DON'T

- Don't make the whole app glassmorphic
- Don't overuse yellow
- Don't use excessive gradients
- Don't use decorative mosque imagery throughout the dashboard
- Don't make every component heavily rounded
- Don't hide important information behind hover
- Don't rely on color alone for status
- Don't create unnecessary animations
- Don't make operational screens look like marketing pages
- Don't expose cross-region information to regional admins

---

# 46. Final Design Direction

The final SIPENRI interface should feel like:

> **A modern, trustworthy digital logistics system belonging to a major pesantren institution.**

The visual formula:

```text
70% Clean Institutional
15% Logistics / Data Dashboard
10% Islamic Identity
5% Modern Accent / Glass / Motion
```

The dominant identity:

```text
GREEN
  +
YELLOW ACCENT
  +
WHITE / SOFT NEUTRALS
  +
CLEAN TYPOGRAPHY
  +
SWISS GRID
  +
TIMELINE
  +
SUBTLE ISLAMIC GEOMETRY
```

The design goal is not to make SIPENRI visually flashy.

The goal is to make users immediately feel:

**"Saya tahu saya sedang berada di sistem SIPENRI, saya tahu wilayah saya, saya tahu status kiriman, dan saya tahu tindakan berikutnya."**
