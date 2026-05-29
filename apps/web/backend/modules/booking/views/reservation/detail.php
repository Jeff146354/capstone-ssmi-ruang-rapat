<?php
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\modules\booking\models\Reservation $model */

$this->title = 'Detail Jadwal';
?>

<h1><?= $this->title ?></h1>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [
            'label' => 'Nama Ruangan',
            'value' => $model->room ? $model->room->name : '(Tidak tersedia)',
        ],
        [
            'label' => 'Nama Peminjam',
            'value' => $model->user ? $model->user->username : '(Tidak tersedia)',
        ],
        'date',
        'start_time',
        'end_time',
        'status',
        'document',
        'affiliation',
        'reason_of_use',
        // tambah kolom lain jika diperlukan
    ],
]) ?>