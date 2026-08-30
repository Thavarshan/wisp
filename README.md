# Wisp

Wisp is a small Laravel application for sharing encrypted, password-protected, one-time secrets through expiring links.

## How it works

- Secret content is encrypted at rest with Laravel's encrypted cast.
- Optional passwords are stored with Laravel's hashed cast.
- Each secret receives independent 256-bit access and revocation tokens. Only SHA-256 token hashes are stored.
- The initial access page contains metadata only. A successful reveal verifies the password, decrypts the content, deletes the row in the same transaction, and then returns the plaintext with no-store headers.
- The creator receives the share URL and revocation token once. They must save the revocation token before refreshing; Wisp intentionally does not persist it.
- Expiration options are defined by App\Enums\ExpirationOption and are passed to Vue by Laravel.

## Requirements

- PHP 8.2+
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

## Configuration

Production must use APP_ENV=production, APP_DEBUG=false, a strong APP_KEY, HTTPS, and a production cache/session store. Set SESSION_SECURE_COOKIE=true and configure TRUSTED_PROXIES with only the proxy addresses or CIDRs that terminate TLS for the application. HSTS is emitted only for secure requests.

Do not put secrets, passwords, access tokens, or revocation tokens in logs, analytics, browser storage, URLs other than the public share URL, or monitoring payloads.

## Testing and quality checks

~~~bash
php artisan test --compact
vendor/bin/pint --test
npm run format:check
npm run lint
npm run test:unit
npm run build
npm run test:e2e
~~~

Wisp does not use Laravel Nightwatch for end-to-end testing. Nightwatch is a monitoring product, not a browser test runner, and it is not installed in this project.

The browser suite runs only against a local Laravel server and an isolated
`storage/wisp-e2e.sqlite` database. It never targets the production URL.

## Deployment and migration warning

The secret-table modernization migration removes the legacy predictable uid and unused name columns. Existing links are deliberately invalidated because legacy rows have no access-token hash. Existing rows are not deleted by the migration; they remain inaccessible and are removed by scheduled model:prune once expired.

Before production deployment, take a database backup, test the migration against a copy, schedule a maintenance window if the database engine requires it, and verify the application key and proxy settings. Rolling back cannot recover legacy uid values or recreate lost token hashes, so restore from the backup if rollback is required. Do not run destructive migration commands against production without that procedure.

The scheduler prunes expired secrets hourly with overlap and multi-server protection:

~~~bash
php artisan schedule:work
~~~

Laravel Cloud must run the Laravel scheduler for this pruning task. Configure a
Cloud scheduler or cron entry to invoke `php artisan schedule:run` every minute;
no Cloud resources or application identifiers are managed by this repository.

## CI

GitHub Actions runs dependency installation from the lockfiles, PHP formatting checks, PHPUnit, frontend formatting/lint/type/unit/build checks, and a separate local-only Playwright job on every branch and pull request. CI never rewrites source files and has read-only repository permissions. A final deployment gate runs only after all checks pass on pushes to `main`; configure the `LARAVEL_CLOUD_DEPLOY_HOOK` GitHub Actions secret with the Laravel Cloud deploy-hook URL. Configure main branch protection in GitHub separately to require the CI checks and pull requests; repository settings are intentionally not changed by this codebase update.

## License

Wisp is released under the MIT License.
