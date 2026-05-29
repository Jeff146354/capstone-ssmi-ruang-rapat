<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\web\Response;
use backend\components\BaseAdminController;
use common\models\User;

/**
 * Site controller
 */
class SiteController extends BaseAdminController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Cek apakah user bukan admin
            if (Yii::$app->user->identity->role !== User::ROLE_ADMIN) {
                Yii::$app->user->logout(); // log out user
                Yii::$app->session->setFlash('error', 'Akun Anda tidak memiliki akses sebagai admin.');
                return $this->redirect(['site/login']);
            }

            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
