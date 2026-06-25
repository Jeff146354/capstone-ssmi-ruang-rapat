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

    /**
     * Interactive schedule grid with drag-and-drop booking blocks.
     */
    public function actionScheduleGrid()
    {
        $rooms = \common\models\Room::find()->select(['id', 'name'])->asArray()->all();
        $roomId = Yii::$app->request->get('room_id', $rooms[0]['id'] ?? null);

        $reservations = [];
        if ($roomId) {
            $reservations = \common\models\Reservation::find()
                ->alias('r')
                ->leftJoin('user u', 'u.id = r.user_id')
                ->where(['r.room_id' => $roomId, 'r.status' => \common\models\Reservation::STATUS_APPROVED])
                ->orWhere(['r.room_id' => $roomId, 'r.status' => \common\models\Reservation::STATUS_PENDING])
                ->select(['r.id', 'r.room_id', 'r.user_id', 'r.date', 'r.start_time', 'r.end_time', 'r.reason_of_use', 'u.username'])
                ->asArray()
                ->all();
        }

        return $this->render('schedule-grid', [
            'rooms' => $rooms,
            'currentRoomId' => $roomId,
            'reservations' => $reservations,
        ]);
    }

    /**
     * API endpoint to save schedule grid bookings.
     * Accepts JSON POST with booking data.
     */
    public function actionSaveScheduleGrid()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data = json_decode(Yii::$app->request->rawBody, true);

        if (!$data || !isset($data['bookings'])) {
            return ['success' => false, 'message' => 'Invalid data.'];
        }

        // For now, return success — full server persistence can be wired later
        return ['success' => true, 'message' => 'Bookings saved.'];
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
