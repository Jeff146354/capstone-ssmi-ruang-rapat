<?php

namespace backend\modules\ruangrapat\controllers;

use backend\components\BaseAdminModuleController;
use common\models\BookingRule;
use Yii;

/**
 * Admin-configurable booking rules/settings.
 */
class SettingsController extends BaseAdminModuleController
{
    public function actionIndex()
    {
        $rules = BookingRule::find()->orderBy(['rule_key' => SORT_ASC])->all();
        return $this->render('index', ['rules' => $rules]);
    }

    public function actionUpdate()
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['index']);
        }

        $data = Yii::$app->request->post('BookingRule', []);
        $saved = 0;

        foreach ($data as $key => $value) {
            if (BookingRule::set($key, trim($value))) {
                $saved++;
            }
        }

        Yii::$app->session->setFlash('success', "{$saved} aturan berhasil disimpan.");
        return $this->redirect(['index']);
    }
}
