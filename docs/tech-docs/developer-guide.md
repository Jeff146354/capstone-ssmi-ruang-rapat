# Developer Guide — IPB Reserve

A guide for anyone continuing development on this project.

---

## Project Conventions

### Code Style
- PHP follows Yii2 conventions (PSR-4 autoloading, namespace per folder)
- View files: HTML with embedded PHP, inline `<style>` blocks per page
- CSS uses custom properties (`:root` variables defined in `frontend/views/layouts/main.php`)
- No external CSS preprocessor (SASS/LESS) — plain CSS

### Design System
- **Font:** Plus Jakarta Sans (loaded from Google Fonts in layout)
- **Primary color:** `#FF6B00` (orange)
- **Dark orange (links):** `#A04100`
- **Text:** `#151C27`
- **Muted text:** `#575E70`
- **Background:** `#F9F9FF`
- **Borders:** `#E2BFB0`
- **Light orange bg:** `#FFDBCC`
- **Footer bg:** `#E7EEFE`
- **Border radius:** 8–16px (cards use 14–16px, inputs use 8–10px)

### Naming
- Model files: PascalCase (`BookingRule.php`, `ReservationWaitlist.php`)
- Controller actions: camelCase (`actionFindAvailableRooms`)
- View files: kebab-case (`daftar-ruangan.php`, `ruangan-detail.php`)
- Migrations: timestamped (`m250629_000001_description.php`)
- DB tables: snake_case (`user_strikes`, `reservation_waitlist`, `booking_rules`)

---

## How to Enable Email Verification (Currently Disabled)

Email verification is fully built but turned off. To enable it:

1. **Configure SMTP** in `common/config/main-local.php`:
   ```php
   'mailer' => [
       'class'            => \yii\symfonymailer\Mailer::class,
       'viewPath'         => '@common/mail',
       'useFileTransport' => false,  // ← change from true to false
       'transport'        => [
           'dsn' => 'smtp://user:pass@smtp.example.com:587',
       ],
   ],
   ```

2. **Edit `frontend/models/SignupForm.php`** — the file has clear comments showing exactly what to uncomment:
   - Change `$user->status` from `STATUS_ACTIVE` to `STATUS_INACTIVE`
   - Uncomment `$user->generateEmailVerificationToken()`
   - Change the return line to include `$this->sendEmail($user)`

3. **Edit `frontend/controllers/SiteController.php`** — in `actionSignup()`:
   - Remove the `Yii::$app->user->login($user)` line
   - Change the flash message to "Check your email"
   - Redirect to login page instead of home

**Everything else is already wired:**
- Email template: `common/mail/emailVerify-html.php`
- Verify action: `SiteController::actionVerifyEmail($token)`
- Resend action: `SiteController::actionResendVerificationEmail()`

---

### Example: Adding a "Room Facilities" feature

1. **Migration** — Create a new table or column:
   ```bash
   docker-compose exec frontend php /app/yii migrate/create add_facilities_table
   ```
   Edit the generated file in `console/migrations/`, then run:
   ```bash
   docker-compose exec frontend php /app/yii migrate
   ```

2. **Model** — Create `common/models/Facility.php` with ActiveRecord rules, labels, and relations.

3. **Controller action** — Add to existing controller or create new one:
   - Frontend: `frontend/modules/ruangrapat/controllers/`
   - Backend: `backend/modules/ruangrapat/controllers/`

4. **View** — Create `.php` view file in the matching `views/` folder.

5. **Service layer** (if complex logic) — Add methods to `common/services/ReservationService.php` or create a new service.

---

## How to Add a New Admin-Configurable Rule

1. Insert into `booking_rules` table (via migration or admin Settings page)
2. Use `BookingRule::get('your_key')` or `BookingRule::getInt('your_key')` in your code
3. It automatically appears in admin Settings page

---

## How to Add a New Notification Type

1. Add a constant to `common/models/Notification.php`:
   ```php
   const TYPE_YOUR_NEW_TYPE = 'your_new_type';
   ```

2. Add it to the `optsType()` array in the same file.

3. Add it to the migration's ENUM (or alter the column if DB already exists).

4. Call `Notification::send($userId, Notification::TYPE_YOUR_NEW_TYPE, 'message')` where needed.

5. Add icon/color mapping in `frontend/modules/ruangrapat/views/default/notifications.php`.

---

## Common Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Restart after code changes (PHP changes are instant due to volume mount)
# Only restart if you change Apache config or Dockerfile:
docker-compose restart frontend backend

# Run migrations
docker-compose exec frontend php /app/yii migrate --interactive=0

# Create a new migration
docker-compose exec frontend php /app/yii migrate/create your_migration_name

# Run composer install (after adding packages to composer.json)
docker-compose exec frontend composer install --no-interaction

# Access MySQL CLI
docker-compose exec mysql mysql -u yii2advanced -psecret yii2advanced

# View PHP error logs
docker-compose exec frontend tail -f /app/frontend/runtime/logs/app.log

# Run cron commands manually
docker-compose exec frontend php /app/yii reservation-cron/expire-pending
docker-compose exec frontend php /app/yii reservation-cron/issue-no-show-strikes
docker-compose exec frontend php /app/yii reservation-cron/expire-waitlist
```

---

## File Upload Paths

| Type | Upload path | Stored in DB as |
|------|-------------|-----------------|
| Room image | `frontend/web/uploads/{filename}` | `room.fr_img` = filename |
| Surat peminjaman | `frontend/web/uploads/documents/{filename}` | `reservations.document` = `documents/filename` |

Images are served via `$room->imageUrl` which returns `http://localhost:20080/uploads/filename`.

---

## Access Control

| Area | Who can access | How it's enforced |
|------|---------------|-------------------|
| Frontend (all pages) | Any user (some pages need login) | Yii2 AccessControl on specific actions |
| Backend (all pages) | Admin only | `BaseAdminModuleController` checks `role === 'admin'` |
| Approve/Cancel reservation | Admin only | AccessControl with `matchCallback` in ReservationController |
| Edit/Delete own reservation | Owner only | `$model->user_id !== Yii::$app->user->id` → ForbiddenHttpException |

---

## Testing Changes

Since there's no automated test suite running yet, test manually:

1. **After model changes:** Try creating/editing/deleting a record through the UI
2. **After validation changes:** Submit invalid data and verify error messages appear
3. **After view changes:** Just refresh the page (PHP files auto-reload, no build step)
4. **After migration changes:** Run `migrate` and check the DB schema
5. **After service layer changes:** Test the full flow (e.g., book → approve → check notifications)

---

## Git Workflow

```bash
# Before starting work
git pull

# After finishing
git add -A
git commit -m "feat: description of what you did"
git push
```

Branch naming (if using branches):
- `feature/add-qr-checkin`
- `fix/room-image-upload`
- `docs/update-setup-guide`

Currently working directly on `master` for simplicity.
