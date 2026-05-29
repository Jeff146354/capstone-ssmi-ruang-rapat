<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Room;

$this->title = 'Form Peminjaman';
$this->params['breadcrumbs'][] = [
    'label' => $room->name,
    'url' => ['view', 'id' => $room->id],
];
$this->params['breadcrumbs'][] = $this->title;
$model->room_id = $room->id; // Auto select
?>

<h1>Ajukan Peminjaman untuk <?= Html::encode($room->name) ?></h1>

<div class="peminjaman-form">
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

    <?= $form->field($model, 'document')->fileInput(['class' => 'form-control'])->label('Surat Peminjaman (PDF/DOC)') ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Ajukan Peminjaman', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
