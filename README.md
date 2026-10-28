# Research Project Management System — **Pro Edition**

> **Gambella University, Ethiopia**
> Full-featured, production-grade research project lifecycle management.

[![CI](https://github.com/kenenisaboru/research-project-management-system/actions/workflows/laravel-tests.yml/badge.svg)](https://github.com/kenenisaboru/research-project-management-system/actions)

## Features

### Core Workflow
- **11-Role RBAC:** PI, Team Member, DH, Coordinator, Reviewer, Dean, IRERC, VP ARTTCS, RCSC, Finance, Admin
- **Project Lifecycle:** Draft → Submitted → DH Screened → Under Review → Approved → Active → Completed
- **Blind Peer Review:** Anonymous double-blind evaluation with scoring rubrics
- **Dual-Threshold Financial Governance:** Dean (<500k ETB) / RCSC (≥500k ETB) tiers
- **Budget Management:** Tranche-based disbursement with payment vouchers (PDF)
- **Progress Reports:** Quarterly/semi-annual/annual with milestone tracking
- **Contract Signing:** Digital PI + VP ARTTCS contract workflow with PDF download
- **PDF Certificates:** Completion certificates via DomPDF
- **Project Lifecycle Events:** Extensions, PI transfers, terminations, budget amendments
- **Calls for Proposals:** Projects, abstracts, community service with deadlines
- **Publication & Award:** Papers, journal indexing, evaluations, awards

### Pro-Level Upgrades ✨
- **API v1 Layer:** RESTful JSON API with Sanctum auth, API Resources, rate limiting
- **Event Architecture:** 8 domain events + queued listeners, all decoupled from controllers
- **Queue System:** Database/Redis queue with ProcessBackupJob, ProcessCsvImportJob, ProjectStatusMail
- **Two-Factor Authentication (TOTP):** QR setup, confirm, challenge (enforced for Admin/Finance)
- **Security Headers:** X-Frame-Options, CSP, Referrer-Policy, Permissions-Policy middleware
- **Password Policy:** Min 10 chars, complexity rules, 90-day rotation warning
- **CacheService:** Role-keyed dashboard stats (60s TTL), thematic areas/departments (300s TTL)
- **Versioned Document Management:** Upload, version tracking, signed temporary download URLs
- **Institutional PDF Reports:** Annual Output, Grant Expenditure, Thematic Impact, Researcher Performance
- **Advanced Analytics Engine:** 5 Chart.js charts, date-range filters, PDF export
- **Advanced Search:** Full-text search, multi-filter, highlighted results, CSV/PDF export, server-side sort
- **Notification Centre:** Categories, priorities, bulk actions, 30s JS polling badge updates
- **Command Palette (Ctrl+K):** Instant page search with keyboard navigation
- **Dark/Light Theme Toggle:** Persisted in localStorage, Inter variable font, RPMS design system
- **Mobile Experience:** Card-stack tables, bottom navigation bar, FAB quick actions
- **Health Check Endpoint:** `/health` — DB, storage, queue status in JSON
- **Docker Compose:** MySQL 8 + Redis 7 + Nginx + PHP-FPM + Supervisor (queue worker)
- **GitHub Actions CI/CD:** Parallel test matrix (PHP 8.1/8.2) + GHCR deploy pipeline
- **Makefile:** `make install`, `make dev`, `make test`, `make build`, `make deploy`
- **Audit Log Enhancement:** IP, user-agent, before/after JSON diff snapshots
- **RPMS Log Channel:** Daily rotating, 90-day retention

## Test Accounts / Demo Logins

For local development only, seed the demo data (`php artisan migrate --seed`) and use one of the following accounts. **All demo accounts share the same password:** `UNI@Demo1`. Never use these seeded accounts in a production deployment.

| Role                               | Email Address                   |
| :--------------------------------- | :------------------------------ |
| **System Administrator**     | `admin@rpms.local`       |
| **Principal Investigator**   | `pi@rpms.local`          |
| **Team Member**              | `tm@rpms.local`          |
| **Department Head**          | `dh@rpms.local`          |
| **Research Coordinator**     | `coordinator@rpms.local` |
| **Blind Reviewer**           | `reviewer@rpms.local`    |
| **College Dean**             | `dean@rpms.local`        |
| **Ethics Committee (IRERC)** | `irerc@rpms.local`       |
| **VP Academic (ARTTCS)**     | `vp@rpms.local`          |
| **RCSC Chair**               | `rcsc@rpms.local`        |
| **Finance Office**           | `finance@rpms.local`     |

## Tech Stack

- **Backend:** Laravel 9 (PHP 8.1)
- **Web Server:** Nginx + PHP-FPM (production)
- **Caching:** OPcache for compiled PHP, file-based cache for application data
- **Database:** SQLite (production) / MySQL (local development)
- **Frontend:** Bootstrap 5.3, Blade Templates, Chart.js
- **PDF Generation:** barryvdh/laravel-dompdf
- **Containerization:** Docker

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Auth, Admin, Projects, Governance, Finance, etc.
│   │   └── Middleware/      # RBAC, TrustProxies, AuditLog
│   ├── Models/             # User, Project, Budget, Review, Certificate, etc.
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database schema
│   └── seeders/            # DatabaseSeeder, RbacSeeder (roles, permissions, demo users)
├── public/
│   └── vendor/             # Bootstrap 5.3 CSS/JS (local assets)
├── resources/
│   └── views/
│       ├── layouts/        # app.blade.php (shared navbar, sidebar, footer)
│       ├── admin/          # Users, Colleges, Departments, Thematic Areas
│       ├── governance/     # Dean Approvals, RCSC Portal
│       ├── projects/       # Project CRUD, Show, Edit
│       └── ...
├── routes/
│   ├── web.php             # All web routes with RBAC middleware
│   └── api.php
├── nginx.conf              # Nginx config for production
├── Dockerfile              # PHP 8.1-FPM + Nginx
└── entrypoint.sh           # Container startup (migrate, seed, cache, launch)
```

## Local Installation

### Prerequisites

- PHP 8.1+
- Composer
- SQLite or MySQL

### Setup

```bash
git clone https://github.com/gemachistesfaye/research-project-management-system.git
cd research-project-management-system
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Visit `http://localhost:8000`

## Environment Variables

Key variables in `.env`:

| Variable           | Description     | Default                   |
| ------------------ | --------------- | ------------------------- |
| `APP_KEY`        | Encryption key  | Pre-configured            |
| `APP_URL`        | Application URL | Auto-detected             |
| `APP_DEBUG`      | Debug mode      | `false` in production   |
| `DB_CONNECTION`  | Database driver | `sqlite`                |
| `DB_DATABASE`    | Database path   | `/data/database.sqlite` |
| `CACHE_DRIVER`   | Cache backend   | `file`                  |
| `SESSION_DRIVER` | Session storage | `file`                  |
| `RPMS_OVERDUE_MONTHS` | Months before an active project is flagged overdue | `12` |
| `RPMS_BUDGET_ALERT_PERCENT` | Released-vs-approved budget alert threshold | `90` |
| `RPMS_BACKUP_RETENTION_DAYS` | Backup retention window (auto-prune) | `30` |
| `RPMS_SCHEDULED_BACKUP` | Enable the daily scheduled backup | `true` |
| `RPMS_SEED_DEMO_DATA` | Seed demo accounts and sample records (local/testing by default; keep `false` in production) | `false` |

## Docker Deployment

The application runs as a Docker container with **Nginx + PHP-FPM**:

```bash
docker build -t rpms .
docker run -p 8000:8000 -v data:/data rpms
```

The container handles automatically on startup:

1. Environment variable overrides for production
2. Database migrations and RBAC role/permission seeding
3. Config, route, and view cache rebuild
4. Nginx and PHP-FPM launch

Demo users and sample records are not seeded in production by default. To provision the first production administrator, run `php artisan rpms:create-admin` inside the running container and follow the prompts. Set `RPMS_SEED_DEMO_DATA=true` only for an explicitly isolated demo deployment; it creates accounts with the published demo password.

## RBAC Permissions

The system enforces 60+ granular permissions across 12 roles:

| Role                   | Key Permissions                                                       |
| ---------------------- | --------------------------------------------------------------------- |
| Principal Investigator | Create/manage own projects, submit progress reports                   |
| Team Member            | View assigned projects, submit contributions                          |
| Department Head        | Screen proposals within department                                    |
| Coordinator            | Assign reviewers, manage review workflow                              |
| Reviewer               | Conduct blind evaluations, submit scores                              |
| College Dean           | Approve/reject projects (<500k ETB), view college analytics           |
| IRERC Ethics Committee | Ethics review and approval                                            |
| VP Academic (ARTTCS)   | Final approval, amendment review, certificate issuance                |
| RCSC Chair             | Financial governance (≥500k ETB), budget approval                    |
| Finance Office         | Process disbursements, financial tracking                             |
| System Administrator   | Full access: users, colleges, departments, thematic areas, audit logs |

## Assumptions & Design Decisions

The following assumptions were made while implementing the specification. They are documented here so reviewers can validate the interpretation of the requirements.

1. **Role model:** The specification references an *Auditor* role, but access control is implemented with the 11 operational roles actually required by the workflow (PI, TM, DH, Coordinator, Reviewer, Dean, IRERC, VP ARTTCS, RCSC, Finance, Admin). The Admin role subsumes read-only audit duties via the Audit Log module. An Auditor role can be added without schema changes (it is a name in the `roles` table).
2. **Approval thresholds:** Financial approvals follow the dual-threshold rule: Dean approves requests **below 500,000 ETB**, RCSC/VP approves requests **at or above 500,000 ETB**. Thresholds are currently constants in the workflow engine and can be moved to configuration if policy changes.
3. **Single active call vs. many:** Multiple calls may be open simultaneously; PIs are not hard-blocked from submitting outside an open call (the call is advisory metadata). Enforcing a hard gate would be a policy decision, so it is intentionally not enforced.
4. **Overdue definition:** A project is *overdue* when it remains `Active`/`Ongoing` past `rpms.overdue_months` (default **12**) from `activated_at` without a `completed_at`. This is configurable via `RPMS_OVERDUE_MONTHS`.
5. **Over-budget definition:** A project is *over-budget* when the sum of `Released` budget requests (`approved_amount`) exceeds `approved_budget` (falling back to `requested_budget` when no approved budget exists).
6. **Paper evaluation:** Paper evaluation is single-round and score-based; any evaluator with `evaluate_paper` may score a paper. Approval (`approve_paper`) is restricted to VP ARTTCS / RCSC / Admin, matching the publication governance chain.
7. **Journal index:** Journal indexing accepts the descriptive labels (`SCIE`, `SSCI`, `Scopus`, `ESCI`, `National`, `None`) as free text rather than a fixed enum, because indexing databases change.
8. **Notifications:** Email delivery uses Laravel's mailer and is **guarded** — on an offline/LAN-only deployment without SMTP, in-app notifications still work and mail failures are swallowed so they never break a user action.
9. **Backups:** SQLite backups use SQLite's consistent `VACUUM INTO` snapshot. Other database drivers use a versioned JSON table backup, which can be restored through the admin recovery screen. JSON restores validate the schema and data shape, run transactionally, and create a pre-restore snapshot first. SQLite restores check file integrity and required tables before staging an atomic replacement. Keep an off-server copy of backups; scheduled backups run `backups:create --prune` daily at 02:00 (enable via cron / `schedule:run`).
10. **Legacy import:** The CSV importer is intentionally forgiving — unknown departments/thematic areas fall back to the first available record and an unmatched `pi_email` falls back to the first PI account, so a bulk import never hard-fails mid-file. Skipped rows are reported back to the administrator.
11. **Public repository fields:** Only title, abstract, status, department and thematic area are exposed publicly. Budget figures, PI contact details, and files are never shown.
12. **File storage:** Paper PDFs (when supplied) are stored on the `public` disk under `storage/app/public/papers`; run `php artisan storage:link` in any real deployment.

## License

MIT License
