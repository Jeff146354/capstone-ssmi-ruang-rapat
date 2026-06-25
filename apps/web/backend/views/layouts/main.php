<?php
/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> — Admin IPB Reserve</title>
    <?php $this->head() ?>
    <?php if (isset($this->params['extraHead'])) echo $this->params['extraHead']; ?>
    <style>
    :root {
        --admin-dark: #151C27;
        --admin-sidebar: #1a2332;
        --admin-orange: #FF6B00;
        --admin-orange-dark: #A04100;
        --admin-text: #E8ECF2;
        --admin-muted: #8A94A6;
        --admin-bg: #F4F6FA;
        --admin-border: #2D3848;
        --admin-card: #ffffff;
    }
    * { font-family: 'Plus Jakarta Sans', sans-serif; }
    body { background: var(--admin-bg); margin: 0; }

    /* ── Sidebar ── */
    .admin-sidebar {
        position: fixed; top: 0; left: 0; bottom: 0;
        width: 260px; background: var(--admin-dark);
        padding: 24px 0; overflow-y: auto;
        z-index: 1000; display: flex; flex-direction: column;
    }
    .sidebar-brand {
        padding: 0 24px 24px;
        border-bottom: 1px solid var(--admin-border);
        margin-bottom: 16px;
    }
    .sidebar-brand a {
        color: var(--admin-orange); font-size: 18px; font-weight: 700;
        text-decoration: none; display: flex; align-items: center; gap: 10px;
    }
    .sidebar-brand small { color: var(--admin-muted); font-size: 11px; font-weight: 500; display: block; margin-top: 2px; }
    .sidebar-nav { flex: 1; padding: 0 12px; }
    .sidebar-nav a {
        display: flex; align-items: center; gap: 12px;
        padding: 11px 16px; border-radius: 8px;
        color: var(--admin-muted); font-size: 14px; font-weight: 500;
        text-decoration: none; margin-bottom: 4px;
        transition: background .15s, color .15s;
    }
    .sidebar-nav a:hover { background: rgba(255,255,255,.05); color: var(--admin-text); }
    .sidebar-nav a.active { background: rgba(255,107,0,.12); color: var(--admin-orange); }
    .sidebar-nav a i { width: 20px; text-align: center; font-size: 15px; }
    .sidebar-nav .section-label {
        color: var(--admin-muted); font-size: 11px; font-weight: 700;
        letter-spacing: 1px; text-transform: uppercase;
        padding: 16px 16px 6px; margin-top: 8px;
    }
    .sidebar-footer {
        padding: 16px 16px; border-top: 1px solid var(--admin-border);
        margin-top: auto;
    }
    .sidebar-footer .user-info {
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 12px;
    }
    .sidebar-footer .user-avatar {
        width: 36px; height: 36px; border-radius: 8px;
        background: var(--admin-orange); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700;
    }
    .sidebar-footer .user-name { color: var(--admin-text); font-size: 13px; font-weight: 600; }
    .sidebar-footer .user-role { color: var(--admin-muted); font-size: 11px; }
    .sidebar-footer .btn-logout {
        width: 100%; padding: 8px;
        background: rgba(239,68,68,.1); color: #ef4444;
        border: 1px solid rgba(239,68,68,.2); border-radius: 6px;
        font-size: 12px; font-weight: 600; cursor: pointer;
        text-align: center; transition: background .15s;
    }
    .sidebar-footer .btn-logout:hover { background: rgba(239,68,68,.2); }

    /* ── Main content area ── */
    .admin-main {
        margin-left: 260px; padding: 32px;
        min-height: 100vh;
    }

    /* ── Cards ── */
    .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.04); }

    /* ── Alerts ── */
    .alert { border-radius: 10px; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<?php if (!Yii::$app->user->isGuest): ?>
<!-- ── Sidebar ── -->
<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= \yii\helpers\Url::to(['/ruang-rapat']) ?>">
            <i class="fas fa-building"></i>
            <div>IPB Reserve<small>Admin Panel</small></div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <?php
        $route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        $module = Yii::$app->controller->module->id ?? '';

        $links = [
            ['label' => 'Dashboard', 'icon' => 'fas fa-chart-pie', 'url' => ['/ruang-rapat'], 'match' => 'default/index'],
            ['label' => 'Kelola Ruang', 'icon' => 'fas fa-door-open', 'url' => ['/ruang-rapat/default/rooms'], 'match' => 'default/rooms'],
        ];
        $bookingLinks = [
            ['label' => 'Semua Reservasi', 'icon' => 'fas fa-calendar-check', 'url' => ['/booking/default/admin'], 'match' => 'default/admin'],
            ['label' => 'Jadwal Grid', 'icon' => 'fas fa-th', 'url' => ['/booking/default/schedule-grid'], 'match' => 'default/schedule-grid'],
        ];
        $systemLinks = [
            ['label' => 'Pengaturan', 'icon' => 'fas fa-cog', 'url' => ['/ruang-rapat/settings/index'], 'match' => 'settings/index'],
            ['label' => 'Strike', 'icon' => 'fas fa-exclamation-triangle', 'url' => ['/ruang-rapat/strike/index'], 'match' => 'strike/'],
        ];
        ?>

        <div class="section-label">Menu Utama</div>
        <?php foreach ($links as $link): ?>
            <a href="<?= \yii\helpers\Url::to($link['url']) ?>"
               class="<?= str_contains($route, $link['match']) ? 'active' : '' ?>">
                <i class="<?= $link['icon'] ?>"></i> <?= $link['label'] ?>
            </a>
        <?php endforeach ?>

        <div class="section-label">Reservasi</div>
        <?php foreach ($bookingLinks as $link): ?>
            <a href="<?= \yii\helpers\Url::to($link['url']) ?>"
               class="<?= str_contains($route, $link['match']) ? 'active' : '' ?>">
                <i class="<?= $link['icon'] ?>"></i> <?= $link['label'] ?>
            </a>
        <?php endforeach ?>

        <div class="section-label">Sistem</div>
        <?php foreach ($systemLinks as $link): ?>
            <a href="<?= \yii\helpers\Url::to($link['url']) ?>"
               class="<?= str_contains($route, $link['match']) ? 'active' : '' ?>">
                <i class="<?= $link['icon'] ?>"></i> <?= $link['label'] ?>
            </a>
        <?php endforeach ?>

        <div class="section-label">Lainnya</div>
        <a href="http://localhost:20080" target="_blank" style="color:#8A94A6;">
            <i class="fas fa-external-link-alt"></i> Lihat Situs (User)
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
            </div>
            <div>
                <div class="user-name"><?= Html::encode(Yii::$app->user->identity->username) ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <?= Html::beginForm(['/site/logout'], 'post') ?>
            <button type="submit" class="btn-logout">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </button>
        <?= Html::endForm() ?>
    </div>
</aside>
<?php endif ?>

<!-- ── Main content ── -->
<main class="admin-main" style="<?= Yii::$app->user->isGuest ? 'margin-left:0' : '' ?>">
    <?= Alert::widget() ?>
    <?= $content ?>
</main>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
