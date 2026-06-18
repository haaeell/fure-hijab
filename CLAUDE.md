# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Laravel dev server
php artisan serve

# Run migrations
php artisan migrate

# Run migrations + seed (fresh)
php artisan migrate:fresh --seed

# Seed only
php artisan db:seed

# Clear all caches
php artisan optimize:clear

# Lint PHP with Pint
./vendor/bin/pint

# Run tests (Pest)
php artisan test
php artisan test --filter=ExampleTest   # single test

# Frontend (Vite — Tailwind via CDN in views, Vite only bundles app.js/bootstrap)
npm run dev
npm run build

# Create storage symlink (required once)
php artisan storage:link
```

## Environment

Copy `.env.example` to `.env`. Key variables to set:

- `DB_DATABASE=e_commerce`, `DB_USERNAME`, `DB_PASSWORD`
- `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`
- `BITESHIP_API_KEY`, `BITESHIP_ORIGIN_*` — can also be set via Admin → Integrasi API
- Mail settings — can also be set via Admin → Integrasi API

Most integration credentials are stored in the `settings` table and read via `Setting::getValue('key', $fallback)`. The fallback reads from `config/services.php` which reads from `.env`.

## Architecture

### Dual-layout, dual-role app

There are two Blade layouts:

| Layout | Path | Used for |
|--------|------|----------|
| `layouts.app` | `resources/views/layouts/app.blade.php` | Admin panel — sidebar nav, DataTables, SweetAlert2 |
| `layouts.customer` | `resources/views/layouts/customer.blade.php` | Storefront — navbar, search overlay, wishlist icon, footer |

Role is a single `role` column (`admin` / `customer`) on the `users` table. Two middleware aliases gate the routes: `admin` and `customer` (registered in `bootstrap/app.php`).

### Route structure (`routes/web.php`)

```
Public (no auth)
  /                          → LandingPageController@index
  /collections, /best-seller, /hijab, /syari, /new-arrived
                             → LandingPageController (catalog pages)
  /collections/{slug}        → product detail
  /search/suggestions|popular → SearchController (AJAX, public)

Auth required
  /wishlist (GET/POST)       → WishlistController

Auth + admin middleware
  /home, /categories, /brands, /products, /koleksi
  /orders, /coupons, /customers, /reviews
  /reports, /profile, /settings, /settings/store
  /landing-content

Auth + customer middleware
  /cart, /checkout, /addresses
  /order-history
```

### Product catalog / collections

Collection pages (`/best-seller`, `/hijab`, etc.) filter products via the **`collections` pivot table** (`collection_product`): products are tagged to `Collection` records by the admin.

`LandingPageController::catalog()` resolves the collection by `slug` matching the URL segment, then does `whereHas('collections', ...)`. If no matching `Collection` row exists it falls back to keyword/sort logic (e.g. best-seller → order by `sold_count`).

Admin manages collections at `/koleksi` (`CollectionController`). The old `collection_type` column on `categories` is a legacy field — the catalog no longer reads it.

### Product model: variants vs. no-variants

- `has_variant = false` → price and stock live on `products` table
- `has_variant = true` → price and stock live on `product_variants`; `products.stock` is the sum, kept in sync by `ProductController`
- Images: `product_images` table, one row flagged `is_primary = true` used as thumbnail
- `ProductController::show()` returns JSON including `collection_ids` array for the edit modal to pre-check the right collections

### Checkout + payment flow

1. Customer adds to cart → `CartController`
2. Checkout page: address selection, ongkir via `BiteshipService::rates()`, coupon apply
3. `CheckoutController::store()` creates `Order` + `OrderItem` + `OrderAddress` + `Shipment` (pending), then calls Midtrans Snap to get a `snap_token`
4. Frontend opens Midtrans Snap popup
5. Midtrans POSTs to `/midtrans/callback` (CSRF-exempt) → `MidtransController` verifies SHA-512 signature, transitions order status and decrements stock on `settlement`
6. Order statuses: `pending → confirmed → processing → shipped → delivered` (+ `cancelled`, `refunded`)

One active unpaid order is enforced: `CheckoutController` blocks new checkouts if an unexpired pending payment exists.

### Shipping (Biteship)

`BiteshipService` wraps the Biteship v1 REST API. Credentials come from `Setting::getValue()`. `ShipmentTrackingService` handles tracking updates. The webhook route is `/midtrans/callback` for Midtrans and Biteship posts directly to order update endpoints handled in `OrderController`.

### Settings

`Setting` model provides a simple key-value store (`settings` table). `Setting::getValue($key, $default)` / `Setting::setValue($key, $value)`. Two admin pages:
- `/settings/store` — store profile (name, logo, contact, social)
- `/settings` — integration keys (Biteship, Midtrans, SMTP)

Changes take effect immediately on the next request (no cache).

### Search overlay

`SearchController` powers the navbar search overlay:
- `suggestions` — `LIKE` query on product name + category name; calls `findClosestMatch()` (PHP `similar_text` + word-level scoring) when zero results to produce "Maksud Anda?"
- `popular` — top 6 products by `sold_count`
- Search history stored in `localStorage` key `fure_search`

### Frontend stack notes

- **Tailwind** is loaded via CDN script tag with an inline `tailwind.config` in both layouts — there is no `tailwind.config.js` file. Vite only bundles `bootstrap` JS.
- **jQuery**, **SweetAlert2**, **DataTables**, **Select2**, **Leaflet**, **Flatpickr** are all loaded from CDN in the layouts.
- Admin product modal uses multiple tabs (Info / Harga & Stok / Gambar / Varian) driven by a `switchTab()` JS function; CKEditor is lazy-initialised on the description field.
- Image management uses per-image rows with AJAX delete/set-primary/replace. New images are staged as individual `<input type="file" name="images[]">` elements and uploaded on form submit.

### Seeders

Run order: `MasterSeeder` (users, collections, coupons, settings, landing content) → `ProductSeeder` (categories, brands, products) → `OrderSeeder` (sample orders with payments/shipments/reviews).

Default credentials after seeding:
- Admin: `admin@mail.com` / `password`
- Customer: `customer@mail.com` / `password`
