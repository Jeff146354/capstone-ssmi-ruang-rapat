<?php
/** @var yii\web\View $this */
/** @var common\models\Notification[] $notifications */

use common\models\Notification;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Notifikasi';

$typeConfig = [
    Notification::TYPE_RESERVATION_APPROVED => [
        'icon'  => 'fas fa-check-circle',
        'color' => '#16a34a',
        'bg'    => '#dcfce7',
        'label' => 'Disetujui',
    ],
    Notification::TYPE_RESERVATION_CANCELED => [
        'icon'  => 'fas fa-times-circle',
        'color' => '#dc2626',
        'bg'    => '#fee2e2',
        'label' => 'Dibatalkan',
    ],
    Notification::TYPE_RESERVATION_BUMPED => [
        'icon'  => 'fas fa-exclamation-triangle',
        'color' => '#d97706',
        'bg'    => '#fef3c7',
        'label' => 'Digeser',
    ],
    Notification::TYPE_WAITLIST_AVAILABLE => [
        'icon'  => 'fas fa-bell',
        'color' => '#2563eb',
        'bg'    => '#dbeafe',
        'label' => 'Waitlist',
    ],
    Notification::TYPE_STRIKE_ISSUED => [
        'icon'  => 'fas fa-bolt',
        'color' => '#d97706',
        'bg'    => '#fef3c7',
        'label' => 'Strike',
    ],
    Notification::TYPE_SUSPENSION_ISSUED => [
        'icon'  => 'fas fa-ban',
        'color' => '#dc2626',
        'bg'    => '#fee2e2',
        'label' => 'Suspensi',
    ],
];
$defaultConfig = ['icon' => 'fas fa-info-circle', 'color' => '#575E70', 'bg' => '#f3f4f6', 'label' => 'Info'];
?>

<style>
.notif-page { background: var(--ipb-bg, #F9F9FF); min-height: calc(100vh - 72px); padding: 48px 0; }

.page-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 32px; flex-wrap: wrap; gap: 12px;
}
.page-header h1 { color: #151C27; font-size: 28px; font-weight: 700; margin: 0; }
.btn-back-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #575E70; font-size: 14px; font-weight: 600;
    text-decoration: none; padding: 8px 16px;
    border: 1.5px solid #E2BFB0; border-radius: 8px;
    transition: border-color .15s, color .15s; background: #fff;
}
.btn-back-link:hover { border-color: #A04100; color: #A04100; }

/* Notification item */
.notif-item {
    background: #fff; border-radius: 14px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    padding: 18px 20px; margin-bottom: 12px;
    display: flex; gap: 16px; align-items: flex-start;
    transition: box-shadow .2s;
}
.notif-item:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); }
.notif-item.unread {
    border-left: 3px solid #FF6B00;
    background: #FFFBF8;
}

.notif-icon-wrap {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 18px;
}

