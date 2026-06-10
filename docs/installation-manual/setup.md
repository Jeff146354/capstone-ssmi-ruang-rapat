# Setup & Installation Guide — IPB Reserve

## Prerequisites

- **Docker Desktop** (Windows/Mac) or **Docker Engine** (Linux)
- **Git**
- (Optional) **PHP 8.1+** if running without Docker

---

## Option 1: Docker (Recommended)

### 1. Clone the repository

```bash
git clone https://github.com/Jeff146354/capstone-ssmi-ruang-rapat.git
cd capstone-ssmi-ruang-rapat/apps/web
```

### 2. Create local config files

These files contain secrets (DB passwords, keys) and are gitignored. You need to create them manually.

**`common/config/main-local.php`:**
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
            'useFileTransport' => true, // Change to false + add SMTP for real emails
        ],
    ],
];
```

**`common/config/params-local.php`:**
```php
<?php
return [];
```

**`frontend/config/main-local.php`:**
```php
<?php
$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-random-string-here',
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

**`frontend/config/params-local.php`:**
```php
<?php
return [];
```

**`backend/config/main-local.php`:** (same structure as frontend, different cookie key)

**`backend/config/params-local.php`:**
```php
<?php
return [];
```

**`console/config/main-local.php`:**
```php
<?php
return [
    'bootstrap' => ['gii'],
    'modules' => ['gii' => 'yii\gii\Module'],
];
```

**`console/config/params-local.php`:**
```php
<?php
return [];
```

### 3. Create entry point files

**`frontend/web/index.php`:**
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

**`backend/web/index.php`:** (same but with backend paths)

**`yii`** (project root console script):
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

### 4. Start Docker

```bash
docker-compose up -d
```

Wait ~30 seconds for MySQL to initialize.

### 5. Install PHP dependencies

```bash
docker-compose exec frontend composer install --no-interaction
```

### 6. Make yii script executable + run migrations

```bash
docker-compose exec frontend chmod +x /app/yii
docker-compose exec frontend php /app/yii migrate --interactive=0
```

### 7. Access the app

| URL | What |
|-----|------|
| http://localhost:20080 | Frontend (user side) |
| http://localhost:21080 | Backend (admin panel) |

### 8. Create your first admin user

Register via frontend signup, then promote in DB:

```bash
docker-compose exec mysql mysql -u yii2advanced -psecret yii2advanced \
  -e "UPDATE user SET role='admin', priority=99, status=10 WHERE email='your@email.com';"
```

---

## Option 2: XAMPP (Local PHP)

1. Install XAMPP (includes Apache + MySQL + PHP)
2. Start Apache + MySQL from XAMPP panel
3. Create database `yii2advanced` in phpMyAdmin
4. Clone repo into `C:\xampp\htdocs\ssmi\`
5. Create local config files (same as above, but `host=localhost`, `username=root`, `password=`)
6. Open terminal in the project's `apps/web` folder
7. Run `composer install`
8. Run `php yii migrate`
9. Access:
   - Frontend: `http://localhost/ssmi/apps/web/frontend/web/`
   - Backend: `http://localhost/ssmi/apps/web/backend/web/`

---

## Production Deployment (Mini-PC)

See [Architecture Diagram](../architecture.md) for the full deployment picture.

1. Install Docker on mini-PC (Linux recommended)
2. Clone repo + create config files with production DB credentials
3. `docker-compose up -d`
4. Set up Nginx Proxy Manager for domain routing + SSL
5. Add cron jobs (see [Cron Jobs](../tech-docs/cron-jobs.md))
6. (Optional) Use Cloudflare Tunnel for public access without port forwarding

---

## Firebase Setup (Google Login)

1. Go to https://console.firebase.google.com
2. Open your project → Project Settings → Service Accounts
3. Generate a new private key (JSON file)
4. Place it at: `apps/web/common/firebase/your-project-firebase-adminsdk.json`
5. Update the filename reference in `frontend/controllers/SiteController.php` if needed

---

## Email Verification Setup

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

**Recommended services:** Mailtrap (dev), Gmail App Password (prod), Brevo (free tier).

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Could not open input file: yii" | Create the `yii` file in project root (see step 3) |
| Directory listing instead of app | `index.php` missing in `frontend/web/` or `backend/web/` |
| "Class not found" errors | Run `composer install` inside the container |
| DB connection refused | MySQL container might still be booting. Wait 30s and retry |
| "Table doesn't exist" | Run `php yii migrate --interactive=0` |
| Firebase JSON error | Place the service account JSON in `common/firebase/` |
| Login fails silently after signup | User status is 9 (inactive). Either configure SMTP for verification, or set `status=10` in DB |
