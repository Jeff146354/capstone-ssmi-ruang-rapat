<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Room $model */

$this->title = 'Buat Ruangan';
$this->params['breadcrumbs'][] = ['label' => 'Ruang Rapat', 'url' => ['/ruang-rapat']];
$this->params['breadcrumbs'][] = ['label' => 'Ruangan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="room-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
