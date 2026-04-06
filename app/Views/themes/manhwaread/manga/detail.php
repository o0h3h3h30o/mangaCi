<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('content') ?>

<?php
helper('text');
$statusMap = [1 => lang('ComixxManga.ongoing'), 2 => lang('ComixxManga.completed')];
$statusLabel = $statusMap[$manga['status_id'] ?? 0] ?? lang('ComixxManga.unknown_status');
$statusCls = ($manga['status_id'] ?? 0) == 2 ? 'completed' : 'ongoing';

$comicTypeLabel = 'Manga';
if (!empty($manga['type_id'])) {
    try {
        $ctRow = \Config\Database::connect()->table('comictype')->where('id', (int)$manga['type_id'])->get()->getRowArray();
        if ($ctRow) $comicTypeLabel = $ctRow['label'] ?? $ctRow['name'] ?? 'Manga';
    } catch (\Throwable $e) {}
}

$firstChapterLink = '#';
$firstChapterLabel = '';
$lastChapterLink = '#';
$lastChapterLabel = '';
if (!empty($chapters)) {
    $lastCh = end($chapters);
    $firstCh = reset($chapters);
    $firstChapterLink = '/manga/' . esc($manga['slug']) . '/' . esc($lastCh['slug']);
    $firstChapterLabel = $lastCh['name'] ?? 'Chapter ' . $lastCh['number'];
    $lastChapterLink = '/manga/' . esc($manga['slug']) . '/' . esc($firstCh['slug']);
    $lastChapterLabel = $firstCh['name'] ?? 'Chapter ' . $firstCh['number'];
}

