<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Room $model */
/** @var \common\models\Room[] $rooms */

$this->title = "Kelola Ruang — IPB Reserve";
?>

<style>
.rooms-page { padding: 32px 0; }
.page-title { color: #151C27; font-size: 28px; font-weight: 700; margin-bottom: 8px; }
.page-subtitle { color: #575E70; font-size: 15px; margin-bottom: 32px; }

/* Form card */
.form-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(226,191,176,.2);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    padding: 32px; margin-bottom: 32px;
}
.form-card .form-title {
    color: #151C27; font-size: 20px; font-weight: 700;
    margin-bottom: 24px; display: flex; align-items: center; gap: 10px;
}
.form-card .form-title i { color: #FF6B00; }
.form-card .form-title.editing { color: #FF6B00; }

/* Fields */
.field-label {
    color: #151C27; font-size: 13px; font-weight: 600;
    margin-bottom: 6px; display: block;
}
.form-card input,
.form-card textarea,
.form-card select,
.form-card .form-control {
    border: 1.5px solid #E2BFB0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-size: 14px; color: #151C27;
    transition: border-color .15s;
    box-shadow: none !important;
}
.form-card input:focus,
.form-card textarea:focus,
.form-card .form-control:focus {
    border-color: #FF6B00 !important;
    box-shadow: 0 0 0 3px rgba(255,107,0,.1) !important;
}
.form-card .help-block,
.form-card .invalid-feedback {
    color: #ef4444; font-size: 12px; margin-top: 4px;
}
.form-card .has-error .form-control,
.form-card .is-invalid { border-color: #ef4444 !important; }

/* File upload */
.img-upload-area {
    border: 2px dashed #E2BFB0; border-radius: 10px;
    padding: 20px; text-align: center; cursor: pointer;
    transition: border-color .15s, background .15s;
    background: #FAFAFA; min-height: 120px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.img-upload-area:hover { border-color: #FF6B00; background: #FFF8F5; }
.img-upload-area i { color: #A04100; font-size: 24px; margin-bottom: 6px; }
.img-upload-area p { color: #575E70; font-size: 13px; margin: 0; }

/* Buttons */
.btn-save {
    padding: 12px 28px; background: #FF6B00; color: #fff;
    font-size: 14px; font-weight: 700; border: none; border-radius: 8px;
    cursor: pointer; transition: background .15s;
    display: inline-flex; align-items: center; gap: 8px;
}
.btn-save:hover { background: #A04100; }
.btn-cancel-edit {
    padding: 12px 28px; background: #fff; color: #FF6B00;
    font-size: 14px; font-weight: 600; border: 1.5px solid #FF6B00; border-radius: 8px;
    cursor: pointer; transition: background .15s, color .15s;
    display: none; margin-left: 12px;
}
.btn-cancel-edit:hover { background: #FF6B00; color: #fff; }

/* Table card */
.table-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(226,191,176,.2);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    padding: 24px; overflow: hidden;
}
.table-card h3 {
    color: #151C27; font-size: 18px; font-weight: 700;
    margin-bottom: 16px;
}
.room-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.room-table th {
    background: #151C27; color: #fff;
    font-size: 12px; font-weight: 700; letter-spacing: .5px;
    text-transform: uppercase; padding: 12px 16px;
}
.room-table th:first-child { border-radius: 8px 0 0 0; }
.room-table th:last-child { border-radius: 0 8px 0 0; }
.room-table td {
    padding: 14px 16px; font-size: 14px; color: #151C27;
    border-bottom: 1px solid #F0E8E5;
    vertical-align: middle;
}
.room-table tr:last-child td { border-bottom: none; }
.room-table tr:hover td { background: #FFF8F5; }

.room-code {
    background: #FFDBCC; color: #A04100;
    font-size: 12px; font-weight: 700; letter-spacing: .5px;
    padding: 4px 10px; border-radius: 4px; display: inline-block;
}
.capacity-badge {
    background: #f0f3ff; color: #3b5998;
    font-size: 12px; font-weight: 600;
    padding: 4px 10px; border-radius: 4px;
}
.room-inactive {
    opacity: .5;
}

.btn-edit-sm {
    padding: 6px 14px; background: #f59e0b; color: #fff;
    font-size: 12px; font-weight: 700; border: none; border-radius: 6px;
    cursor: pointer; transition: background .15s;
}
.btn-edit-sm:hover { background: #d97706; }
.btn-delete-sm {
    padding: 6px 14px; background: #fff; color: #ef4444;
    font-size: 12px; font-weight: 700;
    border: 1.5px solid #ef4444; border-radius: 6px;
    cursor: pointer; transition: background .15s, color .15s;
}
.btn-delete-sm:hover { background: #ef4444; color: #fff; }

/* Modal */
.modal-content { border-radius: 14px; border: none; }
</style>

<div class="rooms-page">
    <div class="container">

        <h1 class="page-title">Kelola Ruang</h1>
        <p class="page-subtitle">Tambah, edit, atau nonaktifkan ruang rapat.</p>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success rounded-3 mb-4"><?= Yii::$app->session->getFlash('success') ?></div>
        <?php endif ?>
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger rounded-3 mb-4"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif ?>

        <!-- ── Form ── -->
        <div class="form-card">
            <div class="form-title" id="formTitle">
                <i class="fas fa-plus-circle"></i>
                <span id="formTitleText">Tambah Ruang Baru</span>
            </div>

            <?php $form = ActiveForm::begin([
                'id'      => 'room-form',
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <!-- Hidden ID for edit mode -->
            <input type="hidden" name="Room[id]" id="room-id-hidden" value="">

            <div class="row g-3">
                <!-- Image -->
                <div class="col-md-4">
                    <label class="field-label">Gambar Ruangan</label>
                    <label for="room-fr_img" class="img-upload-area">
                        <i class="fas fa-image"></i>
                        <p id="imgLabel">Klik untuk upload gambar</p>
                        <?= $form->field($model, 'fr_img')->fileInput([
                            'id'     => 'room-fr_img',
                            'accept' => 'image/*',
                            'style'  => 'display:none',
                        ])->label(false) ?>
                    </label>
                </div>

                <!-- ID Ruangan + Nama -->
                <div class="col-md-4">
                    <label class="field-label">ID Ruangan <span style="color:#ef4444">*</span></label>
                    <?= $form->field($model, 'room')->textInput([
                        'id'          => 'room-room',
                        'placeholder' => 'e.g., A101, RM-201',
                        'class'       => 'form-control',
                    ])->label(false) ?>
                </div>
                <div class="col-md-4">
                    <label class="field-label">Nama Ruangan <span style="color:#ef4444">*</span></label>
                    <?= $form->field($model, 'name')->textInput([
                        'id'          => 'room-name',
                        'placeholder' => 'e.g., Ruang Rapat Utama',
                        'class'       => 'form-control',
                    ])->label(false) ?>
                </div>

                <!-- Description -->
                <div class="col-md-12">
                    <label class="field-label">Deskripsi</label>
                    <?= $form->field($model, 'description')->textarea([
                        'id'          => 'room-description',
                        'rows'        => 3,
                        'placeholder' => 'Deskripsi singkat tentang ruangan dan fasilitasnya',
                        'class'       => 'form-control',
                    ])->label(false) ?>
                </div>

                <!-- Capacity + Contact -->
                <div class="col-md-3">
                    <label class="field-label">Kapasitas <span style="color:#ef4444">*</span></label>
                    <?= $form->field($model, 'capacity')->input('number', [
                        'id'          => 'room-capacity',
                        'placeholder' => 'Min. 1',
                        'min'         => 1,
                        'class'       => 'form-control',
                    ])->label(false) ?>
                </div>
                <div class="col-md-4">
                    <label class="field-label">Kontak PIC</label>
                    <?= $form->field($model, 'contact')->textInput([
                        'id'          => 'room-contact',
                        'placeholder' => 'Nama 08xxxxxxxxxx',
                        'class'       => 'form-control',
                    ])->label(false) ?>
                    <small class="text-muted">Format: Nama diikuti nomor HP</small>
                </div>
                <div class="col-md-5">
                    <label class="field-label">Lokasi</label>
                    <?= $form->field($model, 'location')->textInput([
                        'id'          => 'room-location',
                        'placeholder' => 'Gedung X, Lantai Y atau koordinat',
                        'class'       => 'form-control',
                    ])->label(false) ?>
                </div>

                <!-- Buttons -->
                <div class="col-md-12 text-end mt-3">
                    <button type="submit" class="btn-save" id="saveButton">
                        <i class="fas fa-save"></i> Simpan Ruang
                    </button>
                    <button type="button" class="btn-cancel-edit" id="cancelEdit">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <!-- ── Room List ── -->
        <div class="table-card">
            <h3><i class="fas fa-list me-2" style="color:#FF6B00"></i>Daftar Ruang (<?= count($rooms) ?>)</h3>

            <?php if (empty($rooms)): ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-door-open fa-2x mb-2"></i>
                    <p>Belum ada ruangan. Tambahkan yang pertama di atas.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="room-table">
                        <thead>
                            <tr>
                                <th>ID Ruang</th>
                                <th>Nama</th>
                                <th>Kapasitas</th>
                                <th>Kontak</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr class="<?= $room->is_active ? '' : 'room-inactive' ?>">
                                    <td><span class="room-code"><?= Html::encode($room->room) ?></span></td>
                                    <td><strong><?= Html::encode($room->name) ?></strong></td>
                                    <td><span class="capacity-badge"><i class="fas fa-users me-1"></i><?= (int)$room->capacity ?></span></td>
                                    <td><?= Html::encode($room->contact ?: '—') ?></td>
                                    <td><?= Html::encode($room->location ? mb_substr($room->location, 0, 30) : '—') ?></td>
                                    <td>
                                        <?php if ($room->is_active): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button
                                                type="button"
                                                class="btn-edit-sm editBtn"
                                                data-id="<?= $room->id ?>"
                                                data-room="<?= Html::encode($room->room) ?>"
                                                data-name="<?= Html::encode($room->name) ?>"
                                                data-description="<?= Html::encode($room->description) ?>"
                                                data-capacity="<?= (int)$room->capacity ?>"
                                                data-contact="<?= Html::encode($room->contact) ?>"
                                                data-location="<?= Html::encode($room->location) ?>"
                                            >
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <?php if ($room->is_active): ?>
                                                <button type="button" class="btn-delete-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal-<?= $room->id ?>">
                                                    <i class="fas fa-power-off"></i> Nonaktifkan
                                                </button>
                                            <?php else: ?>
                                                <?= Html::beginForm(['/ruang-rapat/default/activate-room', 'id' => $room->id], 'post', ['style' => 'display:inline']) ?>
                                                    <button type="submit" class="btn-edit-sm" style="background:#22c55e; border:none;">
                                                        <i class="fas fa-check-circle"></i> Aktifkan
                                                    </button>
                                                <?= Html::endForm() ?>
                                            <?php endif ?>
                                        </div>

                                        <!-- Delete confirmation modal -->
                                        <div class="modal fade" id="deleteModal-<?= $room->id ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Nonaktifkan Ruangan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Nonaktifkan ruangan <strong><?= Html::encode($room->name) ?></strong>?</p>
                                                        <p class="text-muted small">Ruangan tidak akan dihapus — hanya disembunyikan dari pengguna. Semua riwayat reservasi tetap tersimpan.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <?= Html::beginForm(['/ruang-rapat/default/delete-room', 'id' => $room->id], 'post') ?>
                                                            <button type="submit" class="btn btn-danger">Nonaktifkan</button>
                                                        <?= Html::endForm() ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Image preview ──
    document.getElementById('room-fr_img').addEventListener('change', function () {
        const label = document.getElementById('imgLabel');
        if (this.files && this.files[0]) {
            label.textContent = this.files[0].name;
        }
    });

    // ── Edit button click ──
    document.querySelectorAll('.editBtn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const room = this.getAttribute('data-room');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const capacity = this.getAttribute('data-capacity');
            const contact = this.getAttribute('data-contact');
            const location = this.getAttribute('data-location');

            // Fill form
            document.getElementById('room-id-hidden').value = id;
            document.getElementById('room-room').value = room;
            document.getElementById('room-name').value = name;
            document.getElementById('room-description').value = description;
            document.getElementById('room-capacity').value = capacity;
            document.getElementById('room-contact').value = contact;
            document.getElementById('room-location').value = location;

            // Change title and button
            document.getElementById('formTitleText').textContent = 'Edit Ruang: ' + name;
            document.getElementById('formTitle').classList.add('editing');
            document.getElementById('saveButton').innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
            document.getElementById('cancelEdit').style.display = 'inline-flex';

            // Scroll to form
            document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // ── Cancel edit ──
    document.getElementById('cancelEdit').addEventListener('click', function () {
        document.getElementById('room-form').reset();
        document.getElementById('room-id-hidden').value = '';
        document.getElementById('formTitleText').textContent = 'Tambah Ruang Baru';
        document.getElementById('formTitle').classList.remove('editing');
        document.getElementById('saveButton').innerHTML = '<i class="fas fa-save"></i> Simpan Ruang';
        this.style.display = 'none';
        document.getElementById('imgLabel').textContent = 'Klik untuk upload gambar';
    });
});
</script>
