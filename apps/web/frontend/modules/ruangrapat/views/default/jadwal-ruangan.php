<?php
/** @var yii\web\View $this */
/** @var common\models\Room $room */
/** @var common\models\Reservation[] $reservations */

use common\models\Reservation;
use yii\bootstrap5\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = 'Jadwal Ruangan: ' . Html::encode($room->name);

// Register schedule grid assets
$this->registerCssFile('@web/css/schedule-grid.css');
$this->registerJsFile('@web/js/schedule-grid.js', ['position' => \yii\web\View::POS_END]);

// Map reservations to booking block format
$colors = ['#FF6B00', '#3b82f6', '#22c55e', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899'];
$bookingsJs = [];
foreach ($reservations as $i => $reservation) {
    if ($reservation->status !== Reservation::STATUS_APPROVED
        && $reservation->status !== Reservation::STATUS_PENDING) {
        continue;
    }
    $bookingsJs[] = [
        'id' => 'srv_' . $reservation->id,
        'labId' => (string)$reservation->room_id,
        'date' => $reservation->date,
        'startTime' => substr($reservation->start_time, 0, 5),
        'endTime' => substr($reservation->end_time, 0, 5),
        'courseName' => $reservation->reason_of_use ?: 'Reservasi #' . $reservation->id,
        'color' => $reservation->status === Reservation::STATUS_PENDING
            ? '#9CA3AF'
            : $colors[$i % count($colors)],
        'ownerId' => $reservation->user_id,
        'ownerName' => $reservation->user->username ?? '',
    ];
}
?>

<div class="container mt-5 pt-4">
    <div class="mb-4">
        <a href="<?= Url::to(['default/view', 'id' => $room->id]) ?>" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Ruangan
        </a>
        <h2 class="mb-1"><?= Html::encode($room->name) ?></h2>
        <p class="text-secondary mb-0">Visualisasi jadwal penggunaan ruangan ini minggu ini.</p>
        <small class="text-muted">
            <span class="badge bg-success">■</span> Dikonfirmasi &nbsp;
            <span class="badge bg-secondary">■</span> Pending
        </small>
    </div>

    <div id="schedule-grid-container"></div>
</div>

<script>
// Read-only mode: users can view but not drag/edit. No room selector needed.
window.__scheduleGridOptions = { readOnly: true, deferSave: false, hideLabSelect: true };
window.__scheduleGridRooms = [<?= Json::encode(['id' => $room->id, 'name' => $room->name]) ?>];
window.__scheduleGridCurrentRoom = '<?= $room->id ?>';
window.__scheduleGridBookings = <?= Json::encode($bookingsJs) ?>;
</script>
