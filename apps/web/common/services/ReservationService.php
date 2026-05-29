<?php

namespace common\services;

use common\models\BookingRule;
use common\models\Notification;
use common\models\Reservation;
use common\models\ReservationWaitlist;
use common\models\User;
use common\models\UserStrike;
use Yii;

/**
 * Central service for all reservation business logic.
 * Controllers should call this instead of manipulating models directly.
 */
class ReservationService
{
    /**
     * Create a reservation inside a transaction with a pessimistic lock
     * to prevent race conditions.
     *
     * @return array ['success' => bool, 'model' => Reservation, 'errors' => array]
     */
    public static function create(array $data, int $userId): array
    {
        $model = new Reservation();
        $model->user_id = $userId;
        $model->load($data, '');

        // Validate first (without saving) to catch rule violations early
        if (!$model->validate()) {
            return ['success' => false, 'model' => $model, 'errors' => $model->errors];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Pessimistic lock: lock all approved reservations for this room+date
            // so concurrent requests queue up at DB level
            Yii::$app->db->createCommand(
                'SELECT id FROM reservations 
                 WHERE room_id = :room AND date = :date AND status = :status
                 FOR UPDATE',
                [
                    ':room'   => $model->room_id,
                    ':date'   => $model->date,
                    ':status' => Reservation::STATUS_APPROVED,
                ]
            )->queryAll();

            // Re-validate availability inside the lock
            $bufferMinutes = BookingRule::getInt('buffer_minutes_between', 15);
            $bufferedStart = date('H:i:s', strtotime($model->start_time) - ($bufferMinutes * 60));
            $bufferedEnd   = date('H:i:s', strtotime($model->end_time)   + ($bufferMinutes * 60));

            $conflict = Reservation::find()
                ->where(['room_id' => $model->room_id, 'date' => $model->date, 'status' => Reservation::STATUS_APPROVED])
                ->andWhere(['<', 'start_time', $bufferedEnd])
                ->andWhere(['>', 'end_time', $bufferedStart])
                ->exists();

            if ($conflict) {
                $transaction->rollBack();
                $model->addError('room_id', 'Ruangan sudah tidak tersedia (ada peminjaman lain yang baru saja disetujui).');
                return ['success' => false, 'model' => $model, 'errors' => $model->errors];
            }

            // Force manual approval if user has requires_manual_approval flag
            $user = User::findOne($userId);
            // (status stays pending regardless — admin must approve)

            if (!$model->save(false)) {
                $transaction->rollBack();
                return ['success' => false, 'model' => $model, 'errors' => $model->errors];
            }

            $transaction->commit();
            return ['success' => true, 'model' => $model, 'errors' => []];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('ReservationService::create failed: ' . $e->getMessage());
            return ['success' => false, 'model' => $model, 'errors' => ['general' => 'Terjadi kesalahan sistem. Silakan coba lagi.']];
        }
    }