function manhwaread_detail_time_ago($datetime) {
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

<!-- Detail Hero -->
<section class="detail-hero">
  <div class="detail-cover">
    <img src="<?= manga_cover_url($manga, '', true) ?>" alt="<?= esc($manga['name']) ?>">
  </div>
  <div class="detail-info">
    <div class="detail-info-top">
      <div>
        <h1 class="detail-title"><?= esc($manga['name']) ?></h1>
        <?php if (!empty($manga['otherNames'])): ?>
        <p class="detail-alt-names"><?= esc($manga['otherNames']) ?></p>
        <?php endif; ?>
        <p class="detail-publication">
          <i class="fas fa-circle" style="color: var(--color-<?= $statusCls ?>); font-size: 8px;"></i>
          <?= esc(strtoupper($comicTypeLabel)) ?> &middot; <?= esc($statusLabel) ?>
        </p>
      </div>
      <div class="detail-actions-top">
        <button class="detail-icon-btn<?= !empty($isBookmarked) ? ' active' : '' ?>" id="bookmarkBtn" data-manga-id="<?= esc($manga['id']) ?>">
          <i class="<?= !empty($isBookmarked) ? 'fas' : 'far' ?> fa-bookmark"></i>
        </button>
        <button class="detail-icon-btn"><i class="fas fa-flag"></i></button>
      </div>
    </div>

    <!-- Rating + Like/Dislike row -->
    <div style="display:flex;gap:8px;margin:12px 0;">
      <div style="flex:1;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;padding:12px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
        <span class="detailScore" style="font-size:28px;font-weight:700;color:var(--text-primary);line-height:1;"><?= number_format((float)$ratingAvg, 1) ?></span>
        <div class="detail-stars" id="ratingStars" style="display:flex;gap:2px;margin:6px 0;cursor:pointer;">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="<?= $i <= round($ratingAvg) ? 'fas' : 'far' ?> fa-star" data-rating="<?= $i ?>" style="font-size:16px;color:#f59e0b;"></i>
          <?php endfor; ?>
        </div>
        <span style="font-size:11px;color:var(--text-secondary);"><span class="detailVotes"><?= (int)$ratingVotes ?></span> votes</span>
      </div>
      <div id="mangaLikeRow" style="flex:1;display:flex;flex-direction:column;gap:8px;">
        <button class="detail-like-btn<?= ($myReaction ?? '') === 'like' ? ' active' : '' ?>" id="mangaLikeBtn" data-type="like" style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;font-size:15px;font-weight:600;color:var(--text-primary);cursor:pointer;transition:border-color .2s;"><span style="font-size:22px;">&#x1F60D;</span><span id="mangaLikeCount"><?= (int)($likes ?? 0) ?></span></button>
        <button class="detail-like-btn<?= ($myReaction ?? '') === 'dislike' ? ' active' : '' ?>" id="mangaDislikeBtn" data-type="dislike" style="flex:1;display:flex;align-items:center;justify-content:center;gap:8px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:10px;font-size:15px;font-weight:600;color:var(--text-primary);cursor:pointer;transition:border-color .2s;"><span style="font-size:22px;">&#x1F624;</span><span id="mangaDislikeCount"><?= (int)($dislikes ?? 0) ?></span></button>
      </div>
    </div>

    <!-- Read buttons -->
    <div class="detail-buttons" style="display:flex;gap:8px;">
      <a href="<?= $firstChapterLink ?>" class="btn-read-first">
        <i class="fas fa-book-open"></i> <?= lang('ComixxManga.start_reading') ?>
      </a>
      <a href="<?= $lastChapterLink ?>" class="btn-read-last">
        <i class="fas fa-book-open"></i> <?= lang('ComixxManga.read_last') ?>
      </a>
    </div>

    <!-- Synopsis -->
    <div class="detail-synopsis">
      <p id="synopsisText" class="synopsis-text collapsed"><?= $manga['summary'] ?? '' ?></p>
      <button class="synopsis-toggle" id="synopsisToggle"><i class="fas fa-chevron-down"></i></button>
    </div>

    <!-- Genres -->
    <?php if (!empty($mangaCats)): ?>
    <div class="detail-genres">
      <?php foreach ($mangaCats as $cat): ?>
      <a href="/search?genre=<?= esc($cat->slug ?? $cat['slug'] ?? '', 'url') ?>" class="genre-tag"><?= esc($cat->name ?? $cat['name'] ?? '') ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Detail Body: Meta + Chapters -->
<section class="detail-body">
  <!-- Left: Meta Info -->
  <aside class="detail-meta">
    <div class="meta-group">
      <span class="meta-label"><i class="fas fa-eye"></i> <?= lang('Comixx.views') ?>:</span>
      <span class="meta-value"><?= number_format($manga['views'] ?? 0) ?></span>
    </div>
    <div class="meta-group">
      <span class="meta-label"><i class="fas fa-bookmark"></i> <?= lang('ComixxManga.followers') ?>:</span>
      <span class="meta-value detailFollowCount"><?= number_format($followCount ?? 0) ?></span>
    </div>

    <?php if (!empty($authors)): ?>
    <div class="meta-group">
      <h4 class="meta-label"><i class="fas fa-pen-nib"></i> <?= lang('ComixxManga.authors') ?>:</h4>
      <div class="meta-tags">
        <?php foreach ($authors as $a): ?>
        <a href="/search?author=<?= esc($a['slug'] ?? '', 'url') ?>" class="meta-tag"><?= esc($a['name'] ?? '') ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($artists)): ?>
    <div class="meta-group">
      <h4 class="meta-label"><i class="fas fa-paint-brush"></i> <?= lang('ComixxManga.artists') ?>:</h4>
      <div class="meta-tags">
        <?php foreach ($artists as $a): ?>
        <a href="/search?artist=<?= esc($a['slug'] ?? '', 'url') ?>" class="meta-tag"><?= esc($a['name'] ?? '') ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($mangaCats)): ?>
    <div class="meta-group">
      <h4 class="meta-label"><i class="fas fa-tags"></i> <?= lang('Comixx.genres') ?>:</h4>
      <div class="meta-tags">
        <?php foreach ($mangaCats as $cat): ?>
        <a href="/search?genre=<?= esc($cat->slug ?? $cat['slug'] ?? '', 'url') ?>" class="meta-tag"><?= esc($cat->name ?? $cat['name'] ?? '') ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($mangaTags)): ?>
    <div class="meta-group">
      <h4 class="meta-label"><i class="fas fa-tag"></i> Tags:</h4>
      <div class="meta-tags">
        <?php foreach ($mangaTags as $tag): ?>
        <a href="/search?tag=<?= esc($tag['slug'] ?? '', 'url') ?>" class="meta-tag"><?= esc($tag['name'] ?? '') ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($manga['created_at'])): ?>
    <div class="meta-group">
      <span class="meta-label"><i class="fas fa-calendar-plus"></i> <?= lang('ComixxManga.date') ?>:</span>
      <span class="meta-value"><?= date('d/m/Y', strtotime($manga['created_at'])) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($manga['update_at'])): ?>
    <div class="meta-group">
      <span class="meta-label"><i class="fas fa-calendar-check"></i> <?= lang('ComixxManga.updated') ?>:</span>
      <span class="meta-value"><?= date('d/m/Y H:i', is_numeric($manga['update_at']) ? $manga['update_at'] : strtotime($manga['update_at'])) ?></span>
    </div>
    <?php endif; ?>

  </aside>

  <!-- Right: Chapters -->
  <div class="detail-chapters">
    <div class="chapters-header">
      <h2 class="chapters-title"><?= lang('ComixxManga.chapter') ?> (<?= count($chapters) ?>)</h2>
      <div class="chapters-controls">
        <div class="chapters-search">
          <i class="fas fa-search"></i>
          <input type="text" id="chapterSearchInput" placeholder="<?= lang('ComixxManga.go_to_chap') ?>">
        </div>
        <button class="btn-reverse" id="reverseOrderBtn"><i class="fas fa-sort-amount-down"></i> <?= lang('ComixxManga.reverse_order') ?></button>
      </div>
    </div>
    <div class="chapters-list" id="chaptersList">
      <?php if (!empty($chapters)): ?>
        <?php foreach ($chapters as $ch): ?>
        <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($ch['slug']) ?>" class="chapter-item" data-chapter-number="<?= esc($ch['number']) ?>">
          <span>Ch. <?= esc($ch['number']) ?><?= !empty($ch['title']) ? ' - ' . esc($ch['title']) : '' ?></span>
          <span class="chapter-date"><?= !empty($ch['created_at']) ? date('d/m/Y', strtotime($ch['created_at'])) : '' ?></span>
        </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="chapter-item"><span><?= lang('ComixxManga.no_chapters') ?></span></div>
      <?php endif; ?>
    </div>
    <button class="btn-show-more" id="showMoreBtn" style="display:none"><i class="fas fa-chevron-down"></i> <?= lang('ComixxManga.show_more') ?></button>
  </div>
