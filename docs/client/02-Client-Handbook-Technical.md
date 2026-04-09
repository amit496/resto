# Restaurant Ordering & Admin System — Client Handbook (Technical)

**Repository:** `laravel/laravel` (multi-branch restaurant ordering + admin)  
**Version:** 1.1  
**For:** Developers, IT staff, agencies, and technical decision-makers  
**Seller / Provider:** [YOUR NAME OR COMPANY]  
**Date:** [DATE]

---

## 1. Purpose

This document aligns **commercial/support rules** with **this codebase**: stack versions, route layout, payment webhooks, PWA, and **production operations** (scheduler, optional queues). Use it alongside the non-technical handbook for the same project.

---

## 2. Technology stack (verified from `composer.json` / `package.json`)

| Layer | Technology | Notes |
|-------|------------|--------|
| **Framework** | **Laravel 13.x** (`laravel/framework: ^13.0`) | HTTP, middleware, Eloquent, etc. |
| **PHP** | **^8.2** | Required on the app server. |
| **Database** | **MySQL / MariaDB** (typical) | Use the driver configured in `.env`; SQLite possible for local dev only. |
| **Frontend build** | **Vite ^8.x** + **Tailwind CSS ^4.x** | Production: `npm ci && npm run build` → assets under `public/build`. |
| **JS** | **Alpine.js**, **Axios** | Used in parts of the UI. |
| **PDF** | **barryvdh/laravel-dompdf** ^3.1 | Bills / PDF exports where implemented. |
| **Admin RBAC** | **spatie/laravel-permission** ^7 | Roles/permissions on admin routes. |
| **Dev tooling** | **Laravel Breeze** ^2.3 (require-dev) | Scaffolding/dev; **storefront customer auth** uses a dedicated **`customer` guard** and routes under `/customer/*`, not the default Breeze web stack for end-users. |

**Authentication model**

- **Admin / staff:** `auth:web` — Laravel session guard for users in the `users` table; Spatie permissions (`can:*` middleware on many admin routes).  
- **Storefront customers:** `auth:customer` — separate customer guard; login/register under `routes/frontend/auth.php` prefix `customer`.  
- **Breeze (dev):** May exist for developer workflows; **do not assume** Breeze drives the production storefront without checking `config/auth.php` and customer controllers.

---

## 3. Product surface area (this repository)

### 3.1 Public / customer HTTP (`routes/frontend/`)

Loaded under `Route::name('frontend.')` in `routes/frontend/web.php`:

| Area | Route files / notes |
|------|---------------------|
| Pages | `pages.php` — `/`, `/about`, `/branches`, `/offers`, `/reviews`, `/contact`, `/reservations`, review POSTs (auth customer). |
| Menu | `menu.php` — `/menu`, category/subcategory slugs, `/menu/feed` (AJAX/infinite feed), product detail `/{slug}`. |
| Cart | `cart.php` — cart CRUD, coupon apply/clear, loyalty points toggle from cart. |
| Checkout & orders | `orders.php` — `/checkout`, order store, receipt link, `/orders` (auth), refund POST (auth), order show. |
| Payments (webhooks) | `payments.php` — **Stripe**, **Razorpay**, **PayPal** webhook/return/callback URLs (no UI; gateway integration). |
| Account | `account.php` — profile, addresses CRUD, `/loyalty` (customer). |
| Auth | `auth.php` — customer login/register/password/verify email. |
| PWA | `PwaManifestController` — `GET /manifest.webmanifest` → JSON manifest; `public/sw.js` service worker; layout registers SW. |

### 3.2 Admin HTTP (`routes/admin/`)

Prefix **`/admin`**, name prefix **`admin.`**, middleware **`auth:web`** + audit logging on the group:

- **Dashboard / overview** — `dashboard.php`  
- **Catalog** — `catalog.php` — restaurants, branches, categories, subcategories, products  
- **Sales** — `sales.php` — orders, customers, delivery boys, coupons, payments, refunds  
- **Operations** — `operations.php` — kitchen, inventory, reservations, delivery tracking, suppliers, purchases, expenses, notifications, admin email, admin loyalty  
- **Engagement** — `engagement.php` — reports (+ Excel/PDF export), review moderation  
- **Settings** — `settings.php` — app settings, staff  
- **Management** — `management.php` — admin users, audit logs (permission-gated)  
- **Auth** — `admin/auth.php` — admin login/logout (web guard)

