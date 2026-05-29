
<?php 
$this->title = 'Riwayat Peminjaman'
?>
<div class="booking-default-index">
    <h1 class="mb-3"><?= $this->title ?></h1>
    <a class="btn btn-success mb-3" href="<?= \yii\helpers\Url::to(['/booking/reservation/create']) ?>">Ajukan Peminjaman</a>
    <a class="btn btn-success mb-3" href="<?= \yii\helpers\Url::to(['/booking/reservation/find-available-rooms']) ?>">Cari Ruangan</a>
    <div class="mb-3">
        <strong>Urutkan berdasarkan:</strong>
        <a href="<?= \yii\helpers\Url::to(['/booking', 'orderBy' => 'status']) ?>" class="btn btn-outline-secondary btn-sm <?= $orderBy === 'status' ? 'active' : '' ?>">Status</a>
        <a href="<?= \yii\helpers\Url::to(['/booking', 'orderBy' => 'date']) ?>" class="btn btn-outline-secondary btn-sm <?= $orderBy === 'date' ? 'active' : '' ?>">Tanggal</a>
        <a href="<?= \yii\helpers\Url::to(['/booking', 'orderBy' => 'room']) ?>" class="btn btn-outline-secondary btn-sm <?= $orderBy === 'room' ? 'active' : '' ?>">Nama Ruang</a>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Nama ruang</th>
            <th scope="col">Tanggal</th>
            <th scope="col">Waktu</th>
            <th scope="col">Status</th>
            <th scope="col">Keperluan</th>
            <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $i => $reservation): ?>
                <tr>
                    <th scope="row"><?= $i + 1 ?></th>
                    <td><?= $reservation['room']['name'] ?></td>
                    <td><?= date('d M Y', strtotime($reservation['date'])) ?></td>
                    <td><?= date('H:i', strtotime($reservation['start_time'])) ?> - <?= date('H:i', strtotime($reservation['end_time'])) ?></td>
                    <td>
                        <?php
                        $status = strtolower($reservation['status']);
                        $badgeClass = match ($status) {
                            'approved' => 'success',
                            'canceled' => 'danger',
                            'pending' => 'warning',
                            default => 'secondary',
                        };
                        ?>
                        <span class="badge bg-<?= $badgeClass ?>">
                            <?= ucfirst($status) ?>
                        </span>
                    </td>
                    <td><?= $reservation['reason_of_use'] ?></td>
                    <td>
                        <a href="#" class="btn btn-secondary btn-sm">QR - Check in</a>

                        <?= \yii\helpers\Html::a('<i class="fas fa-eye"></i>', ['/booking/reservation/detail', 'id' => $reservation['id']], [
                            'class' => 'btn btn-info btn-sm',
                            'title' => 'Lihat Detail',
                        ]) ?>

                        <?= \yii\helpers\Html::a('<i class="fas fa-edit"></i>', ['/booking/reservation/update', 'id' => $reservation['id']], [
                            'class' => 'btn btn-primary btn-sm',
                            'title' => 'Edit',
                        ]) ?>

                        <!-- Delete modal -->
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-<?= $reservation['id'] ?>">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <div class="modal fade" id="deleteModal-<?= $reservation['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus reservasi ini?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                                    <?= \yii\helpers\Html::beginForm(['/booking/reservation/delete', 'id' => $reservation['id']], 'post') ?>
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    <?= \yii\helpers\Html::endForm() ?>
                                </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>
