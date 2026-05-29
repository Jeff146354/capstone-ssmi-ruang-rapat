<?php
/** @var yii\web\View $this */
/** @var common\models\User[] $users */

use common\models\UserStrike;
use yii\bootstrap5\Html;

$this->title = 'Manajemen Strike Pengguna';
?>
<div class="container mt-4">
    <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>

    <?php if (empty($users)): ?>
        <div class="alert alert-info">Tidak ada pengguna dengan strike aktif.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Prioritas</th>
                    <th>Strike Aktif</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php $activeStrikes = UserStrike::countActive($user->id); ?>
                    <tr>
                        <td><?= Html::encode($user->username) ?></td>
                        <td><?= Html::encode($user->email) ?></td>
                        <td>
                            <?php
                            $labels = [1 => 'Mahasiswa', 2 => 'Staff', 3 => 'Dosen'];
                            echo Html::encode($labels[$user->priority] ?? 'User');
                            ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $activeStrikes >= 3 ? 'danger' : ($activeStrikes === 2 ? 'warning text-dark' : 'secondary') ?>">
                                <?= $activeStrikes ?> Strike
                            </span>
                        </td>
                        <td>
                            <?php if ($user->isSuspended()): ?>
                                <span class="badge bg-danger">
                                    Suspended hingga <?= date('d M Y', strtotime($user->booking_suspended_until)) ?>
                                </span>
                            <?php elseif ($user->requires_manual_approval): ?>
                                <span class="badge bg-warning text-dark">Perlu Approval Manual</span>
                            <?php else: ?>
                                <span class="badge bg-success">Normal</span>
                            <?php endif ?>
                        </td>
                        <td>
                            <?= Html::a('Detail', ['view', 'userId' => $user->id], ['class' => 'btn btn-info btn-sm']) ?>
                            <?= Html::a('Hapus Strike', ['clear', 'userId' => $user->id], [
                                'class' => 'btn btn-success btn-sm',
                                'data-confirm' => "Hapus semua strike untuk {$user->username}?",
                                'data-method'  => 'post',
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
</div>
