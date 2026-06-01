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

            // Determine if this is an update (hidden id field present) or a create
            $isUpdate = !empty($post['Room']['id']);

            if ($isUpdate) {
                $model = Room::findOne((int) $post['Room']['id']);
                if (!$model) {
                    throw new NotFoundHttpException("Ruangan tidak ditemukan.");
                }
            }

            // Keep the old image filename in case no new file is uploaded
            $oldImage = $model->fr_img;

            if ($model->load($post)) {
                // Handle image upload
                $uploadedFile = \yii\web\UploadedFile::getInstance($model, 'fr_img');

                if ($uploadedFile && !$uploadedFile->hasError) {
                    // Save to frontend/web/uploads/
                    $uploadDir = Yii::getAlias('@frontend/web/uploads/');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Delete old image if replacing
                    if ($oldImage && file_exists($uploadDir . $oldImage)) {
                        unlink($uploadDir . $oldImage);
                    }

                    $filename = 'room_' . time() . '_' . uniqid() . '.' . $uploadedFile->extension;
                    if ($uploadedFile->saveAs($uploadDir . $filename)) {
                        $model->fr_img = $filename;
                    } else {
                        $model->fr_img = $oldImage; // fallback to old image on save failure
                    }
                } else {
                    // No new file uploaded — keep the existing image
                    $model->fr_img = $oldImage;
                }

                // For updates, skip unique validation on 'room' field if it hasn't changed
                if ($isUpdate) {
                    $model->save();
                } else {
                    $model->save();
                }

                if (!$model->hasErrors()) {
                    Yii::$app->session->setFlash('success', 'Data ruang berhasil disimpan.');
                    return $this->redirect(['rooms']);
                }
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
