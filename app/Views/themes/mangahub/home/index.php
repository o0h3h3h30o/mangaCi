<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('head_extra') ?>
<?php if (!empty($newestManga[0])): ?>
<link rel="preload" href="<?= esc(manga_cover_url($newestManga[0])) ?>" as="image" fetchpriority="high">
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
// Determine if we are on page 1
$isFirstPage = empty($_GET['page']) || (int)$_GET['page'] === 1;

// Time-ago helper
function mangahub_time_ago($datetime) {
    if (empty($datetime)) return '';
    $ts = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
    if (!$ts) return '';
    $diff = time() - $ts;
    if ($diff < 0) return 'just now';
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
    return date('d/m/Y', $ts);
}
?>

<?php if ($isFirstPage): ?>
<!-- ── SPOTLIGHT SLIDER ── -->
<section>
  <div class="sec-head">
    <h2 class="sec-title">Featured</h2>
  </div>

  <div class="slider-section-bg">
    <button class="slider-arrow prev" id="sliderPrev">&#8249;</button>

    <div class="slider-viewport">
      <div class="slider-track" id="sliderTrack">
        <?php foreach (array_slice($newestManga, 0, 9) as $_si => $manga): ?>
        <a href="/manga/<?= esc($manga['slug'], 'url') ?>" class="slide-card">
          <div class="slide-body">
            <div>
              <div class="slide-releasing">RELEASING</div>
              <div class="slide-title"><?= esc($manga['name']) ?></div>
              <?php if (!empty($manga['summary'])): ?>
              <div class="slide-desc"><?= esc(mb_strimwidth(strip_tags($manga['summary']), 0, 120, '...')) ?></div>
              <?php endif; ?>
            </div>
            <div>
              <div class="slide-meta">Chap <?= esc($manga['chapter_1'] ?? '') ?></div>
              <?php if (!empty($manga['categories'])): ?>
              <div class="slide-genres">
                <?php foreach (array_slice($manga['categories'] ?? [], 0, 3) as $cat): ?>
                <span class="slide-genre"><?= esc($cat['name'] ?? $cat) ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="slide-cover">
            <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" width="200" height="300" <?= $_si < 3 ? 'fetchpriority="high" loading="eager"' : 'loading="lazy" decoding="async"' ?>>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <button class="slider-arrow next" id="sliderNext">&#8250;</button>
  </div>
</section>
<?php endif; ?>

<!-- ── LATEST MANGA ── -->
<section>
  <div class="sec-head">
    <h2 class="sec-title">Latest Manga</h2>
    <a href="/search" class="see-all">See all →</a>
  </div>
  <div class="manga-grid">
    <?php foreach ($recentlyUpdated as $_ri => $manga): ?>
    <?php $ago = mangahub_time_ago($manga['update_at'] ?? ''); ?>
    <div class="manga-card">
      <a href="/manga/<?= esc($manga['slug'], 'url') ?>">
        <div class="manga-cover">
          <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" width="200" height="300" <?= $_ri < 6 ? 'loading="eager"' : 'loading="lazy" decoding="async"' ?>>
          <?php if (!empty($manga['chapter_1'])): ?>
          <span class="ch-badge">Ch.<?= esc($manga['chapter_1']) ?></span>
          <?php endif; ?>
        </div>
      </a>
      <div class="manga-name"><a href="/manga/<?= esc($manga['slug'], 'url') ?>" style="color:inherit;text-decoration:none"><?= esc($manga['name']) ?></a></div>
      <div class="manga-ch">
        <?php if (!empty($manga['chap_1_slug'])): ?>
        <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug']) ?>" style="color:var(--txt3);text-decoration:none">Chapter <?= esc($manga['chapter_1'] ?? '') ?></a>
        <?php else: ?>
        <span>Chapter <?= esc($manga['chapter_1'] ?? '') ?></span>
        <?php endif; ?>
        <span class="manga-time"><?= $ago ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if (!empty($pager)): ?>
  <?= $pager->links('default', 'mangahub_full') ?>
  <?php endif; ?>
</section>

<?php if ($isFirstPage): ?>
<!-- ── LATEST COMMENTS ── -->
<?php if (!empty($recentComments)): ?>
<section>
  <div class="sec-head">
    <h2 class="sec-title">Latest Comments</h2>
  </div>
  <div class="comments-grid">
    <?php foreach (array_slice($recentComments, 0, 6) as $comment): ?>
    <?php $cAgo = mangahub_time_ago($comment['created_at'] ?? ''); ?>
    <a href="/manga/<?= esc($comment['manga_slug'], 'url') ?>/<?= esc($comment['chapter_slug'] ?? '', 'url') ?>" class="comment-card">
      <?php if (!empty($comment['manga_cover'])): ?>
      <div class="comment-thumb">
        <img src="<?= esc($comment['manga_cover']) ?>" alt="<?= esc($comment['manga_name']) ?>" width="200" height="300" loading="lazy" decoding="async">
      </div>
      <?php endif; ?>
      <div class="comment-body">
        <div class="comment-manga"><?= esc($comment['manga_name']) ?></div>
        <div class="comment-text"><?= esc(mb_strimwidth($comment['comment'] ?? '', 0, 150, '...')) ?></div>
        <div class="comment-meta">
          <span>@<?= esc($comment['user_name'] ?? $comment['user_username'] ?? 'Guest') ?></span> · <?= $cAgo ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ── POPULAR MANGA ── -->
