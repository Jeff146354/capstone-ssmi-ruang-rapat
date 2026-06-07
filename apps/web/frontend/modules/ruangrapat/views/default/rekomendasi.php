<?php
/** @var yii\web\View $this */
/** @var common\models\Room[] $rooms */
/** @var frontend\modules\ruangrapat\models\FindRoomForm $model */

use yii\widgets\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Cari Ruangan Tersedia';
?>

<style>
.search-hero {
    background: linear-gradient(135deg, #151C27 0%, #1e2d45 100%);
    padding: 48px 24px;
    position: relative; overflow: hidden;
}
.search-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 50% 80% at 90% 50%, rgba(255,107,0,.12) 0%, transparent 60%);
    pointer-events: none;
}
.search-hero .eyebrow {
    color: #FF6B00; font-size: 12px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
    margin-bottom: 12px; display: block; text-align: center;
}
.search-hero h1 {
    color: #fff; font-size: 32px; font-weight: 700;
    text-align: center; margin-bottom: 32px;
}

/* Search form */
.search-form-card {
    max-width: 900px; margin: 0 auto;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(6px);
    border-radius: 14px;
    box-shadow: 0 10px 40px rgba(0,0,0,.15);
    padding: 24px 28px;
}
.search-form-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
    gap: 16px; align-items: end;
}
@media (max-width: 860px) {
    .search-form-grid { grid-template-columns: 1fr 1fr; }
    .search-form-grid .search-btn-wrap { grid-column: span 2; }
}
.sf-label {
    color: #575E70; font-size: 12px; font-weight: 600;
    letter-spacing: .24px; margin-bottom: 4px; display: block;
}
.sf-input,
.sf-input .form-control {
    width: 100%;
    padding: 11px 14px !important;
    border: 1.5px solid #E2BFB0 !important;
    border-radius: 8px !important;
    font-size: 15px; color: #151C27;
    font-family: 'Plus Jakarta Sans', sans-serif;
    outline: none; transition: border-color .15s;
    box-shadow: none !important; background: #fff;
}
.sf-input:focus,
.sf-input .form-control:focus {
    border-color: #FF6B00 !important;
    box-shadow: 0 0 0 3px rgba(255,107,0,.1) !important;
}
.btn-search-submit {
    padding: 11px 28px; background: #FF6B00; color: #fff;
    font-size: 15px; font-weight: 700; border: none; border-radius: 8px;
    cursor: pointer; white-space: nowrap;
    display: flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 12px rgba(255,107,0,.3);
    transition: background .15s; width: 100%;
    justify-content: center;
}
.btn-search-submit:hover { background: #A04100; }

/* Results section */
.results-section { padding: 48px 0; background: var(--ipb-bg, #F9F9FF); }
.results-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 32px; flex-wrap: wrap; gap: 12px;
}
.results-count { color: #151C27; font-size: 20px; font-weight: 700; }
.results-count span { color: #FF6B00; }

/* Room result card */
.result-card {
    background: #fff; border-radius: 14px;
    border: 1px solid rgba(226,191,176,.3);
    box-shadow: 0 4px 16px rgba(0,0,0,.05);
    overflow: hidden; transition: transform .2s, box-shadow .2s;
    cursor: pointer; height: 100%;
    display: flex; flex-direction: column;
}
.result-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.1);
}
.result-card img {
    width: 100%; height: 190px; object-fit: cover;
    transition: transform .3s;
}
.result-card:hover img { transform: scale(1.04); }
.result-card .rc-body {
    padding: 18px 20px; flex-grow: 1; display: flex; flex-direction: column;
}
.result-card .rc-name {
    color: #151C27; font-size: 17px; font-weight: 700; margin-bottom: 6px;
}
.result-card .rc-desc {
    color: #575E70; font-size: 14px; line-height: 1.5; flex-grow: 1;
    margin-bottom: 12px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.result-card .rc-meta {
    display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap;
}
.result-card .rc-meta span {
    color: #575E70; font-size: 13px; font-weight: 500;
    display: flex; align-items: center; gap: 5px;
}
.result-card .rc-meta i { color: #A04100; }
.result-card .rc-badge {
    display: inline-block; background: #dcfce7; color: #16a34a;
    font-size: 11px; font-weight: 700; letter-spacing: .5px;
    text-transform: uppercase; padding: 3px 10px; border-radius: 4px;
    margin-bottom: 14px;
}
.result-card .btn-book {
    display: block; text-align: center;
    padding: 11px; background: #FF6B00; color: #fff;
    font-size: 14px; font-weight: 700; border-radius: 8px;
    text-decoration: none; transition: background .15s;
}
.result-card .btn-book:hover { background: #A04100; color: #fff; }

/* Empty state */
.empty-results { text-align: center; padding: 72px 24px; }
.empty-results i { color: #E2BFB0; font-size: 56px; margin-bottom: 16px; display: block; }
.empty-results h3 { color: #151C27; font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.empty-results p { color: #575E70; font-size: 15px; max-width: 400px; margin: 0 auto 24px; }
.btn-browse-all {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px; background: #fff; color: #A04100;
    font-size: 15px; font-weight: 700;
    border: 1.5px solid #A04100; border-radius: 10px;
    text-decoration: none; transition: background .15s, color .15s;
}
.btn-browse-all:hover { background: #A04100; color: #fff; }
</style>

<!-- Hero with search form -->
<section class="search-hero">
    <div style="position:relative; z-index:1;">
        <span class="eyebrow">IPB Reserve</span>
        <h1>Cari Ruangan Tersedia</h1>

        <div class="search-form-card">
            <?php $form = ActiveForm::begin([
                'method'     => 'get',
                'action'     => Url::to(['default/find-available-rooms']),
                'fieldConfig'=> ['template' => '{input}{error}'],
            ]); ?>
            <div class="search-form-grid">
                <div>
                    <label class="sf-label">Tanggal</label>
                    <?= $form->field($model, 'date')->input('date', ['class' => 'sf-input'])->label(false) ?>
                </div>
                <div>
                    <label class="sf-label">Waktu Mulai</label>
                    <?= $form->field($model, 'startTime')->input('time', ['class' => 'sf-input'])->label(false) ?>
                </div>
                <div>
                    <label class="sf-label">Waktu Selesai</label>
                    <?= $form->field($model, 'endTime')->input('time', ['class' => 'sf-input'])->label(false) ?>
                </div>
                <div>
                    <label class="sf-label">Min. Peserta</label>
                    <?= $form->field($model, 'minCapacity')->input('number', [
                        'class'       => 'sf-input',
                        'placeholder' => 'Jumlah',
                        'min'         => 1,
                    ])->label(false) ?>
                </div>
                <div class="search-btn-wrap">
                    <button type="submit" class="btn-search-submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>

<!-- Results -->
<section class="results-section">
    <div class="container">
        <?php if (!empty($rooms)): ?>
            <div class="results-header">
                <div class="results-count">
                    <span><?= count($rooms) ?></span> ruangan tersedia
                </div>
                <a href="<?= Url::to(['default/daftar-ruangan']) ?>" class="btn-browse-all">
                    Lihat Semua Ruangan <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php foreach ($rooms as $room): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="result-card"
                             onclick="location.href='<?= Url::to(['default/view', 'id' => $room->id]) ?>'">
                            <img src="<?= Html::encode($room->imageUrl) ?>"
                                 alt="<?= Html::encode($room->name) ?>"
                                 loading="lazy">
                            <div class="rc-body">
                                <span class="rc-badge"><i class="fas fa-check me-1"></i>Tersedia</span>
                                <div class="rc-name"><?= Html::encode($room->name) ?></div>
                                <div class="rc-desc"><?= Html::encode($room->description ?: '—') ?></div>
                                <div class="rc-meta">
                                    <?php if ($room->capacity): ?>
                                        <span><i class="fas fa-users"></i> <?= $room->capacity ?> orang</span>
                                    <?php endif ?>
                                    <?php if ($room->location): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?= Html::encode($room->location) ?></span>
                                    <?php endif ?>
                                </div>
                                <a href="<?= Url::to(['default/peminjaman', 'id' => $room->id]) ?>"
                                   class="btn-book"
                                   onclick="event.stopPropagation()">
                                    <i class="fas fa-calendar-plus me-1"></i>Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

        <?php else: ?>
            <div class="empty-results">
                <i class="fas fa-search"></i>
                <h3>Tidak Ada Ruangan Tersedia</h3>
                <p>Tidak ada ruangan yang tersedia pada waktu yang Anda pilih. Coba ubah tanggal atau waktu pencarian.</p>
                <a href="<?= Url::to(['default/daftar-ruangan']) ?>" class="btn-browse-all">
                    <i class="fas fa-door-open me-1"></i>Lihat Semua Ruangan
                </a>
            </div>
        <?php endif ?>
    </div>
</section>
