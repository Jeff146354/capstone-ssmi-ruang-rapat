<?php

/** @var yii\web\View $this */

$this->title = 'Manajemen SSMI';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">
            <b>Dashboard Admin</b> <br>
            Sekolah Sains Data, Matematika, dan Informatika 
        </h1>

        <p class="lead">Terdepan dalam inovasi dan melesat dalam prestasi</p>

        <p><a class="btn btn-lg btn-success" href="https://ssmi.ipb.ac.id/" target="_blank">Tentang SSMI</a></p>
    </div>

    <div class="body-content">
        <div class="row justify-content-around">
            <div class="col-lg-4 card">
                <div class="card-body row align-items-center gap-2">
                    <h2 class="card-title">Ruang Rapat SSMI</h2>
                    <p class="card-text">
                        Layanan pengajuan peminjaman ruang rapat SSMI IPB University secara terjadwal dan terkoordinasi.
                    </p>
                    <a class="btn btn-outline-secondary" href="<?= \yii\helpers\Url::to(['/ruang-rapat']) ?>">Kelola Peminjaman &raquo;</a>
                </div>
            </div>
            <div class="col-lg-4 card">
                <div class="card-body row align-items-center gap-2">
                    <h2 class="card-title">Manajemen Aset SSMI</h2>
                    <p class="card-text">
                        Layanan pengajuan pencatatan dan pengelolaan aset SSMI IPB University secara tertib dan terdokumentasi.
                    </p>
                    <a class="btn btn-outline-secondary" href="<?= \yii\helpers\Url::to(['/manajemen-aset']) ?>">Kelola Aset &raquo;</a>
                </div>
            </div>
        </div>
    </div>
</div>
