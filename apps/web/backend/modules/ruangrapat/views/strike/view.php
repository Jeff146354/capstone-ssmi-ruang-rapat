<?php
/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\UserStrike[] $strikes */

use common\models\UserStrike;
use yii\bootstrap5\Html;

$this->title = 'Strike – ' . $user->username;
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= Html::encode($this->title) ?></h2>
        <?= Html::a('← Kembali', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>

    <!-- User status card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>Username:</strong> <?= Html::encode($user->username) ?></div>
                <div class="col-md-3"><strong>Email:</strong> <?= Html::encode($user->email) ?></div>
                <div class="col-md-3">
                    <strong>Status:</strong>
                    <?php if ($user->isSuspended()): ?>
                        <span class="badge bg-danger">Suspended hingga <?= date('d M Y H:i', strtotime($user->booking_suspended_until)) ?></span>
                    <?php elseif ($user->requires_manual_approval): ?>
                        <span class="badge bg-warning text-dark">Perlu Approval Manual</span>
                    <?php else: ?>
                        <span class="badge bg-success">Normal</span>
                    <?php endif ?>
                </div>
                <div class="col-md-3">
                    <strong>Strike Aktif:</strong>
                    <span class="badge bg-danger"><?= UserStrike::countActive($user->id) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue manual strike -->
    <div class="card mb-4">
        <div class="card-header">Terbitkan Strike Manual</div>
        <div class="card-body">
            <?php $form = \yii\widgets\ActiveForm::begin(['action' => ['issue', 'userId' => $user->id], 'method' => 'post']); ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Alasan</label>
                    <select name="reason" class="form-select">
                        <option value="<?= UserStrike::REASON_NO_SHOW ?>">Tidak Hadir (No-show)</option>
                        <option value="<?= UserStrike::REASON_LATE_CANCEL ?>">Pembatalan Terlambat</option>
                        <option value="<?= UserStrike::REASON_DAMAGE ?>">Kerusakan Ruangan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" class="form-control" placeholder="Keterangan tambahan...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <?= Html::submitButton('Terbitkan', ['class' => 'btn btn-warning w-100']) ?>
                </div>
            </div>
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Strike history -->
    <h5>Riwayat Strike</h5>
    <?php if (empty($strikes)): ?>
        <div class="alert alert-info">Tidak ada strike.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Alasan</th>
                    <th>Catatan</th>
                    <th>Reservasi</th>
                    <th>Diterbitkan</th>
                    <th>Kadaluarsa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($strikes as $strike): ?>
                    <tr>
                        <td><?= Html::encode(UserStrike::reasonLabel($strike->reason)) ?></td>
                        <td><?= Html::encode($strike->notes) ?></td>
                        <td><?= $strike->reservation_id ? "#{$strike->reservation_id}" : '–' ?></td>
                        <td><?= date('d M Y H:i', strtotime($strike->created_at)) ?></td>
                        <td><?= $strike->expires_at ? date('d M Y', strtotime($strike->expires_at)) : 'Permanen' ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>