</section>

<!-- More Like This / Recommendations -->
<?php if (!empty($recommended)): ?>
<section class="section">
  <div class="section-header" style="text-align: center;">
    <h2 style="justify-content: center;"><?= lang('ComixxManga.recommendations') ?></h2>
  </div>
  <div class="carousel-container">
    <button class="carousel-btn carousel-btn-left"><i class="fas fa-chevron-left"></i></button>
    <div class="carousel-track">
      <?php foreach ($recommended as $rec): ?>
      <a href="/manga/<?= esc($rec['slug']) ?>" class="card">
        <div class="card-image">
          <?php if (!empty($rec['caution'])): ?><span class="badge-18">18+</span><?php endif; ?>
          <img src="<?= manga_cover_url($rec) ?>" alt="<?= esc($rec['name']) ?>" loading="lazy">
        </div>
        <div class="card-info">
          <h3 class="card-title"><?= esc($rec['name']) ?></h3>
          <div class="card-meta">
            <span class="rating"><i class="fas fa-star"></i> <?= number_format((float)($rec['rating_avg'] ?? 0), 1) ?></span>
            <?php
              $rSLabel = $statusMap[$rec['status_id'] ?? 0] ?? lang('ComixxManga.unknown_status');
              $rSCls = ($rec['status_id'] ?? 0) == 2 ? 'completed' : 'ongoing';
            ?>
            <span class="status <?= $rSCls ?>"><i class="fas fa-circle"></i> <?= $rSLabel ?></span>
          </div>
          <div class="card-chapters">
            <?php if (!empty($rec['chapter_1'])): ?>
            <div class="chapter-row">
              <span>Ch. <?= esc($rec['chapter_1']) ?></span>
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

<!-- Comments Section -->
<section class="comments-section" id="dc-section">
  <h2 class="comments-title"><?= lang('ComixxManga.comments') ?> <span id="dc-count"></span></h2>
  <div class="comments-box">
    <div class="comments-header">
      <div class="comments-sort">
        <div class="tab-buttons">
          <button class="tab-btn active" data-dc-order="newest"><?= lang('ComixxManga.new') ?></button>
          <button class="tab-btn" data-dc-order="oldest"><?= lang('ComixxManga.older') ?></button>
          <button class="tab-btn" data-dc-order="top"><?= lang('ComixxManga.top') ?></button>
        </div>
      </div>
    </div>

    <?php if (!empty($currentUser)): ?>
    <form id="dc-form" class="comment-input-form">
      <div class="comment-input">
        <div class="comment-avatar"><i class="fas fa-user"></i></div>
        <textarea id="dc-input" rows="2" maxlength="1000" placeholder="<?= lang('ComixxManga.write_comment') ?>"></textarea>
      </div>
      <div id="dc-captcha-box" class="dc-captcha" style="display:none">
        <p class="dc-captcha-label"><?= lang('ComixxManga.captcha_label') ?></p>
        <div class="dc-captcha-row">
          <span id="dc-captcha-q"></span>
          <span>= ?</span>
          <input id="dc-captcha-ans" type="number" min="0" max="99" placeholder="0">
        </div>
      </div>
      <div class="comment-form-footer">
        <span id="dc-char" class="dc-char-count">0 / 1000</span>
        <button type="submit" class="dc-submit-btn"><?= lang('ComixxManga.post_comment') ?></button>
      </div>
    </form>
    <?php else: ?>
    <p class="comment-login-prompt">
      <a href="/login"><?= lang('ComixxManga.login_to_comment') ?></a>
    </p>
    <?php endif; ?>

    <div id="dc-list" class="comments-list">
      <p class="dc-loading"><?= lang('Comixx.loading') ?></p>
    </div>
    <div id="dc-pg" class="dc-pagination"></div>
  </div>
