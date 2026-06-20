<?php

namespace frontend\modules\ruangrapat\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\db\Expression;
use common\models\Room;
use common\models\Reservation;
use common\models\ReservationWaitlist;
use common\models\Notification;
use frontend\modules\ruangrapat\models\FindRoomForm;
use common\services\ReservationService;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        // Find room form
        $model = new FindRoomForm();

        $request = Yii::$app->request;
        $peserta = $request->get('peserta');
        $tanggal = $request->get('tanggal');

        $queryFeatured = Room::find()
            ->select(['room.*', 'reservation_count' => new Expression('COUNT(reservations.id)')])
            ->leftJoin('reservations', 'reservations.room_id = room.id');
            // ->where(['room.is_featured' => 1]);

        $queryOther = Room::find()
            ->select(['room.*', 'reservation_count' => new Expression('COUNT(reservations.id)')])
            ->leftJoin('reservations', 'reservations.room_id = room.id');
            // ->where(['room.is_featured' => 0]);

        $featuredRooms = $queryFeatured
            ->groupBy('room.id')
            ->orderBy(['reservation_count' => SORT_DESC])
            ->all();
        $otherRooms = $queryOther
            ->groupBy('room.id')
            ->orderBy(['reservation_count' => SORT_DESC])
            ->all();

        return $this->render('dashboard', [
            'featuredRooms' => $featuredRooms,
            'otherRooms' => $otherRooms,
            'peserta' => $peserta,
            'tanggal' => $tanggal,
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = Room::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("Ruang tidak ditemukan.");
        }

        return $this->render('ruangan-detail', [
            'model' => $model,
        ]);
    }

    public function actionDaftarRuangan()
    {  
        $rooms = Room::find()->all();
        return $this->render('daftar-ruangan', [
            'rooms' => $rooms
        ]);
    }

    public function actionPeminjaman($id)
    {
        $room = Room::findOne($id);
        if ($room === null) {
            throw new NotFoundHttpException("Ruang dengan ID {$id} tidak ditemukan.");
        }

        $model = new Reservation();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->user_id = Yii::$app->user->id;
            $model->room_id = $room->id;

            // Handle surat peminjaman file upload
            $uploadedFile = \yii\web\UploadedFile::getInstance($model, 'document');
            if ($uploadedFile && !$uploadedFile->hasError) {
                $uploadDir = Yii::getAlias('@frontend/web/uploads/documents/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = 'surat_' . Yii::$app->user->id . '_' . time() . '.' . $uploadedFile->extension;
                if ($uploadedFile->saveAs($uploadDir . $filename)) {
                    $model->document = 'documents/' . $filename;
                }
            } else {
                $model->document = null;
            }

            $result = ReservationService::create(
                $model->toArray(),
                Yii::$app->user->id
            );

            if ($result['success']) {
                Yii::$app->session->setFlash('success', 'Pengajuan peminjaman berhasil diajukan. Menunggu persetujuan admin.');
                return $this->redirect(['/ruang-rapat/default/index']);
            }

            $model = $result['model'];

            // If room availability was the issue, offer waitlist
            if ($model->hasErrors('room_id')) {
                return $this->render('waitlist', [
                    'room'      => $room,
                    'date'      => $model->date,
                    'startTime' => $model->start_time,
                    'endTime'   => $model->end_time,
                ]);
            }
        } else {
            $model->user_id = Yii::$app->user->id;
            $model->room_id = $room->id;
        }

        return $this->render('peminjaman', [
            'model' => $model,
            'room'  => $room,
        ]);
    }

    public function actionRiwayatPeminjaman()
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

        if (!$user) {
            return $this->redirect(['/site/login']);
        }

        $reservations = $user->getReservations()
            ->joinWith(['room'])
            ->with([
                'user' => function($query) {
                    $query->select(['id', 'username']);
                }
            ])
            ->orderBy($sort)
            ->all();

        return $this->render('riwayat', [
            'reservations' => $reservations,
            'orderBy' => $orderBy
        ]);
    }

    public function actionJadwal($room_id)
    {
        $room = Room::findOne($room_id);
        if (!$room) {
            throw new NotFoundHttpException("Ruangan tidak ditemukan.");
        }

        $reservations = Reservation::find()
            ->where(['room_id' => $room_id])
            ->orderBy(['date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        return $this->render('jadwal-ruangan', [
            'room' => $room,
            'reservations' => $reservations,
        ]);
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
            )->all();
        }

        return $this->render('rekomendasi', [
            'model' => $model,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Show user's notifications and mark them as read.
     */
    public function actionNotifications()
    {
        $userId = Yii::$app->user->id;
        $notifications = Notification::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        // Mark all as read
        Notification::updateAll(['is_read' => true], ['user_id' => $userId, 'is_read' => false]);

        return $this->render('notifications', ['notifications' => $notifications]);
    }

    /**
     * User cancels their own reservation.
     */
    public function actionCancel($id)
    {
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\BadRequestHttpException();
        }

        $reservation = Reservation::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$reservation) {
            throw new \yii\web\NotFoundHttpException('Reservasi tidak ditemukan.');
        }

        $result = ReservationService::cancel((int) $id, 'user', 'Dibatalkan oleh pengguna.');
        Yii::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message']);

        return $this->redirect(['/ruang-rapat/default/riwayat-peminjaman']);
    }

    /**
     * Show waitlist form for a specific room.
     */
    public function actionWaitlistForm($id)
    {
        $room = Room::findOne($id);
        if (!$room) {
            throw new NotFoundHttpException("Ruangan tidak ditemukan.");
        }

        $date      = Yii::$app->request->get('date', date('Y-m-d'));
        $startTime = Yii::$app->request->get('startTime', '');
        $endTime   = Yii::$app->request->get('endTime', '');

        return $this->render('waitlist', [
            'room'      => $room,
            'date'      => $date,
            'startTime' => $startTime,
            'endTime'   => $endTime,
        ]);
    }

    /**
     * User joins the waitlist for a room/time slot.
     */
    public function actionJoinWaitlist()
    {
        if (!Yii::$app->request->isPost) {
            throw new \yii\web\BadRequestHttpException();
        }

        $entry = new ReservationWaitlist();
        $entry->user_id = Yii::$app->user->id;

        if ($entry->load(Yii::$app->request->post()) && $entry->save()) {
            Yii::$app->session->setFlash('success', 'Anda telah masuk ke daftar tunggu. Kami akan memberitahu Anda jika slot tersedia.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal mendaftar ke waitlist.');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['/ruang-rapat/default/index']);
    }

    /**
     * User claims a waitlist slot.
     */
    public function actionClaimWaitlist($id)
    {
        $result = ReservationService::claimWaitlist((int) $id, Yii::$app->user->id);
        Yii::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message']);
        return $this->redirect(['/ruang-rapat/default/riwayat-peminjaman']);
    }
}
