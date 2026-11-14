# VALENIA

Clinic information system for Polinema. Native PHP application using a
*front controller* and *file-based routing* (route-centric, no layered MVC).

## Structure

```text
apps/
├── public/            # document root (the only directory exposed by the web server)
│   ├── index.php      # front controller
│   └── assets/        # built CSS (gitignored)
├── app/
│   ├── bootstrap.php  # loads lib + guards, returns the Router
│   ├── routes/        # one file per route (URL derived from the filename)
│   ├── middleware/    # named guards: csrf, auth, password, role
│   ├── lib/           # router, request, response, middleware, db, view,
│   │                  #   config, session, auth, log, reports, reports_pdf
│   ├── components/    # reusable view partials
│   └── views/         # layouts and page templates
├── database/          # migrations/, seeds/, migrate.php, seed.php
├── resources/css/     # Tailwind sources
└── composer.json
```

Route files return an array keyed by HTTP method, with an optional `_middleware`.
Guards `csrf`, `auth`, and `password` run globally (see `app/bootstrap.php`); a
route only adds the guards it needs on top, e.g. `role:admin`:

```php
<?php
// apps/app/routes/admin.php
return [
    '_middleware' => ['role:admin'],
    'GET' => static fn (Request $request): string => view('admin', [
        'user' => (string) $request->attribute('user'),
    ]),
];
```

Everything is protected by default; the anonymous-reachable paths are listed in
`is_public_path()` (`app/lib/auth.php`).

## Running (development)

Choose one of the two setups below depending on whether you use Docker.

### Option 1: With Docker

#### 1. Build assets and start containers

```bash
cd apps/

# 1) Build Tailwind CSS assets (generates public/assets/app.css)
npm install
npm run css:build

# 2) Start containers (Caddy, PHP-FPM, PostgreSQL)
docker compose up -d --build

# 3) Install dependencies
docker compose exec app composer install
```

- Application: <http://localhost:8080>

*Note: Environment variables default automatically in `docker-compose.yaml`; creating `.env` is optional unless overriding credentials.*

#### 1a. Image targets

`docker/Dockerfile` defines four targets:

| Target | Used for |
|--------|----------|
| `dev` | local Compose (bind mounts `apps/` over `/srv`) |
| `builder` | Composer dependencies and optimised autoloader |
| `assets` | Tailwind build, produces `public/assets/app.css` |
| `runtime` | deployable image, non-root, no Composer |

`docker-compose.yaml` builds the `dev` target, so `docker compose exec app composer ...` keeps working. The `runtime` target deliberately has no Composer: build it directly when you need the deployable artefact.

```bash
docker build -f docker/Dockerfile --target runtime -t valenia:runtime .
```

The runtime image runs as an unprivileged user and ships only `libpq`, not the PostgreSQL build headers, which keeps it far smaller than the development image.


#### 2. Migrations (Docker)

```bash
docker compose exec app php database/migrate.php up        # apply pending migrations
docker compose exec app php database/migrate.php status    # show applied / pending
docker compose exec app php database/migrate.php rollback  # revert the last migration (*.down.sql)
docker compose exec app php database/migrate.php fresh     # drop + re-apply everything (dev only)
docker compose exec app php database/seed.php              # seed services + admin (dev only)
```

---

### Option 2: Without Docker (local PHP CLI)

Requires local PHP **8.5.12+** with `pdo_pgsql` extension enabled and a reachable PostgreSQL instance.

> **Security floor:** PHP **8.5.12** or newer is mandatory. It is the first
> release that fixes PHP2Shell (CVE-2026-109618 and CVE-2026-109619). Do not
> lower this floor or use a floating `php:8.5` image.

#### 1. Install dependencies and build assets

```bash
cd apps/

# 1) PHP dependencies
composer install

# 2) Tailwind CSS assets
npm install
npm run css:build
```

#### 2. Configure environment variables

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Adjust variables for your local PostgreSQL:

- `DB_DSN`: `pgsql:host=localhost;port=5432;dbname=valenia`
- `DB_USER`: `valenia`
- `DB_PASSWORD`: `valenia`
- `APP_ENV`: `development`
- `SEED_ADMIN_PASSWORD`: password for the initial `admin` account created by
  `php database/seed.php` (dev only; the account must change it on first login)

#### 3. Migrations (local)

```bash
php database/migrate.php up        # apply pending migrations
php database/migrate.php status    # show applied / pending
php database/migrate.php rollback  # revert the last migration (*.down.sql)
php database/migrate.php fresh     # drop + re-apply everything (dev only)
php database/seed.php              # seed services + admin (dev only)
```

#### 4. Start local web server

```bash
php -S localhost:8000 -t public public/index.php
```

Open <http://localhost:8000>. Routes: `/` (public), `/login` (staff sign-in),
`/admin` and below (requires login as admin; the seeded admin from
`php database/seed.php` forces a password change on first login).

