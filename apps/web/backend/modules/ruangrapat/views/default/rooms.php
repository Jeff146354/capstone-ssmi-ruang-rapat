<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Room $model */
/** @var yii\widgets\ActiveForm $form */
/** @var \common\models\Room[] $rooms */

$this->title = "Kelola Ruang - R3-SSMI";
$this->params['extraHead'] = '<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>';
?>
<div class="container mt-4">
    <h2 class="mb-4">Kelola Ruang</h2>
    <!-- Form Tambah & Edit Ruang -->
    <div class="card card-dashboard">
        <div class="card-body">
            <h5 class="card-title mb-4" id="formTitle">Tambah Ruang Baru</h5>

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="row g-3">
                    <?php if (!$model->isNewRecord): ?>
                        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                    <?php endif; ?>
                    <div class="col-md-4">
                        <?= $form->field($model, 'fr_img')->fileInput(['accept' => 'image/*'])->label('Gambar Ruangan') ?>
                        <img id="previewImage" src="#" alt="Preview" class="img-fluid mt-2" style="display:none; max-height: 200px;" />
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'room')->textInput(['maxlength' => true, 'placeholder' => 'e.g., A101']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'e.g., Ruang Rapat Utama']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Deskripsi singkat tentang ruangan']) ?>
                    </div>
                    <!-- <div class="col-md-4">
                        <label for="roomFacilities" class="form-label">Fasilitas</label>
                        <input type="text" class="form-control" id="roomFacilities" placeholder="e.g., AC, Proyektor, Wi-Fi" required/>
                    </div> -->
                    <div class="col-md-2">
                        <?= $form->field($model, 'capacity')->input('number', ['placeholder' => 'Jumlah Orang']) ?>
                    </div>
                    <!-- <div class="col-md-6">
                        <label for="loanRequirements" class="form-label">Syarat Peminjaman</label>
                        <textarea class="form-control" id="loanRequirements" rows="3" placeholder="Contoh: Formulir lengkap, surat persetujuan admin" required></textarea>
                    </div> -->
                    <div class="col-md-4">
                        <?= $form->field($model, 'contact')->textInput(['placeholder' => 'Nama & nomor HP']) ?>
                    </div>
                    <div class="col-md-8">
                        <?= $form->field($model, 'location')->textInput(['placeholder' => 'Latitude, Longitude', 'id' => 'roomLocation']) ?>
                    </div>
                    <div class="col-md-12">
                        <div id="map" style="height:300px;"></div>
                    </div>
                    <div class="col-md-12 text-end">
                        <?= Html::submitButton('<i class="bi bi-plus-circle"></i> Simpan Ruang', ['class' => 'btn btn-primary', 'id' => 'saveButton']) ?>
                        <?= Html::button('Batal', ['class' => 'btn btn-secondary', 'id' => 'cancelEdit', 'style' => 'display:none;']) ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <!-- Tabel Daftar Ruang -->
    <div class="card card-dashboard">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar Ruang</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <!-- <th scope="col">Gambar</th> -->
                            <th scope="col">ID Ruang</th>
                            <th scope="col">Nama Ruang</th>
                            <th scope="col">Kapasitas</th>
                            <th scope="col">Kontak</th>
                            <!-- <th scope="col">Fasilitas</th> -->
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="roomTableBody">
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <!-- <td><img src="<= $room->fr_img ?>" alt="<= $room->name ?>"></td> -->
                                <td><?= $room->room ?></td>
                                <td><?= $room->name ?></td>
                                <td><?= $room->capacity ?></td>
                                <!-- <td><= $room->facility ?></td> -->
                                <td><?= $room->contact ?></td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-warning me-1 editBtn"
                                        data-id="<?= $room->id ?>"
                                        data-room="<?= Html::encode($room->room) ?>"
                                        data-name="<?= Html::encode($room->name) ?>"
                                        data-description="<?= Html::encode($room->description) ?>"
                                        data-capacity="<?= $room->capacity ?>"
                                        data-contact="<?= Html::encode($room->contact) ?>"
                                        data-location="<?= Html::encode($room->location) ?>"
                                    >
                                        Edit
                                    </button>
                                    <!-- Delete modal -->
                                    <button type="button" class="btn btn-sm btn-danger deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModal-<?= $room->id ?>">
                                        Hapus
                                    </button>
                                    <div class="modal fade" id="deleteModal-<?= $room->id ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus ruangan <?= $room->name ?>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                                                <?= \yii\helpers\Html::beginForm(['/ruang-rapat/default/delete-room', 'id' => $room->id], 'post') ?>
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                <?= \yii\helpers\Html::endForm() ?>
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
        </div>
    </div>
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-warning">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus ruangan ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let map;
    let marker;
    function initMap() {
        const defaultLocation = { lat: -6.5635, lng: 106.7268 };
        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLocation,
            zoom: 15,
        });
        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true,
        });
        google.maps.event.addListener(marker, 'dragend', function(event) {
            document.getElementById('roomLocation').value = event.latLng.lat().toFixed(6) + ", " + event.latLng.lng().toFixed(6);
        });
    }
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".editBtn");

    editButtons.forEach(function (btn) {
        btn.addEventListener("click", function () {
            // Ambil data dari attribute
            const id = btn.getAttribute("data-id");
            const room = btn.getAttribute("data-room");
            const name = btn.getAttribute("data-name");
            const description = btn.getAttribute("data-description");
            const capacity = btn.getAttribute("data-capacity");
            const contact = btn.getAttribute("data-contact");
            const location = btn.getAttribute("data-location");

            // Isi form
            document.getElementById("room-room").value = room;
            document.getElementById("room-name").value = name;
            document.getElementById("room-description").value = description;
            document.getElementById("room-capacity").value = capacity;
            document.getElementById("room-contact").value = contact;
            document.getElementById("room-location").value = location;

            // Optional: ubah judul form dan tombol
            document.getElementById("formTitle").textContent = "Edit Ruang";
            const saveBtn = document.getElementById("saveButton");
            saveBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Perubahan';

            // Tambahkan hidden input untuk ID jika belum ada
            let idInput = document.getElementById("room-id-hidden");
            if (!idInput) {
                idInput = document.createElement("input");
                idInput.type = "hidden";
                idInput.name = "Room[id]";
                idInput.id = "room-id-hidden";
                document.querySelector("form").appendChild(idInput);
            }
            idInput.value = id;

            // Tampilkan tombol batal
            document.getElementById("cancelEdit").style.display = "inline-block";
        });
    });

    // Reset form ketika klik batal
    document.getElementById("cancelEdit").addEventListener("click", function () {
        document.querySelector("form").reset();
        document.getElementById("formTitle").textContent = "Tambah Ruang Baru";
        document.getElementById("saveButton").innerHTML = '<i class="bi bi-plus-circle"></i> Simpan Ruang';
        document.getElementById("cancelEdit").style.display = "none";

        // Hapus hidden input id jika ada
        const idInput = document.getElementById("room-id-hidden");
        if (idInput) idInput.remove();
    });
});
</script>
