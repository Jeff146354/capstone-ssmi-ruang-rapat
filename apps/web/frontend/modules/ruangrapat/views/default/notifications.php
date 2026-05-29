<?php
/** @var yii\web\View $this */
/** @var common\models\Notification[] $notifications */

use common\models\Notification;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Notifikasi';

$typeIcons = [
    Notification::TYPE_RESERVATION_APPROVED => ['icon' => 'fas fa-check-circle', 'color' => 'text-success'],
    Notification::TYPE_RESERVATION_CANCELED => ['icon' => 'fas fa-times-circle', 'color' => 'text-danger'],
    Notification::TYPE_RESERVATION_BUMPED   => ['icon' => 'fas fa-exclamation-circle', 'color' => 'text-warning'],
    Notification::TYPE_WAITLIST_AVAILABLE   => ['icon' => 'fas fa-bell', 'color' => 'text-primary'],
    Notification::TYPE_STRIKE_ISSUED        => ['icon' => 'fas fa-bolt', 'color' => 'text-warning'],
    Notification::TYPE_SUSPENSION_ISSUED    => ['icon' => 'fas fa-ban', 'color' => 'text-danger'],
];
?>

<div class="container mt-5 pt-4" style="max-width: 800px;">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Notifikasi</h2>
        <?= Html::a('← Kembali', ['/ruang-rapat/default/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>

    <?php if (empty($notifications)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-bell-slash fa-3x mb-3"></i>
            <p>Belum ada notifikasi.</p>
        </div>
    <?php else: ?>
        <ul class="list-unstyled">
            <?php foreach ($notifications as $notif): ?>
                <?php
                    $typeInfo = $typeIcons[$notif->type] ?? ['icon' => 'fas fa-info-circle', 'color' => 'text-secondary'];
                    $bgClass  = $notif->is_read ? 'bg-white' : 'bg-light border-start border-primary border-3';
                ?>
                <li class="notification-item mb-3 p-3 rounded shadow-sm <?= $bgClass ?>">
                    <div class="d-flex align-items-start gap-3">
                        <div class="mt-1">
                            <i class="<?= $typeInfo['icon'] ?> fa-lg <?= $typeInfo['color'] ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="message" style="font-size:0.95rem; color:#333;">
                                <?= Html::encode($notif->message) ?>
                            </div>
                            <?php if ($notif->type === Notification::TYPE_WAITLIST_AVAILABLE && $notif->reservation_id === null): ?>
                                <?php
                                    // Find the waitlist entry for this notification
                                    $waitlistEntry = \common\models\ReservationWaitlist::findOne([
                                        'user_id' => Yii::$app->user->id,
                                        'status'  => \common\models\ReservationWaitlist::STATUS_NOTIFIED,
                                    ]);
                                ?>
                                <?php if ($waitlistEntry): ?>
                                    <div class="mt-2">
                                        <?= Html::a(
                                            '<i class="fas fa-check me-1"></i>Klaim Slot Ini',
                                            ['/ruang-rapat/default/claim-waitlist', 'id' => $waitlistEntry->id],
                                            ['class' => 'btn btn-success btn-sm']
                                        ) ?>
                                    </div>
                                <?php endif ?>
                            <?php endif ?>
                            <div class="time text-muted mt-1" style="font-size:0.8rem;">
                                <?= Yii::$app->formatter->asRelativeTime($notif->created_at) ?>
                                · <?= Yii::$app->formatter->asDatetime($notif->created_at, 'short') ?>
                            </div>
                        </div>
                        <?php if (!$notif->is_read): ?>
                            <span class="badge bg-primary rounded-pill align-self-start">Baru</span>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</div>
