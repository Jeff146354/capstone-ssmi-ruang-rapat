# User Flows — IPB Reserve

## Roles

| Role | Priority | Access |
|------|----------|--------|
| Mahasiswa | 1 | Frontend only |
| Staff | 2 | Frontend only |
| Dosen | 3 | Frontend only |
| Admin | 99 | Frontend + Backend |

---

## Flow 1: User Registration

```
User → /site/signup → Fill form (username, email, password)
  → Server validates (unique username/email, password length)
  → Account created with status=INACTIVE
  → Verification email sent
  → User clicks link in email → /site/verify-email?token=xxx
  → Account activated (status=ACTIVE)
  → Can now login
```

**Without SMTP configured:** Account stays inactive. For dev, manually set `status=10` in DB.

---

## Flow 2: Browse & Book a Room

```
User → /ruang-rapat (Dashboard)
  → See featured rooms + search form
  → Click "Browse Rooms" or a room card

User → /ruang-rapat/default/daftar-ruangan
  → Grid of all active rooms
  → Client-side search filter by name
  → Click a room card

User → /ruang-rapat/default/view?id=5 (Room Detail)
  → See image, description, capacity, location, contact
  → Three buttons: "Ajukan Peminjaman", "Lihat Jadwal", "Daftar Tunggu"
  → Click "Ajukan Peminjaman"

User → /ruang-rapat/default/peminjaman?id=5 (Booking Form)
  → Fill: date, start_time, end_time, affiliation, reason, upload surat
  → Submit → ReservationService::create()
  → Validations run (see edge-cases.md for full list)
  → If valid → saved as PENDING → redirect to dashboard with success flash
  → If invalid → form re-shown with error messages
```

---

## Flow 3: Search Available Rooms

```
User → Dashboard search form or /ruang-rapat/default/find-available-rooms
  → Fill: date, start_time, end_time, min_capacity
  → Submit (GET request)
  → Server queries rooms NOT in conflicting approved reservations
  → Results shown as cards with "Pesan Sekarang" button
```

---

## Flow 4: View My Reservations

```
User → /ruang-rapat/default/riwayat-peminjaman
  → See all my reservations as cards (sorted by status/date/room)
  → Each card shows: date, room name, time, status badge, reason
  → Approved: QR button + Cancel button
  → Pending: "Tarik" (withdraw) button
  → Canceled: shows rejection reason + who canceled
```

---

## Flow 5: Cancel My Reservation

```
User → Riwayat page → Click "Batalkan" or "Tarik"
  → Modal confirmation appears
  → Confirm → POST /ruang-rapat/default/cancel?id=X
  → ReservationService::cancel(id, 'user')
  → Checks cancellation deadline (24h before for approved)
  → If too late → rejected with error
  → If allowed → status='canceled', rejected_by='user'
  → If was approved → triggerWaitlist() for the freed slot
```

---

## Flow 6: Notifications

```
User → Bell icon in navbar (shows unread count)
  → /ruang-rapat/default/notifications
  → All notifications listed, newest first
  → Unread items highlighted with orange border + "Baru" badge
  → Opening the page marks all as read
  → Waitlist notifications include "Klaim Slot Ini" button
```

---

## Flow 7: Admin Login

```
Admin → http://localhost:21080/site/login
  → Fill username + password
  → Server checks: user exists? password valid? role === 'admin'?
  → If not admin → logout + error "Akun Anda tidak memiliki akses sebagai admin"
  → If admin → redirect to dashboard
```

---

## Flow 8: Admin Dashboard

```
Admin → /ruang-rapat (Backend)
  → 4 stat cards: Total Rooms, Pending Reservations, Users, Total This Year
  → Monthly reservation bar chart (real data from DB)
  → Status donut chart (approved/pending/canceled)
  → Top 5 rooms horizontal bar chart + table
```

---

## Flow 9: Admin Approve/Reject

```
Admin → /booking/default/admin
  → Table of ALL reservations from all users
  → Columns: user name, priority badge, room, date, time, submitted at, status, actions
  → Users with requires_manual_approval show "⚠ Manual" badge
  → Suspended users show "🚫 Suspended" badge

Approve:
  → Click ✓ → ReservationService::approve(id)
  → Priority comparison + conflict resolution
  → Auto-cancel overlapping pending reservations
  → Notify approved user + canceled users
  → Redirect with success flash

Reject:
  → Click ✗ → Modal with rejection reason textarea
  → Submit → POST ReservationService::cancel(id, 'admin', reason)
  → Notify user with reason
  → If was approved → trigger waitlist
```

---

## Flow 10: Admin Manage Rooms

```
Admin → /ruang-rapat/default/rooms
  → Form at top: add/edit room (image upload, code, name, desc, capacity, contact, location + Google Maps)
  → Table below: list of all rooms with Edit/Delete buttons

Add:
  → Fill form → Submit → image uploaded to frontend/web/uploads/ → saved to DB

Edit:
  → Click "Edit" on table row → JS populates form with existing data
  → Form title changes to "Edit Ruang"
  → Hidden input Room[id] injected
  → Submit → controller detects ID → updates existing record

Delete:
  → Click "Hapus" → modal confirmation
  → POST → Room::softDelete()
  → Checks: any future approved bookings?
  → If yes → error "masih memiliki reservasi"
  → If no → is_active = false → room disappears from user views
```

---

## Flow 11: Admin Settings

```
Admin → /ruang-rapat/settings/index
  → Table of all booking rules (key, current value, description)
  → Edit inline → Submit → all values saved
  → Changes take effect immediately for new bookings
```

---

## Flow 12: Admin Strike Management

```
Admin → /ruang-rapat/strike/index
  → Table of all users with active strikes
  → Shows: username, email, priority, strike count, current status

View user strikes:
  → /ruang-rapat/strike/view?userId=X
  → Full strike history table
  → Manual strike issuance form (reason + notes)
  → "Clear All" button → removes strikes + suspension + manual_approval flag
```

---

## Flow 13: Waitlist

```
Join:
  User → Room detail → "Daftar Tunggu" → /ruang-rapat/default/waitlist-form?id=X
  → Fill date + time → Submit
  → Entry saved as status='waiting'

Trigger (automatic):
  → Any approved reservation gets canceled (by admin, user, or system)
  → ReservationService::triggerWaitlist() called
  → Finds best match (overlap scoring + FIFO)
  → Matched user → status='notified' + notification sent

Claim:
  User → Notifications page → sees "Slot tersedia" notification
  → Click "Klaim Slot Ini"
  → /ruang-rapat/default/claim-waitlist?id=X
  → ReservationService::claimWaitlist() → creates reservation from waitlist data
  → Waitlist entry → status='claimed'

Expire:
  → Cron checks notified entries older than waitlist_claim_days
  → Unclaimed → status='expired' → trigger next in queue
```
