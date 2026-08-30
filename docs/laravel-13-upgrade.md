# Laravel 13 Upgrade

## Summary

Wisp was upgraded from Laravel 12.68.0 to Laravel 13.29.0 on PHP 8.3+.
The existing encrypted, one-time secret lifecycle, rate limits, no-store
headers, and scheduled pruning were preserved. The public-link protocol was
intentionally changed to remove bearer tokens from HTTP request paths.

The upgrade followed the Laravel 13 upgrade guide and the Laravel Boost 2
upgrade-laravel-v13 checklist. The Boost workflow is available through Boost's
MCP prompt; no generated code was accepted without review.

## Official references

- [Laravel 13 release notes](https://laravel.com/docs/13.x/releases)
- [Laravel 13 upgrade guide](https://laravel.com/docs/13.x/upgrade)
- [Laravel controller attributes](https://laravel.com/docs/13.x/controllers)
- [Laravel Eloquent attributes](https://laravel.com/docs/13.x/eloquent)
- [Laravel Cloud documentation](https://cloud.laravel.com/docs)

## Dependencies

| Package | Before | After |
| --- | --- | --- |
| PHP constraint | ^8.2 | ^8.3 |
| laravel/framework | 12.68.0 | 13.29.0 |
| laravel/boost | 1.8.13 | 2.7.0 |
| laravel/tinker | 2.11.1 | 3.0.2 |
| inertiajs/inertia-laravel | 2.0.21 | 2.0.25 |
| tightenco/ziggy | 2.6.4 | 2.6.4 |
| phpunit/phpunit | 11.5.56 | 12.5.34 |
| laravel/pail | 1.2.7 | 1.2.7 |
| laravel/pint | 1.30.5 | 1.30.5 |
| laravel/sail | 1.67.0 | 1.67.0 |
| nunomaduro/collision | 8.9.5 | 8.9.5 |
| tightenco/duster | 3.4.6 | 3.4.6 |
| fakerphp/faker | 1.24.1 | 1.24.1 |
| mockery/mockery | 1.6.15 | 1.6.15 |

Symfony dependencies are locked to the PHP 8.3-compatible 7.4 line through
Composer's config.platform.php. This keeps the lockfile installable on PHP
8.3, 8.4, and 8.5 while Laravel 13 remains current.

composer outdated --direct reports only the available Inertia.js 3 major
upgrade. Inertia.js 2 remains the intentional compatible version for the
existing Vue response contract; no Laravel 13 requirement calls for that
separate major migration.

## Laravel 13 changes applied

- Moved endpoint throttles into repeatable
  Illuminate\Routing\Attributes\Controllers\Middleware attributes on
  SecretController; route names and URLs are unchanged.
- Replaced model fillable and hidden properties with Laravel 13 Fillable and
  Hidden attributes.
- Converted the access-token local scope to a protected Scope-attributed method.
- Added PHP Override only to methods that override framework declarations:
  service provider register, Inertia middleware version and share, Eloquent
  casts, and factory definition.
- Enabled PreventRequestForgery origin-only verification. Same-origin browser
  requests remain valid, while cross-site requests are rejected even if a
  token is supplied.
- Added cache.serializable_classes = false; Wisp stores no PHP objects in cache
  and therefore does not need an allow-list.
- Added JSON session serialization. Wisp's session payload consists of
  JSON-compatible validation and Inertia data.
- Kept explicit Wisp cache, Redis, and session prefixes to avoid accidental key,
  cookie changes, or collisions during the upgrade.
- Added PHP 8.3, 8.4, and 8.5 PHPUnit CI coverage, plus Composer validation and
  audit checks.

## Applicability decisions

| Feature or change | Decision |
| --- | --- |
| Controller middleware attributes | Adopted for endpoint throttles |
| Eloquent Fillable, Hidden, Scope | Adopted for the existing Secret model |
| Authorize | Not applicable; Wisp has no users or policies |
| Queue attributes and Queue::route | Not applicable; operations are synchronous |
| Cache::touch | Not applicable; Wisp has no application cache entries |
| JSON:API resources | Rejected; small security-sensitive contracts do not benefit |
| AI SDK | Rejected; secret content must never be sent to an AI provider |
| Semantic/vector search | Rejected; indexing plaintext conflicts with privacy |
| Events, notifications, and jobs | Not applicable; no async workflow exists |
| Database schema changes | None introduced by Laravel 13 |

The audit found no custom cache stores, queue implementations, HTTP response
classes, notification listeners, polymorphic pivots, pagination overrides,
JobAttempted consumers, QueueBusy consumers, or global array_first /
array_last helpers requiring migration.

## Security and compatibility

- APP_KEY was not changed.
- Secret content remains encrypted at rest; passwords remain hashed; only
  SHA-256 token hashes are stored. New share URLs put the public token hash in
  the path and the raw access token in the URL fragment.
- Reveal remains transactional and one-time, and successful responses remain
  no-store and noindex.
- PreventRequestForgery remains in Laravel's web middleware group and is
  explicitly configured with originOnly: true.
- Changing session serialization from PHP to JSON invalidates existing sessions
  created in PHP format. Wisp has no authenticated users, so this is an
  acceptable security improvement.
- Existing explicit prefixes retain the pre-upgrade Wisp names:
  wisp_cache_, wisp_database_, and wisp_session.

## Verification

The following checks pass locally:

- composer validate --strict
- composer audit
- composer show laravel/framework -> v13.29.0
- php artisan about
- php artisan route:list -vv
- php artisan schedule:list
- php artisan config:cache
- php artisan route:cache
- php artisan view:cache
- vendor/bin/pint --test
- php artisan test --compact -> 28 tests, 157 assertions
- npm run format:check
- npm run lint
- npx vue-tsc --noEmit
- npm run test:unit -> 7 tests
- npm run build
- npm run test:e2e -> 8 tests across desktop and mobile

The feature tests cover secret contracts, encryption and hashing,
serialization protection, expiration, pruning, concurrency, throttling,
controller attributes, and origin-only forgery behavior.

## Laravel Cloud checklist

1. Select PHP 8.3 or newer and Node.js 22.
2. Set APP_ENV=production and APP_DEBUG=false.
3. Keep the deployed APP_KEY unchanged.
4. Set APP_URL to the one canonical HTTPS hostname.
5. Set SESSION_SECURE_COOKIE=true.
6. Configure TRUSTED_PROXIES with only TLS-terminating proxy addresses or CIDRs.
7. Configure CACHE_PREFIX=wisp_cache_, REDIS_PREFIX=wisp_database_, and
   SESSION_COOKIE=wisp_session if overriding application defaults.
8. Keep request bodies out of application, proxy, CDN, and observability logs;
   production CSP must allow only same-origin scripts plus the generated nonce,
   with `style-src-attr 'unsafe-inline'` for Reka positioning.
9. Build with Composer, npm ci, and npm run build.
10. Announce that existing secrets will be invalidated, then run php artisan
   migrate --force only after taking a production backup and reviewing the
   one-way secret-storage migration.
11. Enable the scheduler to run php artisan schedule:run every minute.
12. Verify /up, the home page, create, reveal, revoke, expiration, and
   response no-store headers using the Cloud environment URL before switching
    traffic.

Laravel Cloud manages deployment, web processes, and scheduler configuration
from its environment settings. No production resources were changed during
this upgrade.

## Rollback

1. Keep the previous application commit and composer.lock available.
2. Roll back the application and dependency lockfile together.
3. Restore prior configuration values if the deployment changed them.
4. Clear application, config, route, and view caches in the rolled-back
   environment.
5. Treat sessions created after enabling JSON serialization as disposable; the
   application may require a fresh anonymous session.
6. Do not roll back by deleting or reversing migrations. This upgrade adds no
   migration. Restore the database backup if a separate migration or data issue
   requires recovery.

## Remaining risks

- The production database engine and Cloud proxy configuration were not
  accessible from local verification.
- PHP 8.3 CI coverage is represented in GitHub Actions but requires the remote
  workflow to complete on GitHub.
- Browser security headers and origin behavior should receive one smoke test
  after deployment on the custom HTTPS domain.