    /**
     * Admin approves a reservation.
     * Handles: conflict check, priority bump, auto-cancel lower-priority conflicts,
     * notifications, and waitlist triggering.
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public static function approve(int $reservationId): array
    {
        $reservation = Reservation::findOne($reservationId);
        if (!$reservation) {
            return ['success' => false, 'message' => 'Reservasi tidak ditemukan.'];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Lock this room+date rows
            Yii::$app->db->createCommand(
                'SELECT id FROM reservations WHERE room_id = :room AND date = :date FOR UPDATE',
                [':room' => $reservation->room_id, ':date' => $reservation->date]
            )->queryAll();

            $bufferMinutes = BookingRule::getInt('buffer_minutes_between', 15);
            $bufferedStart = date('H:i:s', strtotime($reservation->start_time) - ($bufferMinutes * 60));
            $bufferedEnd   = date('H:i:s', strtotime($reservation->end_time)   + ($bufferMinutes * 60));

            // Find conflicting approved reservations
            $approvedConflicts = Reservation::find()
                ->where(['room_id' => $reservation->room_id, 'date' => $reservation->date, 'status' => Reservation::STATUS_APPROVED])
                ->andWhere(['<', 'start_time', $bufferedEnd])
                ->andWhere(['>', 'end_time', $bufferedStart])
                ->andWhere(['not', ['id' => $reservationId]])
                ->all();

            $approverUser  = User::findOne($reservation->user_id);
            $approverPriority = $approverUser ? $approverUser->getEffectivePriority() : 1;

            foreach ($approvedConflicts as $conflict) {
                $conflictUser = User::findOne($conflict->user_id);
                $conflictPriority = $conflictUser ? $conflictUser->getEffectivePriority() : 1;

                if ($approverPriority > $conflictPriority) {
                    // Higher priority user bumps lower priority — cancel the existing approved one
                    $conflict->status          = Reservation::STATUS_CANCELED;
                    $conflict->rejected_by     = Reservation::REJECTED_BY_SYSTEM;
                    $conflict->rejection_reason = 'Dibatalkan karena pengguna dengan prioritas lebih tinggi memesan ruangan yang sama.';
                    $conflict->save(false);

                    // Give bumped user a priority boost
                    if ($conflictUser) {
                        $conflictUser->applyPriorityBoost();
                        Notification::send(
                            $conflict->user_id,
                            Notification::TYPE_RESERVATION_BUMPED,
                            "Reservasi Anda untuk ruangan {$conflict->room->name} pada " .
                            date('d M Y', strtotime($conflict->date)) .
                            " dibatalkan karena pengguna dengan prioritas lebih tinggi. " .
                            "Anda mendapat prioritas booking selama " .
                            BookingRule::getInt('priority_boost_days', 7) . " hari.",
                            $conflict->id
                        );
                    }

                    // Trigger waitlist for the bumped slot
                    static::triggerWaitlist($conflict->room_id, $conflict->date, $conflict->start_time, $conflict->end_time);

                } else {
                    // Same or higher priority conflict already approved — cannot approve
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => 'Tidak dapat menyetujui: sudah ada reservasi yang disetujui dengan prioritas sama atau lebih tinggi pada waktu tersebut.',
                    ];
                }
            }

            // Approve the reservation
            $reservation->status = Reservation::STATUS_APPROVED;
            $reservation->save(false);

            // Auto-cancel conflicting pending reservations (same or lower priority)
            $pendingConflicts = Reservation::find()
                ->where(['room_id' => $reservation->room_id, 'date' => $reservation->date, 'status' => Reservation::STATUS_PENDING])
                ->andWhere(['<', 'start_time', $bufferedEnd])
                ->andWhere(['>', 'end_time', $bufferedStart])
                ->andWhere(['not', ['id' => $reservationId]])
                ->all();

            $canceledCount = 0;
            foreach ($pendingConflicts as $pending) {
                $pending->status          = Reservation::STATUS_CANCELED;
                $pending->rejected_by     = Reservation::REJECTED_BY_SYSTEM;
                $pending->rejection_reason = 'Dibatalkan otomatis karena ada reservasi lain yang disetujui pada waktu yang sama.';
                $pending->save(false);
                $canceledCount++;

                Notification::send(
                    $pending->user_id,
                    Notification::TYPE_RESERVATION_CANCELED,
                    "Pengajuan peminjaman Anda untuk ruangan {$pending->room->name} pada " .
                    date('d M Y', strtotime($pending->date)) .
                    " dibatalkan otomatis karena ada peminjaman lain yang disetujui pada waktu yang sama.",
                    $pending->id
                );
            }

            // Notify the approved user
            Notification::send(
                $reservation->user_id,
                Notification::TYPE_RESERVATION_APPROVED,
                "Peminjaman Anda untuk ruangan {$reservation->room->name} pada " .
                date('d M Y', strtotime($reservation->date)) .
                " pukul " . date('H:i', strtotime($reservation->start_time)) .
                " – " . date('H:i', strtotime($reservation->end_time)) .
                " telah disetujui.",
                $reservation->id
            );

            $transaction->commit();

            $msg = "Reservasi berhasil disetujui.";
            if ($canceledCount > 0) {
                $msg .= " {$canceledCount} pengajuan bentrok telah dibatalkan otomatis.";
            }

            return ['success' => true, 'message' => $msg];

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('ReservationService::approve failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem.'];
        }
    }

    /**
     * Cancel a reservation (by admin or user).
     *
     * @param string $canceledBy  'admin' | 'user'
     * @param string $reason      Optional rejection reason
     */
    public static function cancel(int $reservationId, string $canceledBy = 'admin', string $reason = ''): array
    {
        $reservation = Reservation::findOne($reservationId);
        if (!$reservation) {
            return ['success' => false, 'message' => 'Reservasi tidak ditemukan.'];
        }

        // Cancellation deadline check for user-initiated cancellations
        if ($canceledBy === 'user') {
            $cancelHours = BookingRule::getInt('cancellation_hours_before', 24);
            $bookingTimestamp = strtotime($reservation->date . ' ' . $reservation->start_time);
            $hoursUntil = ($bookingTimestamp - time()) / 3600;

            if ($hoursUntil < $cancelHours && $reservation->status === Reservation::STATUS_APPROVED) {
                return [
                    'success' => false,
                    'message' => "Pembatalan hanya bisa dilakukan minimal {$cancelHours} jam sebelum waktu peminjaman. " .
                                 "Hubungi admin untuk pembatalan mendadak.",
                ];
            }

            // Late cancel on pending is fine, but issue a strike if it's within the window
            if ($hoursUntil < $cancelHours && $reservation->status === Reservation::STATUS_PENDING) {
                UserStrike::issue($reservation->user_id, UserStrike::REASON_LATE_CANCEL, $reservation->id, 'Pembatalan terlambat oleh pengguna.');
            }
        }

        $wasApproved = $reservation->isStatusApproved();

        $reservation->status          = Reservation::STATUS_CANCELED;
        $reservation->rejected_by     = $canceledBy;
        $reservation->rejection_reason = $reason ?: null;
        $reservation->save(false);

        // Notify user if canceled by admin
        if ($canceledBy === 'admin') {
            $msg = "Peminjaman Anda untuk ruangan {$reservation->room->name} pada " .
                   date('d M Y', strtotime($reservation->date)) . " telah dibatalkan oleh admin.";
            if ($reason) {
                $msg .= " Alasan: {$reason}";
            }
            Notification::send($reservation->user_id, Notification::TYPE_RESERVATION_CANCELED, $msg, $reservation->id);
        }

        // If an approved reservation was canceled, trigger waitlist
        if ($wasApproved) {
            static::triggerWaitlist(
                $reservation->room_id,
                $reservation->date,
                $reservation->start_time,
                $reservation->end_time
            );
        }

        return ['success' => true, 'message' => 'Reservasi berhasil dibatalkan.'];
    }

