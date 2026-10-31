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
│   │                  #   config, session, auth, log
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
See `docs/guides/src/11-testing.typ` for the setup.

## Notes

- No framework, no ORM, no external routing library.
- Logic shared across routes lives in `app/lib/`; single-use queries are inline in the route files.
