<?php
$this->title = "Monitoring Check-in – R3-SSMI";
?>
<div class="container mt-4">
    <h2 class="mb-4">Monitoring Check-in QR Code</h2>
    <div class="card card-dashboard">
        <div class="card-body">
            <h5 class="card-title mb-3">Daftar Reservasi & Status Check-in</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="checkinTable">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Pemesan</th>
                            <th scope="col">Ruang</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Jam</th>
                            <th scope="col">QR Code</th>
                            <th scope="col">Status Check-in</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal lihat QR -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrModalLabel">QR Code Reservasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrLarge" src="" alt="QR Code" style="width:200px; height:200px;" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="regenerateQrBtn">Re-Generate QR</button>
            </div>
        </div>
    </div>
</div>
<?php
$this->params['extraScript'] = <<<JS
<script>
    // Dummy data
    let reservations = [
      {name: 'Aisyah Rahma', room: 'A101', date: '2025-06-10', time: '10:00 - 12:00', qr: '', checkedIn: false},
      {name: 'Budi Pratama', room: 'B202', date: '2025-06-11', time: '14:00 - 16:00', qr: '', checkedIn: true},
    ];

    const checkinTableBody = document.querySelector('#checkinTable tbody');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    let currentQrIndex = null;

    function renderCheckinTable() {
      checkinTableBody.innerHTML = '';
      reservations.forEach((res, idx) => {
        const statusBadge = res.checkedIn
          ? '<span class="badge bg-success">Sudah Check-in</span>'
          : '<span class="badge bg-warning text-dark">Belum Check-in</span>';
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>\${idx + 1}</td>
          <td>\${res.name}</td>
          <td>\${res.room}</td>
          <td>\${res.date}</td>
          <td>\${res.time}</td>
          <td><img src="\${res.qr || ''}" alt="QR Code" class="qr-code" /></td>
          <td>\${statusBadge}</td>
          <td>
            <button class="btn btn-sm btn-info viewQrBtn">Lihat QR</button>
          </td>`;
        tr.dataset.index = idx;
        checkinTableBody.appendChild(tr);
      });
    }

    checkinTableBody.addEventListener('click', function (e) {
      if (e.target.classList.contains('viewQrBtn')) {
        const row = e.target.closest('tr');
        const idx = parseInt(row.dataset.index);
        currentQrIndex = idx;
        const qrImg = document.getElementById('qrLarge');
        qrImg.src = reservations[idx].qr || 'https://via.placeholder.com/200?text=QR+Belum+Tersedia';
        qrModal.show();
      }
    });

    document.getElementById('regenerateQrBtn').addEventListener('click', function () {
      if (currentQrIndex !== null) {
        reservations[currentQrIndex].qr = 'https://via.placeholder.com/200?text=QR+Baru+' + (currentQrIndex+1);
        renderCheckinTable();
        const qrImg = document.getElementById('qrLarge');
        qrImg.src = reservations[currentQrIndex].qr;
      }
    });

    document.addEventListener('DOMContentLoaded', () => {
      renderCheckinTable();
    });
</script>
JS;
?>