<?php if (!empty($topAll)): ?>
<section>
  <div class="sec-head">
    <h2 class="sec-title">Popular Manga</h2>
  </div>
  <div class="popular-grid">
    <?php foreach ($topAll as $idx => $manga): ?>
    <?php
      $rank = $idx + 1;
      $rankClass = '';
      if ($rank === 1) $rankClass = 'gold';
      elseif ($rank === 2) $rankClass = 'silver';
      elseif ($rank === 3) $rankClass = 'bronze';
    ?>
    <a href="/manga/<?= esc($manga['slug'], 'url') ?>" class="pop-item">
      <div class="pop-rank <?= $rankClass ?>"><?= $rank ?></div>
      <div class="pop-thumb"><img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" width="200" height="300" loading="lazy" decoding="async"></div>
      <div class="pop-info">
        <div class="pop-title"><?= esc($manga['name']) ?></div>
        <?php if (!empty($manga['genres_text'])): ?>
        <div class="pop-genres"><?= esc($manga['genres_text']) ?></div>
        <?php endif; ?>
        <div class="pop-ch">Ch. <?= esc($manga['chapter_1'] ?? '') ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ── GENRES ── -->
<?php if (!empty($categories)): ?>
<section>
  <div class="sec-head">
    <h2 class="sec-title">Genres</h2>
  </div>
  <div class="genre-wrap">
    <?php foreach ($categories as $cat): ?>
    <a href="/search?genre=<?= esc($cat['slug'], 'url') ?>" class="genre-tag">
      <?= esc($cat['name']) ?>
      <?php if (!empty($cat['manga_count'])): ?>
      <span class="genre-cnt"><?= (int)$cat['manga_count'] ?></span>
      <?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ── POPULAR GENRES ── -->
<section>
  <div class="sec-head">
    <h2 class="sec-title">Popular Genres</h2>
  </div>
  <div class="pop-genre-wrap">
    <?php foreach (array_slice($categories, 0, 12) as $cat): ?>
    <a href="/search?genre=<?= esc($cat['slug'], 'url') ?>" class="pop-genre-link"><?= esc($cat['name']) ?></a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
<?php endif; ?>

<!-- ── Scripts ── -->
<script>
document.addEventListener('DOMContentLoaded', function() {

  /* ── 3-Card Slider (drag + swipe) ── */
  var track = document.getElementById('sliderTrack');
  var prevBtn = document.getElementById('sliderPrev');
  var nextBtn = document.getElementById('sliderNext');

  if (track && prevBtn && nextBtn) {
    var TOTAL = track.querySelectorAll('.slide-card').length;
    var INTERVAL = 4000;
    var offset = 0;
    var timer = null;
    var isDrag = false;
    var startX = 0;
    var dragDx = 0;

    function visibleCount() {
      if (window.innerWidth <= 580) return 1;
      if (window.innerWidth <= 900) return 2;
      return 3;
    }
    function stepSize() {
      return (track.parentElement.offsetWidth + 14) / visibleCount();
    }
    function clamp(n) { return Math.max(0, Math.min(n, TOTAL - visibleCount())); }

    function moveTo(n) {
      offset = clamp(n);
      track.style.transition = 'transform .45s cubic-bezier(.4,0,.2,1)';
      track.style.transform = 'translateX(-' + (offset * stepSize()) + 'px)';
      updateArrows();
    }

    function updateArrows() {
      prevBtn.style.opacity = offset <= 0 ? '0.3' : '1';
      nextBtn.style.opacity = offset >= TOTAL - visibleCount() ? '0.3' : '1';
    }

    function startAuto() {
      clearInterval(timer);
      timer = setInterval(function() {
        moveTo(offset >= TOTAL - visibleCount() ? 0 : offset + 1);
      }, INTERVAL);
    }

    prevBtn.addEventListener('click', function() { moveTo(offset - 1); startAuto(); });
    nextBtn.addEventListener('click', function() { moveTo(offset + 1); startAuto(); });

    /* Touch drag */
    function onDragStart(x) {
      isDrag = true; startX = x; dragDx = 0;
      clearInterval(timer);
      track.style.transition = 'none';
    }
    function onDragMove(x) {
      if (!isDrag) return;
      dragDx = x - startX;
      var base = offset * stepSize();
      track.style.transform = 'translateX(' + (-base + dragDx) + 'px)';
    }
    function onDragEnd() {
      if (!isDrag) return;
      isDrag = false;
      if (Math.abs(dragDx) > 50) {
        moveTo(dragDx < 0 ? offset + 1 : offset - 1);
      } else {
        moveTo(offset);
      }
      startAuto();
    }

    track.addEventListener('mousedown', function(e) { e.preventDefault(); onDragStart(e.clientX); });
    window.addEventListener('mousemove', function(e) { onDragMove(e.clientX); });
    window.addEventListener('mouseup', onDragEnd);
    track.addEventListener('touchstart', function(e) { onDragStart(e.touches[0].clientX); }, {passive: true});
    track.addEventListener('touchmove', function(e) { onDragMove(e.touches[0].clientX); }, {passive: true});
    track.addEventListener('touchend', onDragEnd);

    window.addEventListener('resize', function() { moveTo(offset); });
    updateArrows();
    startAuto();
  }

});
</script>

<?= $this->endSection() ?>
