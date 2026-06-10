# Booking Rules (Admin Configurable) — IPB Reserve

## What Are Booking Rules?

All booking policies are stored in the `booking_rules` table and can be changed by admin from the Settings page (`/ruang-rapat/settings/index`) without any code changes.

Rules are read at runtime via `BookingRule::get('rule_key')` or `BookingRule::getInt('rule_key')`.

---

## How to Access

**Admin panel** → Navbar → "Pengaturan" → Table of all rules with editable values.

---

## All Available Rules

### Time Constraints

| Rule | Default | Description |
|------|---------|-------------|
| `operating_hours_start` | `07:00` | Earliest time a booking can start |
| `operating_hours_end` | `21:00` | Latest time a booking can end |
| `min_duration_minutes` | `30` | Shortest allowed booking (minutes) |
| `max_duration_hours` | `4` | Longest allowed booking (hours) |
| `max_advance_days` | `30` | How far ahead users can book (days) |
| `buffer_minutes_between` | `15` | Required gap between consecutive bookings in the same room |

### User Limits

| Rule | Default | Description |
|------|---------|-------------|
| `max_pending_per_user` | `5` | Maximum number of unprocessed pending reservations per user |
| `cancellation_hours_before` | `24` | Minimum hours before booking that user-initiated cancellation is allowed |

### Automation

| Rule | Default | Description |
|------|---------|-------------|
| `pending_expire_hours` | `48` | Hours before booking time that an unreviewed pending reservation is auto-canceled by cron |
| `enable_noshow_strikes` | `0` | Set to `1` to enable automatic no-show strike issuance. Keep `0` until QR check-in is implemented |

### Strike System

| Rule | Default | Description |
|------|---------|-------------|
| `strike_suspend_days` | `3` | Days a user is suspended after Strike 2 |

### Waitlist

| Rule | Default | Description |
|------|---------|-------------|
| `waitlist_claim_days` | `3` | Days a waitlisted user has to claim an opened slot before it passes to the next person |

### Priority

| Rule | Default | Description |
|------|---------|-------------|
| `priority_boost_days` | `7` | Days a priority boost lasts after a user is bumped by a higher-priority user |

---

## Adding New Rules

1. Add a row to the `booking_rules` table (via migration or directly in the Settings page)
2. Use `BookingRule::get('your_new_key')` in your code
3. It will automatically appear in the admin Settings page

**Example migration:**
```php
$this->insert('{{%booking_rules}}', [
    'rule_key'    => 'max_bookings_per_day',
    'rule_value'  => '3',
    'description' => 'Maximum bookings a user can make per day',
]);
```

---

## Code Usage

```php
// Get as string
$startTime = BookingRule::get('operating_hours_start', '07:00');

// Get as integer
$maxHours = BookingRule::getInt('max_duration_hours', 4);

// Set (admin updates via Settings page)
BookingRule::set('max_advance_days', '60');
```