    /**
     * Notify the best waitlist match that a slot has opened.
     */
    public static function triggerWaitlist(int $roomId, string $date, string $startTime, string $endTime): void
    {
        $match = ReservationWaitlist::findBestMatch($roomId, $date, $startTime, $endTime);
        if (!$match) {
            return;
        }

        $match->status      = ReservationWaitlist::STATUS_NOTIFIED;
        $match->notified_at = date('Y-m-d H:i:s');
        $match->save(false);

        $claimDays = BookingRule::getInt('waitlist_claim_days', 3);
        Notification::send(
            $match->user_id,
            Notification::TYPE_WAITLIST_AVAILABLE,
            "Slot ruangan {$match->room->name} pada " .
            date('d M Y', strtotime($match->date)) .
            " pukul " . date('H:i', strtotime($match->start_time)) .
            " – " . date('H:i', strtotime($match->end_time)) .
            " kini tersedia untuk Anda. Klaim dalam {$claimDays} hari sebelum slot diberikan ke orang lain."
        );
    }

    /**
     * User claims a waitlist slot — creates a reservation from it.
     */
    public static function claimWaitlist(int $waitlistId, int $userId): array
    {
        $waitlist = ReservationWaitlist::findOne(['id' => $waitlistId, 'user_id' => $userId, 'status' => ReservationWaitlist::STATUS_NOTIFIED]);

        if (!$waitlist) {
            return ['success' => false, 'message' => 'Slot waitlist tidak ditemukan atau sudah tidak tersedia.'];
        }

        if ($waitlist->isClaimExpired()) {
            $waitlist->status = ReservationWaitlist::STATUS_EXPIRED;
            $waitlist->save(false);
            static::triggerWaitlist($waitlist->room_id, $waitlist->date, $waitlist->start_time, $waitlist->end_time);
            return ['success' => false, 'message' => 'Waktu klaim sudah habis. Slot diberikan ke antrian berikutnya.'];
        }

        $result = static::create([
            'room_id'    => $waitlist->room_id,
            'date'       => $waitlist->date,
            'start_time' => $waitlist->start_time,
            'end_time'   => $waitlist->end_time,
            'waitlist_id'=> $waitlist->id,
        ], $userId);

        if ($result['success']) {
            $waitlist->status = ReservationWaitlist::STATUS_CLAIMED;
            $waitlist->save(false);
        }

        return $result;
    }
}
