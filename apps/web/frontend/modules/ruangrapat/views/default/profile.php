<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\ChangePasswordForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Profil Saya';
?>

<div class="container mt-5 pt-4">
    <div class="row">
        <!-- Info Profil -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> <?= Html::encode($user->nama) ?></p>
                    <p><strong>Email:</strong> <?= Html::encode($user->email) ?></p>
                    <p><strong>Role:</strong> <?= Html::encode($user->role) ?></p>
                    <!-- Tambahkan info lain sesuai kolom di tabel user -->
                </div>
            </div>
        </div>

        <!-- Form Ganti Password -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Ganti Password</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'change-password-form']); ?>

                        <?= $form->field($model, 'currentPassword')->passwordInput(['placeholder' => 'Password saat ini']) ?>

                        <?= $form->field($model, 'newPassword')->passwordInput(['placeholder' => 'Password baru']) ?>

                        <?= $form->field($model, 'confirmPassword')->passwordInput(['placeholder' => 'Konfirmasi password baru']) ?>

                        <div class="form-group mt-3">
                            <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
