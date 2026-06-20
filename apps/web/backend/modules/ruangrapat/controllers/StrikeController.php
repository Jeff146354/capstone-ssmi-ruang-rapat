<?php

namespace backend\modules\ruangrapat\controllers;

use backend\components\BaseAdminModuleController;
use common\models\User;
use common\models\UserStrike;
use Yii;

/**
 * Admin management of user strikes and suspensions.
 */
class StrikeController extends BaseAdminModuleController
{
    /**
     * List all users with active strikes + all users for search.
     */
    public function actionIndex()
    {
        $users = User::find()
            ->joinWith(['strikes'])
            ->where(['>', 'user_strikes.id', 0])
            ->groupBy('user.id')
            ->all();

        // All non-admin users for the search/issue section
        $allUsers = User::find()->where(['!=', 'role', 'admin'])->orderBy(['username' => SORT_ASC])->all();

        return $this->render('index', ['users' => $users, 'allUsers' => $allUsers]);
    }

    /**
     * View all strikes for a specific user.
     */
    public function actionView($userId)
    {
        $user   = User::findOne($userId);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('Pengguna tidak ditemukan.');
        }
        $strikes = UserStrike::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('view', ['user' => $user, 'strikes' => $strikes]);
    }

    /**
     * Clear all strikes and suspensions for a user (admin pardon).
     */
    public function actionClear($userId)
    {
        $user = User::findOne($userId);
        if (!$user) {
            throw new \yii\web\NotFoundHttpException('Pengguna tidak ditemukan.');
        }

        UserStrike::deleteAll(['user_id' => $userId]);
        $user->booking_suspended_until  = null;
        $user->requires_manual_approval = false;
        $user->save(false);

        \common\models\Notification::send(
            $userId,
            \common\models\Notification::TYPE_STRIKE_ISSUED,
            'Semua strike Anda telah dihapus oleh admin. Akun Anda kembali normal.'
        );

        Yii::$app->session->setFlash('success', "Strike pengguna {$user->username} berhasil dihapus.");
        return $this->redirect(['index']);
    }

    /**
     * Manually issue a strike to a user.
     */
    public function actionIssue($userId)
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['view', 'userId' => $userId]);
        }

        $reason = Yii::$app->request->post('reason', UserStrike::REASON_NO_SHOW);
        $notes  = Yii::$app->request->post('notes', '');

        $count = UserStrike::issue($userId, $reason, null, $notes . ' (Diterbitkan manual oleh admin)');

        Yii::$app->session->setFlash('success', "Strike berhasil diterbitkan. Total strike aktif: {$count}.");
        return $this->redirect(['view', 'userId' => $userId]);
    }
}