### 3.3 Legacy / default Breeze routes

`routes/web.php` loads Breeze-style **`/profile`** routes for the default **`web`** user (often unused for restaurant staff if everything is under `/admin`). Confirm your deployment does not expose unintended URLs.

---

## 4. First-time delivery (technical checklist)

What the receiving team should get for a **production** cut:

1. **Source** — Git access or ZIP at agreed **commit/tag**.  
2. **PHP deps** — `composer install --no-dev --optimize-autoloader`  
3. **Front-end assets** — `npm ci && npm run build`  
4. **Environment** — copy `.env.example` → `.env`; set `APP_URL`, `APP_KEY`, DB, mail, and **payment** keys (Stripe/Razorpay/PayPal as used).  
5. **Database** — `php artisan migrate --force` (and seeds **only if** agreed).  
6. **Storage** — `php artisan storage:link`  
7. **Optimize** — `php artisan config:cache`, `route:cache`, `view:cache` when appropriate  

**Delivered build = frozen scope** at handover commit unless a maintenance agreement says otherwise.

---

## 5. Server requirements & operations

| Topic | This project |
|-------|----------------|
| **PHP** | 8.2+ with common extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl` |
| **Web root** | Must point to **`public/`** (Nginx/Apache). |
| **HTTPS** | Strongly recommended; required for many payment flows and PWA behaviour. |
| **Scheduler** | **`routes/console.php`** registers `offers:send` **daily at 10:00**. Production needs **cron**: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1` |
| **Queues** | `composer run dev` runs `queue:listen` in development. If you dispatch queued jobs in production, run a **queue worker** (Supervisor) — verify `config/queue.php` and actual job usage. |
| **Webhooks** | CSRF exceptions in `bootstrap/app.php` for **stripe**, **razorpay**, **paypal** webhook paths — keep URLs reachable from the public internet. |

---

## 6. Support scope (technical)

| In scope (example: **[___] days** after go-live) | Out of scope unless quoted |
|---------------------------------------------------|------------------------------|
| Env vars, `artisan` steps we documented, webhook URL verification | Building full CI/CD pipelines |
| Reproducing **bugs in delivered application code** | Pure infrastructure pentests / hardening |
| Clarifying Spatie roles/permissions setup we delivered | Rewriting app architecture for preferences |
| Gateway **configuration** on provider dashboard (with your keys) | Merchant KYC / banking approval |

**Third-party outages** (hosting, SMTP, DNS, payment provider) are not application bugs.

---

## 7. Changes, upgrades, and pricing model

| Category | Examples | Charging |
|----------|----------|----------|
| **Bugfix** | Regression in agreed feature | In support window if caused by our delivery; else triage + quote |
| **Minor** | Config, small Blade/JS tweak | Fixed or time-boxed |
| **Feature** | New integration, report, workflow | Scope + estimate + milestones |
| **Platform upgrade** | Major PHP/Laravel jump | Separate project + regression pass |

**Upstream changes:** New work in **our** mainline after your handover does **not** auto-deploy to your server. Optional **paid merge**, release package, or **git patch** by agreement.

---

## 8. Security & client operations

- Protect `.env`; restrict `storage/` and `bootstrap/cache/` permissions.  
- **Backups:** database + `storage/app` + secure record of env (not in git).  
- Rotate API keys if staff leave.  
- Admin accounts: strong passwords; use Spatie permissions **least privilege**.

---

## 9. Licensing

- **Laravel** and many Composer packages are **open source** under their respective licenses (e.g. MIT).  
- **Your commercial agreement** with [YOUR NAME OR COMPANY] controls **use, resale, and white-label** of this customised product. This handbook is **not** a software license by itself.

---

## 10. Contact

**Technical:** [EMAIL / SLACK]  
**Commercial:** [EMAIL]  
**Emergency (paid SLA only):** [DETAILS]

---

## 11. Acceptance

The technical receiver accepts that **environment misconfiguration**, **missing cron/queue**, or **gateway mis-setup** may look like “app bugs” but require ops fixes; work outside the agreed support window may be **billable**.

**Provider:** _________________________ **Date:** _________  

**Client technical lead:** _________________________ **Date:** _________  

---

*PDF: Markdown → Docs/Word → Print → Save as PDF.*
