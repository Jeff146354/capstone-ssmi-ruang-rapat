<?php
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Reservation;

$this->title = 'Ajukan Peminjaman — ' . Html::encode($room->name);
$model->room_id = $room->id;

// Register schedule grid assets
$this->registerCssFile('@web/css/schedule-grid.css');
$this->registerJsFile('@web/js/schedule-grid.js', ['position' => \yii\web\View::POS_END]);

// Load existing reservations for this room
$existingReservations = Reservation::find()
    ->where(['room_id' => $room->id])
    ->andWhere(['in', 'status', [Reservation::STATUS_APPROVED, Reservation::STATUS_PENDING]])
    ->select(['id', 'room_id', 'user_id', 'date', 'start_time', 'end_time', 'reason_of_use', 'status'])
    ->asArray()
    ->all();

$colors = ['#FF6B00', '#3b82f6', '#22c55e', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899'];
$bookingsJs = [];
foreach ($existingReservations as $i => $r) {
    $bookingsJs[] = [
        'id' => 'srv_' . $r['id'],
        'labId' => (string)$r['room_id'],
        'date' => $r['date'],
        'startTime' => substr($r['start_time'], 0, 5),
        'endTime' => substr($r['end_time'], 0, 5),
        'courseName' => $r['reason_of_use'] ?: 'Reservasi #' . $r['id'],
        'color' => $r['status'] === Reservation::STATUS_PENDING
            ? '#9CA3AF'
            : $colors[$i % count($colors)],
        'ownerId' => $r['user_id'] ?? null,
    ];
}
?>
<style>
.booking-page {
    background: var(--ipb-bg, #F9F9FF);
    min-height: calc(100vh - 72px);
    padding: 32px 16px;
}

.booking-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
}

@media (max-width: 992px) {
    .booking-layout {
        grid-template-columns: 1fr;
    }
}

/* Room header */
.room-header {
    background: linear-gradient(135deg, #151C27, #1e2d45);
    border-radius: 16px; overflow: hidden;
    display: flex; align-items: stretch;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(0,0,0,.15);
}
.room-header .room-img {
    width: 140px; flex-shrink: 0;
    object-fit: cover;
}
.room-header .room-info {
    padding: 20px 24px; display: flex;
    flex-direction: column; justify-content: center;
}
.room-header .room-tag {
    background: rgba(255,107,0,.2); color: #FF6B00;
    font-size: 11px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; padding: 4px 10px; border-radius: 4px;
    display: inline-block; margin-bottom: 6px; align-self: flex-start;
}
.room-header h2 { color: #fff; font-size: 20px; font-weight: 700; margin: 0 0 6px; }
.room-header .meta { color: rgba(255,255,255,.6); font-size: 12px; display: flex; gap: 12px; flex-wrap: wrap; }
.room-header .meta span { display: flex; align-items: center; gap: 4px; }
.room-header .meta i { color: #FF6B00; }

/* Grid section (main) */
.grid-section {
    min-width: 0; /* prevent grid blowout */
}

.grid-section .section-title {
    color: #151C27; font-size: 18px; font-weight: 700; margin: 0 0 4px;
}
.grid-section .section-subtitle {
    color: #575E70; font-size: 13px; margin: 0 0 16px;
}

/* Form sidebar */
.form-sidebar {
    background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    padding: 28px;
    position: sticky;
    top: 90px;
}
.form-sidebar h3 {
    color: #151C27; font-size: 17px; font-weight: 700;
    margin-bottom: 20px; padding-bottom: 12px;
    border-bottom: 2px solid #FFDBCC;
}
.form-section-title {
    color: #A04100; font-size: 11px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    margin-bottom: 12px; margin-top: 20px;
    display: flex; align-items: center; gap: 8px;
}
.form-section-title::after {
    content: ''; flex: 1; height: 1px; background: #F0E8E5;
}

/* Form fields */
.ipb-form-field { margin-bottom: 16px; }
.ipb-form-field label {
    display: block; color: #151C27; font-size: 13px; font-weight: 600;
    margin-bottom: 4px;
}
.ipb-form-field input,
.ipb-form-field textarea,
.ipb-form-field select,
.ipb-form-field .form-control {
    width: 100%;
    padding: 10px 12px !important;
    background: #fff;
    border: 1.5px solid #E2BFB0 !important;
    border-radius: 8px !important;
    font-size: 14px; color: #151C27;
    font-family: 'Plus Jakarta Sans', sans-serif;
    outline: none; transition: border-color .15s, box-shadow .15s;
    box-shadow: none !important;
}
.ipb-form-field input:focus,
.ipb-form-field textarea:focus,
.ipb-form-field select:focus,
.ipb-form-field .form-control:focus {
    border-color: #FF6B00 !important;
    box-shadow: 0 0 0 3px rgba(255,107,0,.1) !important;
}
.ipb-form-field .help-block,
.ipb-form-field .invalid-feedback {
    color: #ef4444; font-size: 11px; margin-top: 3px; display: block;
}

/* File upload */
.file-upload-area {
    border: 2px dashed #E2BFB0; border-radius: 8px;
    padding: 16px; text-align: center; cursor: pointer;
    transition: border-color .15s, background .15s;
    background: #FAFAFA;
}
.file-upload-area:hover { border-color: #FF6B00; background: #FFF8F5; }
.file-upload-area i { color: #A04100; font-size: 22px; margin-bottom: 4px; display: block; }
.file-upload-area p { color: #575E70; font-size: 13px; margin: 0; }
.file-upload-area small { color: #9CA3AF; font-size: 11px; }
.file-upload-area input[type="file"] { display: none; }
.file-name-display {
    margin-top: 6px; color: #A04100; font-size: 12px;
    font-weight: 600; display: none;
}

/* Submit */
.btn-submit {
    width: 100%; padding: 14px;
    background: #FF6B00; color: #fff;
    font-size: 15px; font-weight: 700;
    border: none; border-radius: 10px; cursor: pointer;
    box-shadow: 0 4px 15px rgba(255,107,0,.3);
    transition: background .15s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { background: #A04100; transform: translateY(-1px); }

.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    color: #575E70; font-size: 14px; font-weight: 600;
    text-decoration: none; margin-bottom: 16px;
    transition: color .15s;
}
.btn-back:hover { color: #A04100; }

/* Sync indicator */
.sync-indicator {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 12px; margin-bottom: 16px;
    background: #F0FDF4; border: 1px solid #BBF7D0;
    border-radius: 8px; font-size: 12px; font-weight: 600; color: #166534;
}
.sync-indicator.sync-active {
    background: #FFF7ED; border-color: #FFDBCC; color: #A04100;
}

@media (max-width: 576px) {
    .room-header { flex-direction: column; }
    .room-header .room-img { width: 100%; height: 120px; }
}
</style>

<div class="booking-page">
    <div class="container" style="max-width: 1200px;">

        <a href="<?= Url::to(['default/view', 'id' => $room->id]) ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail Ruangan
        </a>

        <!-- Room header -->
        <div class="room-header">
            <img src="<?= Html::encode($room->imageUrl) ?>"
                 alt="<?= Html::encode($room->name) ?>"
                 class="room-img">
            <div class="room-info">
                <span class="room-tag"><?= Html::encode($room->room) ?></span>
                <h2><?= Html::encode($room->name) ?></h2>
                <div class="meta">
                    <?php if ($room->capacity): ?>
                        <span><i class="fas fa-users"></i> <?= $room->capacity ?> orang</span>
                    <?php endif ?>
                    <?php if ($room->location): ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?= Html::encode($room->location) ?></span>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <!-- Main layout: Grid (left) + Form (right) -->
        <div class="booking-layout">
            <!-- Schedule Grid (main feature) -->
            <div class="grid-section">
                <h3 class="section-title">
                    <i class="fas fa-calendar-alt me-2" style="color:#FF6B00"></i>Pilih Jadwal di Grid
                </h3>
                <p class="section-subtitle">
                    Klik slot kosong untuk menambah jadwal. Drag untuk memindahkan, resize untuk mengubah durasi.
                    Blok abu-abu adalah jadwal yang sudah ada.
                </p>
                <div id="schedule-grid-container"></div>
            </div>

            <!-- Form sidebar -->
            <div class="form-sidebar">
                <h3><i class="fas fa-edit me-2" style="color:#FF6B00"></i>Detail Peminjaman</h3>

                <div class="sync-indicator" id="syncIndicator">
                    <i class="fas fa-link"></i>
                    <span id="syncText">Form tersinkronisasi dengan grid</span>
                </div>

                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-danger rounded-3 mb-3" style="font-size:13px;">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif ?>

                <?php $form = ActiveForm::begin([
                    'id' => 'booking-form',
                    'options' => ['enctype' => 'multipart/form-data'],
                    'fieldConfig' => ['template' => '{input}{error}'],
                ]); ?>

                <?= $form->field($model, 'room_id')->hiddenInput(['value' => $room->id])->label(false) ?>

                <!-- Time section -->
                <div class="form-section-title"><i class="fas fa-clock"></i>Waktu</div>
                <div class="ipb-form-field">
                    <label>Tanggal</label>
                    <?= $form->field($model, 'date')->input('date', [
                        'class' => 'form-control',
                        'id' => 'form-date',
                    ])->label(false) ?>
                </div>
                <div style="display:flex; gap:8px;">
                    <div class="ipb-form-field" style="flex:1">
                        <label>Mulai</label>
                        <?= $form->field($model, 'start_time')->input('time', [
                            'class' => 'form-control',
                            'id' => 'form-start-time',
                        ])->label(false) ?>
                    </div>
                    <div class="ipb-form-field" style="flex:1">
                        <label>Selesai</label>
                        <?= $form->field($model, 'end_time')->input('time', [
                            'class' => 'form-control',
                            'id' => 'form-end-time',
                        ])->label(false) ?>
                    </div>
                </div>

                <!-- Info section -->
                <div class="form-section-title"><i class="fas fa-user"></i>Informasi</div>
                <div class="ipb-form-field">
                    <label>Nama Kegiatan</label>
                    <?= $form->field($model, 'reason_of_use')->textInput([
                        'class' => 'form-control',
                        'id' => 'form-reason',
                        'placeholder' => 'Contoh: Rapat Koordinasi Tim',
                    ])->label(false) ?>
                </div>
                <div class="ipb-form-field">
                    <label>Afiliasi / Unit</label>
                    <?= $form->field($model, 'affiliation')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Contoh: Prodi Ilmu Komputer',
                    ])->label(false) ?>
                </div>

                <!-- Document section -->
                <div class="form-section-title"><i class="fas fa-file-alt"></i>Dokumen</div>
                <div class="ipb-form-field">
                    <label>Surat Peminjaman <span style="color:#9CA3AF; font-weight:400">(opsional)</span></label>
                    <label for="reservation-document" class="file-upload-area" id="fileDropZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Upload surat</p>
                        <small>PDF, DOC, DOCX — maks. 5MB</small>
                        <?= $form->field($model, 'document')->fileInput([
                            'id' => 'reservation-document',
                            'accept' => '.pdf,.doc,.docx',
                        ])->label(false) ?>
                    </label>
                    <div class="file-name-display" id="fileNameDisplay">
                        <i class="fas fa-check-circle me-1"></i><span id="fileNameText"></span>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-3">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Ajukan Peminjaman
                    </button>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Grid is editable, single room, no room selector, with Konfirmasi pattern
window.__scheduleGridOptions = {
    readOnly: false,
    deferSave: true,
    hideLabSelect: true,
    currentUserId: '<?= Yii::$app->user->id ?>'
};
window.__scheduleGridRooms = [<?= Json::encode(['id' => $room->id, 'name' => $room->name]) ?>];
window.__scheduleGridCurrentRoom = '<?= $room->id ?>';
window.__scheduleGridBookings = <?= Json::encode($bookingsJs) ?>;
</script>

<script>
(function() {
    'use strict';

    // Form elements
    var formDate = document.getElementById('form-date');
    var formStart = document.getElementById('form-start-time');
    var formEnd = document.getElementById('form-end-time');
    var formReason = document.getElementById('form-reason');
    var syncIndicator = document.getElementById('syncIndicator');
    var syncText = document.getElementById('syncText');

    var userBookingId = null; // track the user's own booking block on the grid
    var syncingFromGrid = false;
    var syncingFromForm = false;

    // Wait for ScheduleGrid to initialize
    function waitForGrid(cb) {
        if (window.ScheduleGrid) { cb(); return; }
        var interval = setInterval(function() {
            if (window.ScheduleGrid) { clearInterval(interval); cb(); }
        }, 50);
    }

    waitForGrid(function() {
        // Listen for grid changes (custom event dispatched by schedule-grid.js)
        document.addEventListener('sg-booking-changed', function(e) {
            if (syncingFromForm) return;
            var booking = e.detail;
            if (booking && booking.id === userBookingId) {
                syncGridToForm(booking);
            }
        });

        // Listen for grid booking added
        document.addEventListener('sg-booking-added', function(e) {
            if (syncingFromForm) return;
            var booking = e.detail;
            // If user hasn't created a booking yet, adopt this one
            if (!userBookingId) {
                userBookingId = booking.id;
                syncGridToForm(booking);
                updateSyncStatus(true);
            }
        });

        // Form → Grid sync
        function syncFormToGrid() {
            if (syncingFromGrid) return;
            syncingFromForm = true;

            var date = formDate.value;
            var startTime = formStart.value;
            var endTime = formEnd.value;
            var reason = formReason.value;

            if (!date || !startTime || !endTime) {
                syncingFromForm = false;
                return;
            }

            var bookings = window.ScheduleGrid.getBookings();

            if (userBookingId) {
                var found = bookings.find(function(b) { return b.id === userBookingId; });
                if (found) {
                    found.date = date;
                    found.startTime = startTime;
                    found.endTime = endTime;
                    found.courseName = reason || 'Peminjaman Baru';
                    found._pending = true;
                    window.ScheduleGrid.setBookings(bookings);
                }
            } else if (startTime && endTime) {
                userBookingId = 'user_' + Date.now();
                bookings.push({
                    id: userBookingId,
                    labId: '<?= $room->id ?>',
                    date: date,
                    startTime: startTime,
                    endTime: endTime,
                    courseName: reason || 'Peminjaman Baru',
                    color: '#FF6B00',
                    ownerId: '<?= Yii::$app->user->id ?>',
                    _pending: true,
                });
                window.ScheduleGrid.setBookings(bookings);
                // Navigate grid to the week containing this date
                window.ScheduleGrid.setWeek(new Date(date));
                updateSyncStatus(true);
            }

            syncingFromForm = false;
        }

        // Grid → Form sync
        function syncGridToForm(booking) {
            syncingFromGrid = true;
            formDate.value = booking.date || '';
            formStart.value = booking.startTime;
            formEnd.value = booking.endTime;
            if (booking.courseName && booking.courseName !== 'Peminjaman Baru') {
                formReason.value = booking.courseName;
            }
            updateSyncStatus(true);
            syncingFromGrid = false;
        }

        function updateSyncStatus(active) {
            if (active) {
                syncIndicator.classList.add('sync-active');
                syncText.textContent = 'Tersinkronisasi — perubahan terlihat di grid';
            } else {
                syncIndicator.classList.remove('sync-active');
                syncText.textContent = 'Isi form atau klik grid untuk mulai';
            }
        }

        // Attach form listeners
        [formDate, formStart, formEnd, formReason].forEach(function(el) {
            if (el) {
                el.addEventListener('change', syncFormToGrid);
                el.addEventListener('input', syncFormToGrid);
            }
        });
    });

    // File upload display
    document.getElementById('reservation-document').addEventListener('change', function() {
        var display = document.getElementById('fileNameDisplay');
        var nameText = document.getElementById('fileNameText');
        if (this.files && this.files[0]) {
            nameText.textContent = this.files[0].name;
            display.style.display = 'block';
        } else {
            display.style.display = 'none';
        }
    });
})();
</script>
