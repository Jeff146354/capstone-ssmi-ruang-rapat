<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $user_id
 * @property int    $room_id
 * @property string $date
 * @property string $start_time
 * @property string $end_time
 * @property string $status
 * @property string $notified_at
 * @property string $created_at
 *
 * @property User $user
 * @property Room $room
 */
class ReservationWaitlist extends ActiveRecord
{
    const STATUS_WAITING  = 'waiting';
    const STATUS_NOTIFIED = 'notified';
    const STATUS_CLAIMED  = 'claimed';
    const STATUS_EXPIRED  = 'expired';

    public static function tableName()
    {
        return '{{%reservation_waitlist}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'room_id', 'date', 'start_time', 'end_time'], 'required'],
            [['user_id', 'room_id'], 'integer'],
            [['date', 'start_time', 'end_time', 'notified_at'], 'safe'],
            [['status'], 'in', 'range' => [
                self::STATUS_WAITING,
                self::STATUS_NOTIFIED,
                self::STATUS_CLAIMED,
                self::STATUS_EXPIRED,
            ]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id'     => 'Pengguna',
            'room_id'     => 'Ruangan',
            'date'        => 'Tanggal',
            'start_time'  => 'Waktu Mulai',
            'end_time'    => 'Waktu Selesai',
            'status'      => 'Status',
            'notified_at' => 'Diberitahu Pada',
            'created_at'  => 'Didaftarkan Pada',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::class, ['id' => 'room_id']);
    }

    /**
     * Check if this waitlist entry's claim window has expired.
     */
    public function isClaimExpired(): bool
    {
        if ($this->status !== self::STATUS_NOTIFIED || !$this->notified_at) {
            return false;
        }
        $claimDays = BookingRule::getInt('waitlist_claim_days', 3);
        $expiry = strtotime($this->notified_at) + ($claimDays * 86400);
        return time() > $expiry;
    }

    /**
     * Find the best waitlist match for a newly opened slot.
     * Priority: best time overlap first, then oldest created_at.
     */
    public static function findBestMatch(int $roomId, string $date, string $startTime, string $endTime): ?self
    {
        $candidates = static::find()
            ->where([
                'room_id' => $roomId,
                'date'    => $date,
                'status'  => self::STATUS_WAITING,
            ])
            ->andWhere(['<', 'start_time', $endTime])
            ->andWhere(['>', 'end_time', $startTime])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        if (empty($candidates)) {
            return null;
        }

        // Score by overlap duration — pick the one with the most overlap
        $best = null;
        $bestOverlap = -1;

        foreach ($candidates as $candidate) {
            $overlapStart = max(strtotime($candidate->start_time), strtotime($startTime));
            $overlapEnd   = min(strtotime($candidate->end_time), strtotime($endTime));
            $overlap      = $overlapEnd - $overlapStart;

            if ($overlap > $bestOverlap) {
                $bestOverlap = $overlap;
                $best = $candidate;
            }
        }

        return $best;
    }
}
