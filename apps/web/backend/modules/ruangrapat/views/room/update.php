<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Room $model */

$this->title = 'Update Ruangan: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Ruang Rapat', 'url' => ['/ruang-rapat']];
$this->params['breadcrumbs'][] = ['label' => 'Ruangan', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="room-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
