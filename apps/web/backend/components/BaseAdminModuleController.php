<?php

namespace backend\components;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * All backend module controllers should extend this.
 * Ensures only admin-role users can access backend module actions.
 */
class BaseAdminModuleController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/site/login']);
                    }
                    throw new ForbiddenHttpException('Anda tidak memiliki akses ke halaman ini.');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity &&
                                   Yii::$app->user->identity->role === 'admin';
                        },
                    ],
                ],
            ],
        ]);
    }
}
