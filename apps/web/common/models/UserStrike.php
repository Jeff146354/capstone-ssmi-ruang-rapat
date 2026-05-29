<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $user_id
 * @property int    $reservation_id
 * @property string $reason
 * @property string $notes
 * @property string $created_at
 * @property string $expires_at
 *
 * @property User        $user
 * @property Reservation $reservation
 */
class UserStrike extends ActiveRecord
{
    const REASON_NO_SHOW     = 'no_show';
    const REASON_LATE_CANCEL = 'late_cancel';
    const REASON_DAMAGE      = 'damage';

    public static function tableName()
    {
        return '{{%user_strikes}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'reason'], 'required'],
            [['user_id', 'reservation_id'], 'integer'],
            [['notes'], 'string'],
            [['created_at', 'expires_at'], 'safe'],
            [['reason'], 'in', 'range' => [
                self::REASON_NO_SHOW,
                self::REASON_LATE_CANCEL,
                self::REASON_DAMAGE,
            ]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id'        => 'Pengguna',
            'reservation_id' => 'Reservasi',
            'reason'         => 'Alasan',
            'notes'          => 'Catatan',
            'created_at'     => 'Diterbitkan Pada',
            'expires_at'     => 'Kadaluarsa Pada',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getReservation()
    {
        return $this->hasOne(Reservation::class, ['id' => 'reservation_id']);
    }

    /**
     * Count active (non-expired) strikes for a user.
     */
    public static function countActive(int $userId): int
    {
        return (int) static::find()
            ->where(['user_id' => $userId])
            ->andWhere([
                'or',
                ['expires_at' => null],
                ['>', 'expires_at', date('Y-m-d H:i:s')],
            ])
            ->count();
    }

    /**
     * Issue a strike to a user and apply consequences.
     * Returns the number of active strikes after issuing.
     */
    public static function issue(int $userId, string $reason, ?int $reservationId = null, ?string $notes = null): int
    {
        $strike = new static();
        $strike->user_id        = $userId;
        $strike->reason         = $reason;
        $strike->reservation_id = $reservationId;
        $strike->notes          = $notes;
        $strike->save();

        $activeStrikes = static::countActive($userId);
        $user = User::findOne($userId);

        if (!$user) {
            return $activeStrikes;
        }

        if ($activeStrikes === 2) {
            // Strike 2 — suspend for X days
            $suspendDays = BookingRule::getInt('strike_suspend_days', 3);
            $user->booking_suspended_until = date('Y-m-d H:i:s', strtotime("+{$suspendDays} days"));
            $user->save(false);

            Notification::send(
                $userId,
                Notification::TYPE_SUSPENSION_ISSUED,
                "Anda mendapat Strike 2. Akun Anda disuspend selama {$suspendDays} hari hingga " .
                date('d M Y', strtotime($user->booking_suspended_until)) . '.'
            );
        } elseif ($activeStrikes >= 3) {
            // Strike 3+ — require manual approval forever (until admin clears)
            $user->requires_manual_approval = true;
            $user->save(false);

            Notification::send(
                $userId,
                Notification::TYPE_STRIKE_ISSUED,
                'Anda mendapat Strike 3. Semua peminjaman Anda kini memerlukan persetujuan manual dari admin.'
            );
        } else {
            // Strike 1 — just a warning notification
            Notification::send(
                $userId,
                Notification::TYPE_STRIKE_ISSUED,
                'Anda mendapat Strike 1 karena ' . static::reasonLabel($reason) . '. ' .
                'Strike berikutnya akan mengakibatkan suspensi akun.'
            );
        }

        return $activeStrikes;
    }

    public static function reasonLabel(string $reason): string
    {
        return match ($reason) {
            self::REASON_NO_SHOW     => 'tidak hadir (no-show)',
            self::REASON_LATE_CANCEL => 'pembatalan terlambat',
            self::REASON_DAMAGE      => 'kerusakan ruangan',
            default                  => $reason,
        };
    }
}
