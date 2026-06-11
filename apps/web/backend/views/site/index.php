<?php
/** @var yii\web\View $this */
// Redirect to the actual admin dashboard
Yii::$app->response->redirect(\yii\helpers\Url::to(['/ruang-rapat']))->send();
exit;
