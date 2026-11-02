# Security

## Minimum runtime

PHP **8.5.12** or newer is required. This is the first release containing the
fixes for PHP2Shell:

- CVE-2026-109618
- CVE-2026-109619

The Docker image is pinned to `php:8.5.12-fpm-alpine3.24`. CI asserts the
runtime version so a downgrade fails the build.

## Reporting a vulnerability

Do not open a public issue for a security problem. Contact the maintainers
listed in `composer.json` or the repository owner directly.
