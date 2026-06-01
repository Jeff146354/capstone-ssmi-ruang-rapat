<?php
/** @var yii\web\View $this */
/** @var common\models\Room[] $rooms */

use yii\bootstrap5\Html;

$this->title = 'Daftar Ruangan';
?>
<div class="container mt-5 pt-4">
    <div class="mb-4 text-center">
        <h2 class="mb-3">Daftar Seluruh Ruangan</h2>
        <p class="text-secondary">Berikut daftar ruang rapat yang tersedia di SSMI IPB.</p>
    </div>

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
                        <?= Html::a('Detail Ruang', ['default/view', 'id' => $room->id], ['class' => 'btn btn-primary mt-auto', 'onclick' => 'event.stopPropagation();']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
