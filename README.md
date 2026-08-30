# Wisp

Wisp is a small Laravel application for sharing encrypted, password-protected, one-time secrets through expiring links.

## How it works

- Secret content is encrypted at rest with Laravel's encrypted cast.
- Optional passwords are stored with Laravel's hashed cast.
- Each secret receives independent 256-bit access and revocation tokens. Only SHA-256 token hashes are stored.
- Share URLs contain the public token hash in the path and the bearer access token in the URL fragment. The fragment is consumed in memory and removed from browser history before reveal, so it is never sent to the server or written to HTTP access logs.
- The initial access page contains metadata only. A successful reveal verifies both the fragment token and optional password, decrypts the content, deletes the row in the same transaction, and then returns the plaintext with no-store headers.
- The creator receives the share URL and revocation token once. They must save the revocation token before refreshing; Wisp intentionally does not persist it.
- Expiration options are defined by App\Enums\ExpirationOption and are passed to Vue by Laravel.

## Requirements

- PHP 8.3+
- Laravel 13
- Composer 2
- Node.js 22+ and npm
- SQLite, MySQL, MariaDB, PostgreSQL, or another Laravel-supported database

## Local setup

~~~bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
npm run test:unit
npm run test:e2e
~~~

For development, run composer run dev or start the Laravel and Vite servers separately.

## Laravel Cloud

Wisp is ready for Laravel Cloud with PHP 8.3 or newer and Node.js 22. Configure
the environment to use APP_ENV=production, APP_DEBUG=false, the existing
APP_KEY, the production APP_URL, SESSION_SECURE_COOKIE=true, and the trusted
proxy addresses or CIDRs in TRUSTED_PROXIES.

Use these Cloud commands:

~~~text
Build: composer install --no-interaction --no-progress --prefer-dist && npm ci && npm run build
Migrate: php artisan migrate --force
Health check: /up
Scheduler: php artisan schedule:run
~~~

Enable the Laravel Cloud scheduler to run every minute. Laravel Cloud manages
the web process and scheduler from the application settings; this repository
does not change Cloud organization or application identifiers.

## Configuration

Production must use APP_ENV=production, APP_DEBUG=false, a strong APP_KEY, HTTPS, and a production cache/session store. Set APP_URL to the one canonical HTTPS hostname, SESSION_SECURE_COOKIE=true, and configure TRUSTED_PROXIES with only the proxy addresses or CIDRs that terminate TLS for the application. Laravel rejects requests for other hosts. HSTS is emitted only for secure requests.

Do not put secrets, passwords, access tokens, or revocation tokens in logs, analytics, browser storage, or monitoring payloads. Configure the CDN, load balancer, web server, and application observability stack to omit request bodies and redact secret identifiers if they are retained. The access token is deliberately carried only in the share URL fragment; do not rewrite fragments into request paths or query strings.

Passwords are limited to 72 UTF-8 bytes because Wisp uses bcrypt. NUL characters are rejected. Content is stored in a large text column because encryption expands multibyte content.

Production responses use a per-response CSP nonce for inline bootstrap and tooling scripts. Keep `script-src` restricted to `'self'` plus the generated nonce, keep style elements restricted to `'self'`, and retain only `style-src-attr 'unsafe-inline'` for Reka's runtime positioning.

## Testing and quality checks

~~~bash
composer validate --strict
composer audit
php artisan test --compact
vendor/bin/pint --test
npm run format:check
npm run lint
npm run test:unit
npm run build
npm run test:e2e
~~~

The Laravel 13 compatibility matrix runs PHPUnit on PHP 8.3, 8.4, and 8.5.
The Composer lockfile resolves the framework's Symfony dependencies against
PHP 8.3 so the same lockfile remains installable across all three runtimes.

Application-specific middleware, model metadata, and query scopes use Laravel
13's first-party PHP attributes where they improve locality and correctness;
global middleware and dependency injection remain convention-based.

Wisp does not use Laravel Nightwatch for end-to-end testing. Nightwatch is a monitoring product, not a browser test runner, and it is not installed in this project.

The browser suite runs only against a local Laravel server and an isolated
`storage/wisp-e2e.sqlite` database. It never targets the production URL.

## Deployment and migration warning

The secret-table modernization migration removes the legacy predictable uid and unused name columns. The secure secret storage migration deliberately deletes all existing secrets because their bearer-token URLs cannot be made safe retroactively. Existing share links therefore stop working at deployment.

Before production deployment, take a database backup, announce link invalidation, test the migrations against a copy, schedule a maintenance window, and verify the application key, canonical URL, trusted hosts, and proxy settings. Both secret migrations are intentionally irreversible; restore the backup if rollback is required. Do not run destructive migration commands against production without that procedure.

The scheduler prunes expired secrets hourly with overlap and multi-server protection:

~~~bash
php artisan schedule:work
~~~

Laravel Cloud must run the Laravel scheduler for this pruning task. Configure a
Cloud scheduler or cron entry to invoke `php artisan schedule:run` every minute;
no Cloud resources or application identifiers are managed by this repository.

See docs/laravel-13-upgrade.md for the upgrade decisions, compatibility audit,
deployment checklist, and rollback procedure.

## CI

GitHub Actions runs dependency installation from the lockfiles, PHP formatting checks, PHPUnit, frontend formatting/lint/type/unit/build checks, and a separate local-only Playwright job on every branch and pull request. CI never rewrites source files and has read-only repository permissions. A final deployment gate runs only after all checks pass on pushes to `main`; configure the `LARAVEL_CLOUD_DEPLOY_HOOK` GitHub Actions secret with the Laravel Cloud deploy-hook URL. Configure main branch protection in GitHub separately to require the CI checks and pull requests; repository settings are intentionally not changed by this codebase update.

## License

Wisp is released under the MIT License.
