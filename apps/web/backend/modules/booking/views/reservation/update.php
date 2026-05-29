<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Room;

/* @var $this yii\web\View */
/* @var $model common\models\Schedule */
/* @var $form yii\widgets\ActiveForm */
?>

<h1>Ajukan Ulang Reservasi Ruang Rapat</h1>

<div class="schedule-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <?= $form->field($model, 'room_id')->dropDownList(
        ArrayHelper::map(Room::find()->all(), 'id', 'name'),
        ['prompt' => 'Pilih Ruang Rapat']
    ) ?>

    <?= $form->field($model, 'date')->input('date') ?>

    <?= $form->field($model, 'start_time')->input('time') ?>

    <?= $form->field($model, 'end_time')->input('time') ?>

    <?= $form->field($model, 'affiliation')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reason_of_use')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'document')->fileInput() ?>
    
    <div class="form-group">
        <?= Html::submitButton('Ajukan Ulang', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>