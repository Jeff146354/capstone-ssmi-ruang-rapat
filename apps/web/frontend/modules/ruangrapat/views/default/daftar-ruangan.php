<?php
/** @var yii\web\View $this */
/** @var common\models\Room[] $rooms */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Daftar Ruangan';
?>

<style>
.browse-hero {
    background: linear-gradient(135deg, #151C27 0%, #1e2d45 100%);
    padding: 56px 24px 48px;
    text-align: center;
    position: relative; overflow: hidden;
}
.browse-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 80% at 80% 50%, rgba(255,107,0,.1) 0%, transparent 60%);
    pointer-events: none;
}
.browse-hero .eyebrow {
    color: #FF6B00; font-size: 12px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
    margin-bottom: 12px; display: block;
}
.browse-hero h1 { color: #fff; font-size: 36px; font-weight: 700; margin-bottom: 12px; }
.browse-hero p  { color: rgba(255,255,255,.6); font-size: 16px; max-width: 500px; margin: 0 auto; }

.browse-section { padding: 48px 0; background: var(--ipb-bg, #F9F9FF); }

/* Search bar */
.browse-search {
    background: #fff;
    border: 1.5px solid #E2BFB0;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 40px;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
}
.browse-search i { color: #8E7164; font-size: 18px; flex-shrink: 0; }
.browse-search input {
    border: none; outline: none;
    font-size: 16px; color: #151C27;
    flex-grow: 1; background: transparent;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.browse-search input::placeholder { color: #9CA3AF; }

/* Room card */
.room-browse-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    border: 1px solid rgba(226,191,176,.3);
    transition: transform .2s, box-shadow .2s;
    cursor: pointer; display: flex; flex-direction: column;
    height: 100%;
}
.room-browse-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.12);
}
.room-browse-card .card-img-wrap {
    position: relative; overflow: hidden;
    height: 200px; flex-shrink: 0;
}
.room-browse-card .card-img-wrap img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .3s;
}
.room-browse-card:hover .card-img-wrap img { transform: scale(1.05); }
.room-browse-card .card-body-inner {
    padding: 20px; flex-grow: 1;
    display: flex; flex-direction: column;
}
.room-browse-card .room-name {
    color: #151C27; font-size: 18px; font-weight: 700;
    margin-bottom: 6px; line-height: 1.3;
}
.room-browse-card .room-desc {
    color: #575E70; font-size: 14px; line-height: 1.5;
    flex-grow: 1; margin-bottom: 16px;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
}
.room-browse-card .room-meta {
    display: flex; gap: 16px; margin-bottom: 16px;
}
.room-browse-card .meta-item {
    display: flex; align-items: center; gap: 6px;
    color: #575E70; font-size: 13px; font-weight: 500;
}
.room-browse-card .meta-item i { color: #A04100; font-size: 13px; }
.room-browse-card .btn-detail {
    display: inline-flex; align-items: center; gap: 6px;
    color: #A04100; font-size: 14px; font-weight: 700;
    text-decoration: none; padding: 10px 20px;
    border: 1.5px solid #A04100; border-radius: 8px;
    transition: background .15s, color .15s; align-self: flex-start;
}
.room-browse-card .btn-detail:hover {
    background: #A04100; color: #fff;
}

/* Empty state */
.empty-state { text-align: center; padding: 80px 24px; }
.empty-state i { color: #E2BFB0; font-size: 64px; margin-bottom: 16px; display: block; }
.empty-state h3 { color: #151C27; font-size: 22px; font-weight: 700; margin-bottom: 8px; }
.empty-state p  { color: #575E70; font-size: 15px; }

/* Counter badge */
.result-count {
    color: #575E70; font-size: 14px; font-weight: 500;
    margin-bottom: 24px;
}
.result-count span { color: #151C27; font-weight: 700; }
</style>

<!-- Hero -->
<section class="browse-hero">
    <span class="eyebrow">IPB Reserve</span>
    <h1>Daftar Seluruh Ruangan</h1>
    <p>Temukan ruang rapat yang sesuai dengan kebutuhan Anda.</p>
</section>

<!-- Browse section -->
<section class="browse-section">
    <div class="container">

        <!-- Search filter -->
        <div class="browse-search">
            <i class="fas fa-search"></i>
            <input type="text" id="roomSearch" placeholder="Cari nama ruangan...">
        </div>

        <!-- Result count -->
        <div class="result-count">
            Menampilkan <span id="roomCount"><?= count($rooms) ?></span> ruangan
        </div>

        <?php if (empty($rooms)): ?>
            <div class="empty-state">
                <i class="fas fa-door-open"></i>
                <h3>Belum Ada Ruangan</h3>
                <p>Belum ada ruangan yang terdaftar di sistem.</p>
            </div>
        <?php else: ?>
            <div class="row g-4" id="roomGrid">
                <?php foreach ($rooms as $room): ?>
                    <div class="col-md-4 col-sm-6 room-item"
                         data-name="<?= strtolower(Html::encode($room->name)) ?>">
                        <div class="room-browse-card"
                             onclick="location.href='<?= Url::to(['default/view', 'id' => $room->id]) ?>'">
                            <div class="card-img-wrap">
                                <img src="<?= Html::encode($room->imageUrl) ?>"
                                     alt="<?= Html::encode($room->name) ?>"
                                     loading="lazy">
                            </div>
                            <div class="card-body-inner">
                                <div class="room-name"><?= Html::encode($room->name) ?></div>
                                <div class="room-desc"><?= Html::encode($room->description ?: 'Tidak ada deskripsi.') ?></div>
                                <div class="room-meta">
                                    <?php if ($room->capacity): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-users"></i>
                                            <?= $room->capacity ?> orang
                                        </div>
                                    <?php endif ?>
                                    <?php if ($room->location): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <?= Html::encode($room->location) ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <a href="<?= Url::to(['default/view', 'id' => $room->id]) ?>"
                                   class="btn-detail"
                                   onclick="event.stopPropagation()">
                                    Detail Ruangan <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

    </div>
</section>

<script>
// Live search filter
document.getElementById('roomSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.room-item').forEach(function (item) {
        const name = item.getAttribute('data-name');
        const show = !q || name.includes(q);
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('roomCount').textContent = visible;
});
</script>
