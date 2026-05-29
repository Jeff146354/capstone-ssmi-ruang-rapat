<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string $type
 * @property string $message
 * @property int    $reservation_id
 * @property bool   $is_read
 * @property string $created_at
 *
 * @property User        $user
 * @property Reservation $reservation
 */
class Notification extends ActiveRecord
{
    const TYPE_RESERVATION_APPROVED  = 'reservation_approved';
    const TYPE_RESERVATION_CANCELED  = 'reservation_canceled';
    const TYPE_RESERVATION_BUMPED    = 'reservation_bumped';
    const TYPE_WAITLIST_AVAILABLE    = 'waitlist_available';
    const TYPE_STRIKE_ISSUED         = 'strike_issued';
    const TYPE_SUSPENSION_ISSUED     = 'suspension_issued';

    public static function tableName()
    {
        return '{{%notifications}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'type', 'message'], 'required'],
            [['user_id', 'reservation_id'], 'integer'],
            [['is_read'], 'boolean'],
            [['message'], 'string'],
            [['type'], 'in', 'range' => array_keys(self::optsType())],
        ];
    }

    public static function optsType(): array
    {
        return [
            self::TYPE_RESERVATION_APPROVED => 'Reservasi Disetujui',
            self::TYPE_RESERVATION_CANCELED => 'Reservasi Dibatalkan',
            self::TYPE_RESERVATION_BUMPED   => 'Reservasi Digeser (Prioritas)',
            self::TYPE_WAITLIST_AVAILABLE   => 'Slot Waitlist Tersedia',
            self::TYPE_STRIKE_ISSUED        => 'Strike Diterbitkan',
            self::TYPE_SUSPENSION_ISSUED    => 'Akun Disuspend',
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
     * Create and save a notification in one call.
     */
    public static function send(int $userId, string $type, string $message, ?int $reservationId = null): bool
    {
        $notif = new static();
        $notif->user_id        = $userId;
        $notif->type           = $type;
        $notif->message        = $message;
        $notif->reservation_id = $reservationId;
        $notif->is_read        = false;
        return $notif->save();
    }

    /**
     * Count unread notifications for a user.
     */
    public static function countUnread(int $userId): int
    {
        return (int) static::find()
            ->where(['user_id' => $userId, 'is_read' => false])
            ->count();
    }
}
