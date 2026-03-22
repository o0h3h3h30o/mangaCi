<?= $this->extend('themes/comixx/layouts/main') ?>

<?= $this->section('content') ?>

<?php
helper('text');
$isFirstPage = empty($_GET['page']) || (int)$_GET['page'] === 1;

function comixx_time_ago($datetime) {
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
?>

<!-- Main Layout -->
<div class="container main-layout">
  <div class="content-area">

    <?php if ($isFirstPage): ?>
    <!-- Announcement Banner -->
    <div class="announcement">
      <p>• <?= lang('Comixx.announce_1') ?></p>
      <p>• <?= lang('Comixx.announce_2') ?></p>
      <p>• <?= lang('Comixx.announce_3') ?></p>
    </div>

    <!-- Share Buttons -->
    <?php $homeUrl = rtrim(site_url(), '/'); $homeTitle = site_setting('home_heading', site_setting('site_title', 'MangaCI')); ?>
    <div class="social-buttons">
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($homeUrl) ?>" target="_blank" rel="noopener" class="social-btn facebook"><i class="fab fa-facebook-f"></i> Facebook</a>
      <a href="https://twitter.com/intent/tweet?url=<?= urlencode($homeUrl) ?>&text=<?= urlencode($homeTitle) ?>" target="_blank" rel="noopener" class="social-btn discord"><i class="fab fa-x-twitter"></i> Twitter</a>
      <a href="https://t.me/share/url?url=<?= urlencode($homeUrl) ?>&text=<?= urlencode($homeTitle) ?>" target="_blank" rel="noopener" class="social-btn telegram"><i class="fab fa-telegram-plane"></i> Telegram</a>
    </div>

    <!-- Most Followed -->
    <?php if (!empty($newestManga)): ?>
    <section class="section">
      <div class="section-header">
        <h2><?= lang('Comixx.most_followed') ?></h2>
        <div class="section-nav">
          <button class="nav-arrow"><i class="fas fa-chevron-left"></i></button>
          <button class="nav-arrow"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
      <div class="carousel">
        <div class="carousel-track">
          <?php foreach (array_slice($newestManga, 0, 10) as $idx => $manga): ?>
          <a href="<?= base_url('manga/' . esc($manga['slug'])) ?>" class="popular-card" style="min-width:180px;flex-shrink:0">
            <span class="rank"><?= $idx + 1 ?></span>
            <div class="card-image">
              <?php if (!empty($manga['caution'])): ?><span class="badge-18">18+</span><?php endif; ?>
              <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
            </div>
            <div class="card-meta">
              <?php if (!empty($manga['chapter_1'])): ?>
              <span class="meta-tag">Ch. <?= esc($manga['chapter_1']) ?></span>
              <?php endif; ?>
              <?php if (!empty($manga['update_at'])): ?>
              <span class="meta-time"><?= comixx_time_ago($manga['update_at']) ?></span>
              <?php endif; ?>
            </div>
            <h3 class="card-title"><?= esc($manga['name']) ?></h3>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Latest Updates -->
    <section class="section">
      <div class="section-header">
        <h2><?= lang('Comixx.latest_updates') ?></h2>
        <div class="view-toggle">
          <button class="view-toggle-btn" data-view="list" title="<?= lang('Comixx.list_view') ?>"><i class="fas fa-list"></i></button>
          <button class="view-toggle-btn active" data-view="grid" title="<?= lang('Comixx.grid_view') ?>"><i class="fas fa-th"></i></button>
        </div>
      </div>

      <div class="results-grid" id="resultsGrid">
        <?php if (!empty($recentlyUpdated)): ?>
        <?php foreach ($recentlyUpdated as $manga): ?>
        <a href="<?= base_url('manga/' . esc($manga['slug'])) ?>" class="result-card">
          <div class="result-card-image">
            <?php if (!empty($manga['caution'])): ?><span class="badge-18">18+</span><?php endif; ?>
            <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
          </div>
          <div class="result-card-meta">
            <?php if (!empty($manga['chapter_1'])): ?>
            <span class="ch-tag">Ch. <?= esc($manga['chapter_1']) ?></span>
            <?php endif; ?>
            <?php if (!empty($manga['update_at'])): ?>
            <span><?= comixx_time_ago($manga['update_at']) ?></span>
            <?php endif; ?>
          </div>
          <div class="result-card-title"><?= esc($manga['name']) ?></div>
          <div class="result-card-detail">
            <div class="result-card-detail-title"><?= esc($manga['name']) ?></div>
            <div class="result-card-detail-desc"><?= esc(character_limiter(strip_tags($manga['summary'] ?? ''), 150)) ?></div>
            <div class="result-card-detail-time">
              <?php if (!empty($manga['chapter_1'])): ?>Ch. <?= esc($manga['chapter_1']) ?><?php endif; ?>
              <?php if (!empty($manga['chapter_1']) && !empty($manga['update_at'])): ?> · <?php endif; ?>
              <?php if (!empty($manga['update_at'])): ?><?= comixx_time_ago($manga['update_at']) ?><?php endif; ?>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="no-results"><p><?= lang('Comixx.no_results') ?></p></div>
        <?php endif; ?>
      </div>

      <?php if (!empty($pager)): ?>
      <div class="pagination-wrapper">
        <?= $pager->links('default', 'comixx_full') ?>
      </div>
      <?php endif; ?>
    </section>

  </div>

  <!-- Right Sidebar -->
  <aside class="right-sidebar">
    <!-- Latest Comments -->
    <?php if (!empty($recentComments)): ?>
    <section class="section">
      <div class="section-header">
        <h2><?= lang('Comixx.latest_comments') ?></h2>
      </div>
      <div class="comments-list">
        <?php foreach (array_slice($recentComments, 0, 5) as $comment):
          $cmtLink = '/manga/' . esc($comment['manga_slug'] ?? '', 'url');
          if (!empty($comment['chapter_slug'])) {
              $cmtLink .= '/' . esc($comment['chapter_slug'], 'url');
          }
          $cmtLabel = !empty($comment['chapter_name'])
              ? esc($comment['manga_name'] ?? '') . ' - ' . esc($comment['chapter_name'] ?? '')
              : esc($comment['manga_name'] ?? '');
        ?>
        <a href="<?= $cmtLink ?>" class="comment-item">
          <div class="comment-header">
            <span class="comment-chapter"><i class="fas fa-bookmark"></i> <?= $cmtLabel ?></span>
          </div>
          <p class="comment-text"><?= esc(mb_strimwidth($comment['comment'] ?? '', 0, 120, '...')) ?></p>
          <div class="comment-meta">
            <span class="comment-user"><?= esc($comment['user_name'] ?? $comment['user_username'] ?? 'Anonymous') ?></span>
            <span class="comment-time"><?= comixx_time_ago($comment['created_at'] ?? '') ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Popular -->
    <section class="section">
      <div class="section-header">
        <h2><?= lang('Comixx.popular') ?></h2>
        <div class="tab-buttons">
          <button class="tab-btn active" data-popular="day"><?= lang('Comixx.day') ?></button>
          <button class="tab-btn" data-popular="month"><?= lang('Comixx.month') ?></button>
          <button class="tab-btn" data-popular="all"><?= lang('Comixx.all') ?></button>
        </div>
      </div>
      <div class="popular-list" id="popularList">
        <?php
        // Pre-compute cover URLs for JS tab switching
        $addCovers = function(array $list): array {
            foreach ($list as &$m) {
                $m['cover_url'] = manga_cover_url($m);
            }
            return $list;
        };
        $popularSets = [
            'day' => $addCovers($topDay ?? []),
            'month' => $addCovers($topMonth ?? []),
            'all' => $addCovers($topAll ?? []),
        ];
        $activeSet = !empty($topDay) ? $topDay : (!empty($topMonth) ? $topMonth : ($topAll ?? []));
        ?>
        <?php foreach ($activeSet as $manga): ?>
        <a href="<?= base_url('manga/' . esc($manga['slug'])) ?>" class="sidebar-item">
          <div class="sidebar-thumb">
            <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>" loading="lazy">
          </div>
          <div class="sidebar-info">
            <?php $typeLabel = ($comictypeMap[(int)($manga['type_id'] ?? 0)] ?? 'Manga'); $typeCls = strtolower($typeLabel); ?>
            <span class="sidebar-type <?= esc($typeCls) ?>"><?= esc(strtoupper($typeLabel)) ?></span>
            <h4><?= esc($manga['name']) ?></h4>
            <div class="sidebar-meta">
              <?php if (!empty($manga['chapter_1'])): ?>
              <span>Ch. <?= esc($manga['chapter_1']) ?></span>
              <?php endif; ?>
              <?php if (!empty($manga['update_at'])): ?>
              <span><?= comixx_time_ago($manga['update_at']) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
  </aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Carousel navigation + drag scroll
  document.querySelectorAll('.carousel-track').forEach(function(track) {
    var section = track.closest('.section');
    if (!section) return;
    var arrows = section.querySelectorAll('.nav-arrow');
    if (arrows.length >= 2) {
      var getScrollAmount = function() {
        var card = track.querySelector('.popular-card');
        if (!card) return 200;
        return card.offsetWidth + 14;
      };
      arrows[0].addEventListener('click', function() {
        track.scrollBy({ left: -getScrollAmount() * 2, behavior: 'smooth' });
      });
      arrows[1].addEventListener('click', function() {
        track.scrollBy({ left: getScrollAmount() * 2, behavior: 'smooth' });
      });
    }
    // Mouse drag scroll
    var isDown = false, startX, scrollLeft;
    track.addEventListener('mousedown', function(e) {
      isDown = true; track.style.cursor = 'grabbing';
      startX = e.pageX - track.offsetLeft;
      scrollLeft = track.scrollLeft;
    });
    track.addEventListener('mouseleave', function() { isDown = false; track.style.cursor = ''; });
    track.addEventListener('mouseup', function() { isDown = false; track.style.cursor = ''; });
    track.addEventListener('mousemove', function(e) {
      if (!isDown) return;
      e.preventDefault();
      var x = e.pageX - track.offsetLeft;
      track.scrollLeft = scrollLeft - (x - startX);
    });
  });

  // Popular tabs (Day/Month/All)
  var popularData = <?= json_encode($popularSets ?? []) ?>;
  var comictypeMap = <?= json_encode($comictypeMap ?? []) ?>;
  document.querySelectorAll('[data-popular]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('[data-popular]').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      var period = btn.dataset.popular;
      var list = popularData[period] || [];
      var container = document.getElementById('popularList');
      if (!container) return;
      container.innerHTML = list.map(function(m) {
        var coverUrl = m.cover_url || '';
        var typeLabel = comictypeMap[m.type_id] || 'Manga';
        var typeCls = typeLabel.toLowerCase();
        return '<a href="/manga/' + m.slug + '" class="sidebar-item">'
          + '<div class="sidebar-thumb"><img src="' + coverUrl + '" alt="" loading="lazy"></div>'
          + '<div class="sidebar-info">'
          + '<span class="sidebar-type ' + typeCls + '">' + typeLabel.toUpperCase() + '</span>'
          + '<h4>' + m.name + '</h4>'
          + '<div class="sidebar-meta"><span>' + (m.chapter_1 ? 'Ch. ' + m.chapter_1 : '') + '</span></div>'
          + '</div></a>';
      }).join('');
    });
  });

  // View toggle
  var viewBtns = document.querySelectorAll('.view-toggle-btn');
  var resultsGrid = document.getElementById('resultsGrid');
  var savedView = localStorage.getItem('lastupdate-view');
  if (savedView && resultsGrid) {
    viewBtns.forEach(function(b) { b.classList.remove('active'); });
    viewBtns.forEach(function(b) { if (b.dataset.view === savedView) b.classList.add('active'); });
    if (savedView === 'list') {
      resultsGrid.classList.add('list-view');
    } else {
      resultsGrid.classList.remove('list-view');
    }
  }
  viewBtns.forEach(function(btn) {
    btn.addEventListener('click', function() {
      viewBtns.forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      localStorage.setItem('lastupdate-view', btn.dataset.view);
      if (btn.dataset.view === 'list') {
        resultsGrid.classList.add('list-view');
      } else {
        resultsGrid.classList.remove('list-view');
      }
    });
  });
});
</script>

<?= $this->endSection() ?>
