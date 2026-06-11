# IPB Reserve — Sistem Peminjaman Ruang Rapat SSMI

Aplikasi peminjaman ruang rapat untuk Sekolah Sains Data, Matematika, dan Informatika (SSMI), IPB University.

## Quick Start

```bash
git clone https://github.com/Jeff146354/capstone-ssmi-ruang-rapat.git
cd capstone-ssmi-ruang-rapat/apps/web
# Create config files (see docs/installation-manual/setup.md)
docker-compose up -d
docker-compose exec frontend composer install --no-interaction
docker-compose exec frontend chmod +x /app/yii
docker-compose exec frontend php /app/yii migrate --interactive=0
```

- **Frontend (User):** http://localhost:20080
- **Backend (Admin):** http://localhost:21080

For detailed setup instructions, see [docs/installation-manual/setup.md](docs/installation-manual/setup.md).

---

## Creating an Admin Account

After running migrations, create a user account first (via Google signup on the frontend or regular signup), then promote it to admin:

```bash
# Promote a user to admin by their email
docker-compose exec mysql mysql -u yii2advanced -psecret yii2advanced \
  -e "UPDATE user SET role='admin', priority=99, status=10 WHERE email='your@email.com';"
```

Then login at http://localhost:21080/site/login with that account's username and password.

**Note:** Only accounts with `role='admin'` can access the backend. Non-admin users are rejected at login with "Akun Anda tidak memiliki akses sebagai admin."

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Framework | Yii2 PHP (Advanced Template) |
| Database | MySQL 5.7 |
| Auth | Yii2 + Firebase (Google SSO) |
| Frontend | Custom CSS + Bootstrap 5 + Plus Jakarta Sans |
| Containers | Docker + docker-compose |

---

## Features

### User Side
- Browse rooms with live search
- View room detail (image, capacity, location, contact)
- Submit booking requests with file upload (surat peminjaman)
- Search available rooms by date/time/capacity
- View reservation history with status tracking
- Cancel reservations
- Join waitlist for occupied slots
- Receive notifications (approval, rejection, bumped, waitlist)
- Google SSO login/signup

### Admin Side
- Dashboard with real-time statistics (Chart.js)
- Approve/reject reservations with rejection reason
- Priority system (Dosen > Staff > Mahasiswa)
- Auto-cancel conflicting pending reservations on approval
- Room management (CRUD with image upload, soft-delete)
- Configurable booking rules (duration, operating hours, advance days, etc.)
- Strike management (no-show, late cancel tracking)
- User suspension system

### Edge Case Handling
- Concurrent booking prevention (DB-level pessimistic locks)
- Buffer time between bookings (configurable)
- Cancellation deadline enforcement
- Pending reservation auto-expiry (cron job)
- Waitlist with claim window

---

## Documentation

| Document | Description |
|----------|-------------|
| [Setup Guide](docs/installation-manual/setup.md) | How to run the project from scratch |
| [Architecture](docs/architecture.md) | System overview, tech stack, folder structure |
| [Database Schema](docs/tech-docs/database.md) | All tables, columns, and relationships |
| [Edge Cases](docs/tech-docs/edge-cases.md) | All edge cases and how they're handled |
| [User Flows](docs/tech-docs/user-flows.md) | Detailed flow for each feature |
| [Booking Rules](docs/tech-docs/booking-rules.md) | Admin-configurable policies |
| [Cron Jobs](docs/tech-docs/cron-jobs.md) | Scheduled tasks |
| [Docs Index](docs/README.md) | Full documentation index |

---

## Project Status

| Feature | Status |
|---------|--------|
| Room browsing & detail | ✅ Done |
| Booking with validation | ✅ Done |
| Admin approve/reject | ✅ Done |
| Room management (CRUD) | ✅ Done |
| Google SSO login | ✅ Done |
| Notification system | ✅ Done |
| Priority system | ✅ Done |
| Strike system | ✅ Done |
| Waitlist | ✅ Done |
| Configurable rules | ✅ Done |
| Dashboard statistics | ✅ Done |
| UI Revamp (Figma-based) | ✅ Done |
| Email verification | ⚠️ Code ready, needs SMTP config |
| QR Check-in | ⚠️ UI placeholder, no real QR |
| Cron job scheduling | ⚠️ Commands exist, not scheduled yet |
| Production deployment | ❌ Not yet |

---

## Team

Capstone Project — Ilmu Komputer, SSMI IPB University, Semester Genap TA 2025/2026.

Pembimbing: Dr Eng. Heru Sukoco, S.Si., M.T.
