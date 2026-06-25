<?php
/** @var yii\web\View $this */
/** @var array $rooms */
/** @var int $currentRoomId */
/** @var array $reservations */

use yii\helpers\Json;
use yii\helpers\Url;

$this->title = 'Jadwal Ruangan - Grid Interaktif';

// Register schedule grid assets
$this->registerCssFile('@web/css/schedule-grid.css');
$this->registerJsFile('@web/js/schedule-grid.js', ['position' => \yii\web\View::POS_END]);

// Map reservations to booking block format
$colors = ['#FF6B00', '#3b82f6', '#22c55e', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899'];
$bookingsJs = [];
foreach ($reservations as $i => $r) {
    $bookingsJs[] = [
        'id' => 'srv_' . $r['id'],
        'labId' => (string)$r['room_id'],
        'date' => $r['date'],
        'startTime' => substr($r['start_time'], 0, 5),
        'endTime' => substr($r['end_time'], 0, 5),
        'courseName' => $r['reason_of_use'] ?: 'Reservasi #' . $r['id'],
        'color' => $colors[$i % count($colors)],
        'ownerId' => $r['user_id'] ?? null,
        'ownerName' => $r['username'] ?? '',
    ];
}
?>

<div class="booking-schedule-grid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 style="color:#151C27; font-size:24px; font-weight:700; margin:0;">
                <i class="fas fa-calendar-alt me-2" style="color:#FF6B00"></i><?= $this->title ?>
            </h1>
            <p style="color:#575E70; font-size:13px; margin:4px 0 0;">
                Klik blok jadwal untuk melihat detail atau menolak peminjaman.
                <br><small class="text-muted">Admin hanya dapat melihat dan menolak jadwal, tidak dapat memindahkan atau mengedit.</small>
            </p>
        </div>
    </div>

    <div id="schedule-grid-container"></div>
</div>

<script>
// Admin mode: can view details and deny, but cannot edit/move blocks
window.__scheduleGridOptions = {
    readOnly: false,
    deferSave: true,
    adminMode: true,
    currentUserId: '<?= Yii::$app->user->id ?>'
};
window.__scheduleGridRooms = <?= Json::encode($rooms) ?>;
window.__scheduleGridCurrentRoom = '<?= $currentRoomId ?>';
window.__scheduleGridBookings = <?= Json::encode($bookingsJs) ?>;
window.__scheduleGridSaveUrl = '<?= Url::to(['save-schedule-grid']) ?>';
</script>
