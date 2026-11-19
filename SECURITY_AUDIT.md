# Security audit — 2026-11-19

## Scope and baseline

Audit source code and local tests, not a production penetration test or ASVS certification. Existing uncommitted work was preserved: initial status contained 34 modified and 16 untracked paths. No deployment, credential rotation, database reset, or production scanning performed.

Official baseline verified at https://owasp.org/Top10/2025/: A01 access control, A02 misconfiguration, A03 supply chain, A04 cryptography, A05 injection, A06 insecure design, A07 authentication, A08 integrity, A09 logging/alerting, A10 exceptional conditions. SSRF separately considered. ASVS used as a control framework (authentication, sessions, authorization, validation, files, communication); no claim of full requirement-by-requirement verification.

## Architecture and boundaries

- Laravel 12, PHP >=8.2, Blade templates, public CSS/JavaScript, Vite 7 and Tailwind 3 tooling. Composer lock framework v12.63.0; locally installed framework v12.60.2 according to Composer dry-run. This drift is important.
- Eloquent and MySQL-oriented production queries; automated tests use SQLite memory database.
- Web session auth, email OTP, Google Socialite; Sanctum tokens for mobile API. Roles: admin/anggota; membership types distinguish guests and permanent members.
- Public routes: registration/login, event applications/tickets, guest log, media, AI. Authenticated routes: bookings, profiles, attendance, exams, reports. Admin routes: CRUD, grading, approvals, backup/restore.
- Browser/mobile to Laravel is untrusted boundary; admin to filesystem/database remains privileged boundary. SMTP, Google OAuth/Drive/FCM, AI proxy/Gemini, payment callbacks are separate service boundaries.
- Sensitive data: passwords/hashes, session/token/OTP, email/phone/address, attendance/grades, transfer images, backups and service credentials.

## Findings and disposition

Line references refer to inspected baseline; changed code may shift lines. Symbols are stable locators.

| ID | Severity | Evidence/location | Disposition |
|---|---|---|---|
| S01 | HIGH | `app/Services/GeminiService.php:444,514`, CURLOPT_SSL_VERIFYPEER false | Enable peer and hostname checks. HTTPS service must have valid CA chain. |
| S02 | HIGH | `ChatbotController.php:26,38,123`; `Api/GeminiController.php:33,46`: client session ID alone selected/deleted history | Bind web history to authenticated user or browser session using keyed namespace; API history bound to authenticated user. Anonymous API chat still responds, without persisted shared history. Old unscoped history is retained but not automatically attached. |
| S03 | HIGH, conditional | `Admin/BackupController.php:291-302`: ZIP entry appended to public storage path | Validate filenames before Drive queries; preflight all ZIP paths/types and expanded size/count before writes. Requires malicious/tampered backup and privileged restore. SQL restore remains highly privileged and is not a sandbox. |
| S04 | HIGH, deployment | `Dockerfile:23` COPY all context, no dockerignore | Exclude environment files, credentials, git, logs, backup files and local vendor from build context. |
| S05 | MEDIUM | `docker/nginx.conf:34`: any PHP path executable; hidden-file protection limited to .ht | Only index.php executes; other PHP and dotfiles denied. Nginx runtime not tested here. |
| S06 | MEDIUM | `config/session.php:172`; no application security headers | Secure cookie default in production; nosniff/referrer/frame headers; structural CSP; HSTS only for secure production requests. CSP is NOT full script-src XSS prevention. |
| S07 | MEDIUM | AI and registration routes lacked throttles | Add bounded registration and AI rate limits. Shared proxy/IP deployments require tuning. |
| S08 | CRITICAL/HIGH package advisories | Composer locked audit: 41 advisories affecting 11 packages, including Laravel TokenGuard advisory; npm: 11 vulnerable packages (1 critical, 8 high, 2 moderate) | npm compatible audit fix completed, zero advisories reported. Composer update dry-run only: installed Filament/Livewire absent from lock; install would remove 24 packages. PHP update remains blocked pending reconciliation. Advisory severity is not proof affected code is reachable. |
| S09 | LOW/MEDIUM | Missing focused auth event logging | Add fixed-name success/failure/throttle logs without email/password/token. Central alerting remains operational work. |
| S10 | MEDIUM residual | Trust all proxies in bootstrap; public transfer images; backup SQL restore; inline HTML/JS sinks; exception details in several logs/responses | Further review and deployment decisions required; not claimed resolved. |

