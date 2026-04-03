<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('content') ?>

<?php
helper('text');
$isFirstPage = empty($_GET['page']) || (int)$_GET['page'] === 1;

function manhwaread_time_ago($datetime) {
    if (empty($datetime)) return '';
    $now = new \DateTime();
    if (is_numeric($datetime)) {
        $ago = (new \DateTime())->setTimestamp((int)$datetime);
    } else {
        $ago = new \DateTime($datetime);
    }
    $diff = $now->diff($ago);

    if ($diff->y > 0) return str_replace('{n}', $diff->y, lang('ComixxTime.years_ago'));
    if ($diff->m > 0) return str_replace('{n}', $diff->m, lang('ComixxTime.months_ago'));
    if ($diff->d > 0) {
        if ($diff->d >= 7) {
            $weeks = floor($diff->d / 7);
            return str_replace('{n}', $weeks, lang('ComixxTime.weeks_ago'));
        }
        return str_replace('{n}', $diff->d, lang('ComixxTime.days_ago'));
    }
    if ($diff->h > 0) return str_replace('{n}', $diff->h, lang('ComixxTime.hours_ago'));
    if ($diff->i > 0) return str_replace('{n}', $diff->i, lang('ComixxTime.minutes_ago'));
    return lang('ComixxTime.now');
}

$statusMap = [1 => lang('ComixxManga.ongoing'), 2 => lang('ComixxManga.completed')];
?>

<?php if ($isFirstPage): ?>

<!-- Hot Today Carousel -->
<?php if (!empty($hotToday)): ?>
<section class="section">
  <div class="section-header">
    <h2><span class="fire-icon">&#x1F525;</span> <?= lang('Comixx.popular') ?> <span class="highlight"><?= lang('Comixx.day') ?></span> <span class="arrow-circle"><i class="fas fa-chevron-right"></i></span></h2>
  </div>
  <div class="carousel-container">
    <button class="carousel-btn carousel-btn-left"><i class="fas fa-chevron-left"></i></button>
    <div class="carousel-track" id="hotTodayCarousel">
      <?php foreach (array_slice($hotToday, 0, 12) as $manga): ?>
      <a href="<?= base_url('manga/' . esc($manga['slug'])) ?>" class="card">
        <div class="card-image">
          <?php if (!empty($manga['caution'])): ?><span class="badge-18">18+</span><?php endif; ?>
          <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
        </div>
        <div class="card-info">
          <h3 class="card-title"><?= esc($manga['name']) ?></h3>
          <div class="card-meta">
            <span class="rating"><i class="fas fa-star"></i> <?= number_format((float)($manga['rating'] ?? 0), 1) ?></span>
            <?php
              $sLabel = $statusMap[$manga['status_id'] ?? 0] ?? lang('ComixxManga.unknown_status');
              $sCls = ($manga['status_id'] ?? 0) == 2 ? 'completed' : 'ongoing';
            ?>
            <span class="status <?= $sCls ?>"><i class="fas fa-circle"></i> <?= $sLabel ?></span>
          </div>
          <div class="card-chapters">
            <?php if (!empty($manga['chapter_1'])): ?>
            <div class="chapter-row">
              <span>Ch. <?= esc($manga['chapter_1']) ?></span>
              <?php if (!empty($manga['update_at'])): ?>
              <span class="time"><?= manhwaread_time_ago($manga['update_at']) ?></span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <button class="carousel-btn carousel-btn-right"><i class="fas fa-chevron-right"></i></button>
  </div>
</section>
<?php endif; ?>

<!-- Newest Manga Carousel -->
<?php if (!empty($newestManga)): ?>
<section class="section">
  <div class="section-header">
    <h2><span class="fire-icon">&#x1F4D8;</span> <?= lang('Comixx.new') ?> <span class="highlight"><?= lang('Comixx.manga') ?></span> <span class="arrow-circle"><i class="fas fa-chevron-right"></i></span></h2>
  </div>
  <div class="carousel-container">
    <button class="carousel-btn carousel-btn-left"><i class="fas fa-chevron-left"></i></button>
    <div class="carousel-track" id="newestCarousel">
      <?php foreach (array_slice($newestManga, 0, 12) as $manga): ?>
      <a href="<?= base_url('manga/' . esc($manga['slug'])) ?>" class="card">
        <div class="card-image">
          <?php if (!empty($manga['caution'])): ?><span class="badge-18">18+</span><?php endif; ?>
          <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
        </div>
        <div class="card-info">
          <h3 class="card-title"><?= esc($manga['name']) ?></h3>
          <div class="card-meta">
            <span class="rating"><i class="fas fa-star"></i> <?= number_format((float)($manga['rating'] ?? 0), 1) ?></span>
            <?php
              $sLabel = $statusMap[$manga['status_id'] ?? 0] ?? lang('ComixxManga.unknown_status');
              $sCls = ($manga['status_id'] ?? 0) == 2 ? 'completed' : 'ongoing';
            ?>
            <span class="status <?= $sCls ?>"><i class="fas fa-circle"></i> <?= $sLabel ?></span>
          </div>
          <div class="card-chapters">
            <?php if (!empty($manga['chapter_1'])): ?>
            <div class="chapter-row">
              <span>Ch. <?= esc($manga['chapter_1']) ?></span>
              <?php if (!empty($manga['update_at'])): ?>
              <span class="time"><?= manhwaread_time_ago($manga['update_at']) ?></span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <button class="carousel-btn carousel-btn-right"><i class="fas fa-chevron-right"></i></button>
  </div>
