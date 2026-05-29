<?php
/** @var common\models\Room $room */
use yii\helpers\Html;
?>

<div class="container mt-5">

    <div class="card shadow-sm p-4" style="max-width:600px; margin:0 auto;">

        <div style="margin-bottom: 2px;">
            <?= Html::a('←', ['default/index'], ['class'=>'btn btn-secondary', 'title'=>'Kembali ke Dashboard', 'aria-label'=>'Kembali ke Dashboard']) ?>
        </div>

        <h2 style="text-align:center; margin-top: 0;"><?= Html::encode($model->name) ?></h2>

        <div style="text-align:center; margin:20px 0;">
            <img src="<?= Yii::getAlias('@web') . './uploads/' . Html::encode($model->fr_img ?: 'default-room.jpg') ?>"
                 alt="<?= Html::encode($model->name) ?>"
                 style="max-width:400px; width:100%; height:auto; object-fit:cover; border-radius:8px;" />
        </div>

        <div style="text-align:left; line-height:1.6;">
            <p><strong>Deskripsi:</strong><br><?= nl2br(Html::encode($model->description)) ?></p>
            
            <!-- Belum di implementasi -->
            <!-- <p><strong>Fasilitas:</strong></p> -->
            <!-- <ul> -->
            <?php // foreach ($room->fasilitasList as $fasilitas): ?>
                <!-- <li><?php // Html::encode($fasilitas) ?></li> -->
            <?php // endforeach; ?>
            <!-- </ul> -->

            <p><strong>Kapasitas:</strong> <?= Html::encode($model->capacity) ?> orang</p>
            
            <!-- pas balikin  kasih ? di depan php sama "= Html" -->

            <p><strong>Contact Person:</strong> <?= Html::encode($model->contact) ?></p>
            
            <p><strong>Lokasi:</strong><br><?= Html::encode($model->location) ?></p>
        </div>

        <div style="text-align:center; margin-top:20px;">
            <?= Html::a('Ajukan Peminjaman', ['peminjaman', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Lihat Jadwal', ['jadwal', 'room_id' => $model->id], ['class'=>'btn btn-outline-primary']) ?>
            <?= Html::a('Daftar Tunggu', ['waitlist-form', 'id' => $model->id], ['class'=>'btn btn-outline-warning']) ?>
        </div>
    </div>
</div>
