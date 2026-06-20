<?php
/** @var yii\web\View $this */
/** @var common\models\User[] $users */
/** @var common\models\User[] $allUsers */

use common\models\UserStrike;
use yii\bootstrap5\Html;

$this->title = 'Manajemen Strike Pengguna';
?>
<div class="container mt-4">
    <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success rounded-3"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>

    <!-- Cari & Terbitkan Strike ke Pengguna -->
    <div class="card mb-4" style="border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="fas fa-search me-2" style="color:#FF6B00"></i>Cari Pengguna</h5>
            <p class="text-muted small">Pilih pengguna untuk melihat detail strike atau menerbitkan strike baru.</p>

            <?php if (!empty($allUsers)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="userTable">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Strike</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $u): ?>
                                <?php if ($u->role === 'admin') continue; ?>
                                <?php $count = UserStrike::countActive($u->id); ?>
                                <tr>
                                    <td><strong><?= Html::encode($u->username) ?></strong></td>
                                    <td class="text-muted small"><?= Html::encode($u->email) ?></td>
                                    <td>
                                        <?php
                                        $labels = [1 => 'Mahasiswa', 2 => 'Staff', 3 => 'Dosen'];
                                        echo $labels[$u->priority] ?? 'User';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($count > 0): ?>
                                            <span class="badge bg-<?= $count >= 3 ? 'danger' : ($count === 2 ? 'warning text-dark' : 'secondary') ?>">
                                                <?= $count ?> strike
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">0</span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?= Html::a('<i class="fas fa-eye me-1"></i>Detail / Terbitkan', ['view', 'userId' => $u->id], [
                                            'class' => 'btn btn-sm',
                                            'style' => 'background:#FF6B00; color:#fff; font-size:12px; font-weight:600; border-radius:6px;',
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">Belum ada pengguna terdaftar.</div>
            <?php endif ?>
        </div>
    </div>

    <!-- Pengguna dengan Strike Aktif -->
    <?php if (!empty($users)): ?>
    <div class="card" style="border:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div class="card-body">
            <h5 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle me-2" style="color:#f59e0b"></i>Pengguna dengan Strike Aktif</h5>
            <table class="table table-hover">
                <thead style="background:#151C27; color:#fff;">
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
                            <td><strong><?= Html::encode($user->username) ?></strong></td>
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
                                    <span class="badge bg-danger">Suspended</span>
                                <?php elseif ($user->requires_manual_approval): ?>
                                    <span class="badge bg-warning text-dark">Manual Approval</span>
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
        </div>
    </div>
    <?php endif ?>
</div>
