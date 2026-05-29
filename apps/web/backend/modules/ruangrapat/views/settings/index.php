<?php
/** @var yii\web\View $this */
/** @var common\models\BookingRule[] $rules */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Pengaturan Aturan Peminjaman';
?>
<div class="container mt-4">
    <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif ?>

    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-4">
                Semua aturan di bawah ini dapat diubah kapan saja. Perubahan langsung berlaku untuk peminjaman baru.
            </p>

            <?php $form = ActiveForm::begin(['action' => ['settings/update'], 'method' => 'post']); ?>

            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:35%">Aturan</th>
                        <th style="width:15%">Nilai</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rules as $rule): ?>
                        <tr>
                            <td>
                                <code><?= Html::encode($rule->rule_key) ?></code>
                            </td>
                            <td>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    name="BookingRule[<?= Html::encode($rule->rule_key) ?>]"
                                    value="<?= Html::encode($rule->rule_value) ?>"
                                    required
                                />
                            </td>
                            <td class="text-muted small"><?= Html::encode($rule->description) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <div class="text-end mt-3">
                <?= Html::submitButton('Simpan Semua Perubahan', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