</section>

<!-- Synopsis Toggle JS -->
<script>
(function(){
  var text = document.getElementById('synopsisText');
  var btn = document.getElementById('synopsisToggle');
  if (!text || !btn) return;
  btn.addEventListener('click', function() {
    text.classList.toggle('collapsed');
    var icon = btn.querySelector('i');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
  });
})();
</script>

<!-- Bookmark JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var bookmarkBtn = document.getElementById('bookmarkBtn');
  if (!bookmarkBtn) return;
  var mangaId = bookmarkBtn.getAttribute('data-manga-id');

  bookmarkBtn.addEventListener('click', function() {
    fetch('/api/bookmark/toggle', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
      body: 'manga_id=' + mangaId,
      credentials: 'same-origin'
    })
    .then(function(r) {
      if (r.status === 401) { window.location.href = '/login'; return null; }
      return r.json();
    })
    .then(function(data) {
      if (!data) return;
      if (data.error) { alert(data.error); return; }
      var icon = bookmarkBtn.querySelector('i');
      if (data.bookmarked) {
        icon.className = 'fas fa-bookmark';
        bookmarkBtn.classList.add('active');
      } else {
        icon.className = 'far fa-bookmark';
        bookmarkBtn.classList.remove('active');
      }
      if (typeof reloadMangaState === 'function') reloadMangaState();
    })
    .catch(function(err) { console.error('Bookmark error:', err); });
  });
});
</script>

<!-- Like/Dislike JS -->
<script>
(function(){
  var mangaId = <?= (int)($manga['id'] ?? 0) ?>;
  var likeBtn = document.getElementById('mangaLikeBtn');
  var dislikeBtn = document.getElementById('mangaDislikeBtn');
  var likeCount = document.getElementById('mangaLikeCount');
  var dislikeCount = document.getElementById('mangaDislikeCount');
  if (!likeBtn || !dislikeBtn) return;

  function updateUI(data) {
    likeCount.textContent = data.likes;
    dislikeCount.textContent = data.dislikes;
    if (data.my_reaction === 'like') likeBtn.classList.add('active');
    else likeBtn.classList.remove('active');
    if (data.my_reaction === 'dislike') dislikeBtn.classList.add('active');
    else dislikeBtn.classList.remove('active');
  }

  // Initial state loaded from PHP

  function toggle(type) {
    fetch('/api/content-like', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
      body: 'content_type=manga&content_id=' + mangaId + '&type=' + type,
      credentials: 'same-origin'
    })
    .then(function(r) {
      if (r.status === 401) { window.location.href = '/login'; return null; }
      return r.json();
    })
    .then(function(d) { if (d) updateUI(d); });
  }

  likeBtn.addEventListener('click', function() { toggle('like'); });
  dislikeBtn.addEventListener('click', function() { toggle('dislike'); });
})();
</script>

<!-- Rating JS + State Hydration -->
<script>
(function(){
  var mangaId = <?= (int)($manga['id'] ?? 0) ?>;
  var currentRating = <?= (int)$myRating ?>;

  // Update score/votes in UI after rating
  function reloadMangaState() {
    fetch('/api/manga/' + mangaId + '/state')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.rating_avg !== undefined) {
          document.querySelectorAll('.detailScore').forEach(function(el) { el.textContent = parseFloat(data.rating_avg).toFixed(1); });
        }
        if (data.rating_votes !== undefined) {
          document.querySelectorAll('.detailVotes').forEach(function(el) { el.textContent = data.rating_votes; });
        }
        if (data.follow_count !== undefined) {
          document.querySelectorAll('.detailFollowCount').forEach(function(el) { el.textContent = data.follow_count; });
        }
      })
      .catch(function(){});
  }
  window.reloadMangaState = reloadMangaState;

  function submitRating(rating, starsEls) {
    fetch('/api/rating', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
      body: 'item_id=' + mangaId + '&score=' + rating,
      credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.error) { alert(data.error); return; }
      starsEls.forEach(function(s, idx) {
        s.className = (idx < rating) ? 'fas fa-star' : 'far fa-star';
      });
      currentRating = rating;
      reloadMangaState();
    })
    .catch(function(err) { console.error('Rating error:', err); });
  }

  function highlightStars(starsEls, n) {
    starsEls.forEach(function(s, idx) {
      s.className = (idx < n) ? 'fas fa-star' : 'far fa-star';
    });
  }

  function setupStars(container) {
    if (!container) return;
    var strs = Array.from(container.querySelectorAll('i[data-rating]'));
    if (!strs.length) return;
    // Set initial highlight
    highlightStars(strs, currentRating);
    strs.forEach(function(star) {
      star.style.cursor = 'pointer';
      star.addEventListener('click', function() {
        submitRating(parseInt(this.getAttribute('data-rating')), strs);
      });
      star.addEventListener('mouseenter', function() {
        highlightStars(strs, parseInt(this.getAttribute('data-rating')));
      });
    });
    container.addEventListener('mouseleave', function() {
      highlightStars(strs, currentRating);
    });
  }

  setupStars(document.getElementById('ratingStars'));
})();
</script>

