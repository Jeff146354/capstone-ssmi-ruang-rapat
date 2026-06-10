# Edge Cases & Business Logic — IPB Reserve

## Overview

This document explains every edge case the system handles, where the code lives, and what happens when each scenario occurs.

---

## 1. Concurrent Booking (Race Condition)

**Scenario:** Two users submit reservations for the same room/time at the exact same moment.

**Solution:** Pessimistic database locking via `SELECT ... FOR UPDATE` inside a transaction.

**Code:** `common/services/ReservationService::create()`

**What happens:**
1. First request acquires the row lock
2. Second request waits at the database level
3. First request inserts and commits
4. Second request re-checks availability → finds conflict → fails cleanly with error message

---

## 2. Priority System (Dosen > Mahasiswa)

**Scenario:** A dosen's reservation conflicts with an already-approved mahasiswa reservation.

**Solution:** Priority comparison during admin approval. Higher priority bumps lower.

**Code:** `ReservationService::approve()` — compares `$approverUser->getEffectivePriority()` vs `$conflictUser->getEffectivePriority()`

**What happens:**
1. Admin clicks Approve on dosen's pending reservation
2. System finds conflicting approved reservation from mahasiswa
3. Compares priority: dosen (3) > mahasiswa (1)
4. Auto-cancels mahasiswa's reservation with `rejected_by = 'system'`
5. Sends notification to mahasiswa explaining the bump
6. Gives mahasiswa a `priority_boost_until` (7 days default)
7. Triggers waitlist in case someone else wanted that slot
8. Approves dosen's reservation

**Same priority:** System blocks approval — admin must manually decide who to pick (FIFO visible via `created_at` column in admin table).

---

## 3. Booking in the Past

**Scenario:** User tries to book a room for yesterday.

**Solution:** Server-side validation.

**Code:** `Reservation::validateNotPast()`

**What happens:** Compares `date + start_time` against current server time. Rejects with "Tidak bisa memesan ruangan di waktu yang sudah lewat."

---

## 4. Operating Hours

**Scenario:** User tries to book at 3 AM.

**Solution:** Configurable operating hours from `booking_rules` table.

**Code:** `Reservation::validateOperatingHours()`

**Defaults:** 07:00 – 21:00. Admin can change via Settings page.

---

## 5. Duration Limits

**Scenario:** User books for 1 minute, or for 12 hours straight.

**Solution:** Min and max duration rules.

**Code:** `Reservation::validateDuration()`

**Defaults:** Min 30 minutes, max 4 hours.

---

## 6. Maximum Advance Booking

**Scenario:** User books a room 2 years in advance to squat on it.

**Solution:** `max_advance_days` rule (default: 30 days).

**Code:** `Reservation::validateAdvanceBooking()`

---

## 7. Rate Limiting (Max Pending)

**Scenario:** User spams 50 reservation requests.

**Solution:** `max_pending_per_user` rule (default: 5).

**Code:** `Reservation::validateMaxPendingPerUser()`

---

## 8. Buffer Time Between Bookings

**Scenario:** Room booked 10:00–12:00 and 12:00–14:00 with zero gap for cleanup.

**Solution:** Configurable buffer added to availability checks.

**Code:** `Reservation::validateRoomAvailability()` — adds `buffer_minutes_between` (default: 15 min) to both ends.

**Effect:** With 15min buffer, a 10:00–12:00 booking blocks 09:45–12:15.

---

## 9. User Suspension

**Scenario:** User with active suspension tries to book.

**Solution:** Check `booking_suspended_until` timestamp.

**Code:** `Reservation::validateUserNotSuspended()`

---

## 10. Pending Reservation Expiry

**Scenario:** Admin never reviews a pending reservation and the booking date arrives.

**Solution:** Cron job auto-cancels `pending_expire_hours` before booking time.

**Code:** `console/controllers/ReservationCronController::actionExpirePending()`

**Schedule:** Run every hour.

---

## 11. No-Show Detection

**Scenario:** User's approved reservation passes but they never checked in.

**Solution:** Cron job checks approved reservations with `checked_in_at = NULL` after the date passes, then issues a strike.

**Code:** `ReservationCronController::actionIssueNoShowStrikes()`

**Guard:** Only runs if `enable_noshow_strikes` = '1' in booking_rules (disabled by default until QR check-in is built).

---

## 12. Strike Escalation

**Scenario:** User accumulates strikes.

**Code:** `common/models/UserStrike::issue()`

| Strikes | Consequence |
|---------|-------------|
| 1 | Warning notification only |
| 2 | Account suspended for `strike_suspend_days` (3 days default) |
| 3+ | `requires_manual_approval` = true permanently until admin clears |

---

## 13. Cancellation Deadline

**Scenario:** User tries to cancel an approved reservation 5 minutes before it starts.

**Solution:** Check hours until booking against `cancellation_hours_before` (default: 24h).

**Code:** `ReservationService::cancel()` — if `$canceledBy === 'user'` and within deadline window:
- Approved: rejected outright
- Pending: allowed but issues a `late_cancel` strike

Admin can always cancel regardless of deadline.

---

## 14. Waitlist System

**Scenario:** Room is fully booked. User wants to be notified if a slot opens.

**Flow:**
1. User joins waitlist for room + date + time
2. If an approved reservation for that slot gets canceled → `triggerWaitlist()`
3. System finds best match (overlap scoring + FIFO tiebreaker)
4. Sends notification to matched user
5. User has `waitlist_claim_days` (3 days) to claim
6. If unclaimed → passed to next in queue (cron: `actionExpireWaitlist`)

**Code:** `ReservationWaitlist::findBestMatch()`, `ReservationService::triggerWaitlist()`, `ReservationService::claimWaitlist()`

---

## 15. Soft-Delete Rooms

**Scenario:** Admin deletes a room that still has future approved reservations.

**Solution:** Blocked. `Room::softDelete()` checks for future approved bookings first.

**What happens:** If no future bookings → `is_active = false`. Room disappears from all user-facing views but reservation history is preserved.

---

## 16. Concurrent Admin Approval

**Scenario:** Two admins approve conflicting reservations simultaneously.

**Solution:** Same pessimistic lock as booking creation — `SELECT ... FOR UPDATE` on room+date inside `approve()`.

---

## 17. Edit Restrictions

**Scenario:** User tries to edit an already-approved reservation.

**Solution:** Only pending reservations can be edited. Approved ones must be canceled + rebooked.

**Code:** `backend/modules/booking/controllers/ReservationController::actionUpdate()`

---

## 18. Rejection Reason

**Scenario:** Admin rejects without explanation — user doesn't know why.

**Solution:** Modal with optional textarea for rejection reason. Stored in `rejection_reason` column. Displayed in user's history view.

---

## 19. Priority Boost After Being Bumped

**Scenario:** Mahasiswa gets bumped by dosen. They should get preferential treatment for rebooking.

**Solution:** `priority_boost_until` set to now + `priority_boost_days` (7 days default). `getEffectivePriority()` returns `priority + 1` while boost is active.

**Effect:** Boosted mahasiswa (effective priority 2) beats other regular mahasiswa (priority 1) but still doesn't beat dosen (priority 3).
