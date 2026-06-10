# Changelog — IPB Reserve

All notable changes to this project are documented here.

---

## [Unreleased] — 2026-06-10

### Added
- **Edge case handling:** Race condition prevention (pessimistic DB locks), priority system (Dosen > Staff > Mahasiswa), booking validation rules (past-date, operating hours, duration limits, advance booking, max pending, buffer time, user suspension check)
- **Configurable booking rules:** Admin can change all policies from Settings page without code changes (`booking_rules` table with 13 configurable values)
- **Notification system:** Notifications model + views. Users get notified on approval, rejection, bumping, waitlist availability, and strikes
- **Strike system:** Automatic consequence escalation (warning → suspension → manual approval required). Admin can view, issue, and clear strikes
- **Waitlist system:** Users can join waitlist for occupied slots. System auto-notifies best match when slot opens, with configurable claim window
- **Rejection reason:** Admin must provide a reason when rejecting. Shown to user in their history
- **Soft-delete rooms:** Rooms are deactivated (not hard-deleted), preserving all reservation history. Cannot deactivate if future bookings exist
- **File upload:** Room images and surat peminjaman now properly uploaded and served from `frontend/web/uploads/`
- **Real dashboard statistics:** Monthly reservation chart, status donut, top 5 rooms — all from real DB data
- **Cron job commands:** `expire-pending`, `issue-no-show-strikes`, `expire-waitlist` (not yet scheduled, run manually)
- **UI Revamp (Figma-based design system):** New layout, navbar, footer, login page, signup page, homepage (site index), dashboard, browse rooms, room detail, booking form, search results, reservation history, notifications — all with Plus Jakarta Sans font, orange `#FF6B00` brand color, modern card-based design
- **Documentation:** Full developer docs including architecture, database schema, edge cases, user flows, booking rules, cron jobs, setup guide, developer guide, PUML diagrams (ERD, use case, sequence)
- **Access control fix:** All backend controllers now protected by admin role check via `BaseAdminModuleController`
- **Firebase Google login:** Working integration with signup + login flows

### Fixed
- Room image not displaying on user-side (broken path `@web . './uploads/'` → now uses `$room->imageUrl` helper)
- Edit room creating new record instead of updating (hidden ID field handling + unique validation scoped to exclude self)
- Null user crash on riwayat page when not logged in
- Signup not working (was requiring email verification with no SMTP configured — now activates immediately or shows clear error)
- Firebase errors showing blank page (now displays error message to user)

### Changed
- `Reservation` model: expanded validation rules (was only time range + room availability)
- `User` model: added priority, suspension, manual approval fields
- `Room` model: added `is_active` for soft-delete, `getImageUrl()` helper, scoped `find()` to active only
- Backend booking ReservationController: now uses `ReservationService` instead of direct model manipulation
- Frontend DefaultController: peminjaman action now uses service layer + handles file upload
- Docker setup: config files documented (previously required `init.bat` which needs local PHP)

### Previous Work (by original developers)
- Basic Yii2 Advanced Template setup
- Room and Schedule/Reservation tables
- Login/logout with role-based access
- Firebase Google SSO integration (frontend JS + backend verification)
- Basic room CRUD in backend
- Basic reservation creation in frontend
- Docker compose configuration
