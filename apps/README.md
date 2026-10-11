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
│   ├── middleware/    # named guards: auth, role, csrf
│   ├── lib/           # router, request, response, middleware, db, view, config
│   ├── components/    # reusable view partials
│   └── views/         # layouts and page templates
├── database/          # schema.sql, migrations/, seeds/, migrate.php
├── resources/css/     # Tailwind sources
└── composer.json
```

Route files return an array keyed by HTTP method, with an optional `_middleware`:

```php
<?php
// apps/app/routes/admin.php
return [
    '_middleware' => ['auth', 'role:admin'],
    'GET' => static fn (Request $request): string => view('admin', [
        'user' => (string) $request->attribute('user'),
    ]),
];
```

## Running (development)

```bash
cd apps/

# 1) PHP dependencies (optional; only for .env and tooling)
composer install

# 2) Tailwind CSS assets
npm install
npm run css:build

# 3) run with the PHP built-in server (requires a reachable PostgreSQL per DB_DSN)
php -S localhost:8000 -t public public/index.php
```

Open <http://localhost:8000>. Demonstrator routes: `/` and `/admin`
(requires the `X-Demo-User: admin` header).

### Environment variables

Copy `.env.example` to `.env`:

- `DB_DSN`: PostgreSQL DSN, e.g. `pgsql:host=db;port=5432;dbname=valenia`.
  Required — the former SQLite fallback was removed.
- `DB_USER`, `DB_PASSWORD`: PostgreSQL credentials.
- `APP_ENV`: `development` or `production` (guards `migrate.php fresh`).

## Migrations

PostgreSQL migrations live in `database/migrations/` and are applied in lexical
order, tracked in the `schema_migrations` table with a checksum per file.

```bash
php database/migrate.php up        # apply pending migrations (default)
php database/migrate.php status    # show applied / pending
php database/migrate.php rollback  # revert the last migration (*.down.sql)
php database/migrate.php fresh     # drop + re-apply everything (dev only)
```

## Notes

- No framework, no ORM, no external routing library.
- Logic shared across routes lives in `app/lib/`; single-use queries are inline in the route files.
