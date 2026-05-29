<?php
    /** @var \common\models\User $user */
    /** @var int $totalRuangan */
    /** @var int $permintaanReservasi */
    /** @var int $penggunaTerdaftar */

    $this->title = "Admin Dashboard - Ruang Rapat SSMI";
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['extraHead'] = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
?>
<div class="container mt-4">
    <h2 class="mb-4">Selamat Datang, <?= $user->username ?>!</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Total Ruang</h5>
                    <p class="card-text display-6"><?= $totalRuangan ?></p>
                    <a href="<?= \yii\helpers\Url::to(['default/rooms']) ?>" class="btn btn-sm btn-primary">Kelola Ruang</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Permintaan Reservasi</h5>
                    <p class="card-text display-6"><?= $permintaanReservasi ?></p>
                    <a href="<?= \yii\helpers\Url::to(['/booking/default/admin']) ?>" class="btn btn-sm btn-primary">Lihat & Proses</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Pengguna Terdaftar</h5>
                    <p class="card-text display-6"><?= $penggunaTerdaftar ?></p>
                    <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/strike/index']) ?>" class="btn btn-sm btn-warning">Kelola Strike</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Aturan Peminjaman</h5>
                    <p class="card-text" style="font-size:0.9rem; color:#555;">Konfigurasi batas waktu, durasi, dan kebijakan lainnya.</p>
                    <a href="<?= \yii\helpers\Url::to(['/ruang-rapat/settings/index']) ?>" class="btn btn-sm btn-secondary">Pengaturan</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Grafik Reservasi Bulanan</h5>
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Grafik Tingkat Kehadiran</h5>
                    <canvas id="attendanceChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Grafik Reservasi Bulanan
    const ctx1 = document.getElementById("monthlyChart").getContext("2d");
    const monthlyChart = new Chart(ctx1, {
      type: "bar",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        datasets: [{
          label: "Jumlah Reservasi",
          data: [12, 19, 7, 15, 10, 22, 18, 14, 17, 20, 23, 25],
          backgroundColor: "#003366",
        }],
      },
      options: {
        responsive: true,
        plugins: {legend: {display: false}},
        scales: { y: { beginAtZero: true } }
      }
    });

    // Grafik Tingkat Kehadiran
    const ctx2 = document.getElementById("attendanceChart").getContext("2d");
    const attendanceChart = new Chart(ctx2, {
      type: "line",
      data: {
        labels: ["Minggu 1", "Minggu 2", "Minggu 3", "Minggu 4"],
        datasets: [{
          label: "Presentase Kehadiran (%)",
          data: [85, 90, 75, 80],
          borderColor: "#003366",
          backgroundColor: "rgba(0, 51, 102, 0.1)",
          fill: true,
        }],
      },
      options: {
        responsive: true,
        plugins: {legend: {display: false}},
        scales: { y: { beginAtZero: true, max: 100 } }
      }
    });
</script>
