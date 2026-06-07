<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Ajukan Peminjaman';
$model->room_id = $room->id;
?>

<style>
.form-page {
    background: var(--ipb-bg, #F9F9FF);
    min-height: calc(100vh - 72px);
    padding: 48px 16px;
}

/* Room summary card */
.room-summary {
    background: linear-gradient(135deg, #151C27, #1e2d45);
    border-radius: 16px; overflow: hidden;
    display: flex; align-items: stretch;
    margin-bottom: 32px;
    box-shadow: 0 8px 32px rgba(0,0,0,.15);
}
.room-summary .room-img {
    width: 180px; flex-shrink: 0;
    object-fit: cover;
}
.room-summary .room-info {
    padding: 24px 28px; display: flex;
    flex-direction: column; justify-content: center;
}
.room-summary .room-tag {
    background: rgba(255,107,0,.2); color: #FF6B00;
    font-size: 11px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; padding: 4px 10px; border-radius: 4px;
    display: inline-block; margin-bottom: 8px; align-self: flex-start;
}
.room-summary h2 { color: #fff; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
.room-summary .meta { color: rgba(255,255,255,.6); font-size: 13px; display: flex; gap: 16px; flex-wrap: wrap; }
.room-summary .meta span { display: flex; align-items: center; gap: 5px; }
.room-summary .meta i { color: #FF6B00; }

/* Form card */
.form-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    padding: 36px;
}
.form-card h3 {
    color: #151C27; font-size: 20px; font-weight: 700;
    margin-bottom: 28px; padding-bottom: 16px;
    border-bottom: 2px solid #FFDBCC;
}
.form-section-title {
    color: #A04100; font-size: 12px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    margin-bottom: 16px; margin-top: 28px;
    display: flex; align-items: center; gap: 8px;
}
.form-section-title::after {
    content: ''; flex: 1; height: 1px; background: #F0E8E5;
}

/* Fields */
.ipb-form-field { margin-bottom: 20px; }
.ipb-form-field label {
    display: block; color: #151C27; font-size: 14px; font-weight: 600;
    margin-bottom: 6px;
}
.ipb-form-field .field-hint {
    color: #9CA3AF; font-size: 12px; margin-top: 4px;
}
.ipb-form-field input,
.ipb-form-field textarea,
.ipb-form-field select,
.ipb-form-field .form-control {
    width: 100%;
    padding: 12px 16px !important;
    background: #fff;
    border: 1.5px solid #E2BFB0 !important;
    border-radius: 10px !important;
    font-size: 15px; color: #151C27;
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
    color: #ef4444; font-size: 12px; margin-top: 4px; display: block;
}

/* File upload */
.file-upload-area {
    border: 2px dashed #E2BFB0; border-radius: 10px;
    padding: 24px; text-align: center; cursor: pointer;
    transition: border-color .15s, background .15s;
    background: #FAFAFA;
}
.file-upload-area:hover { border-color: #FF6B00; background: #FFF8F5; }
.file-upload-area i { color: #A04100; font-size: 28px; margin-bottom: 8px; display: block; }
.file-upload-area p { color: #575E70; font-size: 14px; margin: 0; }
.file-upload-area small { color: #9CA3AF; font-size: 12px; }
.file-upload-area input[type="file"] { display: none; }
.file-name-display {
    margin-top: 8px; color: #A04100; font-size: 13px;
    font-weight: 600; display: none;
}

/* Submit */
.btn-submit {
    width: 100%; padding: 16px;
    background: #FF6B00; color: #fff;
    font-size: 16px; font-weight: 700;
    border: none; border-radius: 10px; cursor: pointer;
    box-shadow: 0 4px 15px rgba(255,107,0,.3);
    transition: background .15s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-submit:hover { background: #A04100; transform: translateY(-1px); }
.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    color: #575E70; font-size: 14px; font-weight: 600;
    text-decoration: none; margin-bottom: 24px;
    transition: color .15s;
}
.btn-back:hover { color: #A04100; }

@media (max-width: 576px) {
    .room-summary { flex-direction: column; }
    .room-summary .room-img { width: 100%; height: 160px; }
}
</style>

<div class="form-page">
    <div class="container" style="max-width: 700px;">

        <a href="<?= Url::to(['default/view', 'id' => $room->id]) ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail Ruangan
        </a>

        <!-- Room summary -->
        <div class="room-summary">
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

        <!-- Form -->
        <div class="form-card">
            <h3><i class="fas fa-calendar-plus me-2" style="color:#FF6B00"></i>Form Pengajuan Peminjaman</h3>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger rounded-3 mb-4"><?= Yii::$app->session->getFlash('error') ?></div>
            <?php endif ?>

            <?php $form = ActiveForm::begin([
                'options'    => ['enctype' => 'multipart/form-data'],
                'fieldConfig'=> ['template' => '{input}{error}'],
            ]); ?>

            <!-- Hidden room_id -->
            <?= $form->field($model, 'room_id')->hiddenInput(['value' => $room->id])->label(false) ?>

            <!-- Waktu -->
            <div class="form-section-title"><i class="fas fa-clock"></i>Waktu Peminjaman</div>
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="ipb-form-field">
                        <label>Tanggal</label>
                        <?= $form->field($model, 'date')->input('date', ['class' => 'form-control'])->label(false) ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="ipb-form-field">
                        <label>Waktu Mulai</label>
                        <?= $form->field($model, 'start_time')->input('time', ['class' => 'form-control'])->label(false) ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="ipb-form-field">
                        <label>Waktu Selesai</label>
                        <?= $form->field($model, 'end_time')->input('time', ['class' => 'form-control'])->label(false) ?>
                    </div>
                </div>
            </div>

            <!-- Informasi peminjam -->
            <div class="form-section-title"><i class="fas fa-user"></i>Informasi Peminjam</div>
            <div class="ipb-form-field">
                <label>Afiliasi / Unit</label>
                <?= $form->field($model, 'affiliation')->textInput([
                    'class'       => 'form-control',
                    'placeholder' => 'Contoh: Prodi Ilmu Komputer, Himpunan X',
                ])->label(false) ?>
            </div>
            <div class="ipb-form-field">
                <label>Alasan Penggunaan</label>
                <?= $form->field($model, 'reason_of_use')->textarea([
                    'class'       => 'form-control',
                    'rows'        => 3,
                    'placeholder' => 'Jelaskan keperluan penggunaan ruangan...',
                ])->label(false) ?>
            </div>

            <!-- Dokumen -->
            <div class="form-section-title"><i class="fas fa-file-alt"></i>Dokumen</div>
            <div class="ipb-form-field">
                <label>Surat Peminjaman <span style="color:#9CA3AF; font-weight:400">(opsional)</span></label>
                <label for="reservation-document" class="file-upload-area" id="fileDropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Klik untuk upload atau drag & drop</p>
                    <small>PDF, DOC, DOCX — maks. 5MB</small>
                    <?= $form->field($model, 'document')->fileInput([
                        'id'     => 'reservation-document',
                        'accept' => '.pdf,.doc,.docx',
                    ])->label(false) ?>
                </label>
                <div class="file-name-display" id="fileNameDisplay">
                    <i class="fas fa-check-circle me-1"></i><span id="fileNameText"></span>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-4">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Ajukan Peminjaman
                </button>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
// Show filename when file is selected
document.getElementById('reservation-document').addEventListener('change', function () {
    const display = document.getElementById('fileNameDisplay');
    const nameText = document.getElementById('fileNameText');
    if (this.files && this.files[0]) {
        nameText.textContent = this.files[0].name;
        display.style.display = 'block';
        document.querySelector('#fileDropZone p').textContent = 'File terpilih:';
    } else {
        display.style.display = 'none';
    }
});
</script>