## Quality gates

Run from `apps/`:

```bash
composer check          # code style + static analysis + tests
composer cs             # php-cs-fixer --dry-run --diff
composer stan           # phpstan level 6
composer test:unit      # no database required
composer test:integration
```

Integration tests need a migrated `valenia_test` database and `RUN_DB_TESTS=1`.

## Patient flow

Public patient routes:

| Route | Method | Purpose |
|---|---|---|
| `/` | GET | Landing page |
| `/form` | GET/POST | Choose a poli and show the registration form |
| `/ambil-tiket` | GET/POST | Validate identity and confirm registration |
| `/muncul-qr` | GET/POST | Issue the queue number and show the QR ticket |
| `/form-riwayat` | GET | Patient history lookup form |
| `/portal-pasien` | GET/POST | Look up and display active visit/history |
| `/portal-keluar` | POST | Clear the portal session |

Anonymous visits use the inactive `guest` system account in `users`; the visit
records `registration_source = 'guest'` and still has a non-null `created_by`.
Queue dates use `app_today()` in the Asia/Jakarta timezone. Ticket numbers are
stored as integers and displayed as service-prefixed codes (`PU-01`, `PG-01`,
`PK-01`).

In the portal, selecting a history row updates the right-hand detail card.
Today's own `menunggu` visit can be cancelled through `POST /portal-batal`;
cancellation is a status change, not a delete, and the queue number is not
reused.

## Visit state model

`visits.status` is one of:

```text
menunggu -> dilayani -> selesai
    |           |
    |           +------> batal
    +------------------> batal

menunggu or dilayani from a previous day -> expired
```

Terminal states are `selesai`, `batal`, and `expired`.

Lifecycle columns:

| Column | Set when |
|---|---|
| `served_at` | the visit is called or served |
| `completed_at` | the visit is completed and an examination is written |
| `cancelled_at`, `cancelled_by` | the visit is cancelled by staff or the portal |
| `expired_at` | the daily expiry job closes a previous-day visit |

Rules:

- Only one active visit is allowed per patient, poli, and queue date. A second active registration is rejected with a clear message.
- `expired` visits are closed by `php database/expire_visits.php` and excluded from monthly report totals.
- `visits.handled_by_nurse` was removed because the status and examination record already carry that information.
- `record_examination()` rejects every status except `dilayani`, including direct calls outside the normal completion helper.

## Account and session state

- New accounts and admin password resets set `must_change_password`; the account can only access `/ganti-sandi` and `/logout` until the password changes.
- Five failed logins lock the account for 15 minutes. The counter is incremented atomically.
- Password reset and deactivation delete every session for the account.
- The last active admin cannot be disabled.
- Sessions expire after 30 minutes idle or 12 hours absolute. Idle and absolute expiry are housekeeping; explicit revoke, logout, reset, and deactivation are audited.
- Doctor attendance changes are recorded in `audit_logs` through the `doctor_attendance` UUID id, and attendance is rejected for inactive doctors.

## Monthly recap

Admin only. The page reads `?bulan=Y-m` and falls back to the current
Asia/Jakarta month.

| Route | Method | Purpose |
|---|---|---|
| `/admin/rekap-bulanan` | GET | Weekly recap of registered visits per poli |
| `/admin/rekap-bulanan/unduh` | POST | Stream the recap PDF and log the export |

Counts are registered visits per `queue_date`, excluding `batal` and
`expired`. Weeks are 7 day chunks counted from the period start, so a month
reads as Minggu 1 (1-7), Minggu 2 (8-14), and so on.

The PDF is rendered with dompdf and streamed from memory. It is never written
to disk, `public/`, or object storage. `report_exports` keeps the metadata:
period, service, actor, filename, byte size, a JSONB aggregate snapshot, and a
SHA-256 checksum of the snapshot. The snapshot lets an old report be
re-rendered exactly even when the underlying visits change later. It contains
aggregate counts only, no patient level data.

dompdf caches fonts in `sys_get_temp_dir()/valenia-dompdf`, so the non-root
runtime image needs no writable application path.

## Scheduled maintenance

Mark previous-day waiting or served visits as expired once per day:

```bash
php database/expire_visits.php
```

Example cron (Asia/Jakarta):

```cron
5 0 * * * cd /srv && php database/expire_visits.php >> /var/log/valenia-expire.log 2>&1
```

Expired visits are terminal and are excluded from monthly report totals.

## Session expiry

Session rows removed by idle or absolute expiry in the session handler are
housekeeping and are not written to `audit_logs`. Explicit admin revoke,
logout, password reset, and deactivation are audited.

## Notes

- No framework, no ORM, no external routing library.
- Logic shared across routes lives in `app/lib/`; single-use queries are inline in the route files.
