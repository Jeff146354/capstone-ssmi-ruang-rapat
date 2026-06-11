<?php
/** @var \common\models\User $user */
/** @var int $totalRuangan */
/** @var int $permintaanReservasi */
/** @var int $penggunaTerdaftar */
/** @var int[] $monthlyData */
/** @var array $statusData */
/** @var array $topRooms */
/** @var string $currentYear */

use yii\helpers\Json;

$this->title = "Admin Dashboard - Ruang Rapat SSMI";
$this->params['breadcrumbs'][] = $this->title;
$this->params['extraHead'] = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

$monthlyJson = Json::encode(array_values($monthlyData));
$topRoomLabels = Json::encode(array_column($topRooms, 'name'));
$topRoomValues = Json::encode(array_map('intval', array_column($topRooms, 'total')));
?>

<div class="container mt-4">
    <h2 class="mb-1">Selamat Datang, <?= \yii\helpers\Html::encode($user->username) ?>!</h2>
    <p class="text-muted mb-4">Data reservasi tahun <?= $currentYear ?></p>

    <!-- ── Stat cards ── -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-5">
        <div class="col">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Ruang</div>
                    <div class="display-6 fw-bold"><?= $totalRuangan ?></div>
                    <a href="<?= \yii\helpers\Url::to(['default/rooms']) ?>" class="btn btn-sm btn-primary mt-2">Kelola Ruang</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard h-100 border-warning">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pending Reservasi</div>
                    <div class="display-6 fw-bold text-warning"><?= $permintaanReservasi ?></div>
                    <a href="<?= \yii\helpers\Url::to(['/booking/default/admin']) ?>" class="btn btn-sm btn-warning mt-2">Lihat & Proses</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Pengguna Terdaftar</div>
                    <div class="display-6 fw-bold"><?= $penggunaTerdaftar ?></div>
                    <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/strike/index']) ?>" class="btn btn-sm mt-2" style="background:#FF6B00; color:#fff; font-weight:600;">Kelola Strike</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Reservasi <?= $currentYear ?></div>
                    <div class="display-6 fw-bold"><?= array_sum($monthlyData) ?></div>
                    <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/settings/index']) ?>" class="btn btn-sm btn-secondary mt-2">Pengaturan</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Charts row ── -->
    <div class="row g-4 mb-4">
        <!-- Monthly bar chart -->
        <div class="col-md-8">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <h5 class="card-title">Reservasi Bulanan <?= $currentYear ?></h5>
                    <canvas id="monthlyChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Status donut -->
        <div class="col-md-4">
            <div class="card card-dashboard h-100">
                <div class="card-body">
                    <h5 class="card-title">Status Reservasi</h5>
                    <canvas id="statusChart" height="200"></canvas>
                    <div class="mt-3 d-flex justify-content-around text-center small">
                        <div><span class="badge bg-success">&nbsp;</span> Approved<br><strong><?= $statusData['approved'] ?></strong></div>
                        <div><span class="badge bg-warning text-dark">&nbsp;</span> Pending<br><strong><?= $statusData['pending'] ?></strong></div>
                        <div><span class="badge bg-danger">&nbsp;</span> Canceled<br><strong><?= $statusData['canceled'] ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top rooms -->
    <?php if (!empty($topRooms)): ?>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Ruangan Paling Sering Dipesan</h5>
                    <canvas id="topRoomsChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Detail Top Ruangan</h5>
                    <table class="table table-sm table-hover mt-2">
                        <thead><tr><th>#</th><th>Ruangan</th><th>Total Reservasi</th></tr></thead>
                        <tbody>
                            <?php foreach ($topRooms as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= \yii\helpers\Html::encode($r['name']) ?></td>
                                <td><strong><?= (int)$r['total'] ?></strong></td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
</div>

<script>
// Monthly bar chart — real data from DB
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Jumlah Reservasi',
            data: <?= $monthlyJson ?>,
            backgroundColor: '#FF6B00',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Status donut
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending', 'Canceled'],
        datasets: [{
            data: [<?= $statusData['approved'] ?>, <?= $statusData['pending'] ?>, <?= $statusData['canceled'] ?>],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        cutout: '65%',
    }
});

<?php if (!empty($topRooms)): ?>
// Top rooms horizontal bar
new Chart(document.getElementById('topRoomsChart'), {
    type: 'bar',
    data: {
        labels: <?= $topRoomLabels ?>,
        datasets: [{
            label: 'Reservasi',
            data: <?= $topRoomValues ?>,
            backgroundColor: ['#FF6B00','#f59e0b','#3b82f6','#8b5cf6','#22c55e'],
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
<?php endif ?>
</script>
