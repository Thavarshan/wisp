# Contributing to Wisp

Wisp is a Laravel application for sharing **encrypted, one-time secrets**. Its security assumptions matter as much as its UI.

## Before changing code

Read the [README](README.md), especially **How it works**, **Configuration**, and **Testing and quality checks**. Check existing issues or open a small proposal that explains the use case.

- Do not post real secret text, bearer tokens, revocation tokens, passwords, credentials or production logs in issues or test fixtures.
- Report vulnerabilities privately using [SECURITY.md](SECURITY.md).
- Changes to token flows, encryption, one-time deletion, session state, headers, logging, authorization or proxies need explicit security reasoning and negative tests.

## Local development

```bash
git clone https://github.com/Thavarshan/wisp.git
cd wisp
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm ci
npm run build
```

Run the relevant checks:

```bash
composer validate --strict
php artisan test --compact
vendor/bin/pint --test
npm run format:check
npm run lint
npm run test:unit
npm run build
npm run test:e2e
```

The full test suite may require browser tooling and a local runtime. Report checks you actually ran and any environment limitations; never claim a security audit based solely on CI.

## Pull request checklist

- One focused change and a concise explanation of the expected behavior.
- Automated tests for success, failure and replay/expiry cases when applicable.
- Synthetic data only, no analytics or logs containing secret material.
- README and deployment notes updated if a setting or security requirement changed.
- Database/rollback impact stated for migration changes.
- No silent weakening of the CSP, trusted-host checks, CSRF protection, cookie flags or no-store headers.

Bug reports with a minimal reproduction, doc corrections, and accessibility improvements are welcome too.