<!-- Chapter List JS: search, sort, show more -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  var SHOW_INITIAL = 20;
  var allItems = Array.from(document.querySelectorAll('.chapter-item'));
  var searchInput = document.getElementById('chapterSearchInput');
  var showMoreBtn = document.getElementById('showMoreBtn');
  var reverseBtn = document.getElementById('reverseOrderBtn');
  var listEl = document.getElementById('chaptersList');
  var isReversed = false;
  var showAll = false;

  function render() {
    var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
    var filtered = allItems;
    if (query) {
      filtered = allItems.filter(function(el) {
        return el.textContent.toLowerCase().includes(query);
      });
    }

    // Order
    var ordered = filtered.slice();
    if (isReversed) ordered.reverse();

    // Clear + append
    while (listEl.firstChild) listEl.removeChild(listEl.firstChild);
    var limit = (showAll || query) ? ordered.length : Math.min(SHOW_INITIAL, ordered.length);
    for (var i = 0; i < ordered.length; i++) {
      ordered[i].style.display = i < limit ? '' : 'none';
      listEl.appendChild(ordered[i]);
    }

    if (showMoreBtn) {
      showMoreBtn.style.display = (!query && !showAll && ordered.length > SHOW_INITIAL) ? '' : 'none';
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', function() { render(); });
  }

  if (reverseBtn) {
    reverseBtn.addEventListener('click', function() {
      isReversed = !isReversed;
      render();
    });
  }

  if (showMoreBtn) {
    showMoreBtn.addEventListener('click', function() {
      showAll = true;
      render();
    });
  }

  render();
});
</script>

<!-- Carousel JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
  document.querySelectorAll('.carousel-track').forEach(function(track) {
    var isDown = false, isDragging = false, startX, scrollLeft;
    track.addEventListener('mousedown', function(e) {
      isDown = true; isDragging = false;
      startX = e.pageX - track.offsetLeft; scrollLeft = track.scrollLeft;
    });
    track.addEventListener('mouseleave', function() { isDown = false; isDragging = false; track.classList.remove('dragging'); });
    track.addEventListener('mouseup', function() { isDown = false; setTimeout(function(){ isDragging = false; track.classList.remove('dragging'); }, 10); });
    track.addEventListener('mousemove', function(e) {
      if (!isDown) return;
      var dx = Math.abs(e.pageX - track.offsetLeft - startX);
      if (dx > 5) { isDragging = true; track.classList.add('dragging'); }
      if (!isDragging) return;
      e.preventDefault();
      track.scrollLeft = scrollLeft - (e.pageX - track.offsetLeft - startX) * 1.5;
    });
    track.addEventListener('click', function(e) { if (isDragging) e.preventDefault(); }, true);
  });
});
</script>

