<?php

namespace common\models;

use Yii;

use common\models\BookingRule;

/**
 * This is the model class for table "reservations".
 *
 * @property int $id
 * @property int $user_id
 * @property int $room_id
 * @property string|null $document
 * @property string|null $affiliation
 * @property string|null $reason_of_use
 * @property string|null $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $rejection_reason
 * @property string|null $rejected_by
 * @property string|null $checked_in_at
 * @property int|null    $waitlist_id
 *
 * @property Room $room
 * @property User $user
 */
class Reservation extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_CANCELED = 'canceled';

    const REJECTED_BY_ADMIN  = 'admin';
    const REJECTED_BY_SYSTEM = 'system';
    const REJECTED_BY_USER   = 'user';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reservations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document', 'affiliation', 'reason_of_use', 'rejection_reason', 'rejected_by', 'checked_in_at'], 'default', 'value' => null],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            [['user_id', 'room_id', 'date', 'start_time', 'end_time'], 'required'],
            [['user_id', 'room_id', 'waitlist_id'], 'integer'],
            [['reason_of_use', 'status', 'rejection_reason', 'rejected_by'], 'string'],
            [['date', 'start_time', 'end_time', 'created_at', 'checked_in_at'], 'safe'],
            [['document', 'affiliation'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::class, 'targetAttribute' => ['room_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],

            // Business rule validations
            [['start_time', 'end_time'], 'validateTimeRange'],
            [['date', 'start_time'], 'validateNotPast'],
            [['date', 'start_time', 'end_time'], 'validateOperatingHours'],
            [['start_time', 'end_time'], 'validateDuration'],
            [['date'], 'validateAdvanceBooking'],
            [['user_id'], 'validateUserNotSuspended'],
            [['user_id'], 'validateMaxPendingPerUser'],
            [['room_id'], 'validateRoomAvailability'],
        ];
    }

    public function validateTimeRange($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (strtotime($this->start_time) >= strtotime($this->end_time)) {
                $this->addError($attribute, 'Waktu mulai harus lebih awal dari waktu selesai.');
            }
        }
    }

    public function validateNotPast($attribute, $params)
    {
        if (!$this->hasErrors('date') && !$this->hasErrors('start_time')) {
            $bookingTimestamp = strtotime($this->date . ' ' . $this->start_time);
            if ($bookingTimestamp <= time()) {
                $this->addError('date', 'Tidak bisa memesan ruangan di waktu yang sudah lewat.');
            }
        }
    }

    public function validateOperatingHours($attribute, $params)
    {
        if ($this->hasErrors()) return;

        $opStart = BookingRule::get('operating_hours_start', '07:00');
        $opEnd   = BookingRule::get('operating_hours_end', '21:00');

        $start = strtotime($this->start_time);
        $end   = strtotime($this->end_time);
        $opS   = strtotime($opStart);
        $opE   = strtotime($opEnd);

        if ($start < $opS || $end > $opE) {
            $this->addError('start_time', "Peminjaman hanya diperbolehkan antara {$opStart} – {$opEnd}.");
        }
    }

    public function validateDuration($attribute, $params)
    {
        if ($this->hasErrors()) return;

        $durationMinutes = (strtotime($this->end_time) - strtotime($this->start_time)) / 60;
        $minMinutes = BookingRule::getInt('min_duration_minutes', 30);
        $maxHours   = BookingRule::getInt('max_duration_hours', 4);

        if ($durationMinutes < $minMinutes) {
            $this->addError('end_time', "Durasi peminjaman minimal {$minMinutes} menit.");
        }
        if ($durationMinutes > $maxHours * 60) {
            $this->addError('end_time', "Durasi peminjaman maksimal {$maxHours} jam.");
        }
    }

    public function validateAdvanceBooking($attribute, $params)
    {
        if ($this->hasErrors('date')) return;

        $maxDays = BookingRule::getInt('max_advance_days', 30);
        $maxDate = strtotime("+{$maxDays} days");

        if (strtotime($this->date) > $maxDate) {
            $this->addError('date', "Peminjaman hanya bisa dilakukan maksimal {$maxDays} hari ke depan.");
        }
    }

    public function validateUserNotSuspended($attribute, $params)
    {
        if ($this->hasErrors()) return;

        $user = User::findOne($this->user_id);
        if ($user && $user->isSuspended()) {
            $until = date('d M Y H:i', strtotime($user->booking_suspended_until));
            $this->addError('user_id', "Akun Anda disuspend hingga {$until}. Anda tidak dapat membuat peminjaman baru.");
        }
    }

    public function validateMaxPendingPerUser($attribute, $params)
    {
        if ($this->hasErrors()) return;

        $maxPending = BookingRule::getInt('max_pending_per_user', 5);
        $currentPending = static::find()
            ->where(['user_id' => $this->user_id, 'status' => self::STATUS_PENDING])
            ->andWhere(['not', ['id' => $this->id]])
            ->count();

        if ($currentPending >= $maxPending) {
            $this->addError('user_id', "Anda sudah memiliki {$maxPending} peminjaman pending. Tunggu hingga salah satu diproses.");
        }
    }

    public function validateRoomAvailability($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // Apply buffer time between bookings
            $bufferMinutes = BookingRule::getInt('buffer_minutes_between', 15);
            $bufferedStart = date('H:i:s', strtotime($this->start_time) - ($bufferMinutes * 60));
            $bufferedEnd   = date('H:i:s', strtotime($this->end_time)   + ($bufferMinutes * 60));

            $query = self::find()
                ->andWhere(['room_id' => $this->room_id])
                ->andWhere(['date' => $this->date])
                ->andWhere(['status' => self::STATUS_APPROVED])
                ->andWhere(['not', ['id' => $this->id]])
                ->andWhere(['<', 'start_time', $bufferedEnd])
                ->andWhere(['>', 'end_time', $bufferedStart]);

            if ($query->exists()) {
                $this->addError($attribute, "Ruangan sudah digunakan pada waktu tersebut (termasuk buffer {$bufferMinutes} menit antar sesi).");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID Pengguna',
            'room_id' => 'ID Ruangan',
            'document' => 'Surat Peminjaman',
            'affiliation' => 'Afiliasi',
            'reason_of_use' => 'Alasan Penggunaan',
            'date' => 'Tanggal Peminjaman',
            'start_time' => 'Waktu Mulai',
            'end_time' => 'Waktu Selesai',
            'status' => 'Status Peminjaman',
            'created_at' => 'Diajukan pada',
        ];
    }

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::class, ['id' => 'room_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_APPROVED => 'approved',
            self::STATUS_CANCELED => 'canceled',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function setStatusToApproved()
    {
        $this->status = self::STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isStatusCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

    public function setStatusToCanceled()
    {
        $this->status = self::STATUS_CANCELED;
    }

    /**
     * Get user reservation based on userId
     * 
     * @param int $userId User ID
     */
    public function getUserReservations($userId)
    {
        return $this->hasMany(self::class, ['user_id' => $userId]);
    }

    /**
     * Find available room based on time and capacity
     *
     * @param string $date Format: YYYY-MM-DD
     * @param string $startTime Format: HH:MM:SS
     * @param string $endTime Format: HH:MM:SS
     * @param int|null $minCapacity Maximum capacity (optional)
     * @return \yii\db\ActiveQuery
     */
    public static function findAvailableRooms($date, $startTime, $endTime, $minCapacity = null)
    {
        $subQuery = self::find()
            ->select('room_id')
            ->andWhere(['date' => $date])
            ->andWhere(['status' => self::STATUS_APPROVED])
            ->andWhere([
                'or',
                ['and', ['<=', 'start_time', $startTime], ['>', 'end_time', $startTime]],
                ['and', ['<', 'start_time', $endTime], ['>=', 'end_time', $endTime]],
                ['and', ['>=', 'start_time', $startTime], ['<=', 'end_time', $endTime]],
            ]);

        $roomQuery = Room::find()
            ->where(['not in', 'id', $subQuery]);

        if ($minCapacity !== null) {
            $roomQuery->andWhere(['>=', 'capacity', $minCapacity]);
        }

        return $roomQuery;
    }
}
