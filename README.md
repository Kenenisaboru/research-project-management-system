# Research Project Management System

A full-featured web application for managing the complete lifecycle of academic research projects, from proposal submission to completion and certificate issuance. Built for Research Project Management System.

## Features

- **12 Role-Based Access Control (RBAC):** Principal Investigator, Team Member, Department Head, Coordinator, Reviewer, College Dean, IRERC Ethics Committee, VP Academic (ARTTCS), RCSC Chair, Finance Office, System Administrator
- **Workflow Engine:** Draft → Submitted → DH Screened → Under Review → Approved → Active → Completed
- **Blind Peer Review:** Anonymous double-blind evaluation with scoring rubrics
- **Dual-Threshold Financial Governance:** Dean (<500k ETB) and RCSC (≥500k ETB) approval tiers
- **Budget Management:** Tranche-based disbursement tracking with approval workflows
- **Progress Reports:** Quarterly/semi-annual/annual reporting with team member contributions
- **Contract Signing:** Digital contract generation and tracking
- **PDF Certificates:** Research Project Management System-branded completion certificates with PDF download
- **Project Lifecycle:** Extensions, amendments, PI transfers, and termination with refund calculations
- **Procurement Requests:** Equipment and supplies procurement tied to projects
- **Analytics Dashboard:** Real-time project statistics, budget charts, and departmental metrics
- **Calls for Proposals:** Post and manage calls (projects, abstracts, community service) with deadlines and budget ceilings
- **Publication & Award:** Journal-index paper submission, evaluation, approval, recording, and award tracking
- **Notifications Centre:** In-app + email alerts with alerter roll-ups (overdue projects, over-budget disbursements)
- **Public Research Repository:** Unauthenticated catalogue of ongoing/completed/terminated/rejected research
- **Backup & Recovery:** On-demand and scheduled database backups with download/restore
- **Legacy Data Migration:** Import existing paper-based project records from CSV/Excel
- **Audit Trail:** Complete action logging for compliance
- **Mobile Responsive:** Dark-themed, professional UI with collapsible sidebar on mobile

## Test Accounts / Demo Logins

If you have run the database seeders (`php artisan migrate --seed`), you can log in with any of the following demo accounts. **All accounts share the same password:** `UNI@Demo1`

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

## Docker Deployment

The application runs as a Docker container with **Nginx + PHP-FPM**:

```bash
docker build -t rpms .
docker run -p 8000:8000 -v data:/data rpms
```

The container handles automatically on startup:

1. Environment variable overrides for production
2. Database migration and seeding
3. Config, route, and view cache rebuild
4. Nginx and PHP-FPM launch

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
9. **Backups:** For SQLite the backup is a byte-for-byte copy of the database file; for MySQL `mysqldump` is used when available, otherwise a portable JSON table dump is produced. Scheduled backups run `backups:create --prune` daily at 02:00 (enable via cron / `schedule:run`).
10. **Legacy import:** The CSV importer is intentionally forgiving — unknown departments/thematic areas fall back to the first available record and an unmatched `pi_email` falls back to the first PI account, so a bulk import never hard-fails mid-file. Skipped rows are reported back to the administrator.
11. **Public repository fields:** Only title, abstract, status, department and thematic area are exposed publicly. Budget figures, PI contact details, and files are never shown.
12. **File storage:** Paper PDFs (when supplied) are stored on the `public` disk under `storage/app/public/papers`; run `php artisan storage:link` in any real deployment.

## License

MIT License