<!-- Comment System JS -->
<script>
(function(){
  var mangaId = <?= (int)($manga['id'] ?? 0) ?>;
  var mangaSlug = <?= json_encode($manga['slug']) ?>;
  var CURRENT_UID = <?= !empty($currentUser) ? (int)$currentUser['id'] : 0 ?>;
  var BG_COLORS = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];
  var currentOrder = 'newest';
  var currentPage = 1;

  var __lang = {
    reply: <?= json_encode(lang('Comixx.reply')) ?>,
    cancel: <?= json_encode(lang('Comixx.cancel')) ?>,
    send: <?= json_encode(lang('Comixx.send')) ?>,
    no_comments: <?= json_encode(lang('ComixxManga.no_comments')) ?>,
    login_to_comment: <?= json_encode(lang('ComixxManga.login_to_comment')) ?>,
    now: <?= json_encode(lang('ComixxTime.now')) ?>,
    js_min: <?= json_encode(lang('ComixxTime.js_min')) ?>,
    js_hour: <?= json_encode(lang('ComixxTime.js_hour')) ?>,
    js_day: <?= json_encode(lang('ComixxTime.js_day')) ?>,
    view_replies: <?= json_encode(lang('ComixxManga.view_replies')) ?>,
    hide_replies: <?= json_encode(lang('ComixxManga.hide_replies')) ?>,
    show_more_replies: <?= json_encode(lang('ComixxManga.show_more_replies')) ?>
  };

  function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function timeAgo(str) {
    var d = new Date(str.replace(' ','T'));
    var diff = Math.floor((Date.now() - d.getTime()) / 1000);
    if (diff < 60) return __lang.now;
    if (diff < 3600) return __lang.js_min.replace('{n}', Math.floor(diff/60));
    if (diff < 86400) return __lang.js_hour.replace('{n}', Math.floor(diff/3600));
    if (diff < 604800) return __lang.js_day.replace('{n}', Math.floor(diff/86400));
    return Math.floor(diff/604800) + 'w';
  }

  function avatar(name, uid, sz) {
    sz = sz || 32;
    var ch = ((name||'?')[0]).toUpperCase();
    var bg = BG_COLORS[parseInt(uid||0) % 6];
    return '<div class="comment-avatar-img" style="background:'+bg+';width:'+sz+'px;height:'+sz+'px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:'+(sz*0.4)+'px;color:#fff;font-weight:700;flex-shrink:0">'+ch+'</div>';
  }

  function renderComment(c) {
    var name = c.user_name || c.user_username || '?';
    var likeClass = c.my_reaction === 'like' ? ' active' : '';
    var chapLabel = c.chapter_name && c.chapter_slug ? '<a href="/manga/'+mangaSlug+'/'+escHtml(c.chapter_slug)+'" style="font-size:11px;color:var(--accent,#a855f7);font-weight:600;margin-left:6px;text-decoration:none;" onclick="event.stopPropagation()">'+escHtml(c.chapter_name)+'</a>' : '';
    return '<div class="comment" data-id="'+c.id+'">'
      + '<div class="comment-body-wrap">'
      + avatar(name, c.user_id)
      + '<div class="comment-body">'
      + '<div class="comment-header"><span class="comment-author">'+escHtml(name)+'</span>'+chapLabel
      + '<span class="comment-time"><i class="fas fa-clock"></i> '+timeAgo(c.created_at)+'</span></div>'
      + '<p class="comment-text">'+escHtml(c.comment)+'</p>'
      + '<div class="comment-actions">'
      + '<span class="comment-like-btn'+likeClass+'" data-id="'+c.id+'"><i class="fas fa-thumbs-up"></i> <span class="lc">'+c.likes_count+'</span></span>'
      + '<span>&mdash;</span>'
      + (CURRENT_UID > 0 ? '<span class="comment-reply-btn" data-id="'+c.id+'" data-name="'+escHtml(name)+'"><i class="fas fa-reply"></i> '+__lang.reply+'</span>' : '')
      + '</div>'
      + (c.reply_count > 0 ? '<button class="dc-toggle-replies" data-id="'+c.id+'" data-count="'+c.reply_count+'">'+__lang.view_replies.replace('{n}', ' ' + c.reply_count + ' ')+'</button>' : '')
      + '<div class="dc-replies" id="dc-replies-'+c.id+'"></div>'
      + '</div></div></div>';
  }

  function loadComments(page, order) {
    var listEl = document.getElementById('dc-list');
    var pgEl = document.getElementById('dc-pg');
    listEl.style.minHeight = listEl.offsetHeight + 'px';
    listEl.style.pointerEvents = 'none';
    listEl.style.opacity = '0.5';
    listEl.style.transition = 'opacity 0.15s';

    fetch('/api/comments/manga/' + mangaId + '/all?order=' + order + '&page=' + page)
      .then(function(r) { return r.json(); })
      .then(function(d) {
        var countEl = document.getElementById('dc-count');
        if (countEl) countEl.textContent = '(' + (d.total || 0) + ')';

        if (!d.comments || !d.comments.length) {
          listEl.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:20px 0">' + __lang.no_comments + '</p>';
          pgEl.innerHTML = '';
          listEl.style.minHeight = ''; listEl.style.opacity = '1'; listEl.style.pointerEvents = '';
          return;
        }
        listEl.innerHTML = d.comments.map(renderComment).join('');
        listEl.style.minHeight = ''; listEl.style.opacity = '1'; listEl.style.pointerEvents = '';
        // pagination
        var totalPages = d.last_page || 1;
        if (totalPages <= 1) { pgEl.innerHTML = ''; return; }
        var html = '';
        html += '<button class="ch-page-btn' + (page <= 1 ? ' disabled' : '') + '" data-page="' + (page-1) + '"' + (page<=1?' disabled':'') + '><i class="fas fa-chevron-left"></i></button>';
        for (var i = 1; i <= totalPages; i++) {
          html += '<button class="ch-page-btn' + (i===page?' active':'') + '" data-page="'+i+'">'+i+'</button>';
        }
        html += '<button class="ch-page-btn' + (page >= totalPages ? ' disabled' : '') + '" data-page="' + (page+1) + '"' + (page>=totalPages?' disabled':'') + '><i class="fas fa-chevron-right"></i></button>';
        pgEl.innerHTML = html;
        pgEl.querySelectorAll('.ch-page-btn:not(.disabled)').forEach(function(b) {
          b.addEventListener('click', function() {
            currentPage = parseInt(this.dataset.page);
            loadComments(currentPage, currentOrder);
          });
        });
              })
      .catch(function() { listEl.innerHTML = ''; listEl.style.minHeight = ''; listEl.style.opacity = '1'; listEl.style.pointerEvents = ''; });
  }

  // Tab switching
  document.querySelectorAll('[data-dc-order]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('[data-dc-order]').forEach(function(b) { b.classList.remove('active'); });
      btn.classList.add('active');
      currentOrder = btn.dataset.dcOrder;
      currentPage = 1;
      loadComments(currentPage, currentOrder);
    });
  });

  // Comment form
  var form = document.getElementById('dc-form');
  var input = document.getElementById('dc-input');
  var charCount = document.getElementById('dc-char');
  var captchaBox = document.getElementById('dc-captcha-box');
  var captchaQ = document.getElementById('dc-captcha-q');
  var captchaAns = document.getElementById('dc-captcha-ans');
  var captchaA, captchaB;

  if (input && charCount) {
    input.addEventListener('input', function() {
      charCount.textContent = input.value.length + ' / 1000';
    });
  }

  var captchaShown = false;

  function newCaptcha() {
    captchaA = Math.floor(Math.random() * 10) + 1;
    captchaB = Math.floor(Math.random() * 10) + 1;
    if (captchaQ) captchaQ.textContent = captchaA + ' + ' + captchaB;
    if (captchaAns) captchaAns.value = '';
  }

  function showCaptcha() {
    if (captchaBox) {
      captchaBox.style.display = '';
      captchaBox.scrollIntoView({behavior:'smooth',block:'center'});
      if (captchaAns) captchaAns.focus();
    }
    captchaShown = true;
  }

  if (form) {
    newCaptcha();
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      if (!input || !input.value.trim()) return;
      if (captchaShown && captchaAns) {
        if (parseInt(captchaAns.value) !== captchaA + captchaB) {
          captchaAns.style.borderColor = 'red';
          captchaAns.focus();
          return;
        }
      }
      var body = 'manga_id=' + mangaId + '&comment=' + encodeURIComponent(input.value.trim());
      if (captchaShown) body += '&captcha_passed=1';
      fetch('/api/comments', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: body,
        credentials: 'same-origin'
      })
      .then(function(r) {
        if (r.status === 401) { window.location.href = '/login'; return null; }
        return r.json().then(function(d) { d._status = r.status; return d; });
      })
      .then(function(d) {
        if (!d) return;
        if (d.need_captcha) {
          showCaptcha();
          newCaptcha();
          return;
        }
        input.value = '';
        if (charCount) charCount.textContent = '0 / 1000';
        captchaShown = false;
        if (captchaBox) captchaBox.style.display = 'none';
        newCaptcha();
        loadComments(1, currentOrder);
      });
    });
  }

  // Delegated events: like, reply, toggle replies
  document.getElementById('dc-list').addEventListener('click', function(e) {
    // Like
    var likeEl = e.target.closest('.comment-like-btn');
    if (likeEl) {
      var cid = likeEl.dataset.id;
      fetch('/api/comments/' + cid + '/react', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'type=like',
        credentials: 'same-origin'
      })
      .then(function(r) {
        if (r.status === 401) { window.location.href = '/login'; return null; }
        return r.json();
      })
      .then(function(d) {
        if (!d) return;
        var lc = likeEl.querySelector('.lc');
        if (lc) lc.textContent = d.likes_count;
        likeEl.classList.toggle('active', d.my_reaction === 'like');
      });
      return;
    }

    // Toggle replies
    var toggleBtn = e.target.closest('.dc-toggle-replies');
    if (toggleBtn) {
      var cid2 = toggleBtn.dataset.id;
      var container = document.getElementById('dc-replies-' + cid2);
      if (toggleBtn.dataset.open === '1') {
        container.innerHTML = '';
        toggleBtn.dataset.open = '0';
        toggleBtn.textContent = __lang.view_replies.replace('{n}', ' ' + toggleBtn.dataset.count + ' ');
        return;
      }
      toggleBtn.disabled = true;
      toggleBtn.textContent = '...';
      fetch('/api/comments/' + cid2 + '/replies')
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (d.replies && d.replies.length) {
            container.innerHTML = d.replies.map(function(r) { return renderComment(r); }).join('');
          }
          toggleBtn.dataset.open = '1';
          toggleBtn.textContent = __lang.hide_replies;
          toggleBtn.disabled = false;
        });
      return;
    }

    // Reply
    var replyBtn = e.target.closest('.comment-reply-btn');
    if (replyBtn && CURRENT_UID > 0) {
      // Find root parent: if inside dc-replies-{id}, use that id
      var repliesContainer = replyBtn.closest('[id^="dc-replies-"]');
      var parentId = repliesContainer ? repliesContainer.id.replace('dc-replies-', '') : replyBtn.dataset.id;
      var existing = document.getElementById('dc-reply-form-' + parentId);
      if (existing) { existing.remove(); return; }
      var rCa = Math.floor(Math.random()*10)+1, rCb = Math.floor(Math.random()*10)+1;
      var replyHtml = '<div class="dc-reply-form-wrap" id="dc-reply-form-' + parentId + '" style="margin-top:8px">'
        + '<textarea class="dc-reply-input" rows="2" maxlength="1000" placeholder="' + __lang.reply + '...">@' + escHtml(replyBtn.dataset.name) + ' </textarea>'
        + '<div class="dc-reply-captcha" data-a="'+rCa+'" data-b="'+rCb+'" style="display:none;margin-top:6px;padding:8px;background:var(--bg-base);border-radius:6px;border:1px solid var(--border-color);font-size:14px;">'
        + '<span style="font-weight:600;">'+rCa+' + '+rCb+' = ? </span>'
        + '<input type="number" class="dc-reply-captcha-ans" min="0" max="99" style="width:50px;padding:4px 8px;border-radius:4px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-primary);font-size:14px;text-align:center;">'
        + '</div>'
        + '<div style="display:flex;gap:6px;justify-content:flex-end;margin-top:4px">'
        + '<button class="dc-reply-cancel" data-parent="'+parentId+'">' + __lang.cancel + '</button>'
        + '<button class="dc-reply-submit" data-parent="'+parentId+'">' + __lang.reply + '</button>'
        + '</div></div>';
      replyBtn.closest('.comment-body').insertAdjacentHTML('beforeend', replyHtml);
    }
  });

  // Delegated: cancel/submit reply
  document.addEventListener('click', function(e) {
    var cancelBtn = e.target.closest('.dc-reply-cancel');
    if (cancelBtn) {
      var wrap = document.getElementById('dc-reply-form-' + cancelBtn.dataset.parent);
      if (wrap) wrap.remove();
      return;
    }
    var submitBtn = e.target.closest('.dc-reply-submit');
    if (submitBtn) {
      var pid = submitBtn.dataset.parent;
      var wrap2 = document.getElementById('dc-reply-form-' + pid);
      var ta = wrap2 ? wrap2.querySelector('textarea') : null;
      if (!ta || !ta.value.trim()) return;
      var captchaWrap = wrap2.querySelector('.dc-reply-captcha');
      var captchaAnsInput = wrap2.querySelector('.dc-reply-captcha-ans');
      if (captchaWrap && captchaWrap.style.display !== 'none') {
        var ca = parseInt(captchaWrap.dataset.a), cb = parseInt(captchaWrap.dataset.b);
        if (parseInt(captchaAnsInput.value) !== ca + cb) {
          captchaAnsInput.style.borderColor = 'red';
          captchaAnsInput.focus();
          return;
        }
      }
      fetch('/api/comments', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'manga_id=' + mangaId + '&comment=' + encodeURIComponent(ta.value.trim()) + '&parent_comment=' + pid + (captchaWrap && captchaWrap.style.display !== 'none' ? '&captcha_passed=1' : ''),
        credentials: 'same-origin'
      })
      .then(function(r) {
        if (r.status === 429) return r.json().then(function(d) { throw d; });
        return r.json();
      })
      .then(function(d) {
        if (d.error) { alert(d.error); return; }
        if (wrap2) wrap2.remove();
        // Reload replies for parent
        var repliesContainer = document.getElementById('dc-replies-' + pid);
        if (repliesContainer) {
          fetch('/api/comments/' + pid + '/replies')
            .then(function(r) { return r.json(); })
            .then(function(rd) {
              if (rd.replies && rd.replies.length) {
                repliesContainer.innerHTML = rd.replies.map(renderComment).join('');
              }
              // Update toggle button
              var toggleBtn = document.querySelector('.dc-toggle-replies[data-id="'+pid+'"]');
              if (toggleBtn) {
                toggleBtn.dataset.open = '1';
                toggleBtn.textContent = __lang.hide_replies;
              }
            });
        }
      })
      .catch(function(d) {
        if (d && d.need_captcha && captchaWrap) {
          captchaWrap.style.display = '';
          if (captchaAnsInput) captchaAnsInput.focus();
        }
      });
    }
  });

  // Initial load
  loadComments(1, 'newest');
})();
</script>

<?= $this->endSection() ?>
