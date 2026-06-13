<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'About';
?>

<div class="about-container container py-5">
    <!-- Hero Section -->
    <div class="about-hero row align-items-center g-5">
        <div class="col-lg-6 hero-content">
            <h1 class="about-hero-title">Solusi Reservasi SSMI</h1>
            <p class="about-hero-text">
                IPB Reserve adalah platform terintegrasi yang dirancang untuk menyederhanakan pengelolaan ruang dan fasilitas di lingkungan SSMI IPB University. Kami menghubungkan efisiensi operasional dengan kemudahan akses bagi seluruh civitas akademika.
            </p>
            <a href="<?= Url::to(['/ruang-rapat/default/daftar-ruangan']) ?>" class="btn-jelajah">
                Jelajah Fasilitas
            </a>
        </div>
        <div class="col-lg-6 hero-visual">
            <div class="about-image-wrapper">
                <img src="<?= Yii::getAlias('@web') ?>/images/lobby_ssmi.png" alt="Lobby SSMI IPB" class="about-image">
                <div class="badge-verified">
                    <span class="badge-verified-icon">
                        <i class="fa-solid fa-check"></i>
                    </span>
                    <span>100+ Ruang Terverifikasi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Utama Kami Section -->
    <div class="values-section">
        <div class="values-header">
            <h2 class="values-title">Nilai Utama Kami</h2>
            <p class="values-subtitle">Membangun ekosistem reservasi yang kredibel dan transparan.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="value-card efisiensi">
                    <div class="value-icon-container">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="value-card-title">Efisiensi</h3>
                    <p class="value-card-text">
                        Proses pemesanan yang cepat dan otomatis, menghilangkan birokrasi manual yang memakan waktu lama.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card transparansi">
                    <div class="value-icon-container">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h3 class="value-card-title">Transparansi</h3>
                    <p class="value-card-text">
                        Status ketersediaan gedung dan ruangan dapat dipantau secara real-time oleh seluruh pengguna tanpa terkecuali.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="value-card aksesibilitas">
                    <div class="value-icon-container">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <h3 class="value-card-title">Aksesibilitas</h3>
                    <p class="value-card-text">
                        Dapat diakses kapan saja dan di mana saja melalui berbagai perangkat, baik desktop maupun mobile.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dikelola Oleh & Bantuan Section -->
    <div class="info-section row g-5">
        <div class="col-lg-6">
            <h2 class="info-heading">Dikelola Oleh</h2>
            <div class="managed-card">
                <div class="managed-content">
                    <div class="phone-mockup">
                        <div class="phone-speaker"></div>
                        <div class="phone-screen">
                            <div class="phone-header">IPB Reserve</div>
                            <div class="phone-body">
                                <div class="phone-circle"></div>
                                <div class="phone-line long"></div>
                                <div class="phone-line short"></div>
                            </div>
                        </div>
                    </div>
                    <div class="managed-details">
                        <h4>Unit Manajemen Fasilitas</h4>
                        <div class="sub">Direktorat Umum dan Sarana Prasarana SSMI IPB University</div>
                        <p>
                            Kami berkomitmen untuk pemeliharaan standar fasilitas kelas dunia.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <h2 class="info-heading">Bantuan & Dukungan</h2>
            <div class="help-grid">
                <div class="help-card">
                    <div class="help-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="help-info">
                        <h5>Email Kami</h5>
                        <p>halo@ipb.ac.id</p>
                    </div>
                </div>
                <div class="help-card">
                    <div class="help-icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="help-info">
                        <h5>Hubungi</h5>
                        <p>+62 251 123 4567</p>
                    </div>
                </div>
                <div class="help-card">
                    <div class="help-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="help-info">
                        <h5>Kantor</h5>
                        <p>Gedung Sekretariat SSMI</p>
                    </div>
                </div>
                <div class="help-card">
                    <div class="help-icon"><i class="fa-solid fa-comments"></i></div>
                    <div class="help-info">
                        <h5>Live Chat</h5>
                        <p>Senin - Jumat, 08:00 - 16:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-container { font-family: 'Plus Jakarta Sans', sans-serif; }

