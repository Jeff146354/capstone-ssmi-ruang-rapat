# Database Schema — IPB Reserve

## Overview

Database: **MySQL 5.7** — Name: `yii2advanced`

6 custom tables + 1 Yii2 default table:

| Table | Purpose |
|-------|---------|
| `user` | All users (admin + regular) |
| `room` | Room definitions |
| `reservations` | Booking requests & approved bookings |
| `booking_rules` | Admin-configurable policies |
| `notifications` | User notification inbox |
| `user_strikes` | Behavioral strike tracking |
| `reservation_waitlist` | Waitlist queue for occupied slots |

---

## Table: `user`

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| id | INT PK AUTO_INCREMENT | NO | — | Primary key |
| username | VARCHAR(255) | NO | — | Unique login name |
| auth_key | VARCHAR(32) | NO | — | Cookie auth key |
| password_hash | VARCHAR(255) | NO | — | Bcrypt password |
| password_reset_token | VARCHAR(255) | YES | NULL | Temp reset token |
| email | VARCHAR(255) | NO | — | Unique email |
| status | INT | NO | 10 | 0=deleted, 9=inactive, 10=active |
| created_at | INT | NO | — | Unix timestamp |
| updated_at | INT | NO | — | Unix timestamp |
| verification_token | VARCHAR(255) | YES | NULL | Email verification |
| role | ENUM('admin','user') | NO | 'user' | Access level |
| sso_provider | ENUM('google','firebase') | YES | NULL | SSO method |
| sso_uid | VARCHAR(255) | YES | NULL | Firebase UID |
| priority | INT | NO | 1 | 1=mahasiswa, 2=staff, 3=dosen, 99=admin |
| booking_suspended_until | DATETIME | YES | NULL | Suspension end date |
| requires_manual_approval | BOOLEAN | NO | false | Strike 3+ flag |
| priority_boost_until | DATETIME | YES | NULL | Temporary priority boost |

---

## Table: `room`

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| id | INT PK AUTO_INCREMENT | NO | — | Primary key |
| room | VARCHAR(255) | NO | — | Unique code (e.g. "A101") |
| name | VARCHAR(255) | NO | — | Display name |
| description | TEXT | YES | NULL | Room description |
| fr_img | VARCHAR(255) | YES | NULL | Image filename (stored in uploads/) |
| location | VARCHAR(255) | YES | NULL | Coordinates or address |
| contact | VARCHAR(255) | YES | NULL | Contact person name & phone |
| capacity | INT | YES | 0 | Max people |
| is_active | BOOLEAN | NO | true | Soft-delete flag |

**Notes:**
- `Room::find()` automatically filters `is_active = true`
- Images stored at `frontend/web/uploads/{filename}`
- Accessed via `$room->imageUrl` helper

---

## Table: `reservations`

| Column | Type | Null | Default | Description |
|--------|------|------|---------|-------------|
| id | INT PK AUTO_INCREMENT | NO | — | Primary key |
| user_id | INT FK→user.id | NO | — | Who booked |
| room_id | INT FK→room.id | NO | — | Which room |
| document | VARCHAR(255) | YES | NULL | Uploaded file path |
| affiliation | VARCHAR(255) | YES | NULL | Organization/unit |
| reason_of_use | TEXT | YES | NULL | Purpose of booking |
| date | DATE | NO | — | Booking date |
| start_time | TIME | NO | — | Start time |
| end_time | TIME | NO | — | End time |
| status | ENUM('pending','approved','canceled') | NO | 'pending' | Current state |
| rejection_reason | TEXT | YES | NULL | Why it was canceled |
| rejected_by | ENUM('admin','system','user') | YES | NULL | Who canceled |
| checked_in_at | DATETIME | YES | NULL | QR check-in timestamp |
| waitlist_id | INT | YES | NULL | If created from waitlist |
| created_at | TIMESTAMP | NO | CURRENT_TIMESTAMP | When submitted |

**Status flow:**
```
[new] → pending → approved → (checked_in_at set on arrival)
                 → canceled (by admin, system, or user)
```

---

