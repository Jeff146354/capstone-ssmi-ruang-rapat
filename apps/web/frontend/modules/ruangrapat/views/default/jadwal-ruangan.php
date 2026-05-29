<?php
/** @var yii\web\View $this */
/** @var common\models\Room $room */
/** @var common\models\Reservation[] $reservations */
use common\models\Reservation;
use yii\bootstrap5\Html;

$this->title = 'Jadwal Ruangan: ' . Html::encode($room->name);
?>
<div class="container mt-5 pt-4">
    <div class="mb-4 text-center">
        <h2 class="mb-3"><?= Html::encode($room->name) ?></h2>
        <p class="text-secondary">Jadwal penggunaan ruang rapat ini.</p>
        <?= Html::a('Kembali ke Ruangan', ['default/view', 'id' => $room->id], ['class' => 'btn btn-outline-primary']) ?>
    </div>

    <?php if (empty($reservations)): ?>
        <div class="alert alert-info text-center">
            Belum ada jadwal penggunaan untuk ruangan ini.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Acara / Kegiatan</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?= Html::encode(Yii::$app->formatter->asDate($reservation->date)) ?></td>
                            <td><?= Html::encode(Yii::$app->formatter->asTime($reservation->start_time)) ?></td>
                            <td><?= Html::encode(Yii::$app->formatter->asTime($reservation->end_time)) ?></td>
                            <td><?= Html::encode($reservation->reason_of_use) ?></td>
                            <td><?= Html::encode($reservation->user->username) ?></td>
                            <td>
                                <?php if ($reservation->status === Reservation::STATUS_APPROVED): ?>
                                    <span class="badge bg-success">Dikonfirmasi</span>
                                <?php elseif ($reservation->status === Reservation::STATUS_PENDING): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Batal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
