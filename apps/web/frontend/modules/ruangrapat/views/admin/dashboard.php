<?php
$this->title = "Admin Dashboard – R3-SSMI";
$this->params['extraHead'] = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
?>
<div class="container mt-4">
    <h2 class="mb-4">Selamat Datang, Admin!</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Total Ruang</h5>
                    <p class="card-text display-6">12</p>
                    <a href="<?= \yii\helpers\Url::to(['rooms/index']) ?>" class="btn btn-sm btn-primary">Kelola Ruang</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Permintaan Reservasi</h5>
                    <p class="card-text display-6">8</p>
                    <a href="<?= \yii\helpers\Url::to(['reservations/index']) ?>" class="btn btn-sm btn-primary">Lihat Detail</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-dashboard">
                <div class="card-body">
                    <h5 class="card-title">Pengguna Terdaftar</h5>
                    <p class="card-text display-6">250</p>
                    <a href="<?= \yii\helpers\Url::to(['users/index']) ?>" class="btn btn-sm btn-primary">Kelola Pengguna</a>
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
<?php
$this->params['extraScript'] = <<<JS
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
JS;
?>