## Table: `booking_rules`

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | — |
| rule_key | VARCHAR(100) UNIQUE | Machine-readable key |
| rule_value | VARCHAR(255) | Current value |
| description | TEXT | Human description |
| updated_at | TIMESTAMP | Last change |

**Seeded defaults:**

| Key | Default | Unit |
|-----|---------|------|
| max_duration_hours | 4 | hours |
| min_duration_minutes | 30 | minutes |
| max_advance_days | 30 | days |
| cancellation_hours_before | 24 | hours |
| max_pending_per_user | 5 | count |
| operating_hours_start | 07:00 | HH:MM |
| operating_hours_end | 21:00 | HH:MM |
| buffer_minutes_between | 15 | minutes |
| pending_expire_hours | 48 | hours |
| strike_suspend_days | 3 | days |
| waitlist_claim_days | 3 | days |
| priority_boost_days | 7 | days |
| enable_noshow_strikes | 0 | boolean (0/1) |

---

## Table: `notifications`

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | — |
| user_id | INT FK→user.id | Recipient |
| type | ENUM(...) | Category |
| message | TEXT | Display text |
| reservation_id | INT FK→reservations.id (nullable) | Related reservation |
| is_read | BOOLEAN | Read status |
| created_at | TIMESTAMP | When created |

**Types:** `reservation_approved`, `reservation_canceled`, `reservation_bumped`, `waitlist_available`, `strike_issued`, `suspension_issued`

---

## Table: `user_strikes`

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | — |
| user_id | INT FK→user.id | Who got the strike |
| reservation_id | INT FK→reservations.id (nullable) | Related reservation |
| reason | ENUM('no_show','late_cancel','damage') | Why |
| notes | TEXT | Admin/system notes |
| created_at | TIMESTAMP | When issued |
| expires_at | DATETIME (nullable) | NULL = permanent |

**Consequence escalation:**
- Strike 1 → warning notification
- Strike 2 → booking_suspended_until = now + strike_suspend_days
- Strike 3+ → requires_manual_approval = true

---

## Table: `reservation_waitlist`

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | — |
| user_id | INT FK→user.id | Who's waiting |
| room_id | INT FK→room.id | Which room |
| date | DATE | Desired date |
| start_time | TIME | Desired start |
| end_time | TIME | Desired end |
| status | ENUM('waiting','notified','claimed','expired') | Current state |
| notified_at | DATETIME (nullable) | When slot opened |
| created_at | TIMESTAMP | When registered |

**Flow:** waiting → notified (slot opened) → claimed (user booked) or expired (window passed)

---

## Foreign Key Behavior

| FK | ON DELETE | ON UPDATE |
|----|-----------|-----------|
| reservations.user_id → user.id | CASCADE | CASCADE |
| reservations.room_id → room.id | CASCADE | CASCADE |
| notifications.user_id → user.id | CASCADE | CASCADE |
| notifications.reservation_id → reservations.id | SET NULL | CASCADE |
| user_strikes.user_id → user.id | CASCADE | CASCADE |
| user_strikes.reservation_id → reservations.id | SET NULL | CASCADE |
| reservation_waitlist.user_id → user.id | CASCADE | CASCADE |
| reservation_waitlist.room_id → room.id | CASCADE | CASCADE |

---

## Migration Order

Run with `php yii migrate --interactive=0`

1. `m130524_201442_init` — user table
2. `m190124_110200_add_verification_token` — email verification
3. `m250529_113845_create_room_table`
4. `m250529_114318_create_schedule_table`
5. `m250606_025803_add_role_and_sso_provider_to_user_table`
6. `m250606_030657_update_schedule_to_reservations_table`
7. `m250615_135943_add_sso_uid_to_user_table`
8. `m250616_054351_add_contact_column_to_room_table`
9. `m250629_000001_add_priority_to_user_table`
10. `m250629_000002_add_fields_to_reservations_table`
11. `m250629_000003_create_booking_rules_table`
12. `m250629_000004_create_user_strikes_table`
13. `m250629_000005_create_reservation_waitlist_table`
14. `m250629_000006_create_notifications_table`
15. `m250629_000007_add_is_active_to_room_table`
