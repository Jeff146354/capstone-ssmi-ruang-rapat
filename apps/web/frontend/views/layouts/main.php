<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
$this->registerMetaTag(['name' => 'csrf-token', 'content' => Yii::$app->request->getCsrfToken()], 'csrf-token');

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
            padding: 14px 24px;
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            display: flex; align-items: center; justify-content: space-between;
        }
        .ipb-navbar .brand {
            color: var(--ipb-orange-dark);
            font-size: 20px; font-weight: 700; text-decoration: none;
        }
        .ipb-navbar .nav-links {
            display: flex; align-items: center; gap: 0;
        }
        .ipb-navbar .nav-link-item {
            color: var(--ipb-muted);
            font-size: 15px; font-weight: 600; text-decoration: none;
            padding: 6px 16px; border-radius: 6px;
            transition: color .15s, background .15s;
        }
        .ipb-navbar .nav-link-item:hover { color: var(--ipb-orange-dark); background: rgba(255,107,0,.05); }
        .ipb-navbar .nav-link-item.active {
            color: var(--ipb-orange-dark);
            border-bottom: 2px solid var(--ipb-orange-dark);
            border-radius: 0;
        }
        .ipb-navbar .btn-signin {
            color: var(--ipb-orange-dark);
            font-size: 15px; font-weight: 700; text-decoration: none;
            padding: 6px 12px;
        }
        .ipb-navbar .btn-orange {
            background: var(--ipb-orange);
            color: #fff; font-size: 13px; font-weight: 700;
            border: none; border-radius: 8px;
            padding: 8px 18px; text-decoration: none;
            transition: background .15s;
        }
        .ipb-navbar .btn-orange:hover { background: var(--ipb-orange-dark); color: #fff; }
        .ipb-navbar .notif-badge {
            background: #ef4444; color: #fff;
            font-size: 10px; font-weight: 700;
            border-radius: 9999px; padding: 1px 5px;
            position: absolute; top: -4px; right: -6px;
        }
        .ipb-navbar .right-side {
            display: flex; align-items: center; gap: 12px;
        }

        /* ── Hamburger button ── */
        .hamburger-btn {
            display: none;
            background: none; border: none; cursor: pointer;
            width: 36px; height: 36px;
            flex-direction: column; align-items: center; justify-content: center; gap: 5px;
            border-radius: 6px; padding: 6px;
            transition: background .15s;
        }
        .hamburger-btn:hover { background: rgba(255,107,0,.08); }
        .hamburger-btn span {
            display: block; width: 22px; height: 2.5px;
            background: var(--ipb-text); border-radius: 2px;
            transition: transform .2s, opacity .2s;
        }
        .hamburger-btn.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hamburger-btn.open span:nth-child(2) { opacity: 0; }
        .hamburger-btn.open span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

        /* ── Mobile menu ── */
        .mobile-menu {
            display: none;
            position: fixed; top: 64px; left: 0; right: 0; bottom: 0;
            background: #fff; z-index: 999;
            padding: 24px;
            flex-direction: column; gap: 8px;
            overflow-y: auto;
            animation: slideDown .2s ease;
        }
        .mobile-menu.show { display: flex; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .mobile-menu .mobile-nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; border-radius: 10px;
            color: var(--ipb-text); font-size: 16px; font-weight: 600;
            text-decoration: none; transition: background .15s;
        }
        .mobile-menu .mobile-nav-item:hover { background: rgba(255,107,0,.06); }
        .mobile-menu .mobile-nav-item.active { background: rgba(255,107,0,.1); color: var(--ipb-orange-dark); }
        .mobile-menu .mobile-nav-item i { width: 20px; text-align: center; color: var(--ipb-orange); }
        .mobile-menu .mobile-divider { height: 1px; background: #F0E8E5; margin: 12px 0; }
        .mobile-menu .mobile-auth-btns { display: flex; flex-direction: column; gap: 10px; margin-top: auto; }
        .mobile-menu .btn-mobile-signin {
            display: block; text-align: center;
            padding: 14px; border: 1.5px solid var(--ipb-orange);
            border-radius: 10px; color: var(--ipb-orange-dark);
            font-size: 15px; font-weight: 700; text-decoration: none;
        }
        .mobile-menu .btn-mobile-signup {
            display: block; text-align: center;
            padding: 14px; background: var(--ipb-orange);
            border-radius: 10px; color: #fff;
            font-size: 15px; font-weight: 700; text-decoration: none;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .ipb-navbar .nav-links { display: none; }
            .ipb-navbar .right-side .btn-signin,
            .ipb-navbar .right-side .btn-orange:not(.dropdown-toggle) { display: none; }
            .hamburger-btn { display: flex; }
            .page-body { padding-top: 64px; }
            .ipb-footer .row { text-align: center; }
            .ipb-footer .row > div { margin-bottom: 16px; }
        }
        @media (min-width: 769px) {
            .mobile-menu { display: none !important; }
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

        /* ── Page body ── */
        .page-body { padding-top: 72px; }
        .alert { border-radius: 8px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<?php $this->beginBody() ?>

<?php
$navLinks = [
    ['label' => 'Home',         'icon' => 'fas fa-home',        'url' => ['/site/index']],
    ['label' => 'Browse Rooms', 'icon' => 'fas fa-door-open',   'url' => ['/ruang-rapat/default/daftar-ruangan']],
    ['label' => 'Reservations', 'icon' => 'fas fa-calendar-alt','url' => ['/ruang-rapat/default/riwayat-peminjaman']],
    ['label' => 'About',        'icon' => 'fas fa-info-circle', 'url' => ['/site/about']],
];
?>

<!-- ── Navbar ── -->
<nav class="ipb-navbar">
    <!-- Brand -->
    <a href="<?= Yii::$app->homeUrl ?>" class="brand">IPB Reserve</a>

    <!-- Desktop nav links -->
    <div class="nav-links">
        <?php foreach ($navLinks as $link):
            $isActive = Yii::$app->request->url === \yii\helpers\Url::to($link['url']);
        ?>
            <a href="<?= \yii\helpers\Url::to($link['url']) ?>"
               class="nav-link-item <?= $isActive ? 'active' : '' ?>">
                <?= $link['label'] ?>
            </a>
        <?php endforeach ?>
    </div>

    <!-- Right side -->
    <div class="right-side">
        <?php if (Yii::$app->user->isGuest): ?>
            <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="btn-signin">Sign In</a>
            <a href="<?= \yii\helpers\Url::to(['/site/signup']) ?>" class="btn-orange">Sign Up</a>
        <?php else: ?>
            <!-- Notification bell -->
            <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/default/notifications']) ?>"
               style="position:relative; color:var(--ipb-muted); font-size:18px; text-decoration:none;" title="Notifikasi">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="notif-badge"><?= $unreadCount ?></span>
                <?php endif ?>
            </a>

            <!-- User dropdown (desktop) -->
            <div class="dropdown d-none d-md-block">
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
                        <?php if ($unreadCount > 0): ?><span class="badge bg-danger ms-1"><?= $unreadCount ?></span><?php endif ?>
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

        <!-- Hamburger (mobile only) -->
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- ── Mobile menu ── -->
<div class="mobile-menu" id="mobileMenu">
    <?php foreach ($navLinks as $link):
        $isActive = Yii::$app->request->url === \yii\helpers\Url::to($link['url']);
    ?>
        <a href="<?= \yii\helpers\Url::to($link['url']) ?>"
           class="mobile-nav-item <?= $isActive ? 'active' : '' ?>">
            <i class="<?= $link['icon'] ?>"></i> <?= $link['label'] ?>
        </a>
    <?php endforeach ?>

    <?php if (!Yii::$app->user->isGuest): ?>
        <div class="mobile-divider"></div>
        <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/default/riwayat-peminjaman']) ?>" class="mobile-nav-item">
            <i class="fas fa-calendar-check"></i> Riwayat Peminjaman
        </a>
        <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/default/notifications']) ?>" class="mobile-nav-item">
            <i class="fas fa-bell"></i> Notifikasi
            <?php if ($unreadCount > 0): ?>
                <span class="badge bg-danger ms-1"><?= $unreadCount ?></span>
            <?php endif ?>
        </a>
        <div class="mobile-divider"></div>
        <div class="mobile-auth-btns">
            <?= Html::beginForm(['/site/logout'], 'post') ?>
                <button type="submit" class="btn-mobile-signin" style="color:#ef4444; border-color:#ef4444;">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            <?= Html::endForm() ?>
        </div>
    <?php else: ?>
        <div class="mobile-divider"></div>
        <div class="mobile-auth-btns">
            <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="btn-mobile-signin">Sign In</a>
            <a href="<?= \yii\helpers\Url::to(['/site/signup']) ?>" class="btn-mobile-signup">Sign Up</a>
        </div>
    <?php endif ?>
</div>

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
            <div class="col-md-4 col-12">
                <div class="brand mb-2">IPB Reserve</div>
                <p class="tagline">
                    Memberikan solusi reservasi fasilitas terbaik untuk mendukung
                    produktivitas dan kolaborasi civitas akademika IPB University.
                </p>
            </div>
            <div class="col-md-2 col-6">
                <div class="footer-heading mb-2">Layanan</div>
                <a href="#">Pesan Ruang</a>
                <a href="#">Manajemen Aset</a>
                <a href="#">Sewa Alat</a>
            </div>
            <div class="col-md-2 col-6">
                <div class="footer-heading mb-2">Pusat Bantuan</div>
                <a href="#">Panduan Pengguna</a>
                <a href="#">FAQ</a>
                <a href="#">Hubungi Kami</a>
            </div>
            <div class="col-md-2 col-6">
                <div class="footer-heading mb-2">Legalitas</div>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
            <div class="col-md-2 col-6">
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

<!-- ── Hamburger toggle script ── -->
<script>
document.getElementById('hamburgerBtn').addEventListener('click', function () {
    this.classList.toggle('open');
    document.getElementById('mobileMenu').classList.toggle('show');
    document.body.style.overflow = this.classList.contains('open') ? 'hidden' : '';
});
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
