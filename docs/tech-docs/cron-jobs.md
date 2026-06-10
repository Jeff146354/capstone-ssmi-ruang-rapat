# Cron Jobs — IPB Reserve

## Overview

The system has 3 scheduled tasks that need to run periodically. They live in:

```
console/controllers/ReservationCronController.php
```

---

## Available Commands

| Command | Schedule | What it does |
|---------|----------|--------------|
| `php yii reservation-cron/expire-pending` | Every hour | Auto-cancels pending reservations too close to their booking time |
| `php yii reservation-cron/issue-no-show-strikes` | Daily (23:00) | Issues strikes for approved reservations with no check-in |
| `php yii reservation-cron/expire-waitlist` | Every hour | Expires unclaimed waitlist slots and passes to next person |

---

## 1. Expire Pending

**What:** Finds all pending reservations where the booking starts within `pending_expire_hours` (48h default) from now. Cancels them with `rejected_by = 'system'` and notifies the user.

**Why:** Prevents zombie pending reservations from blocking slots that admin forgot to review.

**Also:** Triggers waitlist for the freed slot.

---

## 2. Issue No-Show Strikes

**What:** Finds all approved reservations from yesterday (or earlier) where `checked_in_at IS NULL`. Issues a strike to the user.

**Guard:** Only runs if `BookingRule::get('enable_noshow_strikes') === '1'`. Keep this **disabled** (`0`) until QR check-in is implemented — otherwise every user gets a strike since nobody can check in yet.

**Enable via:** Admin → Pengaturan → Set `enable_noshow_strikes` to `1`.

---

## 3. Expire Waitlist

**What:** Finds all waitlist entries with `status = 'notified'` where the notification was sent more than `waitlist_claim_days` (3 days) ago. Marks them as `expired` and triggers `triggerWaitlist()` again to pass the slot to the next person in queue.

---

## How to Run (Docker)

### One-time (testing):
```bash
docker-compose exec frontend php /app/yii reservation-cron/expire-pending
docker-compose exec frontend php /app/yii reservation-cron/issue-no-show-strikes
docker-compose exec frontend php /app/yii reservation-cron/expire-waitlist
```

### Scheduled (Linux cron on mini-PC):
```bash
crontab -e
```
Add:
```
0 * * * * docker-compose -f /path/to/apps/web/docker-compose.yml exec -T frontend php /app/yii reservation-cron/expire-pending
0 23 * * * docker-compose -f /path/to/apps/web/docker-compose.yml exec -T frontend php /app/yii reservation-cron/issue-no-show-strikes
30 * * * * docker-compose -f /path/to/apps/web/docker-compose.yml exec -T frontend php /app/yii reservation-cron/expire-waitlist
```

### Scheduled (Windows Task Scheduler):
1. Create a new task
2. Action: Start a program
3. Program: `docker-compose`
4. Arguments: `-f D:\path\to\apps\web\docker-compose.yml exec -T frontend php /app/yii reservation-cron/expire-pending`
5. Schedule: Every 1 hour

---

## Logs

Each cron outputs a summary to stdout:
```
Expired 3 pending reservations.
Issued 1 no-show strikes.
Expired 0 waitlist claim windows and passed to next in line.
```

In production, redirect to a log file:
```bash
docker-compose exec -T frontend php /app/yii reservation-cron/expire-pending >> /var/log/ipb-reserve-cron.log 2>&1
```
