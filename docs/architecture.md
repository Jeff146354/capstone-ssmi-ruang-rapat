# Arsitektur Sistem — IPB Reserve

## Tech Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| Framework | Yii2 PHP (Advanced Template) | Split frontend + backend apps |
| Database | MySQL 5.7 | Via Docker service |
| Auth | Yii2 built-in + Firebase (Google SSO) | Role-based: admin, user |
| Frontend UI | HTML + CSS (custom design system) | Font: Plus Jakarta Sans |
| CSS Framework | Bootstrap 5 (layout utilities) | Custom styling overrides |
| Charts | Chart.js | Admin dashboard stats |
| Container | Docker + docker-compose | Apache + PHP 8.1 image |

---

## Current Development Setup

```
┌─────────────────────────────────────────────────────┐
│              Developer Machine (localhost)           │
│                                                     │
│  ┌──────────────────── Docker ───────────────────┐  │
│  │                                               │  │
│  │  ┌───────────┐  ┌───────────┐  ┌──────────┐  │  │
│  │  │ Frontend  │  │ Backend   │  │  MySQL   │  │  │
│  │  │  :20080   │  │  :21080   │  │  :3306   │  │  │
│  │  │ (Apache)  │  │ (Apache)  │  │          │  │  │
│  │  └───────────┘  └───────────┘  └──────────┘  │  │
│  │       │               │              │        │  │
│  │       └───────────────┴──────────────┘        │  │
│  │               Docker Network                  │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  Browser → http://localhost:20080 (user)            │
│  Browser → http://localhost:21080 (admin)           │
└─────────────────────────────────────────────────────┘
```

---

## Yii2 Application Structure

```
apps/web/
├── frontend/           ← User-facing app (booking, browse, history)
│   ├── controllers/    ← SiteController (login, signup, Firebase auth)
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
│   └── migrations/     ← All database migrations (15 total)
│
└── docker-compose.yml  ← Container orchestration
```

---

## Request Flow

1. User visits `http://localhost:20080/index.php?r=ruang-rapat/default/peminjaman&id=5`
2. Docker routes to frontend container port 80
3. Apache → `frontend/web/index.php`
4. Yii2 routing → `frontend\modules\ruangrapat\controllers\DefaultController::actionPeminjaman(5)`
5. Controller calls `ReservationService::create()` (business logic layer)
6. Service validates via `Reservation` model rules → queries MySQL
7. Response rendered via `views/default/peminjaman.php`

---

## Key Design Decisions

| Decision | Why |
|----------|-----|
| Service Layer (`ReservationService`) | All booking logic in one place — controllers stay thin |
| `BookingRule` table | All policies are admin-configurable, not hardcoded |
| Pessimistic DB locks | Prevents race conditions on concurrent bookings |
| Soft-delete for rooms | Never lose reservation history — rooms deactivated, not deleted |
| Notification model | `Notification::send()` called from service layer when state changes |
| Priority system on User | Dosen > Staff > Mahasiswa, enforced at approval time |
| Strike system | Behavioral enforcement (no-show, late cancel) with escalating consequences |

---

## Module Routing

| URL Prefix | Module | App | Port |
|------------|--------|-----|------|
| `/ruang-rapat/` | `frontend\modules\ruangrapat` | Frontend | 20080 |
| `/manajemen-aset/` | `frontend\modules\manajemenaset` | Frontend | 20080 |
| `/ruang-rapat/` | `backend\modules\ruangrapat` | Backend | 21080 |
| `/booking/` | `backend\modules\booking` | Backend | 21080 |

Frontend and backend are separate Yii2 applications on different ports. Same URL prefix routes to different controllers depending on which app handles the request.

---

## External Dependencies

| Service | Purpose | Required? |
|---------|---------|-----------|
| Firebase (Google) | SSO / Google Login | Optional — regular login works without it |
| SMTP Server | Email verification | Optional — accounts can be manually activated |
| Docker | Container runtime | Required for development |
