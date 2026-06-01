<?php
/** @var yii\web\View $this */
/** @var array $recommendations */
/** @var common\models\Room[] $rooms */
/** @var frontend\modules\ruangrapat\models\FindRoomForm $model */

use yii\widgets\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Rekomendasi Ruangan';
?>
<div class="container mt-5 pt-4">
    <div class="mb-4 text-center">
        <h2 class="mb-3">Rekomendasi Ruangan</h2>
        <p class="text-secondary">Berikut adalah ruangan yang direkomendasikan untuk Anda berdasarkan preferensi penggunaan dan kebutuhan Anda.</p>
    </div>

    <!-- FORM FILTERING -->
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['default/find-available-rooms'],
        'options' => [
            'class' => 'd-flex justify-content-center align-items-end gap-3 position-relative mb-4'
        ]
    ]); ?>
        <?= $form->field($model, 'date')->input('date')->label('Tanggal') ?>
        <?= $form->field($model, 'startTime')->input('time')->label('Mulai') ?>
        <?= $form->field($model, 'endTime')->input('time')->label('Selesai') ?>
        <?= $form->field($model, 'minCapacity')->input('number')->label('Peserta') ?>

        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

    <div class="row g-4">
        <?php foreach ($rooms as $room): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm" style="cursor:pointer;" onclick="location.href='<?= \yii\helpers\Url::to(['default/view', 'id' => $room->id]) ?>'">
                    <img src="<?= Html::encode($room->imageUrl) ?>" class="card-img-top" alt="<?= Html::encode($room->name) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= Html::encode($room->name) ?></h5>
                        <p class="card-text text-secondary flex-grow-1">
                            <?= Html::encode($room->description) ?><br>
                            Kapasitas: <?= (int)$room->capacity ?> orang
                        </p>
                        <?= Html::a('Lihat Detail', ['default/view', 'id' => $room->id], ['class' => 'btn btn-success mt-auto', 'onclick' => 'event.stopPropagation();']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
