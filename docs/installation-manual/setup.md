# Setup & Installation Guide — IPB Reserve

## Prerequisites

- **Docker Desktop** (Windows/Mac) or **Docker Engine** (Linux)
- **Git**

---

## Step-by-Step: Running the App with Docker

### 1. Clone the repository

```bash
git clone https://github.com/Jeff146354/capstone-ssmi-ruang-rapat.git
cd capstone-ssmi-ruang-rapat/apps/web
```

### 2. Create local config files

These files contain secrets and are gitignored. Create them manually.

#### `common/config/main-local.php`
```php
<?php
return [
    'components' => [
        'db' => [
            'class'    => \yii\db\Connection::class,
            'dsn'      => 'mysql:host=mysql;dbname=yii2advanced',
            'username' => 'yii2advanced',
            'password' => 'secret',
            'charset'  => 'utf8',
        ],
        'mailer' => [
            'class'            => \yii\symfonymailer\Mailer::class,
            'viewPath'         => '@common/mail',
            'useFileTransport' => true,
        ],
    ],
];
```

#### `common/config/params-local.php`
```php
<?php
return [];
```

#### `frontend/config/main-local.php`
```php
<?php
$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-random-string-frontend',
        ],
    ],
];
if (!YII_ENV_TEST) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => \yii\debug\Module::class];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = ['class' => \yii\gii\Module::class];
}
return $config;
```

#### `frontend/config/params-local.php`
```php
<?php
return [];
```

#### `backend/config/main-local.php`
```php
<?php
$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-random-string-backend',
        ],
    ],
];
if (!YII_ENV_TEST) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => \yii\debug\Module::class];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = ['class' => \yii\gii\Module::class];
}
return $config;
```

#### `backend/config/params-local.php`
```php
<?php
return [];
```

#### `console/config/main-local.php`
```php
<?php
return [
    'bootstrap' => ['gii'],
    'modules' => ['gii' => 'yii\gii\Module'],
];
```

#### `console/config/params-local.php`
```php
<?php
return [];
```

### 3. Create entry-point files

These are also gitignored (created by Yii's `init` script which requires PHP locally).

#### `frontend/web/index.php`
```php
<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);
(new yii\web\Application($config))->run();
```

#### `backend/web/index.php`
Same as above but replace `frontend` paths with `backend`.

#### `yii` (project root — console entry point)
```php
#!/usr/bin/env php
<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/console/config/bootstrap.php';
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/console/config/main.php',
    require __DIR__ . '/console/config/main-local.php'
);
$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
```

### 4. Start Docker Desktop

Open Docker Desktop and wait until it shows "Engine running" (green status).

### 5. Start the containers

```bash
docker-compose up -d
```

Wait ~30 seconds for MySQL to initialize.

### 6. Install PHP dependencies

```bash
docker-compose exec frontend composer install --no-interaction
```

This downloads all libraries into the vendor/ folder inside the container.

### 7. Make the yii script executable and run migrations

```bash
docker-compose exec frontend chmod +x /app/yii
docker-compose exec frontend php /app/yii migrate --interactive=0
```

This creates all database tables (user, room, reservations, booking_rules, notifications, etc.)

### 8. Access the app

| URL | What |
|-----|------|
| http://localhost:20080 | Frontend (user side) |
| http://localhost:21080 | Backend (admin panel) |

### 9. Create your first user

**Option A — Sign up with Google:**
Click "Daftar dengan Google" on the signup page (requires Firebase setup, see below).

**Option B — Regular signup:**
Go to http://localhost:20080/site/signup and register. Then activate the account manually:

```bash
docker-compose exec mysql mysql -u yii2advanced -psecret yii2advanced -e "UPDATE user SET status=10 WHERE email='your@email.com';"
```

### 10. Promote a user to admin

```bash
docker-compose exec mysql mysql -u yii2advanced -psecret yii2advanced -e "UPDATE user SET role='admin', priority=99 WHERE email='your@email.com';"
```

Then login to backend at http://localhost:21080/site/login.

---

## Firebase Setup (Google Login)

Google login is optional but already integrated.

### 1. Get access to the Firebase project

Either use the existing project (`kepston17-8c88b`) or create your own at https://console.firebase.google.com.

### 2. Enable Google Sign-In

Firebase Console → Authentication → Sign-in method → Google → Enable.

### 3. Download service account key

Firebase Console → Project Settings → Service Accounts → Generate new private key.

### 4. Place the JSON file

Put it in:
```
apps/web/common/firebase/
```

### 5. Update the filename in code

Open `apps/web/frontend/controllers/SiteController.php` and ensure the filename in `withServiceAccount(...)` matches your downloaded JSON filename. There are 2 places to update (in `actionFirebaseLogin` and `actionFirebaseSignup`).

### 6. Update firebaseConfig in views (if using your own project)

Update the `firebaseConfig` object in:
- `frontend/views/site/login.php`
- `frontend/views/site/signup.php`

---

## Email Verification (Optional)

By default, emails are saved to files (not sent). To enable real email sending:

Update `common/config/main-local.php`:
```php
'mailer' => [
    'class'            => \yii\symfonymailer\Mailer::class,
    'viewPath'         => '@common/mail',
    'useFileTransport' => false,
    'transport'        => [
        'dsn' => 'smtp://username:password@smtp.example.com:587',
    ],
],
```

Recommended: Use Mailtrap.io for development (catches emails in a fake inbox).

---

## Stopping and Restarting

```bash
# Stop everything
docker-compose down

# Start again
docker-compose up -d

# Restart after code changes
docker-compose restart frontend backend
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Could not open input file: yii" | Create the `yii` file in project root (see step 3) |
| Directory listing (Index of /) | `index.php` missing in `frontend/web/` or `backend/web/` |
| "Class not found" errors | Run `composer install` inside the container |
| DB connection refused | MySQL container still booting. Wait 30s and retry |
| "Table doesn't exist" | Run `php /app/yii migrate --interactive=0` |
| Firebase JSON error | Service account JSON missing or wrong filename in code |
| Login fails after signup | User status is 9 (inactive). Set `status=10` in DB or configure SMTP |
| "Docker daemon not running" | Open Docker Desktop and wait for green "Engine running" status |
| `version` is obsolete warning | Harmless. Docker Compose v2 no longer needs the `version` field |

---

## What's NOT set up yet

- Production deployment (VPS/mini-PC) — not yet deployed
- QR Check-in system — UI placeholder only, no real QR generation
- Real SMTP email sending — using file transport in dev
- Cron jobs — commands exist but not scheduled yet
