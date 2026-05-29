<?php

namespace console\controllers;

use common\models\BookingRule;
use common\models\Notification;
use common\models\Reservation;
use common\models\ReservationWaitlist;
use common\models\UserStrike;
use common\services\ReservationService;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Cron jobs for reservation maintenance.
 *
 * Schedule these via Windows Task Scheduler or cron:
 *   php yii reservation-cron/expire-pending       — run every hour
 *   php yii reservation-cron/issue-no-show-strikes — run daily at midnight
 *   php yii reservation-cron/expire-waitlist       — run every hour
 */
class ReservationCronController extends Controller
{
    /**
     * Auto-cancel pending reservations that are too close to their booking time
     * without having been reviewed by admin.
     */
    public function actionExpirePending(): int
    {
        $expireHours = BookingRule::getInt('pending_expire_hours', 48);

        // Find pending reservations where booking starts within $expireHours from now
        $cutoff = date('Y-m-d H:i:s', strtotime("+{$expireHours} hours"));

        $expired = Reservation::find()
            ->where(['status' => Reservation::STATUS_PENDING])
            ->andWhere(['<=',
                // Combine date + start_time into a datetime for comparison
                "CONCAT(date, ' ', start_time)",
                $cutoff
            ])
            ->all();

        $count = 0;
        foreach ($expired as $reservation) {
            $reservation->status          = Reservation::STATUS_CANCELED;
            $reservation->rejected_by     = Reservation::REJECTED_BY_SYSTEM;
            $reservation->rejection_reason = "Dibatalkan otomatis karena belum diproses admin dalam {$expireHours} jam sebelum waktu peminjaman.";
            $reservation->save(false);

            Notification::send(
                $reservation->user_id,
                Notification::TYPE_RESERVATION_CANCELED,
                "Pengajuan peminjaman Anda untuk ruangan {$reservation->room->name} pada " .
                date('d M Y', strtotime($reservation->date)) .
                " dibatalkan otomatis karena belum diproses admin.",
                $reservation->id
            );

            // Trigger waitlist in case someone was waiting
            ReservationService::triggerWaitlist(
                $reservation->room_id,
                $reservation->date,
                $reservation->start_time,
                $reservation->end_time
            );

            $count++;
        }

        $this->stdout("Expired {$count} pending reservations.\n");
        return ExitCode::OK;
    }

    /**
     * Issue no-show strikes for approved reservations that passed without check-in.
     * Run daily after operating hours end.
     *
     * IMPORTANT: This cron only runs if 'enable_noshow_strikes' rule is set to '1'.
     * Keep it disabled (default '0') until the QR check-in system is fully implemented,
     * otherwise every approved reservation will generate a strike since no one can check in yet.
     * Enable via Admin → Pengaturan → enable_noshow_strikes = 1
     */
    public function actionIssueNoShowStrikes(): int
    {
        if (BookingRule::get('enable_noshow_strikes', '0') !== '1') {
            $this->stdout("No-show strikes are disabled. Set 'enable_noshow_strikes' to '1' in booking rules to enable.\n");
            return ExitCode::OK;
        }

        // Find approved reservations from yesterday or earlier with no check-in
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $noShows = Reservation::find()
            ->where(['status' => Reservation::STATUS_APPROVED])
            ->andWhere(['<=', 'date', $yesterday])
            ->andWhere(['checked_in_at' => null])
            ->all();

        $count = 0;
        foreach ($noShows as $reservation) {
            UserStrike::issue(
                $reservation->user_id,
                UserStrike::REASON_NO_SHOW,
                $reservation->id,
                "Tidak hadir pada reservasi ruangan {$reservation->room->name} tanggal " .
                date('d M Y', strtotime($reservation->date)) . "."
            );
            $count++;
        }

        $this->stdout("Issued {$count} no-show strikes.\n");
        return ExitCode::OK;
    }

    /**
     * Expire waitlist entries whose claim window has passed,
     * then pass the slot to the next person in line.
     */
    public function actionExpireWaitlist(): int
    {
        $notified = ReservationWaitlist::find()
            ->where(['status' => ReservationWaitlist::STATUS_NOTIFIED])
            ->all();

        $count = 0;
        foreach ($notified as $entry) {
            if ($entry->isClaimExpired()) {
                $entry->status = ReservationWaitlist::STATUS_EXPIRED;
                $entry->save(false);

                // Pass to next in line
                ReservationService::triggerWaitlist(
                    $entry->room_id,
                    $entry->date,
                    $entry->start_time,
                    $entry->end_time
                );

                $count++;
            }
        }

        $this->stdout("Expired {$count} waitlist claim windows and passed to next in line.\n");
        return ExitCode::OK;
    }
}
