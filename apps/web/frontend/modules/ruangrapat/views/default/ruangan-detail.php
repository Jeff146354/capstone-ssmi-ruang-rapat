<?php
/** @var common\models\Room $model */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<style>
.detail-hero {
    position: relative; height: 380px; overflow: hidden;
    background: #151C27;
}
.detail-hero img {
    width: 100%; height: 100%; object-fit: cover; opacity: .7;
}
.detail-hero .overlay {
    position: absolute; inset: 0;
    background: linear-gradient(0deg, rgba(0,0,0,.75) 0%, rgba(0,0,0,.1) 60%, transparent 100%);
}
.detail-hero .hero-content {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 32px;
}
.detail-hero .back-btn {
    position: absolute; top: 24px; left: 24px;
    background: rgba(255,255,255,.15); backdrop-filter: blur(6px);
    color: #fff; border: 1px solid rgba(255,255,255,.25);
    border-radius: 8px; padding: 8px 16px;
    text-decoration: none; font-size: 14px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.detail-hero .back-btn:hover { background: rgba(255,255,255,.25); color: #fff; }
.detail-hero .room-tag {
    background: #FF6B00; color: #fff;
    font-size: 11px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; padding: 4px 10px; border-radius: 4px;
    display: inline-block; margin-bottom: 10px;
}
.detail-hero h1 {
    color: #fff; font-size: 32px; font-weight: 700;
    line-height: 1.2; margin-bottom: 12px;
}
.detail-hero .hero-meta {
    display: flex; gap: 20px; flex-wrap: wrap;
}
.detail-hero .hero-meta span {
    color: rgba(255,255,255,.85); font-size: 14px; font-weight: 500;
    display: flex; align-items: center; gap: 6px;
}
.detail-hero .hero-meta i { color: #FF6B00; }

/* Content area */
.detail-body { background: var(--ipb-bg, #F9F9FF); padding: 48px 0; }
.detail-card {
    background: #fff; border-radius: 16px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    padding: 32px; margin-bottom: 24px;
}
.detail-card h3 {
    color: #151C27; font-size: 18px; font-weight: 700;
    margin-bottom: 16px; padding-bottom: 12px;
    border-bottom: 2px solid #FFDBCC;
}
.detail-card p {
    color: #575E70; font-size: 15px; line-height: 1.7;
    margin-bottom: 0;
}
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.info-item { display: flex; flex-direction: column; gap: 4px; }
.info-item .label {
    color: #9CA3AF; font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .5px;
}
.info-item .value {
    color: #151C27; font-size: 15px; font-weight: 600;
}

/* CTA buttons */
.detail-cta { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-cta-primary {
    flex: 1; min-width: 160px;
    padding: 14px 24px; background: #FF6B00; color: #fff;
    font-size: 15px; font-weight: 700; border: none; border-radius: 10px;
    text-decoration: none; text-align: center;
    box-shadow: 0 4px 15px rgba(255,107,0,.3);
    transition: background .15s, transform .1s; display: inline-block;
}
.btn-cta-primary:hover { background: #A04100; color: #fff; transform: translateY(-1px); }
.btn-cta-outline {
    flex: 1; min-width: 140px;
    padding: 14px 24px; background: #fff; color: #151C27;
    font-size: 15px; font-weight: 600;
    border: 1.5px solid #E2BFB0; border-radius: 10px;
    text-decoration: none; text-align: center;
    transition: border-color .15s, color .15s; display: inline-block;
}
.btn-cta-outline:hover { border-color: #A04100; color: #A04100; }
.btn-cta-waitlist {
    flex: 1; min-width: 140px;
    padding: 14px 24px; background: #fff; color: #f59e0b;
    font-size: 15px; font-weight: 600;
    border: 1.5px solid #f59e0b; border-radius: 10px;
    text-decoration: none; text-align: center;
    transition: background .15s, color .15s; display: inline-block;
}
.btn-cta-waitlist:hover { background: #f59e0b; color: #fff; }
</style>

<!-- Hero with room image -->
<div class="detail-hero">
    <img src="<?= Html::encode($model->imageUrl) ?>" alt="<?= Html::encode($model->name) ?>">
    <div class="overlay"></div>

    <a href="<?= Url::to(['default/daftar-ruangan']) ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="hero-content">
        <span class="room-tag"><?= Html::encode($model->room) ?></span>
        <h1><?= Html::encode($model->name) ?></h1>
        <div class="hero-meta">
            <?php if ($model->capacity): ?>
                <span><i class="fas fa-users"></i> <?= $model->capacity ?> orang</span>
            <?php endif ?>
            <?php if ($model->location): ?>
                <span><i class="fas fa-map-marker-alt"></i> <?= Html::encode($model->location) ?></span>
            <?php endif ?>
            <?php if ($model->contact): ?>
                <span><i class="fas fa-phone"></i> <?= Html::encode($model->contact) ?></span>
            <?php endif ?>
        </div>
    </div>
</div>

<!-- Detail body -->
<section class="detail-body">
    <div class="container" style="max-width: 860px;">

        <!-- Description -->
        <?php if ($model->description): ?>
        <div class="detail-card">
            <h3><i class="fas fa-info-circle me-2" style="color:#FF6B00"></i>Deskripsi</h3>
            <p><?= nl2br(Html::encode($model->description)) ?></p>
        </div>
        <?php endif ?>

        <!-- Info grid -->
        <div class="detail-card">
            <h3><i class="fas fa-clipboard-list me-2" style="color:#FF6B00"></i>Informasi Ruangan</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">ID Ruangan</span>
                    <span class="value"><?= Html::encode($model->room) ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Kapasitas</span>
                    <span class="value"><?= $model->capacity ? $model->capacity . ' orang' : '—' ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Lokasi</span>
                    <span class="value"><?= $model->location ? Html::encode($model->location) : '—' ?></span>
                </div>
                <div class="info-item">
                    <span class="label">Contact Person</span>
                    <span class="value"><?= $model->contact ? Html::encode($model->contact) : '—' ?></span>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="detail-card">
            <h3><i class="fas fa-calendar-plus me-2" style="color:#FF6B00"></i>Aksi</h3>
            <div class="detail-cta">
                <a href="<?= Url::to(['peminjaman', 'id' => $model->id]) ?>" class="btn-cta-primary">
                    <i class="fas fa-plus me-2"></i>Ajukan Peminjaman
                </a>
                <a href="<?= Url::to(['jadwal', 'room_id' => $model->id]) ?>" class="btn-cta-outline">
                    <i class="fas fa-calendar me-2"></i>Lihat Jadwal
                </a>
                <a href="<?= Url::to(['waitlist-form', 'id' => $model->id]) ?>" class="btn-cta-waitlist">
                    <i class="fas fa-clock me-2"></i>Daftar Tunggu
                </a>
            </div>
        </div>

    </div>
</section>
