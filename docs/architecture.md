# Arsitektur Sistem — IPB Reserve

## Tech Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| Framework | Yii2 PHP (Advanced Template) | Split frontend + backend apps |
| Database | MySQL 5.7 | Via Docker service |
| Auth | Yii2 built-in + Firebase (Google SSO) | Role-based: admin, user |
| Frontend UI | HTML + CSS (custom design system) | Font: Plus Jakarta Sans |
| CSS Framework | Bootstrap 5 (layout only) | Custom styling overrides Bootstrap |
| Charts | Chart.js | Admin dashboard stats |
| Container | Docker + docker-compose | Apache + PHP 8.1 image |
| Reverse Proxy | Nginx Proxy Manager (production) | SSL via Let's Encrypt |

---

## High-Level Architecture

```
┌──────────────────────────────────────────────────────┐
│                    INTERNET                           │
└────────────────────────┬─────────────────────────────┘
                         │
              ┌──────────┴──────────┐
              │  Nginx Proxy Mgr    │  (SSL termination)
              │  or Cloudflare      │
              └──────────┬──────────┘
                         │
         ┌───────────────┼───────────────┐
         │               │               │
    ┌────┴────┐    ┌────┴────┐    ┌────┴────┐
    │Frontend │    │Backend  │    │  MySQL  │
    │  :20080 │    │  :21080 │    │  :3306  │
    │ (Apache)│    │ (Apache)│    │ (Docker)│
    └─────────┘    └─────────┘    └─────────┘
         │               │               │
         └───────────────┴───────────────┘
                    Docker Network
```

---

## Yii2 Application Structure

```
apps/web/
├── frontend/           ← User-facing app (booking, browse, history)
│   ├── controllers/    ← SiteController (login, signup)
│   ├── modules/
│   │   └── ruangrapat/ ← Main module: browse, book, history, notifications
│   ├── views/          ← Layouts + site pages (login, signup, index)
│   └── web/            ← Web root (index.php, uploads/, assets/)
│
├── backend/            ← Admin app (manage rooms, approve, strikes, settings)
│   ├── controllers/    ← SiteController (admin login)
│   ├── modules/
│   │   ├── ruangrapat/ ← Room CRUD, dashboard, strikes, settings
│   │   └── booking/    ← Reservation approval workflow
│   └── views/          ← Admin layouts + pages
│
├── common/             ← Shared between frontend & backend
│   ├── models/         ← Room, Reservation, User, Notification, etc.
│   ├── services/       ← ReservationService (central business logic)
│   └── config/         ← Database, aliases, shared params
│
├── console/            ← CLI commands
│   ├── controllers/    ← ReservationCronController (cron jobs)
│   └── migrations/     ← All database migrations
│
└── docker-compose.yml  ← Container orchestration
```

---

## Request Flow

1. User hits `http://domain.com/ruang-rapat/default/peminjaman?id=5`
2. Nginx Proxy Manager → forwards to `frontend:80`
3. Apache inside Docker → `frontend/web/index.php`
4. Yii2 routing → `frontend\modules\ruangrapat\controllers\DefaultController::actionPeminjaman(5)`
5. Controller calls `ReservationService::create()` (business logic)
6. Service validates via `Reservation` model rules → talks to MySQL
7. Response rendered via `views/default/peminjaman.php`

---

## Key Design Decisions

| Decision | Why |
|----------|-----|
| Service Layer (`ReservationService`) | All booking logic in one place — controllers stay thin, easy to test |
| `BookingRule` table | All policies are admin-configurable, not hardcoded. SSMI hasn't defined policies yet |
| Pessimistic DB locks | Prevents race conditions on concurrent bookings |
| Soft-delete for rooms | Never lose reservation history — rooms deactivated, not deleted |
| Notification model | Decoupled from business logic — `Notification::send()` called from service |
| Priority system on User | Dosen > Staff > Mahasiswa, enforced at approval time |
| Strike system | Behavioral enforcement without blocking legitimate users |

---

## Module Routing

| URL Prefix | Module | App |
|------------|--------|-----|
| `/ruang-rapat/` | `frontend\modules\ruangrapat` | Frontend |
| `/manajemen-aset/` | `frontend\modules\manajemenaset` | Frontend |
| `/ruang-rapat/` | `backend\modules\ruangrapat` | Backend |
| `/booking/` | `backend\modules\booking` | Backend |

Note: Frontend and backend are separate Yii2 applications running on different ports. The URL prefix `/ruang-rapat/` exists in both but routes to different controllers.
