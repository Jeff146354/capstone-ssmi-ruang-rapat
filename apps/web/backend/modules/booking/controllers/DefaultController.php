<?php

namespace backend\modules\booking\controllers;

use common\models\Reservation;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default controller for the `booking` module
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /** 
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {        
        $orderBy = Yii::$app->request->get('orderBy', 'status');
        switch ($orderBy) {
            case 'date':
                $sort = ['date' => SORT_ASC, 'start_time' => SORT_ASC];
                break;
            case 'room':
                $sort = ['room.name' => SORT_ASC];
                break;
            case 'status':
            default:
                $sort = [new Expression(sprintf(
                    "FIELD(status, '%s', '%s', '%s')",
                    Reservation::STATUS_APPROVED,
                    Reservation::STATUS_PENDING,
                    Reservation::STATUS_CANCELED
                ))];
                break;
        }

        $user = Yii::$app->user->identity;
        $reservations = $user->getReservations()
            ->joinWith(['room'])
            ->with([
                'user' => function($query) {
                    $query->select(['id', 'username']);
                }
            ])
            ->orderBy($sort)
            ->asArray()
            ->all();

        return $this->render('index', [
            'reservations' => $reservations,
            'orderBy' => $orderBy
        ]);
    }

    public function actionAdmin()
    {
        $orderBy = Yii::$app->request->get('orderBy', 'status');
        switch ($orderBy) {
            case 'date':
                $sort = ['date' => SORT_ASC, 'start_time' => SORT_ASC];
                break;
            case 'room':
                $sort = ['room.name' => SORT_ASC];
                break;
            case 'status':
            default:
                $sort = [new Expression(sprintf(
                    "FIELD(status, '%s', '%s', '%s')",
                    Reservation::STATUS_PENDING,
                    Reservation::STATUS_CANCELED,
                    Reservation::STATUS_APPROVED,
                ))];
                break;
        }

        $reservations = Reservation::find()
            ->joinWith(['room'])
            ->with([
                'user' => function($query) {
                    $query->select(['id', 'username']);
                }
            ])
            ->orderBy($sort)
            ->asArray()
            ->all();

        return $this->render('admin', [
            'reservations' => $reservations,
            'orderBy' => $orderBy
        ]);
    }
}