/* Hero */
.about-hero { margin-bottom: 64px; padding-top: 20px; }
.about-hero-title {
    font-size: 3rem; font-weight: 800;
    color: var(--ipb-orange-dark, #A04100);
    margin-bottom: 24px; letter-spacing: -0.02em;
}
.about-hero-text {
    font-size: 1.15rem; line-height: 1.8;
    color: var(--ipb-muted, #575E70); margin-bottom: 36px;
}
.btn-jelajah {
    background: var(--ipb-orange, #FF6B00); color: #fff;
    font-size: 1rem; font-weight: 600;
    padding: 14px 32px; border-radius: 10px; border: none;
    text-decoration: none; display: inline-block;
    box-shadow: 0 4px 14px rgba(255,107,0,.15);
    transition: all .25s;
}
.btn-jelajah:hover {
    background: var(--ipb-orange-dark, #A04100); color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,107,0,.3);
}

/* Image */
.about-image-wrapper { position: relative; border-radius: 20px; overflow: visible; }
.about-image {
    width: 100%; height: auto; border-radius: 20px; display: block;
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
}
.badge-verified {
    position: absolute; bottom: -15px; right: 25px;
    background: #fff; border-radius: 30px;
    padding: 10px 20px; box-shadow: 0 8px 24px rgba(0,0,0,.12);
    display: flex; align-items: center; gap: 10px;
    font-size: .9rem; font-weight: 700; color: var(--ipb-text, #151C27);
    border: 1px solid rgba(226,191,176,.3); z-index: 10;
    animation: floatBadge 4s ease-in-out infinite;
}
.badge-verified-icon {
    width: 22px; height: 22px; background: #d1fae5; color: #10b981;
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: .75rem;
}
@keyframes floatBadge {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* Values Section */
.values-section { margin-bottom: 72px; padding: 40px 0; }
.values-header { text-align: center; margin-bottom: 48px; }
.values-title { font-size: 2.25rem; font-weight: 800; color: var(--ipb-text, #151C27); margin-bottom: 12px; }
.values-subtitle { color: var(--ipb-muted, #575E70); font-size: 1.1rem; font-weight: 500; }
.value-card {
    background: #fff; border-radius: 16px; padding: 32px 28px;
    box-shadow: 0 4px 20px rgba(0,0,0,.02); height: 100%;
    transition: all .3s; border: 1px solid rgba(21,28,39,.05);
}
.value-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,.06); }
.value-card.efisiensi { border-left: 4px solid var(--ipb-orange, #FF6B00); }
.value-card.transparansi { border-left: 4px solid #10b981; }
.value-card.aksesibilitas { border-left: 4px solid #3b82f6; }
.value-icon-container {
    width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 24px; font-size: 1.25rem;
}
.efisiensi .value-icon-container { background: var(--ipb-orange-light, #FFDBCC); color: var(--ipb-orange, #FF6B00); }
.transparansi .value-icon-container { background: #d1fae5; color: #10b981; }
.aksesibilitas .value-icon-container { background: #dbeafe; color: #3b82f6; }
.value-card-title { font-size: 1.35rem; font-weight: 700; color: var(--ipb-text, #151C27); margin-bottom: 14px; }
.value-card-text { color: var(--ipb-muted, #575E70); font-size: .98rem; line-height: 1.65; margin: 0; }

/* Info Section */
.info-section { margin-bottom: 48px; }
.info-heading { font-size: 1.75rem; font-weight: 800; color: var(--ipb-text, #151C27); margin-bottom: 28px; }
.managed-card {
    background: #fff; border-radius: 16px; padding: 36px;
    box-shadow: 0 4px 20px rgba(0,0,0,.02);
    border: 1px solid rgba(21,28,39,.05); height: 100%;
    display: flex; align-items: center;
}
.managed-content { display: flex; gap: 28px; align-items: center; }

/* Phone mockup */
.phone-mockup {
    width: 80px; height: 140px; border: 4px solid var(--ipb-text, #151C27);
    border-radius: 14px; position: relative; background: var(--ipb-text, #151C27);
    flex-shrink: 0; box-shadow: 0 8px 20px rgba(0,0,0,.1);
    display: flex; flex-direction: column; justify-content: space-between; padding: 2px;
}
.phone-speaker {
    width: 24px; height: 4px; background: #334155; border-radius: 2px;
    position: absolute; top: 6px; left: 50%; transform: translateX(-50%); z-index: 10;
}
.phone-screen {
    width: 100%; height: 100%; background: #fff; border-radius: 9px;
    overflow: hidden; display: flex; flex-direction: column; padding: 14px 6px 6px;
}
.phone-header {
    font-size: 8px; font-weight: 800; color: var(--ipb-orange-dark, #A04100);
    border-bottom: 1px solid #f1f5f9; padding-bottom: 4px; text-align: center;
}
.phone-body { flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 6px; }
.phone-circle {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--ipb-orange-light, #FFDBCC);
    display: flex; align-items: center; justify-content: center; margin-bottom: 4px;
}
.phone-circle::after {
    content: "\f1ad"; font-family: "Font Awesome 6 Free";
    font-weight: 900; font-size: 11px; color: var(--ipb-orange, #FF6B00);
}
.phone-line { height: 3px; background: #e2e8f0; border-radius: 2px; }
.phone-line.long { width: 85%; }
.phone-line.short { width: 55%; }
.managed-details h4 { font-size: 1.4rem; font-weight: 700; color: var(--ipb-text, #151C27); margin-bottom: 6px; }
.managed-details .sub { font-size: .95rem; color: var(--ipb-orange-dark, #A04100); margin-bottom: 16px; font-weight: 600; }
.managed-details p { font-size: 1rem; line-height: 1.6; color: var(--ipb-muted, #575E70); margin: 0; }

/* Help grid */
.help-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.help-card {
    background: #fff; border-radius: 16px; padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,.02);
    border: 1px solid rgba(21,28,39,.05);
    display: flex; align-items: center; gap: 20px;
    transition: all .25s;
}
.help-card:hover {
    border-color: var(--ipb-orange-light, #FFDBCC);
    box-shadow: 0 8px 24px rgba(255,107,0,.08);
    transform: translateY(-2px);
}
.help-icon {
    width: 44px; height: 44px; border-radius: 10px;
    background: #fff5f0; color: var(--ipb-orange, #FF6B00);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1.25rem;
}
.help-info h5 { font-size: .9rem; font-weight: 600; color: var(--ipb-muted, #575E70); margin-bottom: 4px; }
.help-info p { font-size: 1rem; font-weight: 700; color: var(--ipb-text, #151C27); margin: 0; }

/* Mobile */
@media (max-width: 768px) {
    .about-hero-title { font-size: 2rem; }
    .managed-content { flex-direction: column; text-align: center; }
    .help-grid { grid-template-columns: 1fr; }
}
</style>