## Coverage by risk

- A01: admin middleware and member ownership inspected; account-state, chatbot ownership and file traversal regression tests. Public proof-of-payment files and ticket bearer access need privacy redesign review.
- A02/A04: header/cookie/TLS/build-context hardening. Production debug/CORS/proxy/host settings not independently verified. HSTS assumes correct TLS proxy chain.
- A03: Composer/npm audits and compatible npm update. PHP dependency drift is a release blocker.
- A05: Eloquent parameter bindings and selected JS encoding reviewed. Login SQL payload and social URL regression covered. This does not prove all DOM sinks safe.
- A06/A07: booking transaction/capacity and existing auth regression retained; throttling added. OTP cache remains plaintext; per-account guessing limits, MFA, reset enumeration and session revocation after password change need further work.
- A08: webhook signature regression retained; ZIP preflight added. SQL archive authenticity/signing and Drive folder scoping remain unresolved.
- A09: focused logging added; API/admin coverage, retention and alerts need completion.
- A10: malformed inputs/rate limits tested; backup failure atomicity and outbound failure timeouts not fully covered.
- SSRF: inspected outbound calls use configured/fixed service endpoints; TLS checks restored. Dompdf, remote image fetching, user-supplied embed URLs and network egress require further review. No blanket SSRF-safe claim.

## Verification

- `composer audit --locked --no-interaction`: 41 advisories/11 packages; connection timeout warning also emitted. Installed and locked package sets differ.
- Targeted Composer update with `--dry-run --minimal-changes --no-scripts`: same-major updates resolvable, but would remove 24 installed extras. No PHP packages updated.
- `npm audit fix --ignore-scripts`: 19 packages changed, 1 removed; zero vulnerabilities reported.
- `php artisan test`: 34 passed, 126 assertions before final auth-log-only change; final rerun recorded in delivery response.
- `npm run build`: initial failure because resources/css/app.css and resources/js/app.js absent; add expected Vite entrypoints; subsequent build succeeded (61 modules). Existing public stylesheet/script layout unchanged.
- `git diff --check`: passed.
- Secret exposure check: git tracked-path check did not list .env/key/PEM/service-account files; `.env` ignored. This is NOT full historical secret scanning. No secret values printed.
- No dedicated SAST/DAST, full secret-history scanner, browser penetration test, production TLS test, real email/Google/payment test, or Nginx config runtime validation performed.
- Added explicit CSRF enforcement test (test bypass disabled): login without token returns 419. Added rejected PHP-content upload with executable MIME and escaped announcement markup regression. These cover selected paths, not all uploads or DOM sinks.
- Follow-up search found no Filament/Livewire references in app, resources, or config. Removal of locally installed extra packages still requires approval because dependency installation would materially alter the local environment.

## Modified paths in this security pass

`app/Services/GeminiService.php`, `app/Http/Controllers/ChatbotController.php`, `app/Http/Controllers/Api/GeminiController.php`, `app/Http/Controllers/Admin/BackupController.php`, `app/Http/Controllers/Auth/AuthController.php`, `app/Http/Middleware/SecurityHeaders.php`, `bootstrap/app.php`, `config/session.php`, `routes/web.php`, `routes/api.php`, `docker/nginx.conf`, `.dockerignore`, `package-lock.json`, `resources/css/app.css`, `resources/js/app.js`, `tests/Feature/SecurityHardeningTest.php`, this report and checklist. Other working-tree changes predate this pass.

## Release decision

Partial hardening completed, not production clearance. Reconcile installed Composer packages before approved install/update; verify MySQL migration compatibility and all external integrations in staging. Preserve database backups and obtain approval for privacy/API changes, stronger CSP, MFA enrollment and proxy trust changes that can alter existing flows.