</section>
<?php endif; ?>

<?php endif; /* isFirstPage */ ?>

<!-- Latest Release Grid -->
<section class="section">
  <div class="section-header">
    <h2><span class="fire-icon">&#x26A1;</span> <?= lang('Comixx.latest_updates') ?> <span class="highlight-red"><?= lang('Comixx.release') ?></span> <span class="arrow-circle"><i class="fas fa-chevron-right"></i></span></h2>
  </div>
  <div class="grid-container" id="latestReleaseGrid">
    <?php if (!empty($recentlyUpdated)): ?>
    <?php foreach ($recentlyUpdated as $manga): ?>
    <a href="<?= base_url('manga/' . esc($manga['slug'])) ?>" class="card">
      <div class="card-image">
        <?php if (!empty($manga['caution'])): ?><span class="badge-18">18+</span><?php endif; ?>
        <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
      </div>
      <div class="card-info">
        <h3 class="card-title"><?= esc($manga['name']) ?></h3>
        <div class="card-meta">
          <span class="rating"><i class="fas fa-star"></i> <?= number_format((float)($manga['rating'] ?? 0), 1) ?></span>
          <?php
            $sLabel = $statusMap[$manga['status_id'] ?? 0] ?? lang('ComixxManga.unknown_status');
            $sCls = ($manga['status_id'] ?? 0) == 2 ? 'completed' : 'ongoing';
          ?>
          <span class="status <?= $sCls ?>"><i class="fas fa-circle"></i> <?= $sLabel ?></span>
        </div>
        <div class="card-chapters">
          <?php if (!empty($manga['chapter_1'])): ?>
          <div class="chapter-row">
            <span>Ch. <?= esc($manga['chapter_1']) ?></span>
            <?php if (!empty($manga['update_at'])): ?>
            <span class="time"><?= manhwaread_time_ago($manga['update_at']) ?></span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="no-results"><p><?= lang('Comixx.no_results') ?></p></div>
    <?php endif; ?>
  </div>

  <?php if (!empty($pager)): ?>
    <?= $pager->links('default', 'manhwaread') ?>
  <?php endif; ?>
</section>

<!-- Latest Comments - temporarily hidden -->
<?php /* if (!empty($recentComments)): ?>
<section class="section latest-comments-section">
  ...
</section>
<?php endif; */ ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Carousel scroll buttons
  document.querySelectorAll('.carousel-btn-left').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var track = btn.parentElement.querySelector('.carousel-track');
      var card = track.querySelector('.card');
      var cardW = card ? card.offsetWidth + 14 : 300;
      track.scrollBy({ left: -cardW * 2, behavior: 'smooth' });
    });
  });
  document.querySelectorAll('.carousel-btn-right').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var track = btn.parentElement.querySelector('.carousel-track');
      var card = track.querySelector('.card');
      var cardW = card ? card.offsetWidth + 14 : 300;
      track.scrollBy({ left: cardW * 2, behavior: 'smooth' });
    });
  });

  // Carousel drag-to-scroll (mouse + touch)
  document.querySelectorAll('.carousel-track').forEach(function(track) {
    var isDown = false, startX, scrollLeft, dragged = false;

    function onStart(x) {
      isDown = true; dragged = false;
      startX = x;
      scrollLeft = track.scrollLeft;
      track.classList.add('dragging');
    }
    function onMove(x, e) {
      if (!isDown) return;
      var diff = x - startX;
      if (Math.abs(diff) > 5) { dragged = true; if (e.cancelable) e.preventDefault(); }
      track.scrollLeft = scrollLeft - diff;
    }
    function onEnd() {
      isDown = false;
      track.classList.remove('dragging');
    }

    // Mouse events
    track.addEventListener('mousedown', function(e) { onStart(e.pageX); });
    track.addEventListener('mousemove', function(e) { onMove(e.pageX, e); });
    track.addEventListener('mouseup', onEnd);
    track.addEventListener('mouseleave', onEnd);

    // Touch events
    track.addEventListener('touchstart', function(e) { onStart(e.touches[0].pageX); }, { passive: true });
    track.addEventListener('touchmove', function(e) { onMove(e.touches[0].pageX, e); }, { passive: false });
    track.addEventListener('touchend', onEnd);

    // Block click only if user dragged
    track.addEventListener('click', function(e) {
      if (dragged) { e.preventDefault(); e.stopPropagation(); }
    }, true);
  });
});
</script>

<?= $this->endSection() ?>
