<?php
/** @var yii\web\View $this */
/** @var common\models\Room[] $featuredRooms */
/** @var common\models\Room[] $otherRooms */
/** @var frontend\modules\ruangrapat\models\FindRoomForm $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Home';
?>

<style>
/* ── Hero ── */
.hero-section {
    padding: 32px 24px 96px;
    background:
        radial-gradient(ellipse 105% 317% at 100% 0%, rgba(255,107,0,.05) 0%, rgba(255,107,0,0) 40%),
        radial-gradient(ellipse 105% 317% at 0% 100%, rgba(255,107,0,.03) 0%, rgba(255,107,0,0) 30%),
        var(--ipb-bg);
    display: flex; flex-direction: column; align-items: center;
}
.hero-badge {
    background: #FFDBCC; color: #351000;
    font-size: 12px; font-weight: 700; letter-spacing: .24px;
    padding: 4px 16px; border-radius: 9999px;
    display: inline-block; margin-bottom: 16px;
}
.hero-title {
    color: #151C27; font-size: 36px; font-weight: 700; line-height: 44px;
    text-align: center; margin-bottom: 16px;
}
.hero-subtitle {
    color: #575E70; font-size: 16px; font-weight: 400; line-height: 24px;
    text-align: center; max-width: 672px; margin-bottom: 32px;
}

