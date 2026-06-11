<?php
/** @var yii\web\View $this */
/** @var array $reservations */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Admin - Semua Pengajuan Reservasi';
?>

<div class="booking-default-admin">
    <h1 class="mb-1" style="color:#151C27; font-size:26px; font-weight:700;"><?= $this->title ?></h1>
    <p style="color:#575E70; font-size:14px; margin-bottom:24px;">Kelola semua pengajuan reservasi dari pengguna.</p>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success rounded-3"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger rounded-3"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif ?>

    <div class="mb-4 d-flex align-items-center gap-2 flex-wrap">
        <span style="color:#575E70; font-size:13px; font-weight:600;">Urutkan:</span>
        <?= Html::a('<i class="fas fa-circle me-1"></i>Status', ['/booking/default/admin', 'orderBy' => 'status'], [
            'class' => 'btn btn-sm rounded-pill ' . ($orderBy === 'status' ? 'text-white' : ''),
            'style' => $orderBy === 'status'
                ? 'background:#FF6B00; border:1.5px solid #FF6B00; font-size:13px; font-weight:600;'
                : 'background:#fff; border:1.5px solid #E2BFB0; color:#575E70; font-size:13px; font-weight:600;',
        ]) ?>
        <?= Html::a('<i class="fas fa-calendar me-1"></i>Tanggal', ['/booking/default/admin', 'orderBy' => 'date'], [
            'class' => 'btn btn-sm rounded-pill ' . ($orderBy === 'date' ? 'text-white' : ''),
            'style' => $orderBy === 'date'
                ? 'background:#FF6B00; border:1.5px solid #FF6B00; font-size:13px; font-weight:600;'
                : 'background:#fff; border:1.5px solid #E2BFB0; color:#575E70; font-size:13px; font-weight:600;',
        ]) ?>
        <?= Html::a('<i class="fas fa-door-open me-1"></i>Nama Ruang', ['/booking/default/admin', 'orderBy' => 'room'], [
            'class' => 'btn btn-sm rounded-pill ' . ($orderBy === 'room' ? 'text-white' : ''),
            'style' => $orderBy === 'room'
                ? 'background:#FF6B00; border:1.5px solid #FF6B00; font-size:13px; font-weight:600;'
                : 'background:#fff; border:1.5px solid #E2BFB0; color:#575E70; font-size:13px; font-weight:600;',
        ]) ?>
    </div>

    <div class="card" style="border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.04); overflow:hidden;">
    <table class="table table-hover mb-0" style="font-size:14px;">
        <thead style="background:#151C27; color:#fff;">
            <tr>
                <th>#</th>
                <th>Nama Pemesan</th>
                <th>Prioritas</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Diajukan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $i => $reservation): ?>
                <tr>
                    <th><?= $i + 1 ?></th>
                    <td>
                        <?= Html::encode($reservation['user']['username']) ?>
                        <?php if (!empty($reservation['user']['requires_manual_approval'])): ?>
                            <br>
                            <span
                                class="badge bg-warning text-dark"
                                title="Pengguna ini wajib approval manual (Strike 3+)"
                            >⚠ Manual Approval</span>
                        <?php endif ?>
                        <?php if (!empty($reservation['user']['booking_suspended_until']) && strtotime($reservation['user']['booking_suspended_until']) > time()): ?>
                            <br>
                            <span class="badge bg-danger" title="Akun sedang disuspend">
                                🚫 Suspended
                            </span>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php
                        $priority = $reservation['user']['priority'] ?? 1;
                        $priorityLabels = [1 => 'Mahasiswa', 2 => 'Staff', 3 => 'Dosen', 99 => 'Admin'];
                        $priorityColors = [1 => 'secondary', 2 => 'info', 3 => 'primary', 99 => 'dark'];
                        $label = $priorityLabels[$priority] ?? 'User';
                        $color = $priorityColors[$priority] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                        <?php if (!empty($reservation['user']['priority_boost_until']) && strtotime($reservation['user']['priority_boost_until']) > time()): ?>
                            <br><span class="badge bg-info text-dark" title="Pengguna ini sedang mendapat priority boost">⬆ Boosted</span>
                        <?php endif ?>
                    </td>
                    <td><?= Html::encode($reservation['room']['name']) ?></td>
                    <td><?= date('d M Y', strtotime($reservation['date'])) ?></td>
                    <td><?= date('H:i', strtotime($reservation['start_time'])) ?> – <?= date('H:i', strtotime($reservation['end_time'])) ?></td>
                    <td>
                        <?php if (!empty($reservation['created_at'])): ?>
                            <span class="text-muted small">
                                <?= date('d M Y', strtotime($reservation['created_at'])) ?>
                                <br><?= date('H:i', strtotime($reservation['created_at'])) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted small">–</span>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php
                        $status = strtolower($reservation['status']);
                        $badgeClass = match ($status) {
                            'approved' => 'success',
                            'canceled' => 'danger',
                            'pending'  => 'warning',
                            default    => 'secondary',
                        };
                        ?>
                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                        <?php if (!empty($reservation['rejection_reason'])): ?>
                            <br><small class="text-muted"><?= Html::encode($reservation['rejection_reason']) ?></small>
                        <?php endif ?>
                    </td>
                    <td>
                        <?= Html::a('<i class="fas fa-eye"></i>', ['/booking/reservation/detail', 'id' => $reservation['id']], [
                            'class' => 'btn btn-info btn-sm',
                            'title' => 'Lihat Detail',
                        ]) ?>

                        <?php if ($status === 'pending'): ?>
                            <?= Html::a('<i class="fas fa-check"></i>', ['/booking/reservation/approve', 'id' => $reservation['id']], [
                                'class' => 'btn btn-success btn-sm',
                                'title' => 'Setujui',
                            ]) ?>

                            <!-- Cancel with reason modal trigger -->
                            <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                title="Tolak"
                                data-bs-toggle="modal"
                                data-bs-target="#cancelModal"
                                data-id="<?= $reservation['id'] ?>"
                                data-name="<?= Html::encode($reservation['user']['username']) ?>"
                                data-room="<?= Html::encode($reservation['room']['name']) ?>"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    </div><!-- /card -->
</div>

<!-- Cancel / Reject Modal with Reason -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Tolak Reservasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php $form = \yii\widgets\ActiveForm::begin([
                'id'     => 'cancelForm',
                'action' => ['/booking/reservation/cancel', 'id' => 0],
                'method' => 'post',
            ]); ?>
            <div class="modal-body">
                <p>Tolak reservasi dari <strong id="cancelUserName"></strong> untuk ruangan <strong id="cancelRoomName"></strong>?</p>
                <div class="mb-3">
                    <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-muted">(opsional)</span></label>
                    <textarea
                        class="form-control"
                        id="rejection_reason"
                        name="rejection_reason"
                        rows="3"
                        placeholder="Contoh: Ruangan sedang dalam perbaikan, waktu bentrok dengan acara resmi, dll."
                    ></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Reservasi</button>
            </div>
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelModal = document.getElementById('cancelModal');
    cancelModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        const id   = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const room = btn.getAttribute('data-room');

        document.getElementById('cancelUserName').textContent = name;
        document.getElementById('cancelRoomName').textContent = room;

        // Update form action with correct reservation id
        const form = document.getElementById('cancelForm');
        form.action = form.action.replace(/id=\d*/, 'id=' + id);
    });
});
</script>
