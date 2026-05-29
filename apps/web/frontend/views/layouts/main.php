<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()], 'csrf-token');

// Unread notification count
$unreadCount = (!Yii::$app->user->isGuest)
    ? \common\models\Notification::countUnread(Yii::$app->user->id)
    : 0;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — IPB Reserve</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->head() ?>
    <style>
        :root {
            --ipb-orange:      #FF6B00;
            --ipb-orange-dark: #A04100;
            --ipb-orange-light:#FFDBCC;
            --ipb-text:        #151C27;
            --ipb-muted:       #575E70;
            --ipb-border:      #E2BFB0;
            --ipb-bg:          #F9F9FF;
            --ipb-footer-bg:   #E7EEFE;
        }
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--ipb-bg); color: var(--ipb-text); }

        /* ── Navbar ── */
        .ipb-navbar {
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
            padding: 16px 24px;
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
        }
        .ipb-navbar .brand {
            color: var(--ipb-orange-dark);
            font-size: 20px; font-weight: 700; text-decoration: none;
        }
        .ipb-navbar .nav-link-item {
            color: var(--ipb-muted);
            font-size: 16px; font-weight: 600; text-decoration: none;
            padding: 0 16px;
            transition: color .15s;
        }
        .ipb-navbar .nav-link-item:hover,
        .ipb-navbar .nav-link-item.active { color: var(--ipb-orange-dark); }
        .ipb-navbar .nav-link-item.active {
            border-bottom: 2px solid var(--ipb-orange-dark);
            padding-bottom: 2px;
        }
        .ipb-navbar .btn-signin {
            color: var(--ipb-orange-dark);
            font-size: 16px; font-weight: 700; text-decoration: none;
            border-bottom: 2px solid var(--ipb-orange-dark);
            padding-bottom: 2px;
        }
        .ipb-navbar .btn-orange {
            background: var(--ipb-orange);
            color: #fff; font-size: 14px; font-weight: 700;
            border: none; border-radius: 8px;
            padding: 8px 20px; text-decoration: none;
            transition: background .15s;
        }
        .ipb-navbar .btn-orange:hover { background: var(--ipb-orange-dark); color: #fff; }
        .ipb-navbar .notif-badge {
            background: #ef4444; color: #fff;
            font-size: 10px; font-weight: 700;
            border-radius: 9999px; padding: 1px 5px;
            vertical-align: top; margin-left: 2px;
        }

        /* ── Footer ── */
        .ipb-footer {
            background: var(--ipb-footer-bg);
            padding: 32px 24px;
        }
        .ipb-footer .brand { color: var(--ipb-orange-dark); font-size: 20px; font-weight: 700; }
        .ipb-footer .tagline { color: #5A4136; font-size: 14px; font-weight: 400; line-height: 20px; }
        .ipb-footer .footer-heading { color: var(--ipb-text); font-size: 16px; font-weight: 600; }
        .ipb-footer a { color: #5A4136; font-size: 12px; font-weight: 500; text-decoration: none; display: block; margin-top: 8px; }
        .ipb-footer a:hover { color: var(--ipb-orange-dark); }
        .ipb-footer .copyright { color: #5A4136; font-size: 12px; font-weight: 500; }

        /* ── Page body offset for fixed navbar ── */
        .page-body { padding-top: 72px; }

        /* ── Alert ── */
        .alert { border-radius: 8px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<?php $this->beginBody() ?>

<!-- ── Navbar ── -->
<nav class="ipb-navbar d-flex align-items-center justify-content-between">
    <!-- Brand -->
    <a href="<?= Yii::$app->homeUrl ?>" class="brand d-flex align-items-center gap-2">
        <span>IPB Reserve</span>
    </a>

    <!-- Nav links -->
    <div class="d-none d-md-flex align-items-center">
        <?php
        $currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $navLinks = [
            'Home'         => ['/site/index'],
            'Browse Rooms' => ['/ruang-rapat/default/daftar-ruangan'],
            'Reservations' => ['/ruang-rapat/default/riwayat-peminjaman'],
            'About'        => ['/site/about'],
        ];
        foreach ($navLinks as $label => $url):
            $isActive = Yii::$app->request->url === \yii\helpers\Url::to($url);
        ?>
            <a href="<?= \yii\helpers\Url::to($url) ?>"
               class="nav-link-item <?= $isActive ? 'active' : '' ?>">
                <?= $label ?>
            </a>
        <?php endforeach ?>
    </div>

    <!-- Right side -->
    <div class="d-flex align-items-center gap-3">
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="btn-signin">Sign In</a>
            <a href="<?= \yii\helpers\Url::to(['/site/signup']) ?>" class="btn-orange">Sign Up</a>
        <?php else: ?>
            <!-- Notification bell -->
            <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/default/notifications']) ?>"
               class="nav-link-item position-relative" title="Notifikasi">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="notif-badge"><?= $unreadCount ?></span>
                <?php endif ?>
            </a>

            <!-- User dropdown -->
            <div class="dropdown">
                <button class="btn-orange dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user me-1"></i>
                    <?= Html::encode(Yii::$app->user->identity->username) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/ruang-rapat/default/riwayat-peminjaman']) ?>">
                        <i class="fas fa-calendar-alt me-2"></i>Riwayat Peminjaman
                    </a></li>
                    <li><a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/ruang-rapat/default/notifications']) ?>">
                        <i class="fas fa-bell me-2"></i>Notifikasi
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger ms-1"><?= $unreadCount ?></span>
                        <?php endif ?>
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'm-0']) ?>
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        <?= Html::endForm() ?>
                    </li>
                </ul>
            </div>
        <?php endif ?>
    </div>
</nav>

<!-- ── Main content ── -->
<main class="flex-grow-1 page-body">
    <div class="container-fluid px-0">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<!-- ── Footer ── -->
<footer class="ipb-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="brand mb-2">IPB Reserve</div>
                <p class="tagline">
                    Memberikan solusi reservasi fasilitas terbaik untuk mendukung
                    produktivitas dan kolaborasi civitas akademika IPB University.
                </p>
            </div>
            <div class="col-md-2">
                <div class="footer-heading mb-2">Layanan</div>
                <a href="#">Pesan Ruang</a>
                <a href="#">Manajemen Aset</a>
                <a href="#">Sewa Alat</a>
            </div>
            <div class="col-md-2">
                <div class="footer-heading mb-2">Pusat Bantuan</div>
                <a href="#">Panduan Pengguna</a>
                <a href="#">FAQ</a>
                <a href="#">Hubungi Kami</a>
            </div>
            <div class="col-md-2">
                <div class="footer-heading mb-2">Legalitas</div>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
            <div class="col-md-2">
                <div class="footer-heading mb-2">Sosial Media</div>
                <a href="#"><i class="fab fa-instagram me-1"></i>Instagram</a>
                <a href="#"><i class="fab fa-twitter me-1"></i>Twitter</a>
            </div>
        </div>
        <hr style="border-color: var(--ipb-border); margin-top: 24px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="copyright">© 2024 IPB University Facility Management. All rights reserved.</span>
            <span class="copyright">Powered by IPB IT Center</span>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
