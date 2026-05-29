<?php
/** @var yii\web\View $this */
/** @var common\models\Room $room */
/** @var string $date */
/** @var string $startTime */
/** @var string $endTime */

use yii\bootstrap5\Html;
use yii\widgets\ActiveForm;

$this->title = 'Daftar Tunggu – ' . $room->name;
?>

<div class="container mt-5 pt-4" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Ruangan Tidak Tersedia</h5>
        </div>
        <div class="card-body">
            <p>
                Ruangan <strong><?= Html::encode($room->name) ?></strong> sudah dipesan pada:
            </p>
            <ul>
                <li><strong>Tanggal:</strong> <?= date('d M Y', strtotime($date)) ?></li>
                <li><strong>Waktu:</strong> <?= date('H:i', strtotime($startTime)) ?> – <?= date('H:i', strtotime($endTime)) ?></li>
            </ul>

            <p class="text-muted">
                Anda bisa mendaftar ke <strong>daftar tunggu</strong>. Jika slot ini dibatalkan,
                Anda akan mendapat notifikasi dan punya waktu
                <strong><?= \common\models\BookingRule::getInt('waitlist_claim_days', 3) ?> hari</strong>
                untuk mengklaimnya.
            </p>

            <?php $form = ActiveForm::begin(['action' => ['/ruang-rapat/default/join-waitlist'], 'method' => 'post']); ?>
                <?= Html::hiddenInput('ReservationWaitlist[room_id]', $room->id) ?>
                <?= Html::hiddenInput('ReservationWaitlist[date]', $date) ?>
                <?= Html::hiddenInput('ReservationWaitlist[start_time]', $startTime) ?>
                <?= Html::hiddenInput('ReservationWaitlist[end_time]', $endTime) ?>

                <div class="d-flex gap-2 mt-3">
                    <?= Html::submitButton('<i class="fas fa-list me-1"></i>Masuk Daftar Tunggu', ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('Cari Ruangan Lain', ['/ruang-rapat/default/find-available-rooms', 'date' => $date, 'startTime' => $startTime, 'endTime' => $endTime], ['class' => 'btn btn-outline-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
