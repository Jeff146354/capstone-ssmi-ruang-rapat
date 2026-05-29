<?php

namespace backend\modules\ruangrapat\controllers;

use Yii;
use yii\web\Controller;
use common\models\Room;
use common\models\Reservation;
use common\models\User;
use yii\web\NotFoundHttpException;
use backend\components\BaseAdminModuleController;

/**
 * Default controller for the `ruang-rapat` module
 */
class DefaultController extends BaseAdminModuleController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $totalRuangan = Room::find()->count();
        $permintaanReservasi = Reservation::find()->where(['status' => Reservation::STATUS_PENDING])->count();
        $penggunaTerdaftar = User::find()->where(['role' => User::ROLE_USER])->count();
        return $this->render('index', [
            'user' => $user,
            'totalRuangan' => $totalRuangan,
            'permintaanReservasi' => $permintaanReservasi,
            'penggunaTerdaftar' => $penggunaTerdaftar,
        ]);
    }

    public function actionRooms()
    {
        $request = Yii::$app->request;
        $model = new Room();

        if ($request->isPost) {
            $post = $request->post();

            // Jika terdapat Room[id], berarti ini update
            if (!empty($post['Room']['id'])) {
                $model = Room::findOne($post['Room']['id']);
                if (!$model) {
                    throw new NotFoundHttpException("Ruangan tidak ditemukan.");
                }
            }

            if ($model->load($post) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Data ruang berhasil disimpan.');
                return $this->redirect(['index']);
            }
        }

        $rooms = Room::find()->all();
        return $this->render('rooms', [
            'model' => $model,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Deletes an existing Room model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteRoom($id)
    {
        $model = Room::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("Ruangan dengan ID $id tidak ditemukan.");
        }

        $model->delete();

        Yii::$app->session->setFlash('success', "Ruangan $id berhasil dihapus.");
        return $this->redirect(['rooms']);
    }
}