.notif-body { flex-grow: 1; min-width: 0; }
.notif-message {
    color: #151C27; font-size: 14px; line-height: 1.6;
    margin-bottom: 6px;
}
.notif-meta {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.notif-type-badge {
    font-size: 11px; font-weight: 700; letter-spacing: .3px;
    text-transform: uppercase; padding: 2px 8px; border-radius: 4px;
}
.notif-time {
    color: #9CA3AF; font-size: 12px;
    display: flex; align-items: center; gap: 4px;
}
.notif-time i { font-size: 11px; }

.notif-actions { flex-shrink: 0; }
.btn-claim {
    padding: 8px 16px; background: #22c55e; color: #fff;
    font-size: 13px; font-weight: 700; border: none; border-radius: 8px;
    text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
    transition: background .15s;
}
.btn-claim:hover { background: #16a34a; color: #fff; }

.unread-dot {
    width: 8px; height: 8px; background: #FF6B00;
    border-radius: 50%; flex-shrink: 0; margin-top: 6px;
}

/* Empty state */
.empty-notif { text-align: center; padding: 80px 24px; }
.empty-notif i { color: #E2BFB0; font-size: 64px; margin-bottom: 16px; display: block; }
.empty-notif h3 { color: #151C27; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
.empty-notif p  { color: #575E70; font-size: 15px; }

/* Section divider */
.section-divider {
    color: #9CA3AF; font-size: 12px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    margin: 24px 0 12px; padding-bottom: 8px;
    border-bottom: 1px solid #F0E8E5;
}
</style>

<div class="notif-page">
    <div class="container" style="max-width: 760px;">

        <div class="page-header">
            <h1><i class="fas fa-bell me-2" style="color:#FF6B00; font-size:24px"></i>Notifikasi</h1>
            <a href="<?= Url::to(['/ruang-rapat/default/index']) ?>" class="btn-back-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success rounded-3 mb-4"><?= Yii::$app->session->getFlash('success') ?></div>
        <?php endif ?>

        <?php if (empty($notifications)): ?>
            <div class="empty-notif">
                <i class="fas fa-bell-slash"></i>
                <h3>Belum Ada Notifikasi</h3>
                <p>Semua notifikasi akan muncul di sini.</p>
            </div>
        <?php else: ?>

            <?php
                $unread = array_filter($notifications, fn($n) => !$n->is_read);
                $read   = array_filter($notifications, fn($n) =>  $n->is_read);
            ?>

            <?php if (!empty($unread)): ?>
                <div class="section-divider">
                    <i class="fas fa-circle me-1" style="color:#FF6B00; font-size:8px"></i>
                    Baru (<?= count($unread) ?>)
                </div>
                <?php foreach ($unread as $notif): ?>
                    <?php $cfg = $typeConfig[$notif->type] ?? $defaultConfig; ?>
                    <div class="notif-item unread">
                        <div class="notif-icon-wrap" style="background:<?= $cfg['bg'] ?>; color:<?= $cfg['color'] ?>">
                            <i class="<?= $cfg['icon'] ?>"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-message"><?= Html::encode($notif->message) ?></div>
                            <div class="notif-meta">
                                <span class="notif-type-badge"
                                      style="background:<?= $cfg['bg'] ?>; color:<?= $cfg['color'] ?>">
                                    <?= $cfg['label'] ?>
                                </span>
                                <span class="notif-time">
                                    <i class="fas fa-clock"></i>
                                    <?= Yii::$app->formatter->asRelativeTime($notif->created_at) ?>
                                </span>
                            </div>

                            <?php if ($notif->type === Notification::TYPE_WAITLIST_AVAILABLE && $notif->reservation_id === null): ?>
                                <?php $waitlistEntry = \common\models\ReservationWaitlist::findOne([
                                    'user_id' => Yii::$app->user->id,
                                    'status'  => \common\models\ReservationWaitlist::STATUS_NOTIFIED,
                                ]); ?>
                                <?php if ($waitlistEntry): ?>
                                    <div class="mt-2">
                                        <a href="<?= Url::to(['/ruang-rapat/default/claim-waitlist', 'id' => $waitlistEntry->id]) ?>"
                                           class="btn-claim">
                                            <i class="fas fa-check"></i> Klaim Slot Ini
                                        </a>
                                    </div>
                                <?php endif ?>
                            <?php endif ?>
                        </div>
                        <div class="unread-dot"></div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>

            <?php if (!empty($read)): ?>
                <?php if (!empty($unread)): ?>
                    <div class="section-divider mt-4">Sebelumnya</div>
                <?php endif ?>
                <?php foreach ($read as $notif): ?>
                    <?php $cfg = $typeConfig[$notif->type] ?? $defaultConfig; ?>
                    <div class="notif-item">
                        <div class="notif-icon-wrap" style="background:<?= $cfg['bg'] ?>; color:<?= $cfg['color'] ?>; opacity:.7">
                            <i class="<?= $cfg['icon'] ?>"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-message" style="color:#575E70"><?= Html::encode($notif->message) ?></div>
                            <div class="notif-meta">
                                <span class="notif-type-badge"
                                      style="background:#f3f4f6; color:#9CA3AF">
                                    <?= $cfg['label'] ?>
                                </span>
                                <span class="notif-time">
                                    <i class="fas fa-clock"></i>
                                    <?= Yii::$app->formatter->asDatetime($notif->created_at, 'short') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>

        <?php endif ?>
    </div>
</div>