/* ── Search card ── */
.search-card {
    width: 100%; max-width: 1152px;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(5px);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,.10);
    padding: 24px;
}
.search-card .search-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr auto;
    gap: 16px;
    align-items: end;
}
@media (max-width: 900px) {
    .search-card .search-grid { grid-template-columns: 1fr 1fr; }
    .search-card .search-grid .btn-search { grid-column: span 2; }
}
@media (max-width: 600px) {
    .search-card .search-grid { grid-template-columns: 1fr; }
    .search-card .search-grid .btn-search { grid-column: span 1; }
}
.search-field-label {
    color: #575E70; font-size: 12px; font-weight: 500;
    letter-spacing: .24px; margin-bottom: 4px; display: block;
}
.search-input {
    width: 100%;
    padding: 12px 16px 12px 40px;
    background: #fff;
    border: 1px solid #E2BFB0;
    border-radius: 8px;
    font-size: 16px; font-weight: 400; color: #151C27;
    outline: none; transition: border-color .15s;
}
.search-input:focus { border-color: #FF6B00; box-shadow: 0 0 0 3px rgba(255,107,0,.1); }
.search-input-wrap { position: relative; }
.search-input-icon {
    position: absolute; left: 12px; top: 0;
    height: 44px; display: flex; align-items: center;
    color: #8E7164; font-size: 14px; pointer-events: none;
}
.btn-search {
    padding: 12px 24px;
    background: #FF6B00; color: #fff;
    font-size: 16px; font-weight: 600; text-transform: uppercase;
    border: none; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    white-space: nowrap;
    box-shadow: 0 4px 6px -4px rgba(0,0,0,.1), 0 10px 15px -3px rgba(0,0,0,.1);
    transition: background .15s;
}
.btn-search:hover { background: #A04100; }

/* ── Section ── */
.section-header {
    display: flex; justify-content: space-between; align-items: flex-end;
    margin-bottom: 32px;
}
.section-title { color: #151C27; font-size: 28px; font-weight: 700; line-height: 36px; }
.section-subtitle { color: #575E70; font-size: 14px; font-weight: 400; line-height: 20px; }
.section-link {
    color: #A04100; font-size: 16px; font-weight: 600;
    text-decoration: none; display: flex; align-items: center; gap: 4px;
}
.section-link:hover { color: #FF6B00; }

/* ── Featured grid ── */
.featured-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-template-rows: 600px;
    gap: 16px;
}
.featured-grid .right-col {
    display: grid;
    grid-template-rows: 1fr 1fr;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.room-card-hero {
    position: relative; border-radius: 12px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    cursor: pointer; text-decoration: none; display: block;
}
.room-card-hero img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .3s;
}
.room-card-hero:hover img { transform: scale(1.03); }
.room-card-hero .overlay {
    position: absolute; inset: 0;
    background: linear-gradient(0deg, rgba(0,0,0,.8) 0%, rgba(0,0,0,.2) 50%, rgba(0,0,0,0) 100%);
}
.room-card-hero .card-content {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 24px;
}
.room-card-hero .card-content.sm { padding: 16px; }
.badge-premium {
    background: #FF6B00; color: #fff;
    font-size: 12px; font-weight: 500; letter-spacing: .24px;
    padding: 4px 8px; border-radius: 4px; display: inline-block;
}
.badge-capacity {
    background: rgba(255,255,255,.2); color: #fff;
    font-size: 12px; font-weight: 500; letter-spacing: .24px;
    padding: 4px 8px; border-radius: 4px;
    backdrop-filter: blur(6px); display: inline-block;
}
.card-room-title-lg {
    color: #fff; font-size: 28px; font-weight: 700; line-height: 36px;
    margin: 8px 0 4px;
}
.card-room-title-md {
    color: #fff; font-size: 20px; font-weight: 700; line-height: 28px;
    margin-bottom: 4px;
}
.card-room-title-sm {
    color: #fff; font-size: 16px; font-weight: 600; line-height: 24px;
}
.card-room-desc {
    color: rgba(255,255,255,.8); font-size: 14px; font-weight: 400; line-height: 20px;
}
.card-amenity {
    color: rgba(255,255,255,.9); font-size: 12px; font-weight: 500;
    letter-spacing: .24px; display: flex; align-items: center; gap: 6px;
}

/* ── Other rooms grid ── */
.other-rooms-section {
    background: #F0F3FF;
    padding: 32px 24px;
}
.room-card-other {
    background: #fff; border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,.05);
    overflow: hidden; transition: box-shadow .2s;
    text-decoration: none; color: inherit; display: block;
}
.room-card-other:hover { box-shadow: 0 8px 30px rgba(0,0,0,.1); }
.room-card-other img {
    width: 100%; height: 200px; object-fit: cover;
}
.room-card-other .card-img-wrap { position: relative; }
.room-card-other .availability-badge {
    position: absolute; top: 12px; left: 12px;
    font-size: 12px; font-weight: 500; letter-spacing: .24px;
    padding: 4px 8px; border-radius: 4px;
}
.availability-badge.available { background: #22c55e; color: #fff; }
.availability-badge.full      { background: #ef4444; color: #fff; }
.room-card-other .card-body-other { padding: 16px; }
.room-card-other .room-name {
    color: #151C27; font-size: 20px; font-weight: 700; line-height: 28px;
    margin-bottom: 4px;
}
.room-card-other .room-meta {
    color: #575E70; font-size: 14px; font-weight: 400; line-height: 20px;
    display: flex; align-items: center; gap: 4px;
}
.room-card-other .btn-detail {
    color: #A04100; font-size: 16px; font-weight: 700; line-height: 24px;
    text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
    margin-top: 12px;
}
.room-card-other .btn-detail:hover { color: #FF6B00; }
.room-card-other .btn-disabled {
    color: #575E70; font-size: 16px; font-weight: 700; line-height: 24px;
    margin-top: 12px; display: inline-block;
}

/* ── Stats bar ── */
.stats-bar {
    background: #fff;
    padding: 32px 24px;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}
.stat-item { text-align: center; }
.stat-number {
    color: #FF6B00; font-size: 36px; font-weight: 700; line-height: 44px;
}
.stat-label {
    color: #575E70; font-size: 14px; font-weight: 400;
    text-transform: uppercase; letter-spacing: .7px; line-height: 20px;
}
</style>

<!-- ── Hero ── -->
<section class="hero-section">
    <span class="hero-badge">IPB Reserve</span>
    <h1 class="hero-title">Selamat Datang di SSMI IPB</h1>
    <p class="hero-subtitle">
        Temukan dan pesan ruang rapat terbaik di seluruh lingkungan IPB University dengan
        cepat, transparan, dan tanpa repot.
    </p>

    <!-- Search card -->
    <div class="search-card">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Url::to(['/ruang-rapat/default/find-available-rooms']),
            'options' => ['class' => 'w-100'],
        ]); ?>
        <div class="search-grid">
            <!-- Tanggal -->
            <div>
                <label class="search-field-label">Tanggal</label>
                <div class="search-input-wrap">
                    <i class="fas fa-calendar search-input-icon"></i>
                    <?= $form->field($model, 'date')->input('date', [
                        'class' => 'search-input',
                    ])->label(false) ?>
                </div>
            </div>
            <!-- Waktu Mulai -->
            <div>
                <label class="search-field-label">Waktu Mulai</label>
                <div class="search-input-wrap">
                    <i class="fas fa-clock search-input-icon"></i>
                    <?= $form->field($model, 'startTime')->input('time', [
                        'class' => 'search-input',
                    ])->label(false) ?>
                </div>
            </div>
            <!-- Waktu Selesai -->
            <div>
                <label class="search-field-label">Waktu Selesai</label>
                <div class="search-input-wrap">
                    <i class="fas fa-clock search-input-icon"></i>
                    <?= $form->field($model, 'endTime')->input('time', [
                        'class' => 'search-input',
                    ])->label(false) ?>
                </div>
            </div>
            <!-- Peserta -->
            <div>
                <label class="search-field-label">Peserta</label>
                <div class="search-input-wrap">
                    <i class="fas fa-users search-input-icon"></i>
                    <?= $form->field($model, 'minCapacity')->input('number', [
                        'class'       => 'search-input',
                        'placeholder' => 'Jumlah',
                        'min'         => 1,
                    ])->label(false) ?>
                </div>
            </div>
            <!-- Button -->
            <div class="btn-search-wrap">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i>
                    CARI RUANG
                </button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

<!-- ── Featured Rooms ── -->
<section style="padding: 32px 24px; max-width: 1280px; margin: 0 auto;">
    <div class="section-header">
        <div>
            <div class="section-title">Ruang Unggulan</div>
            <div class="section-subtitle">Rekomendasi terbaik berdasarkan fasilitas dan kenyamanan.</div>
        </div>
        <a href="<?= Url::to(['/ruang-rapat/default/daftar-ruangan']) ?>" class="section-link">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <?php if (!empty($featuredRooms)): ?>
    <div class="featured-grid">
        <!-- Left: first room (large) -->
        <?php $first = $featuredRooms[0]; ?>
        <a href="<?= Url::to(['/ruang-rapat/default/view', 'id' => $first->id]) ?>" class="room-card-hero">
            <img src="<?= Html::encode($first->imageUrl) ?>"
                 alt="<?= Html::encode($first->name) ?>">
            <div class="overlay"></div>
            <div class="card-content">
                <div class="d-flex gap-2 mb-2">
                    <span class="badge-premium">UNGGULAN</span>
                    <?php if ($first->capacity): ?>
                        <span class="badge-capacity"><?= $first->capacity ?> Orang</span>
                    <?php endif ?>
                </div>
                <div class="card-room-title-lg"><?= Html::encode($first->name) ?></div>
                <div class="card-room-desc">
                    <?= Html::encode(mb_substr($first->description ?? '', 0, 120)) ?>
                </div>
            </div>
        </a>

        <!-- Right: remaining rooms in 2x2 grid -->
        <div class="right-col">
            <?php for ($i = 1; $i <= 3; $i++):
                if (empty($featuredRooms[$i])) break;
                $room = $featuredRooms[$i];
            ?>
            <a href="<?= Url::to(['/ruang-rapat/default/view', 'id' => $room->id]) ?>" class="room-card-hero">
                <img src="<?= Html::encode($room->imageUrl) ?>"
                     alt="<?= Html::encode($room->name) ?>">
                <div class="overlay"></div>
                <div class="card-content sm">
                    <div class="card-room-title-sm"><?= Html::encode($room->name) ?></div>
                    <?php if ($room->capacity): ?>
                        <div class="card-amenity mt-1">
                            <i class="fas fa-users"></i> <?= $room->capacity ?> orang
                        </div>
                    <?php endif ?>
                </div>
            </a>
            <?php endfor ?>
        </div>
    </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="fas fa-door-open fa-3x mb-3"></i>
            <p>Belum ada ruangan tersedia.</p>
        </div>
    <?php endif ?>
</section>

<!-- ── Other Rooms ── -->
<section class="other-rooms-section">
    <div style="max-width: 1280px; margin: 0 auto;">
        <div class="section-header">
            <div>
                <div class="section-title">Ruang Lainnya</div>
            </div>
            <a href="<?= Url::to(['/ruang-rapat/default/daftar-ruangan']) ?>" class="section-link">
                Selengkapnya <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($otherRooms, 0, 6) as $room):
                // Simple availability check: any approved reservation today?
                $isBusy = \common\models\Reservation::find()
                    ->where(['room_id' => $room->id, 'date' => date('Y-m-d'), 'status' => \common\models\Reservation::STATUS_APPROVED])
                    ->exists();
            ?>
            <div class="col-md-4">
                <div class="room-card-other">
                    <div class="card-img-wrap">
                        <img src="<?= Html::encode($room->imageUrl) ?>"
                             alt="<?= Html::encode($room->name) ?>">
                        <span class="availability-badge <?= $isBusy ? 'full' : 'available' ?>">
                            <?= $isBusy ? 'Penuh' : 'Tersedia' ?>
                        </span>
                    </div>
                    <div class="card-body-other">
                        <div class="room-name"><?= Html::encode($room->name) ?></div>
                        <div class="room-meta">
                            <i class="fas fa-users"></i>
                            <?= $room->capacity ?> Peserta
                        </div>
                        <?php if ($room->location): ?>
                        <div class="room-meta mt-1">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= Html::encode($room->location) ?>
                        </div>
                        <?php endif ?>

                        <?php if (!$isBusy): ?>
                            <a href="<?= Url::to(['/ruang-rapat/default/view', 'id' => $room->id]) ?>"
                               class="btn-detail">
                                Detail Ruangan <i class="fas fa-arrow-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="btn-disabled">Booking Tidak Tersedia</span>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<!-- ── Stats bar ── -->
<section class="stats-bar">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">150+</div>
                <div class="stat-label">Ruang Tersedia</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">5k+</div>
                <div class="stat-label">Booking Selesai</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Akses Sistem</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">4.8</div>
                <div class="stat-label">Rating Pengguna</div>
            </div>
        </div>
    </div>
</section>
