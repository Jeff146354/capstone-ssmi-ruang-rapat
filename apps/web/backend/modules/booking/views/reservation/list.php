<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\FindRoomForm $model */
/** @var array $rooms */
?>

<h1>Find Available Rooms</h1>

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => ['reservation/find-available-rooms']
]); ?>

    <?= $form->field($model, 'date')->input('date') ?>
    <?= $form->field($model, 'startTime')->input('time') ?>
    <?= $form->field($model, 'endTime')->input('time') ?>
    <?= $form->field($model, 'minCapacity')->input('number') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<hr>

<?php if (!empty($rooms)): ?>
    <h2>Available Rooms</h2>
    <ul>
        <?php foreach ($rooms as $room): ?>
            <li>
                <?= Html::encode($room['name']) ?> - Capacity: <?= Html::encode($room['capacity']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php elseif ($model->date && $model->startTime && $model->endTime): ?>
    <p>No rooms available for the selected time and capacity.</p>
<?php endif; ?>
