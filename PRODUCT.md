# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Laravel 13 + Inertia + Vue 3 + Tailwind CSS v4 + shadcn-vue/Reka UI component system. Styling lives in Tailwind tokens (`resources/css/app.css`), components in `resources/js/components/ui`, pages in `resources/js/pages`.

## Users

Primary users are **Admin IKSASS Daerah** (regional admins) who use the system daily, on desktop, to record and track shipments (kiriman) of money and goods (parcels) from guardians to students (santri) at boarding schools. **Super Admin / Pusat** uses the system to supervise all regions, manage IKSASS regional records (wilayah), and manage users. Both sign in with email/password; regional admins see only their own region's data.

## Product Purpose

SIPENRI is the payment/shipment tracking system for IKSASS, a pesantren organization. Regional admins record a shipment (sender details, package count, amount of money, destination student, region), generate a tracking code, and update its status as it travels until it reaches the student. Success means every shipment is traceable from registration to handover, with clean records of money amounts across all regions.

## Positioning

A dedicated, per-region shipment-and-money ledger for a pesantren organization: guardians and admins have a formal, auditable trail of every kiriman (who sent it, how much, to which santri, in which region), instead of paper notes or chat messages. Regional isolation (each IKSASS daerah only manages its own records) plus central oversight by Pusat is the structural mechanism.

## Operating Context

- Daily data-entry workflow for admins: input a new kiriman, look up a santri by NIS, change shipment status, print tracking-code stickers, filter and search records.
- Super Admin additionally manages wilayah (IKSASS regions) and user accounts.
- Bulk workflows: Excel import of shipments (template download → upload), bulk status update, bulk delete, CSV/Excel export/print of records including sticker sheets.
- Paper artifacts are used operationally: printed sticker labels from a Print Stickers page.
- Language: Indonesian (all UI copy is Bahasa Indonesia).

## Capabilities and Constraints

- Auth via Laravel Fortify (email/password, verification, 2FA); roles `super_admin` and `iksass_admin`.
- Shipment model: tracking code, sender, destination, NIS-linked student, package count, money amount, region, status lifecycle with `ShipmentLog` history.
- Dashboard KPIs: total shipments, in transit, delivered, total money; region distribution; recent shipments.
- Student records keyed by NIS; region records (wilayah IKSASS) managed by Super Admin.
- Existing visual identity is institutional green (brand → emerald direction), shadcn-vue component conventions, Inter typeface, light mode with dark mode support. Direction confirmed with user: emerald-elegant identity, full visual refresh, airy and elegant density, light default with dark mode retained.
- Wayfinder-generated routes (`resources/js/routes`, `resources/js/actions`) are gitignored and regenerated at build with `--with-form`.

## Brand Commitments

- Product name: **SIPENRI** (currently a codename/working name; no external brand authority found).
- Existing logo and wordmark asset (green, institutional) is kept and refined — user confirmed using current assets.
- Indonesian UI copy throughout.

## Evidence on Hand

- No docs/ files, no README, no DESIGN.md, no official asset files beyond the codebase's inline SVG `AppLogoIcon`.
- No testimonials, marketing claims, or external brand references exist; none should be fabricated.

## Product Principles

1. **Regional trust through isolation** — regional admins manage only their own data; the system protects that boundary.
2. **Traceability is the product** — every kiriman must show a clear status path and logged history.
3. **Speed at data entry** — daily input of many shipments must stay fast: search by NIS, template import, quick statuses.
4. **Craft that befits the work** — a calm, elegant, professional feel; clarity for daily operators first.
5. **Indonesian-first** — voice, labels, and copy are Bahasa Indonesia; no English filler.

## Accessibility & Inclusion

No product-specific accessibility standard was established. Keep WCAG AA contrast, keyboard operability, and responsive layout as baseline expectations. Dark mode must be fully usable, not an afterthought.