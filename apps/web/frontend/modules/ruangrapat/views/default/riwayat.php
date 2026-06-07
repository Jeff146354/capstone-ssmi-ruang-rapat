<?php
/** @var yii\web\View $this */
/** @var common\models\Reservation[] $reservations */
/** @var string $orderBy */

use common\models\Reservation;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Riwayat Pemesanan';
?>

<style>
.riwayat-page { background: var(--ipb-bg, #F9F9FF); min-height: calc(100vh - 72px); padding: 48px 0; }

/* Page header */
.page-header { margin-bottom: 32px; }
.page-header h1 { color: #151C27; font-size: 28px; font-weight: 700; margin-bottom: 6px; }
.page-header p  { color: #575E70; font-size: 15px; }

/* Sort bar */
.sort-bar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    margin-bottom: 28px;
}
.sort-bar .sort-label { color: #575E70; font-size: 13px; font-weight: 600; }
.sort-btn {
    padding: 6px 16px; border-radius: 20px;
    font-size: 13px; font-weight: 600; text-decoration: none;
    border: 1.5px solid #E2BFB0; color: #575E70;
    transition: all .15s; background: #fff;
}
.sort-btn:hover { border-color: #FF6B00; color: #FF6B00; }
.sort-btn.active { background: #FF6B00; border-color: #FF6B00; color: #fff; }

/* Reservation card */
.reservation-card {
    background: #fff; border-radius: 14px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    padding: 20px 24px; margin-bottom: 16px;
    display: flex; gap: 20px; align-items: flex-start;
    transition: box-shadow .2s;
}
.reservation-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.08); }

/* Status accent bar on left */
.reservation-card.status-approved { border-left: 4px solid #22c55e; }
.reservation-card.status-pending  { border-left: 4px solid #f59e0b; }
.reservation-card.status-canceled { border-left: 4px solid #ef4444; }

.rc-date-block {
    text-align: center; min-width: 60px; flex-shrink: 0;
}
.rc-date-block .day { color: #151C27; font-size: 28px; font-weight: 800; line-height: 1; }
.rc-date-block .month { color: #575E70; font-size: 12px; font-weight: 600; text-transform: uppercase; }
.rc-date-block .year { color: #9CA3AF; font-size: 11px; }

.rc-divider {
    width: 1px; background: #F0E8E5; flex-shrink: 0; align-self: stretch;
}

.rc-content { flex-grow: 1; min-width: 0; }
.rc-room-name { color: #151C27; font-size: 17px; font-weight: 700; margin-bottom: 4px; }
.rc-time { color: #575E70; font-size: 13px; margin-bottom: 8px; display: flex; align-items: center; gap: 5px; }
.rc-time i { color: #A04100; }
.rc-reason { color: #575E70; font-size: 13px; margin-bottom: 8px;
    display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
}
.rc-rejection {
    background: #FEF2F2; border: 1px solid #FECACA; border-radius: 6px;
    padding: 8px 12px; margin-top: 6px;
    color: #991B1B; font-size: 12px;
}

/* Status badge */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700; letter-spacing: .3px;
}
.status-badge.approved { background: #dcfce7; color: #16a34a; }
.status-badge.pending  { background: #fef3c7; color: #92400e; }
.status-badge.canceled { background: #fee2e2; color: #991b1b; }

/* Actions */
.rc-actions { display: flex; flex-direction: column; gap: 8px; flex-shrink: 0; align-items: flex-end; }
.btn-rc-action {
    padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
    text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
    border: none; cursor: pointer; white-space: nowrap;
    transition: background .15s, color .15s;
}
.btn-qr    { background: #FF6B00; color: #fff; }
.btn-qr:hover { background: #A04100; color: #fff; }
.btn-cancel { background: #fff; color: #ef4444; border: 1.5px solid #ef4444; }
.btn-cancel:hover { background: #ef4444; color: #fff; }

/* Modal overrides */
.modal-content { border-radius: 14px; border: none; }
.modal-header { border-bottom: 1px solid #F0E8E5; padding: 20px 24px; }
.modal-footer { border-top: 1px solid #F0E8E5; padding: 16px 24px; }
.btn-modal-cancel { background: #fff; color: #575E70; border: 1.5px solid #E2BFB0; border-radius: 8px; padding: 10px 20px; font-weight: 600; }
.btn-modal-confirm { background: #ef4444; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; }

/* Empty state */
.empty-riwayat { text-align: center; padding: 80px 24px; }
.empty-riwayat i { color: #E2BFB0; font-size: 64px; margin-bottom: 16px; display: block; }
.empty-riwayat h3 { color: #151C27; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
.empty-riwayat p  { color: #575E70; font-size: 15px; margin-bottom: 24px; }
.btn-book-now {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 28px; background: #FF6B00; color: #fff;
    font-size: 15px; font-weight: 700; border-radius: 10px;
    text-decoration: none; box-shadow: 0 4px 15px rgba(255,107,0,.3);
    transition: background .15s;
}
.btn-book-now:hover { background: #A04100; color: #fff; }

@media (max-width: 576px) {
    .reservation-card { flex-direction: column; }
    .rc-divider { display: none; }
    .rc-actions { flex-direction: row; align-items: flex-start; flex-wrap: wrap; }
}
</style>

<div class="riwayat-page">
    <div class="container" style="max-width: 860px;">

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success rounded-3 mb-4"><?= Yii::$app->session->getFlash('success') ?></div>
        <?php endif ?>
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger rounded-3 mb-4"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif ?>

        <div class="page-header">
            <h1>Riwayat Pemesanan</h1>
            <p>Riwayat peminjaman ruang rapat Anda di SSMI IPB.</p>
        </div>

        <!-- Sort bar -->
        <div class="sort-bar">
            <span class="sort-label">Urutkan:</span>
            <?= Html::a('<i class="fas fa-circle me-1"></i>Status',    ['/ruang-rapat/default/riwayat-peminjaman', 'orderBy' => 'status'], ['class' => 'sort-btn ' . ($orderBy === 'status' ? 'active' : '')]) ?>
            <?= Html::a('<i class="fas fa-calendar me-1"></i>Tanggal', ['/ruang-rapat/default/riwayat-peminjaman', 'orderBy' => 'date'],   ['class' => 'sort-btn ' . ($orderBy === 'date'   ? 'active' : '')]) ?>
            <?= Html::a('<i class="fas fa-door-open me-1"></i>Ruangan',['/ruang-rapat/default/riwayat-peminjaman', 'orderBy' => 'room'],   ['class' => 'sort-btn ' . ($orderBy === 'room'   ? 'active' : '')]) ?>
        </div>

        <?php if (empty($reservations)): ?>
            <div class="empty-riwayat">
                <i class="fas fa-calendar-times"></i>
                <h3>Belum Ada Peminjaman</h3>
                <p>Anda belum memiliki riwayat peminjaman. Mulai pesan ruangan sekarang.</p>
                <a href="<?= Url::to(['/ruang-rapat/default/index']) ?>" class="btn-book-now">
                    <i class="fas fa-search"></i> Cari Ruangan
                </a>
            </div>
        <?php else: ?>

            <?php foreach ($reservations as $reservation):
                $statusClass = $reservation->status;
                $isPast = strtotime($reservation->date . ' ' . $reservation->end_time) < time();
            ?>
                <div class="reservation-card status-<?= $statusClass ?>">

                    <!-- Date block -->
                    <div class="rc-date-block">
                        <div class="day"><?= date('d', strtotime($reservation->date)) ?></div>
                        <div class="month"><?= date('M', strtotime($reservation->date)) ?></div>
                        <div class="year"><?= date('Y', strtotime($reservation->date)) ?></div>
                    </div>

                    <div class="rc-divider"></div>

                    <!-- Content -->
                    <div class="rc-content">
                        <div class="rc-room-name"><?= Html::encode($reservation->room->name) ?></div>
                        <div class="rc-time">
                            <i class="fas fa-clock"></i>
                            <?= date('H:i', strtotime($reservation->start_time)) ?> –
                            <?= date('H:i', strtotime($reservation->end_time)) ?>
                        </div>
                        <?php if ($reservation->reason_of_use): ?>
                            <div class="rc-reason">
                                <i class="fas fa-tag me-1" style="color:#A04100"></i>
                                <?= Html::encode($reservation->reason_of_use) ?>
                            </div>
                        <?php endif ?>

                        <?php
                            $sLabel = match($reservation->status) {
                                Reservation::STATUS_APPROVED => 'Disetujui',
                                Reservation::STATUS_PENDING  => 'Menunggu',
                                Reservation::STATUS_CANCELED => 'Dibatalkan',
                                default => $reservation->status,
                            };
                            $sIcon = match($reservation->status) {
                                Reservation::STATUS_APPROVED => 'fas fa-check-circle',
                                Reservation::STATUS_PENDING  => 'fas fa-hourglass-half',
                                Reservation::STATUS_CANCELED => 'fas fa-times-circle',
                                default => 'fas fa-circle',
                            };
                        ?>
                        <span class="status-badge <?= $statusClass ?>">
                            <i class="<?= $sIcon ?>"></i> <?= $sLabel ?>
                        </span>

                        <?php if ($reservation->status === Reservation::STATUS_CANCELED && $reservation->rejection_reason): ?>
                            <div class="rc-rejection">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= Html::encode($reservation->rejection_reason) ?>
                                <?php if ($reservation->rejected_by): ?>
                                    <em class="ms-1">(oleh <?= Html::encode($reservation->rejected_by) ?>)</em>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <!-- Actions -->
                    <div class="rc-actions">
                        <?php if ($reservation->status === Reservation::STATUS_APPROVED && !$isPast): ?>
                            <button class="btn-rc-action btn-qr"
                                    onclick="alert('QR Check-in belum diimplementasi.')">
                                <i class="fas fa-qrcode"></i> QR
                            </button>
                            <button type="button"
                                    class="btn-rc-action btn-cancel"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal-<?= $reservation->id ?>">
                                <i class="fas fa-times"></i> Batalkan
                            </button>

                        <?php elseif ($reservation->status === Reservation::STATUS_PENDING): ?>
                            <button type="button"
                                    class="btn-rc-action btn-cancel"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal-<?= $reservation->id ?>">
                                <i class="fas fa-undo"></i> Tarik
                            </button>
                        <?php endif ?>
                    </div>
                </div>

                <!-- Cancel modal -->
                <?php if (in_array($reservation->status, [Reservation::STATUS_APPROVED, Reservation::STATUS_PENDING]) && !$isPast): ?>
                <div class="modal fade" id="cancelModal-<?= $reservation->id ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">
                                    <?= $reservation->status === Reservation::STATUS_APPROVED ? 'Batalkan Reservasi' : 'Tarik Pengajuan' ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" style="padding: 20px 24px;">
                                <p style="color:#575E70; margin:0;">
                                    <?= $reservation->status === Reservation::STATUS_APPROVED
                                        ? 'Apakah Anda yakin ingin membatalkan reservasi'
                                        : 'Tarik pengajuan peminjaman' ?>
                                    <strong> <?= Html::encode($reservation->room->name) ?></strong>
                                    pada <strong><?= date('d M Y', strtotime($reservation->date)) ?></strong>?
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Tidak</button>
                                <?= Html::beginForm(['/ruang-rapat/default/cancel', 'id' => $reservation->id], 'post') ?>
                                    <button type="submit" class="btn-modal-confirm">
                                        <?= $reservation->status === Reservation::STATUS_APPROVED ? 'Ya, Batalkan' : 'Ya, Tarik' ?>
                                    </button>
                                <?= Html::endForm() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif ?>

            <?php endforeach ?>
        <?php endif ?>

    </div>
</div>
