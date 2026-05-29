<?php
/** @var yii\web\View $this */
/** @var common\models\Reservation[] $reservations */
/** @var string $orderBy */

use common\models\Reservation;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Riwayat Pemesanan';
?>

<div class="container mt-5 pt-4">
    <div class="mb-4 text-center">
        <h2 class="mb-3"><?= Html::encode($this->title) ?></h2>
        <p class="text-secondary">Berikut adalah riwayat pemesanan ruangan Anda di SSMI IPB.</p>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif ?>

    <div class="mb-3 d-flex gap-2 justify-content-center">
        <strong class="align-self-center">Urutkan:</strong>
        <?= Html::a('Status',    ['/ruang-rapat/default/riwayat-peminjaman', 'orderBy' => 'status'], ['class' => 'btn btn-outline-secondary btn-sm ' . ($orderBy === 'status' ? 'active' : '')]) ?>
        <?= Html::a('Tanggal',   ['/ruang-rapat/default/riwayat-peminjaman', 'orderBy' => 'date'],   ['class' => 'btn btn-outline-secondary btn-sm ' . ($orderBy === 'date'   ? 'active' : '')]) ?>
        <?= Html::a('Nama Ruang',['/ruang-rapat/default/riwayat-peminjaman', 'orderBy' => 'room'],   ['class' => 'btn btn-outline-secondary btn-sm ' . ($orderBy === 'room'   ? 'active' : '')]) ?>
    </div>

    <?php if (empty($reservations)): ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-calendar-times fa-3x mb-3"></i>
            <p>Belum ada riwayat peminjaman.</p>
            <?= Html::a('Pesan Ruangan Sekarang', ['/ruang-rapat/default/index'], ['class' => 'btn btn-primary mt-2']) ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm bg-white">
                <thead class="table-light">
                    <tr>
                        <th>Nama Ruang</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <?php
                            $statusBadge = match ($reservation->status) {
                                Reservation::STATUS_APPROVED => '<span class="badge bg-success">Disetujui</span>',
                                Reservation::STATUS_PENDING  => '<span class="badge bg-warning text-dark">Menunggu</span>',
                                Reservation::STATUS_CANCELED => '<span class="badge bg-danger">Dibatalkan</span>',
                                default                      => '<span class="badge bg-secondary">' . Html::encode($reservation->status) . '</span>',
                            };

                            $isPast = strtotime($reservation->date . ' ' . $reservation->end_time) < time();
                        ?>
                        <tr>
                            <td><?= Html::encode($reservation->room->name) ?></td>
                            <td><?= date('d M Y', strtotime($reservation->date)) ?></td>
                            <td>
                                <?= date('H:i', strtotime($reservation->start_time)) ?>
                                – <?= date('H:i', strtotime($reservation->end_time)) ?>
                            </td>
                            <td>
                                <?= $statusBadge ?>
                                <?php if ($reservation->status === Reservation::STATUS_CANCELED && $reservation->rejection_reason): ?>
                                    <br><small class="text-muted">
                                        <?= Html::encode($reservation->rejection_reason) ?>
                                        <?php if ($reservation->rejected_by): ?>
                                            <em>(oleh <?= Html::encode($reservation->rejected_by) ?>)</em>
                                        <?php endif ?>
                                    </small>
                                <?php endif ?>
                            </td>
                            <td><?= Html::encode($reservation->reason_of_use) ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php if ($reservation->status === Reservation::STATUS_APPROVED && !$isPast): ?>
                                        <!-- QR Check-in button -->
                                        <button class="btn btn-primary btn-sm" onclick="alert('QR Check-in belum diimplementasi.')">
                                            <i class="fas fa-qrcode me-1"></i>QR
                                        </button>

                                        <!-- Cancel button (only if not past) -->
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal-<?= $reservation->id ?>"
                                        >
                                            <i class="fas fa-times me-1"></i>Batalkan
                                        </button>

                                        <!-- Cancel confirmation modal -->
                                        <div class="modal fade" id="cancelModal-<?= $reservation->id ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Batalkan Reservasi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah Anda yakin ingin membatalkan reservasi
                                                        <strong><?= Html::encode($reservation->room->name) ?></strong>
                                                        pada <?= date('d M Y', strtotime($reservation->date)) ?>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                                        <?= Html::beginForm(['/ruang-rapat/default/cancel', 'id' => $reservation->id], 'post') ?>
                                                            <?= Html::submitButton('Ya, Batalkan', ['class' => 'btn btn-danger']) ?>
                                                        <?= Html::endForm() ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php elseif ($reservation->status === Reservation::STATUS_PENDING): ?>
                                        <!-- Cancel pending -->
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal-<?= $reservation->id ?>"
                                        >
                                            <i class="fas fa-times me-1"></i>Tarik
                                        </button>

                                        <div class="modal fade" id="cancelModal-<?= $reservation->id ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tarik Pengajuan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tarik pengajuan peminjaman
                                                        <strong><?= Html::encode($reservation->room->name) ?></strong>
                                                        pada <?= date('d M Y', strtotime($reservation->date)) ?>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                                        <?= Html::beginForm(['/ruang-rapat/default/cancel', 'id' => $reservation->id], 'post') ?>
                                                            <?= Html::submitButton('Ya, Tarik', ['class' => 'btn btn-danger']) ?>
                                                        <?= Html::endForm() ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</div>
