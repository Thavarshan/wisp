# Security policy

Wisp handles sensitive user-provided text. Please **do not disclose suspected security vulnerabilities in a public GitHub issue**.

## Private reporting

Email [tjthavarshan@gmail.com](mailto:tjthavarshan@gmail.com) with the subject `Wisp security report`. Include the affected commit or version, the impact, reproduction steps using **synthetic secrets only**, and your contact details. Do not send live passwords, credentials, access tokens or private user content.

If GitHub's private vulnerability reporting is enabled for this repository, you may use it instead. Please allow time for review and coordinated disclosure; no fixed response or remediation time is promised.

## Supported scope

The maintained `main` branch is the reference for reports. A formal supported-release/security-fix schedule has not been established. Do not assume a public demo or every previous release receives security fixes.

Useful areas to test include:

- Token hashing, fragment-only bearer credentials, link expiry and single-use transaction behavior.
- Password checks, revocation, content encryption, logging and cache controls.
- CSRF, authorization, session/cookie configuration, CSP, trusted proxies and host validation.
- Backups, migrations and any leak of secret material to analytics, logs, URL queries, redirects or exception reports.

The [README](README.md) documents expected behavior, production configuration and test commands. Passing tests is not a professional security audit.

## Public issue guidance

For non-sensitive bugs or feature requests, use [GitHub Issues](https://github.com/Thavarshan/wisp/issues) and remove all real secret values from logs, screenshots and requests.
