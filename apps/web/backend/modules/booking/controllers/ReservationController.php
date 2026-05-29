<?php

namespace backend\modules\booking\controllers;

use backend\components\BaseAdminModuleController;
use backend\modules\booking\models\FindRoomForm;
use common\models\Reservation;
use common\models\UserStrike;
use common\services\ReservationService;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ReservationController extends BaseAdminModuleController
{
    /**
     * Override: allow regular logged-in users for user-facing actions,
     * admin-only for approve/cancel.
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    // User-facing actions — any logged-in user
                    [
                        'actions' => ['create', 'update', 'delete', 'detail', 'find-available-rooms'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                    // Admin-only actions
                    [
                        'actions' => ['approve', 'cancel'],
                        'allow'   => true,
                        'roles'   => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity &&
                                   Yii::$app->user->identity->role === 'admin';
                        },
                    ],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // User-facing actions
    // -------------------------------------------------------------------------

    public function actionCreate()
    {
        $result = ReservationService::create(
            Yii::$app->request->post('Reservation', []),
            Yii::$app->user->id
        );

        if ($result['success']) {
            Yii::$app->session->setFlash('success', 'Pengajuan berhasil diajukan.');
            return $this->redirect(['/booking/default/index']);
        }

        return $this->render('create', ['model' => $result['model']]);
    }

    public function actionDelete($id)
    {
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\BadRequestHttpException('Request tidak valid.');
        }

        $model = Reservation::findOne($id);
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Jadwal tidak ditemukan.');
            return $this->redirect(['/booking/default/index']);
        }

        if ($model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $result = ReservationService::cancel($id, 'user', 'Dibatalkan oleh pengguna.');
        Yii::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message']);

        return $this->redirect(['/booking/default/index']);
    }

    public function actionUpdate($id)
    {
        $model = Reservation::findOne($id);

        if (!$model) {
            Yii::$app->session->setFlash('error', 'Jadwal tidak ditemukan.');
            return $this->redirect(['/booking/default/index']);
        }

        if ($model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        // Only allow editing pending reservations
        if (!$model->isStatusPending()) {
            Yii::$app->session->setFlash('error', 'Hanya reservasi berstatus pending yang bisa diedit. Batalkan dan buat baru jika perlu mengubah reservasi yang sudah disetujui.');
            return $this->redirect(['/booking/default/index']);
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->status = Reservation::STATUS_PENDING;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Jadwal berhasil diperbarui.');
                return $this->redirect(['/booking/default/index']);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDetail($id)
    {
        $model = Reservation::findOne($id);

        if (!$model) {
            Yii::$app->session->setFlash('error', 'Jadwal tidak ditemukan.');
            return $this->redirect(['/booking/default/index']);
        }

        if ($model->user_id !== Yii::$app->user->id && Yii::$app->user->identity->role !== 'admin') {
            throw new ForbiddenHttpException();
        }

        return $this->render('detail', ['model' => $model]);
    }

    public function actionFindAvailableRooms()
    {
        $model = new FindRoomForm();
        $rooms = [];

        if ($model->load(Yii::$app->request->get()) && $model->validate()) {
            $rooms = Reservation::findAvailableRooms(
                $model->date,
                $model->startTime,
                $model->endTime,
                $model->minCapacity
            )->asArray()->all();
        }

        return $this->render('list', ['model' => $model, 'rooms' => $rooms]);
    }

    // -------------------------------------------------------------------------
    // Admin-only actions
    // -------------------------------------------------------------------------

    public function actionApprove($id)
    {
        $result = ReservationService::approve((int) $id);
        Yii::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message']);
        return $this->redirect(['/booking/default/admin']);
    }

    public function actionCancel($id)
    {
        $reason = Yii::$app->request->post('rejection_reason', '');
        $result = ReservationService::cancel((int) $id, 'admin', $reason);
        Yii::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message']);
        return $this->redirect(['/booking/default/admin']);
    }
}
