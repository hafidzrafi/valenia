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

# 3) run with the PHP built-in server (SQLite is used automatically if DB_DSN is empty)
php -S localhost:8000 -t public public/index.php
```

Open <http://localhost:8000>. Demonstrator routes: `/`, `/sandbox`,
`/admin` (requires the `X-Demo-User: admin` header).

### Environment variables

Copy `.env.example` (if present) to `.env`:

- `DB_DSN`: PostgreSQL DSN, e.g. `pgsql:host=db;port=5432;dbname=valenia`.
  If empty, the app uses SQLite at `database/app.sqlite`.
- `DB_USER`, `DB_PASSWORD`: PostgreSQL credentials.

## Migrations

```bash
php database/migrate.php
```

## Notes

- No framework, no ORM, no external routing library.
- Logic shared across routes lives in `app/lib/`; single-use queries are inline in the route files.
