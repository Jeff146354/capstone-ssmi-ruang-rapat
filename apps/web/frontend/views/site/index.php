<?php
/** @var yii\web\View $this */
use yii\helpers\Url;

$this->title = 'SSMI IPB';
?>

<style>
/* ── Hero ── */
.ssmi-hero {
    min-height: 420px;
    background: linear-gradient(135deg, #151C27 0%, #1e2d45 60%, #2a1a0e 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
    padding: 80px 24px 60px;
    position: relative; overflow: hidden;
}
.ssmi-hero::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 60% 50% at 80% 20%, rgba(255,107,0,.15) 0%, transparent 60%),
        radial-gradient(ellipse 40% 60% at 10% 80%, rgba(255,107,0,.08) 0%, transparent 50%);
    pointer-events: none;
}
.ssmi-hero .eyebrow {
    background: rgba(255,107,0,.15);
    border: 1px solid rgba(255,107,0,.3);
    color: #FF6B00;
    font-size: 12px; font-weight: 700; letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 6px 18px; border-radius: 9999px;
    margin-bottom: 24px; display: inline-block;
}
.ssmi-hero h1 {
    color: #fff;
    font-size: clamp(28px, 4vw, 52px);
    font-weight: 700; line-height: 1.15;
    max-width: 800px; margin-bottom: 16px;
}
.ssmi-hero h1 span { color: #FF6B00; }
.ssmi-hero p {
    color: rgba(255,255,255,.65);
    font-size: 18px; font-weight: 400;
    max-width: 560px; margin-bottom: 36px;
}
.ssmi-hero .btn-hero {
    background: #FF6B00; color: #fff;
    font-size: 15px; font-weight: 700;
    padding: 14px 32px; border-radius: 8px;
    text-decoration: none; border: none;
    box-shadow: 0 4px 20px rgba(255,107,0,.4);
    transition: background .15s, transform .15s;
    display: inline-block;
}
.ssmi-hero .btn-hero:hover {
    background: #A04100; color: #fff;
    transform: translateY(-1px);
}

/* ── Module cards section ── */
.modules-section {
    padding: 72px 24px;
    background: var(--ipb-bg, #F9F9FF);
}
.modules-section .section-label {
    text-align: center;
    color: #575E70; font-size: 13px; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    margin-bottom: 12px;
}
.modules-section .section-title {
    text-align: center;
    color: #151C27; font-size: 32px; font-weight: 700;
    margin-bottom: 8px;
}
.modules-section .section-subtitle {
    text-align: center;
    color: #575E70; font-size: 16px;
    max-width: 520px; margin: 0 auto 48px;
}

/* ── Module card ── */
.module-card {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    padding: 40px 36px 36px;
    text-decoration: none;
    display: flex; flex-direction: column;
    min-height: 320px;
    transition: transform .2s, box-shadow .2s;
    cursor: pointer;
}
.module-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 48px rgba(0,0,0,.15);
}

/* Ruang Rapat — dark with orange accent */
.module-card.ruang-rapat {
    background: linear-gradient(145deg, #1a1f2e 0%, #151C27 100%);
    border: 1px solid rgba(255,107,0,.2);
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
}
.module-card.ruang-rapat .card-glow {
    position: absolute; top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(255,107,0,.25) 0%, transparent 70%);
    pointer-events: none;
}
.module-card.ruang-rapat .card-icon-wrap {
    width: 56px; height: 56px; border-radius: 14px;
    background: rgba(255,107,0,.15);
    border: 1px solid rgba(255,107,0,.3);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 24px;
    font-size: 24px; color: #FF6B00;
}
.module-card.ruang-rapat .card-tag {
    display: inline-block;
    background: rgba(255,107,0,.15); color: #FF6B00;
    font-size: 11px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; padding: 4px 10px;
    border-radius: 4px; margin-bottom: 16px;
}
.module-card.ruang-rapat h2 {
    color: #fff; font-size: 26px; font-weight: 700;
    margin-bottom: 12px; line-height: 1.2;
}
.module-card.ruang-rapat p {
    color: rgba(255,255,255,.55); font-size: 15px;
    line-height: 1.6; flex-grow: 1; margin-bottom: 28px;
}
.module-card.ruang-rapat .card-cta {
    display: inline-flex; align-items: center; gap: 8px;
    background: #FF6B00; color: #fff;
    font-size: 14px; font-weight: 700;
    padding: 12px 24px; border-radius: 8px;
    text-decoration: none; align-self: flex-start;
    transition: background .15s;
}
.module-card.ruang-rapat .card-cta:hover { background: #A04100; color: #fff; }

/* Manajemen Aset — light with blue accent */
.module-card.manajemen-aset {
    background: linear-gradient(145deg, #f0f4ff 0%, #e8eeff 100%);
    border: 1px solid rgba(99,102,241,.15);
    box-shadow: 0 8px 32px rgba(99,102,241,.08);
}
.module-card.manajemen-aset .card-glow {
    position: absolute; top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(99,102,241,.15) 0%, transparent 70%);
    pointer-events: none;
}
.module-card.manajemen-aset .card-icon-wrap {
    width: 56px; height: 56px; border-radius: 14px;
    background: rgba(99,102,241,.1);
    border: 1px solid rgba(99,102,241,.2);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 24px;
    font-size: 24px; color: #6366f1;
}
.module-card.manajemen-aset .card-tag {
    display: inline-block;
    background: rgba(99,102,241,.1); color: #6366f1;
    font-size: 11px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; padding: 4px 10px;
    border-radius: 4px; margin-bottom: 16px;
}
.module-card.manajemen-aset h2 {
    color: #151C27; font-size: 26px; font-weight: 700;
    margin-bottom: 12px; line-height: 1.2;
}
.module-card.manajemen-aset p {
    color: #575E70; font-size: 15px;
    line-height: 1.6; flex-grow: 1; margin-bottom: 28px;
}
.module-card.manajemen-aset .card-cta {
    display: inline-flex; align-items: center; gap: 8px;
    background: #6366f1; color: #fff;
    font-size: 14px; font-weight: 700;
    padding: 12px 24px; border-radius: 8px;
    text-decoration: none; align-self: flex-start;
    transition: background .15s;
}
.module-card.manajemen-aset .card-cta:hover { background: #4f46e5; color: #fff; }

/* Feature pills */
.feature-pill {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 500;
    padding: 4px 10px; border-radius: 9999px;
    margin-right: 6px; margin-bottom: 6px;
}
.ruang-rapat .feature-pill {
    background: rgba(255,255,255,.08); color: rgba(255,255,255,.6);
    border: 1px solid rgba(255,255,255,.1);
}
.manajemen-aset .feature-pill {
    background: rgba(99,102,241,.08); color: #6366f1;
    border: 1px solid rgba(99,102,241,.15);
}
</style>

<!-- ── Hero ── -->
<section class="ssmi-hero">
    <span class="eyebrow">IPB University</span>
    <h1>Sekolah Sains Data,<br><span>Matematika, dan Informatika</span></h1>
    <p>Terdepan dalam inovasi dan melesat dalam prestasi</p>
    <a href="https://ssmi.ipb.ac.id/" target="_blank" class="btn-hero">
        Tentang SSMI &nbsp;→
    </a>
</section>

<!-- ── Module Cards ── -->
<section class="modules-section">
    <div class="container" style="max-width: 1100px;">
        <div class="section-label">Layanan Kami</div>
        <div class="section-title">Pilih Layanan</div>
        <p class="section-subtitle">Akses semua layanan fasilitas SSMI IPB University dalam satu platform terintegrasi.</p>

        <div class="row g-4">
            <!-- Ruang Rapat -->
            <div class="col-md-6">
                <div class="module-card ruang-rapat" onclick="location.href='<?= Url::to(['/ruang-rapat']) ?>'">
                    <div class="card-glow"></div>
                    <div class="card-icon-wrap">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <span class="card-tag">Reservasi</span>
                    <h2>Ruang Rapat SSMI</h2>
                    <p>Pesan ruang rapat SSMI IPB University secara terjadwal, transparan, dan tanpa repot. Tersedia berbagai pilihan ruangan sesuai kebutuhan.</p>
                    <div class="mb-4">
                        <span class="feature-pill"><i class="fas fa-check-circle"></i> Booking Online</span>
                        <span class="feature-pill"><i class="fas fa-check-circle"></i> Real-time Availability</span>
                        <span class="feature-pill"><i class="fas fa-check-circle"></i> QR Check-in</span>
                    </div>
                    <a href="<?= Url::to(['/ruang-rapat']) ?>" class="card-cta" onclick="event.stopPropagation()">
                        Kelola Peminjaman <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Manajemen Aset -->
            <div class="col-md-6">
                <div class="module-card manajemen-aset" onclick="location.href='<?= Url::to(['/manajemen-aset']) ?>'">
                    <div class="card-glow"></div>
                    <div class="card-icon-wrap">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <span class="card-tag">Inventaris</span>
                    <h2>Manajemen Aset SSMI</h2>
                    <p>Pencatatan dan pengelolaan aset SSMI IPB University secara tertib, terdokumentasi, dan mudah dilacak kapan saja.</p>
                    <div class="mb-4">
                        <span class="feature-pill"><i class="fas fa-check-circle"></i> Pencatatan Aset</span>
                        <span class="feature-pill"><i class="fas fa-check-circle"></i> Tracking Status</span>
                        <span class="feature-pill"><i class="fas fa-check-circle"></i> Laporan Berkala</span>
                    </div>
                    <a href="<?= Url::to(['/manajemen-aset']) ?>" class="card-cta" onclick="event.stopPropagation()">
                        Kelola Aset <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
