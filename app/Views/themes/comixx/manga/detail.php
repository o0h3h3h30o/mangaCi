<?= $this->extend('themes/comixx/layouts/main') ?>

<?= $this->section('content') ?>

<?php
$statusMap = [1 => 'En Curso', 2 => 'Completado'];
$statusLabel = $statusMap[$manga['status_id'] ?? 0] ?? 'Desconocido';

$comicTypeLabel = 'Manga';
if (!empty($manga['type_id'])) {
    try {
        $ctRow = \Config\Database::connect()->table('comictype')->where('id', (int)$manga['type_id'])->get()->getRowArray();
        if ($ctRow) $comicTypeLabel = $ctRow['label'] ?? $ctRow['name'] ?? 'Manga';
    } catch (\Throwable $e) {}
}

$firstChapterLink = '#';
if (!empty($chapters)) {
    $lastCh = end($chapters);
    $firstChapterLink = '/manga/' . esc($manga['slug']) . '/' . esc($lastCh['slug']);
}

$currentUrl = current_url();
$shareText = esc($manga['name']);
?>

<!-- Main Layout -->
<div class="container main-layout">
  <div class="content-area">

    <!-- Manga Hero Section -->
    <div class="detail-hero">
      <div class="detail-hero-cover">
        <img src="<?= manga_cover_url($manga) ?>" alt="<?= esc($manga['name']) ?>">
      </div>
      <div class="detail-hero-info">
        <div class="detail-hero-tags">
          <span class="detail-tag-number"><?= esc(strtoupper($comicTypeLabel)) ?></span>
          <span class="detail-tag-year"><?= esc($statusLabel) ?></span>
        </div>
        <h1 class="detail-title"><?= esc($manga['name']) ?></h1>
        <?php if (!empty($manga['otherNames'])): ?>
          <p class="detail-alt-titles"><?= esc($manga['otherNames']) ?></p>
        <?php endif; ?>
        <div class="detail-actions">
          <a href="<?= $firstChapterLink ?>" class="detail-read-btn"><i class="fas fa-play"></i> EMPEZAR A LEER</a>
          <button class="detail-icon-btn" id="bookmarkBtn" data-manga-id="<?= esc($manga['id']) ?>"><i class="far fa-bookmark"></i></button>
          <button class="detail-icon-btn"><i class="fas fa-flag"></i></button>
        </div>
        <div class="detail-like-row" id="mangaLikeRow">
          <button class="detail-like-btn" id="mangaLikeBtn" data-type="like"><span class="like-emoji">😍</span> <span id="mangaLikeCount">0</span></button>
          <button class="detail-like-btn" id="mangaDislikeBtn" data-type="dislike"><span class="like-emoji">😤</span> <span id="mangaDislikeCount">0</span></button>
        </div>
        <div class="detail-synopsis clamped" id="synopsisBox">
          <p><?= $manga['summary'] ?? '' ?></p>
          <a href="javascript:void(0)" class="detail-more-link" id="synopsisToggle">[+más]</a>
        </div>
        <div class="detail-share">
          <span class="detail-share-count"><?= number_format($manga['views'] ?? 0) ?></span>
          <div class="detail-share-icons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank" rel="noopener" class="detail-share-btn detail-share-facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode($shareText) ?>" target="_blank" rel="noopener" class="detail-share-btn detail-share-x"><i class="fab fa-x-twitter"></i></a>
            <a href="https://pinterest.com/pin/create/button/?url=<?= urlencode($currentUrl) ?>&media=<?= urlencode(manga_cover_url($manga)) ?>&description=<?= urlencode($shareText) ?>" target="_blank" rel="noopener" class="detail-share-btn detail-share-messenger"><i class="fab fa-pinterest-p"></i></a>
            <button class="detail-share-btn detail-share-reddit" onclick="navigator.clipboard.writeText(window.location.href)"><i class="fas fa-link"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Rating Info (Mobile) -->
    <div class="detail-rating-mobile">
      <div class="detail-rating-section">
        <div class="detail-stars" id="mobileRatingStars">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="far fa-star" data-rating="<?= $i ?>"></i>
          <?php endfor; ?>
        </div>
        <div class="detail-score" id="detailScore">0.0</div>
        <div class="detail-stats-list">
          <div class="detail-stat-row">
            <span class="detail-stat-label">Seguidores</span>
            <span class="detail-stat-value" id="detailFollowCount">0 usuarios</span>
          </div>
          <div class="detail-stat-row">
            <span class="detail-stat-label">Puntuación</span>
            <span class="detail-stat-value" id="detailRatingText">0.0 por 0 usuarios</span>
          </div>
          <?php if (!empty($authors)): ?>
            <div class="detail-stat-row">
              <span class="detail-stat-label">Autores</span>
              <span class="detail-stat-value">
                <?php
                $authorNames = array_map(function ($a) {
                    return '<a href="/search?author=' . esc($a['slug'] ?? '', 'url') . '">' . esc($a['name'] ?? '') . '</a>';
                }, $authors);
                echo implode(', ', $authorNames);
                ?>
              </span>
            </div>
          <?php endif; ?>
          <?php if (!empty($artists)): ?>
            <div class="detail-stat-row">
              <span class="detail-stat-label">Artistas</span>
              <span class="detail-stat-value">
                <?php
                $artistNames = array_map(function ($a) {
                    return '<a href="/search?artist=' . esc($a['slug'] ?? '', 'url') . '">' . esc($a['name'] ?? '') . '</a>';
                }, $artists);
                echo implode(', ', $artistNames);
                ?>
              </span>
            </div>
          <?php endif; ?>
          <?php if (!empty($mangaCats)): ?>
            <div class="detail-stat-row">
              <span class="detail-stat-label">Géneros</span>
              <span class="detail-stat-value detail-genres">
                <?php foreach ($mangaCats as $idx => $cat): ?>
                  <?php if ($idx > 0): ?>, <?php endif; ?>
                  <a href="/search?genre=<?= esc($cat->slug ?? $cat['slug'] ?? '', 'url') ?>"><?= esc($cat->name ?? $cat['name'] ?? '') ?></a>
                <?php endforeach; ?>
              </span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Chapter List Section -->
    <section class="detail-chapter-section">
      <div class="detail-chapter-header">
        <div class="detail-chapter-search">
          <input type="text" id="chapterSearchInput" placeholder="Ir al cap..">
        </div>
        <div class="detail-chapter-controls">
          <button class="detail-view-btn active"><i class="fas fa-list"></i></button>
          <button class="detail-view-btn"><i class="fas fa-th"></i></button>
          <button class="detail-filter-dropdown">Todos <i class="fas fa-chevron-down"></i></button>
        </div>
      </div>

      <div class="detail-chapter-table">
        <div class="detail-chapter-table-header">
          <span class="detail-col-chapter"><i class="fas fa-sort-down"></i> Capítulo</span>
          <span class="detail-col-views"><i class="fas fa-eye"></i> Vistas</span>
          <span class="detail-col-updated"><i class="fas fa-clock"></i> Fecha</span>
        </div>

        <?php if (!empty($chapters)): ?>
          <?php foreach ($chapters as $ch): ?>
            <div class="detail-chapter-row" data-chapter-number="<?= esc($ch['number']) ?>">
              <span class="detail-col-chapter"><a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($ch['slug']) ?>">Ch. <?= esc($ch['number']) ?><?= !empty($ch['title']) ? ' - ' . esc($ch['title']) : '' ?></a></span>
              <span class="detail-col-views"><i class="fas fa-eye"></i> <?= number_format($ch['view'] ?? 0) ?></span>
              <span class="detail-col-updated"><?= !empty($ch['created_at']) ? date('d/m/y', strtotime($ch['created_at'])) : '' ?></span>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="detail-chapter-row">
            <span class="detail-col-chapter">Aún no hay capítulos disponibles.</span>
          </div>
        <?php endif; ?>
      </div>

      <div class="detail-chapter-footer">
        <span class="detail-showing-text" id="chapterShowingText"></span>
        <div class="detail-chapter-pagination" id="chapterPagination"></div>
      </div>
    </section>

    <!-- Comments Section -->
    <section class="detail-comments" id="dc-section">
      <div class="detail-comments-warning">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Nota: Por favor, tómate un momento para leer las reglas de comentarios antes de publicar.</span>
      </div>
      <div class="detail-comments-header">
        <span class="detail-comments-count">Comentarios <span id="dc-count"></span></span>
        <div class="tab-buttons">
          <button class="tab-btn active" data-dc-order="newest">NUEVO</button>
          <button class="tab-btn" data-dc-order="oldest">ANTERIOR</button>
          <button class="tab-btn" data-dc-order="top">TOP</button>
        </div>
      </div>

      <p class="detail-comment-login" id="dcLoginPrompt"><a href="/login">Inicia sesión</a> o <a href="/register">Regístrate</a> para unirte a la conversación</p>
      <form id="dc-form" class="detail-comment-form" style="display:none">
        <textarea id="dc-input" rows="3" maxlength="1000" placeholder="Escribe un comentario..."></textarea>
        <div id="dc-captcha-box" class="dc-captcha" style="display:none">
          <p class="dc-captcha-label">Acabas de comentar. Resuelve el captcha para continuar:</p>
          <div class="dc-captcha-row">
            <span id="dc-captcha-q"></span>
            <span>= ?</span>
            <input id="dc-captcha-ans" type="number" min="0" max="99" placeholder="0">
          </div>
        </div>
        <div class="detail-comment-form-footer">
          <span id="dc-char" class="dc-char-count">0 / 1000</span>
          <button type="submit" class="dc-submit-btn">Publicar comentario</button>
        </div>
      </form>

      <div id="dc-list" class="detail-comment-list">
        <p class="dc-loading">Cargando...</p>
      </div>
      <div id="dc-pg" class="dc-pagination"></div>
    </section>

  </div>

  <!-- Right Sidebar -->
  <aside class="right-sidebar detail-sidebar">

    <!-- Rating Section -->
    <div class="detail-rating-section">
      <div class="detail-stars" id="ratingStars">
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <?php if ($i <= floor($ratingAvg)): ?>
            <i class="fas fa-star" data-rating="<?= $i ?>"></i>
          <?php elseif ($i - 0.5 <= $ratingAvg): ?>
            <i class="fas fa-star-half-alt" data-rating="<?= $i ?>"></i>
          <?php else: ?>
            <i class="far fa-star" data-rating="<?= $i ?>"></i>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
      <div class="detail-score"><?= number_format($ratingAvg, 1) ?></div>
      <div class="detail-stats-list">
        <div class="detail-stat-row">
          <span class="detail-stat-label">Seguidores</span>
          <span class="detail-stat-value"><?= number_format($followCount ?? 0) ?> usuarios</span>
        </div>
        <div class="detail-stat-row">
          <span class="detail-stat-label">Puntuación</span>
          <span class="detail-stat-value"><?= number_format($ratingAvg, 1) ?> por <?= number_format($ratingVotes) ?> usuarios</span>
        </div>
        <?php if (!empty($authors)): ?>
          <div class="detail-stat-row">
            <span class="detail-stat-label">Autores</span>
            <span class="detail-stat-value">
              <?php
              $authorLinks = array_map(function ($a) {
                  return '<a href="/search?author=' . esc($a['slug'] ?? '', 'url') . '">' . esc($a['name'] ?? '') . '</a>';
              }, $authors);
              echo implode(', ', $authorLinks);
              ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if (!empty($artists)): ?>
          <div class="detail-stat-row">
            <span class="detail-stat-label">Artistas</span>
            <span class="detail-stat-value">
              <?php
              $artistLinks = array_map(function ($a) {
                  return '<a href="/search?artist=' . esc($a['slug'] ?? '', 'url') . '">' . esc($a['name'] ?? '') . '</a>';
              }, $artists);
              echo implode(', ', $artistLinks);
              ?>
            </span>
          </div>
        <?php endif; ?>
        <?php if (!empty($mangaCats)): ?>
          <div class="detail-stat-row">
            <span class="detail-stat-label">Géneros</span>
            <span class="detail-stat-value detail-genres">
              <?php foreach ($mangaCats as $idx => $cat): ?>
                <?php if ($idx > 0): ?>, <?php endif; ?>
                <a href="/search?genre=<?= esc($cat->slug ?? $cat['slug'] ?? '', 'url') ?>"><?= esc($cat->name ?? $cat['name'] ?? '') ?></a>
              <?php endforeach; ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recommendations Section -->
    <?php if (!empty($recommended)): ?>
      <div class="detail-recommendations">
        <div class="section-header">
          <h2>Recomendados</h2>
          <div class="section-nav">
            <button class="nav-arrow"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-arrow"><i class="fas fa-chevron-right"></i></button>
          </div>
        </div>
        <div class="sidebar-list">
          <?php foreach ($recommended as $rec): ?>
            <a href="/manga/<?= esc($rec['slug']) ?>" class="sidebar-item">
              <div class="sidebar-thumb">
                <img src="<?= manga_cover_url($rec) ?>" alt="<?= esc($rec['name']) ?>">
              </div>
              <div class="sidebar-info">
                <span class="sidebar-type manga">MANGA</span>
                <h4><?= esc($rec['name']) ?></h4>
                <div class="sidebar-meta">
                  <span><i class="fas fa-eye"></i> <?= number_format($rec['views'] ?? 0) ?></span>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </aside>
</div>

<!-- Synopsis Toggle JS -->
<script>
(function(){
  var box = document.getElementById('synopsisBox');
  var btn = document.getElementById('synopsisToggle');
  if (!box || !btn) return;
  var p = box.querySelector('p');
  if (p && p.scrollHeight <= p.offsetHeight) { btn.style.display = 'none'; }
  btn.addEventListener('click', function() {
    if (box.classList.contains('clamped')) {
      box.classList.remove('clamped');
      btn.textContent = '[-menos]';
    } else {
      box.classList.add('clamped');
      btn.textContent = '[+más]';
    }
  });
})();
</script>

<!-- Bookmark JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bookmarkBtn = document.getElementById('bookmarkBtn');
    if (bookmarkBtn) {
        const mangaId = bookmarkBtn.getAttribute('data-manga-id');

        bookmarkBtn.addEventListener('click', function () {
            fetch('/api/bookmark/toggle', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                body: 'manga_id=' + mangaId,
                credentials: 'same-origin'
            })
            .then(function(response) {
                if (response.status === 401) { window.location.href = '/login'; return null; }
                return response.json();
            })
            .then(function(data) {
                if (!data) return;
                if (data.error) { alert(data.error); return; }
                var icon = bookmarkBtn.querySelector('i');
                if (data.bookmarked) {
                    icon.className='fas fa-bookmark';
                    bookmarkBtn.classList.add('active');
                } else {
                    icon.className='far fa-bookmark';
                    bookmarkBtn.classList.remove('active');
                }
                if(typeof reloadMangaState==='function') reloadMangaState();
            })
            .catch(function(err) { console.error('Bookmark error:', err); });
        });
    }
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
  if(!likeBtn||!dislikeBtn) return;

  function updateUI(data){
    likeCount.textContent = data.likes;
    dislikeCount.textContent = data.dislikes;
    if(data.my_reaction==='like') likeBtn.classList.add('active');
    else likeBtn.classList.remove('active');
    if(data.my_reaction==='dislike') dislikeBtn.classList.add('active');
    else dislikeBtn.classList.remove('active');
  }

  function toggle(type){
    fetch('/api/content-like',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
      body:'content_type=manga&content_id='+mangaId+'&type='+type,
      credentials:'same-origin'
    })
    .then(function(r){
      if(r.status===401){window.location.href='/login';return null;}
      return r.json();
    })
    .then(function(d){if(d) updateUI(d);});
  }

  likeBtn.addEventListener('click',function(){toggle('like');});
  dislikeBtn.addEventListener('click',function(){toggle('dislike');});
})();
</script>

<!-- Chapter Pagination + Search JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var PER_PAGE = 10;
    var allRows = Array.from(document.querySelectorAll('.detail-chapter-row'));
    var filteredRows = allRows.slice();
    var currentPage = 1;
    var paginationEl = document.getElementById('chapterPagination');
    var showingEl = document.getElementById('chapterShowingText');
    var searchInput = document.getElementById('chapterSearchInput');

    function getTotalPages() {
        return Math.max(1, Math.ceil(filteredRows.length / PER_PAGE));
    }

    function render() {
        var total = filteredRows.length;
        var totalPages = getTotalPages();
        if (currentPage > totalPages) currentPage = totalPages;
        var start = (currentPage - 1) * PER_PAGE;
        var end = Math.min(start + PER_PAGE, total);

        // Hide all rows first
        allRows.forEach(function(r) { r.style.display = 'none'; });
        // Show only current page rows
        for (var i = start; i < end; i++) {
            filteredRows[i].style.display = '';
        }

        // Update showing text
        if (total === 0) {
            showingEl.textContent = 'No se encontraron capítulos';
        } else {
            showingEl.textContent = 'Mostrando ' + (start + 1) + ' a ' + end + ' de ' + total + ' capítulos';
        }

        // Build pagination buttons
        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        var html = '';
        // Prev
        html += '<button class="ch-page-btn' + (currentPage === 1 ? ' disabled' : '') + '" data-page="' + (currentPage - 1) + '"' + (currentPage === 1 ? ' disabled' : '') + '><i class="fas fa-chevron-left"></i></button>';

        // Page numbers
        var pages = buildPageNumbers(currentPage, totalPages);
        pages.forEach(function(p) {
            if (p === '...') {
                html += '<span class="ch-page-dots">...</span>';
            } else {
                html += '<button class="ch-page-btn' + (p === currentPage ? ' active' : '') + '" data-page="' + p + '">' + p + '</button>';
            }
        });

        // Next
        html += '<button class="ch-page-btn' + (currentPage === totalPages ? ' disabled' : '') + '" data-page="' + (currentPage + 1) + '"' + (currentPage === totalPages ? ' disabled' : '') + '><i class="fas fa-chevron-right"></i></button>';

        paginationEl.innerHTML = html;

        // Bind click events
        paginationEl.querySelectorAll('.ch-page-btn:not(.disabled)').forEach(function(btn) {
            btn.addEventListener('click', function() {
                currentPage = parseInt(this.dataset.page);
                render();
                // Scroll to chapter section top
                var section = document.querySelector('.detail-chapter-section');
                if (section) section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function buildPageNumbers(cur, total) {
        var pages = [];
        if (total <= 7) {
            for (var i = 1; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            if (cur > 3) pages.push('...');
            var s = Math.max(2, cur - 1);
            var e = Math.min(total - 1, cur + 1);
            for (var i = s; i <= e; i++) pages.push(i);
            if (cur < total - 2) pages.push('...');
            pages.push(total);
        }
        return pages;
    }

    // Search filter
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var query = this.value.trim().toLowerCase();
            if (query === '') {
                filteredRows = allRows.slice();
            } else {
                filteredRows = allRows.filter(function(row) {
                    var num = row.getAttribute('data-chapter-number') || '';
                    var text = row.textContent || '';
                    return num.toLowerCase().includes(query) || text.toLowerCase().includes(query);
                });
            }
            currentPage = 1;
            render();
        });
    }

    // Initial render
    render();
});
</script>

<!-- Rating JS -->
<script>
(function(){
  var mangaId = <?= (int)($manga['id'] ?? 0) ?>;
  var currentRating = 0;

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
      if(typeof reloadMangaState==='function') reloadMangaState();
    })
    .catch(function(err) { alert('Error al calificar'); });
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
    strs.forEach(function(star) {
      star.style.cursor = 'pointer';
      star.addEventListener('click', function() {
        var r = parseInt(this.getAttribute('data-rating'));
        submitRating(r, strs);
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
  setupStars(document.getElementById('mobileRatingStars'));
})();
</script>

<!-- Comment System CSS -->
<style>
.detail-comment-login {
  text-align: center;
  padding: 20px 0;
  color: var(--text-muted);
  font-size: 14px;
}
.detail-comment-login a {
  color: var(--accent-blue);
  font-weight: 600;
}
.detail-comment-login a:hover { text-decoration: underline; }
.detail-comment-form { margin-bottom: 16px; }
.detail-comment-form textarea {
  width: 100%;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-primary);
  font-family: var(--font);
  font-size: 13px;
  padding: 10px 12px;
  resize: none;
  outline: none;
}
.detail-comment-form textarea:focus { border-color: var(--accent-blue); }
.detail-comment-form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 8px;
}
.dc-char-count { font-size: 12px; color: var(--text-muted); }
.dc-submit-btn {
  background: var(--accent-blue);
  color: var(--bg-primary);
  border: none;
  border-radius: 6px;
  padding: 6px 14px;
  font-size: 12px;
  font-family: var(--font);
  font-weight: 600;
  cursor: pointer;
  transition: opacity .2s;
}
.dc-submit-btn:hover { opacity: .85; }
.dc-captcha {
  margin-top: 8px;
  padding: 10px 12px;
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border);
  border-radius: 8px;
}
.dc-captcha-label { font-size: 12px; color: var(--text-muted); margin-bottom: 6px; }
.dc-captcha-row { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; color: var(--text-primary); }
.dc-captcha-row input {
  width: 56px;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 4px 8px;
  font-size: 13px;
  font-family: var(--font);
  color: var(--text-primary);
  outline: none;
}
.detail-comment-list { min-height: 60px; }
.dc-loading { text-align: center; padding: 20px 0; color: var(--text-muted); font-size: 13px; }
.dc-item { padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
.dc-item:last-child { border-bottom: none; }
.dc-item-body { display: flex; gap: 10px; }
.dc-avatar {
  width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 700; color: #fff;
}
.dc-content { flex: 1; min-width: 0; }
.dc-bubble {
  background: var(--bg-card);
  border-radius: 10px;
  padding: 8px 12px;
}
.dc-user { font-weight: 700; font-size: 13px; color: var(--text-primary); }
.dc-chapter-tag {
  font-size: 11px;
  background: rgba(52,211,153,.12);
  color: var(--accent-blue);
  padding: 1px 6px;
  border-radius: 4px;
  margin-left: 6px;
  text-decoration: none;
}
.dc-chapter-tag:hover { text-decoration: underline; }
.dc-text { font-size: 13px; color: var(--text-secondary); margin-top: 4px; white-space: pre-wrap; word-break: break-word; }
.dc-actions {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 12px; margin-top: 4px; padding: 0 4px;
}
.dc-actions-left { display: flex; align-items: center; gap: 8px; color: var(--text-muted); }
.dc-react {
  display: inline-flex; align-items: center; gap: 3px;
  background: none; border: none; font-size: 12px; font-family: var(--font);
  color: var(--text-muted); cursor: pointer; padding: 2px 4px; border-radius: 4px;
  transition: color .2s;
}
.dc-react:hover { color: var(--accent-blue); }
.dc-react.liked { color: var(--accent-blue); }
.dc-reply-btn {
  background: none; border: none; font-size: 12px; font-family: var(--font);
  color: var(--text-muted); cursor: pointer; padding: 2px 6px; border-radius: 4px;
  transition: color .2s;
}
.dc-reply-btn:hover { color: var(--accent-blue); }
.dc-replies {
  margin-left: 16px; padding-left: 12px;
  border-left: 1px solid rgba(52,211,153,.2);
  margin-top: 6px;
}
.dc-replies .dc-item { padding: 6px 0; }
.dc-replies .dc-avatar { width: 28px; height: 28px; font-size: 11px; }
.dc-reply-form { margin-top: 8px; }
.dc-reply-form textarea {
  width: 100%;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-primary);
  font-family: var(--font);
  font-size: 12px;
  padding: 8px 10px;
  resize: none;
  outline: none;
}
.dc-reply-form textarea:focus { border-color: var(--accent-blue); }
.dc-reply-form-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 6px; }
.dc-reply-cancel-btn {
  background: none; border: none; font-size: 12px; font-family: var(--font);
  color: var(--text-muted); cursor: pointer;
}
.dc-reply-submit-btn {
  background: var(--accent-blue); color: var(--bg-primary); border: none;
  border-radius: 6px; padding: 4px 12px; font-size: 12px;
  font-family: var(--font); font-weight: 600; cursor: pointer;
}
.dc-pagination {
  display: flex; justify-content: center; align-items: center;
  gap: 4px; flex-wrap: wrap; margin-top: 12px;
}
.dc-pagination button {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 32px; height: 32px; padding: 0 6px;
  border-radius: 6px; font-size: 13px; font-family: var(--font);
  border: 1px solid var(--border); background: var(--bg-card);
  color: var(--text-muted); cursor: pointer; transition: background .15s;
}
.dc-pagination button:hover:not([disabled]):not(.pg-active) {
  background: var(--bg-hover); color: var(--text-primary);
}
.dc-pagination .pg-active {
  background: var(--accent-blue) !important; border-color: var(--accent-blue) !important;
  color: var(--bg-primary) !important; font-weight: 700; pointer-events: none;
}
.dc-pagination button[disabled] { opacity: .4; cursor: default; pointer-events: none; }
.dc-show-more {
  background: none; border: none; font-size: 12px; font-family: var(--font);
  color: var(--accent-blue); cursor: pointer; padding: 4px 0; margin-top: 4px;
}
.dc-show-more:hover { text-decoration: underline; }
</style>

<!-- Comment System JS -->
<script>
(function() {
  var MANGA_ID    = <?= (int) $manga['id'] ?>;
  var MANGA_SLUG  = <?= json_encode($manga['slug']) ?>;
  var CURRENT_UID = 0;
  var page = 1, totalPages = 1, loading = false, order = 'newest';
  var BG = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];

  function esc(s){ return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function timeAgo(str){
    var d=new Date(str.replace(' ','T'));
    var diff=Math.floor((Date.now()-d.getTime())/1000);
    if(diff<60) return 'hace '+diff+'s';
    if(diff<3600) return 'hace '+Math.floor(diff/60)+'m';
    if(diff<86400) return 'hace '+Math.floor(diff/3600)+'h';
    if(diff<604800) return 'hace '+Math.floor(diff/86400)+'d';
    return 'hace '+Math.floor(diff/604800)+'sem';
  }

  function avatar(name, username, uid, size){
    var sz=size||36;
    var ch=((name||username||'?')[0]).toUpperCase();
    var bg=BG[parseInt(uid||0)%6];
    return '<div class="dc-avatar" style="width:'+sz+'px;height:'+sz+'px;font-size:'+(sz*0.4)+'px;background:'+bg+'">'+ch+'</div>';
  }

  var likeIcon = '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>';

  function likeBtnHtml(c){
    var isLiked = c.my_reaction==='like';
    if(CURRENT_UID>0){
      return '<button class="dc-react'+(isLiked?' liked':'')+'" data-id="'+c.id+'" data-type="like">'+likeIcon+'<span class="like-count">'+c.likes_count+'</span></button>';
    }
    return '<span class="dc-react" style="cursor:default">'+likeIcon+'<span>'+c.likes_count+'</span></span>';
  }

  function renderReply(c, topParentId){
    var name=c.user_name||c.user_username||'?';
    var replyBtn=(CURRENT_UID>0&&topParentId)
      ? '<button class="dc-reply-btn" data-id="'+topParentId+'" data-reply-to="'+c.id+'" data-name="'+esc(name)+'">Responder</button>'
      : '';
    return '<div class="dc-item" data-id="'+c.id+'">'+
      '<div class="dc-item-body">'+
      avatar(c.user_name,c.user_username,c.user_id,28)+
      '<div class="dc-content">'+
      '<div class="dc-bubble">'+
      '<span class="dc-user">'+esc(name)+'</span>'+
      '<div class="dc-text">'+esc(c.comment)+'</div>'+
      '</div>'+
      '<div class="dc-actions">'+
      '<div class="dc-actions-left">'+likeBtnHtml(c)+'<small>'+timeAgo(c.created_at)+'</small></div>'+
      (replyBtn?'<div>'+replyBtn+'</div>':'')+
      '</div>'+
      '</div></div></div>';
  }

  function replyFormHtml(parentId, parentName, replyToId){
    return '<div class="dc-reply-form" id="dc-rf-'+parentId+'">'+
      '<input type="hidden" class="dc-reply-to-id" value="'+(replyToId||0)+'">'+
      '<textarea class="dc-reply-input" rows="2" maxlength="1000">@'+esc(parentName)+' </textarea>'+
      '<div class="dc-rf-captcha-box dc-captcha" style="display:none">'+
      '<p class="dc-captcha-label">Acabas de comentar. Resuelve el captcha para continuar:</p>'+
      '<div class="dc-captcha-row"><span class="dc-rf-captcha-q"></span><span>= ?</span>'+
      '<input class="dc-rf-captcha-ans" type="number" min="0" max="99" placeholder="0"></div></div>'+
      '<div class="dc-reply-form-actions">'+
      '<button class="dc-reply-cancel-btn dc-reply-cancel" data-parent="'+parentId+'">Cancelar</button>'+
      '<button class="dc-reply-submit-btn dc-reply-submit" data-parent="'+parentId+'">Responder</button>'+
      '</div></div>';
  }

  function fetchReplies(commentId, btn){
    var container=document.getElementById('dc-replies-'+commentId);
    if(!container) return;
    if(btn){btn.disabled=true;btn.textContent='Cargando...';}
    fetch('/api/comments/'+commentId+'/replies')
      .then(function(r){return r.json();})
      .then(function(d){
        if(!d.replies||!d.replies.length){
          if(btn){btn.disabled=false;var cnt=btn.dataset.count;btn.textContent='Ver '+cnt+' respuesta'+(parseInt(cnt)===1?'':'s');}
          return;
        }
        var LIMIT=5,visible=d.replies.slice(0,LIMIT),hidden=d.replies.slice(LIMIT);
        container.innerHTML=visible.map(function(r){return renderReply(r,commentId);}).join('');
        if(hidden.length>0){
          var mBtn=document.createElement('button');
          mBtn.className='dc-show-more';
          mBtn.textContent='Mostrar '+hidden.length+' respuesta'+(hidden.length===1?'':'s')+' más...';
          mBtn.onclick=function(){mBtn.remove();container.insertAdjacentHTML('beforeend',hidden.map(function(r){return renderReply(r,commentId);}).join(''));};
          container.appendChild(mBtn);
        }
        if(btn){btn.textContent='Ocultar respuestas';btn.disabled=false;btn.dataset.open='1';}
      })
      .catch(function(){
        if(btn){btn.disabled=false;var cnt=btn.dataset.count;btn.textContent='Ver '+cnt+' respuesta'+(parseInt(cnt)===1?'':'s');}
      });
  }

  function renderCmt(c){
    var name=c.user_name||c.user_username||'?';
    var replyBtn='';
    if(CURRENT_UID>0){
      replyBtn='<button class="dc-reply-btn" data-id="'+c.id+'" data-name="'+esc(name)+'">Responder</button>';
    }
    var chapterTag='';
    if(c.chapter_slug){
      chapterTag='<a href="/manga/'+MANGA_SLUG+'/'+esc(c.chapter_slug)+'" class="dc-chapter-tag" onclick="event.stopPropagation()">'+esc(c.chapter_name)+'</a>';
    }
    var replyCount=parseInt(c.reply_count||0);
    var repliesToggle='';
    if(replyCount>0){
      repliesToggle='<button class="dc-reply-btn dc-toggle-replies" data-id="'+c.id+'" data-count="'+replyCount+'">Ver '+replyCount+' respuesta'+(replyCount===1?'':'s')+'</button>';
    }
    return '<div class="dc-item" data-id="'+c.id+'">'+
      '<div class="dc-item-body">'+
      avatar(c.user_name,c.user_username,c.user_id)+
      '<div class="dc-content">'+
      '<div class="dc-bubble">'+
      '<span class="dc-user">'+esc(name)+'</span>'+chapterTag+
      '<div class="dc-text">'+esc(c.comment)+'</div>'+
      '</div>'+
      '<div class="dc-actions">'+
      '<div class="dc-actions-left">'+likeBtnHtml(c)+'<small>'+timeAgo(c.created_at)+'</small>'+repliesToggle+'</div>'+
      (replyBtn?'<div>'+replyBtn+'</div>':'')+
      '</div>'+
      '<div id="dc-reply-area-'+c.id+'"></div>'+
      '<div id="dc-replies-'+c.id+'" class="dc-replies"></div>'+
      '</div></div></div>';
  }

  function renderPg(){
    var el=document.getElementById('dc-pg');
    if(!el) return;
    if(totalPages<=1){el.innerHTML='';return;}
    var h='';
    h+=page>1?'<button data-page="'+(page-1)+'">&#8249;</button>':'<button disabled>&#8249;</button>';
    var s=Math.max(1,page-2),e=Math.min(totalPages,s+4);s=Math.max(1,e-4);
    if(s>1){h+='<button data-page="1">1</button>';if(s>2)h+='<span style="padding:0 4px;color:var(--text-muted)">...</span>';}
    for(var i=s;i<=e;i++) h+=i===page?'<button class="pg-active">'+i+'</button>':'<button data-page="'+i+'">'+i+'</button>';
    if(e<totalPages){if(e<totalPages-1)h+='<span style="padding:0 4px;color:var(--text-muted)">...</span>';h+='<button data-page="'+totalPages+'">'+totalPages+'</button>';}
    h+=page<totalPages?'<button data-page="'+(page+1)+'">&#8250;</button>':'<button disabled>&#8250;</button>';
    el.innerHTML=h;
  }

  function fetchComments(p){
    if(loading) return;
    loading=true; page=p;
    fetch('/api/comments/manga/'+MANGA_ID+'/all?page='+p+'&order='+order)
      .then(function(r){return r.json();})
      .then(function(data){
        var list=document.getElementById('dc-list');
        var countEl=document.getElementById('dc-count');
        if(countEl) countEl.textContent=data.total>0?'('+data.total+')':'';
        totalPages=data.total>0?Math.ceil(data.total/10):1;
        list.innerHTML=(!data.comments||!data.comments.length)
          ?'<p class="dc-loading">Sin comentarios aún. ¡Sé el primero!</p>'
          :data.comments.map(renderCmt).join('');
        if(data.comments) data.comments.forEach(function(c){
          if(parseInt(c.reply_count||0)>0) fetchReplies(c.id,null);
        });
        renderPg();
        loading=false;
      })
      .catch(function(){loading=false;});
  }

  fetchComments(1);

  document.querySelectorAll('[data-dc-order]').forEach(function(btn){
    btn.addEventListener('click',function(){
      document.querySelectorAll('[data-dc-order]').forEach(function(b){b.classList.remove('active');});
      btn.classList.add('active');
      order=btn.dataset.dcOrder;
      fetchComments(1);
    });
  });

  var pgEl=document.getElementById('dc-pg');
  if(pgEl) pgEl.addEventListener('click',function(e){
    var btn=e.target.closest('[data-page]');
    if(!btn) return;
    var p=parseInt(btn.dataset.page);
    if(p&&p!==page){fetchComments(p);document.getElementById('dc-section').scrollIntoView({behavior:'smooth',block:'start'});}
  });

  // Comment form
  var form=document.getElementById('dc-form');
  if(form){
    var inp=document.getElementById('dc-input');
    var charEl=document.getElementById('dc-char');
    var captchaReady=false;
    var LAST_KEY='lct_'+CURRENT_UID;
    inp.addEventListener('input',function(){charEl.textContent=inp.value.length+' / 1000';});

    function showCaptcha(question){
      document.getElementById('dc-captcha-q').textContent=question;
      var box=document.getElementById('dc-captcha-box');
      box.style.display='block';
      captchaReady=true;
      box.scrollIntoView({behavior:'smooth',block:'center'});
      var ans=document.getElementById('dc-captcha-ans');
      if(ans) ans.focus();
    }
    function hideCaptcha(){
      var box=document.getElementById('dc-captcha-box');
      if(box) box.style.display='none';
      var ans=document.getElementById('dc-captcha-ans');
      if(ans) ans.value='';
      captchaReady=false;
    }

    form.addEventListener('submit',function(e){
      e.preventDefault();
      var text=inp.value.trim();
      if(!text) return;
      var last=parseInt(localStorage.getItem(LAST_KEY)||'0');
      var withinLimit=(CURRENT_UID>0)&&((Date.now()-last)<300000);
      if(withinLimit&&!captchaReady){
        fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){showCaptcha(d.question);});
        return;
      }
      var fd=new FormData();
      fd.append('manga_id',MANGA_ID);
      fd.append('comment',text);
      if(captchaReady){
        var ans=document.getElementById('dc-captcha-ans');
        if(!ans||!ans.value.trim()){ans&&ans.focus();return;}
        fd.append('captcha_answer',ans.value.trim());
      }
      fetch('/api/comments',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(c){
          if(c.need_captcha){
            fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){showCaptcha(d.question);});
            return;
          }
          if(c.error){alert(c.error);return;}
          localStorage.setItem(LAST_KEY,Date.now());
          hideCaptcha();
          var list=document.getElementById('dc-list');
          var ph=list.querySelector('.dc-loading');
          if(ph) ph.remove();
          c.reply_count=0;
          list.insertAdjacentHTML('afterbegin',renderCmt(c));
          inp.value='';charEl.textContent='0 / 1000';
          var countEl=document.getElementById('dc-count');
          if(countEl){var cur=parseInt((countEl.textContent||'').replace(/\D/g,''))||0;countEl.textContent='('+(cur+1)+')';}
        })
        .catch(function(){alert('Algo salió mal, por favor intenta de nuevo.');});
    });
  }

  // Event delegation on comment list
  document.getElementById('dc-list').addEventListener('click',function(e){
    var target=e.target.closest('button');
    if(!target) return;

    // Like
    if(target.classList.contains('dc-react')){
      var cid=parseInt(target.dataset.id);
      var fd=new FormData();
      fd.append('type','like');
      fetch('/api/comments/'+cid+'/react',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(d){
          if(d.error) return;
          var item=target.closest('.dc-item');
          if(!item) return;
          var lb=item.querySelector('.dc-react[data-type="like"]');
          if(lb){
            if(d.my_reaction==='like') lb.classList.add('liked'); else lb.classList.remove('liked');
            var lc=lb.querySelector('.like-count');
            if(lc) lc.textContent=d.likes_count;
          }
        });
      return;
    }

    // Toggle replies
    if(target.classList.contains('dc-toggle-replies')){
      var cid=target.dataset.id;
      var container=document.getElementById('dc-replies-'+cid);
      if(!container) return;
      if(target.dataset.open==='1'){
        container.innerHTML='';
        target.dataset.open='0';
        var cnt=target.dataset.count;
        target.textContent='Ver '+cnt+' respuesta'+(parseInt(cnt)===1?'':'s');
      } else {
        fetchReplies(parseInt(cid),target);
      }
      return;
    }

    // Reply button
    if(target.classList.contains('dc-reply-btn')&&!target.classList.contains('dc-toggle-replies')){
      var parentId=target.dataset.id;
      var parentName=target.dataset.name;
      var replyToId=target.dataset.replyTo||0;
      var area=document.getElementById('dc-reply-area-'+parentId);
      if(!area) return;
      var existing=document.getElementById('dc-rf-'+parentId);
      if(existing){existing.remove();return;}
      area.innerHTML=replyFormHtml(parentId,parentName,replyToId);
      var ta=area.querySelector('.dc-reply-input');
      ta.focus();ta.setSelectionRange(ta.value.length,ta.value.length);
      return;
    }

    // Cancel reply
    if(target.classList.contains('dc-reply-cancel')){
      var rf=document.getElementById('dc-rf-'+target.dataset.parent);
      if(rf) rf.remove();
      return;
    }

    // Submit reply
    if(target.classList.contains('dc-reply-submit')){
      var parentId=target.dataset.parent;
      var rf=document.getElementById('dc-rf-'+parentId);
      if(!rf) return;
      var ta=rf.querySelector('.dc-reply-input');
      var text=ta?ta.value.trim():'';
      if(!text) return;
      var captchaBox=rf.querySelector('.dc-rf-captcha-box');
      var captchaVisible=captchaBox&&captchaBox.style.display!=='none';
      if(captchaVisible){
        var captchaAns=rf.querySelector('.dc-rf-captcha-ans');
        if(!captchaAns||!captchaAns.value.trim()){captchaAns&&captchaAns.focus();return;}
      }
      target.disabled=true;target.textContent='Enviando...';
      var replyToInput=rf.querySelector('.dc-reply-to-id');
      var fd=new FormData();
      fd.append('manga_id',MANGA_ID);
      fd.append('comment',text);
      fd.append('parent_comment',parentId);
      fd.append('reply_to_id',replyToInput?replyToInput.value:0);
      if(captchaVisible){
        var captchaAns=rf.querySelector('.dc-rf-captcha-ans');
        if(captchaAns&&captchaAns.value.trim()) fd.append('captcha_answer',captchaAns.value.trim());
      }
      fetch('/api/comments',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(c){
          if(c.need_captcha){
            fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){
              var box=rf.querySelector('.dc-rf-captcha-box');
              var q=rf.querySelector('.dc-rf-captcha-q');
              var ans=rf.querySelector('.dc-rf-captcha-ans');
              if(box) box.style.display='block';
              if(q) q.textContent=d.question;
              if(ans){ans.value='';ans.focus();}
            });
            target.disabled=false;target.textContent='Responder';
            return;
          }
          if(c.error){alert(c.error);target.disabled=false;target.textContent='Responder';return;}
          rf.remove();
          var container=document.getElementById('dc-replies-'+parentId);
          if(container){
            var moreBtn=container.querySelector('.dc-show-more');
            if(moreBtn) moreBtn.insertAdjacentHTML('beforebegin',renderReply(c,parentId));
            else container.insertAdjacentHTML('beforeend',renderReply(c,parentId));
          }
        })
        .catch(function(){target.disabled=false;target.textContent='Responder';alert('Algo salió mal.');});
      return;
    }
  });
})();

// ── Hydrate dynamic data from API ──
(function(){
  var MID = <?= (int) $manga['id'] ?>;

  function updateStars(containerId, avg){
    var el=document.getElementById(containerId);if(!el)return;
    var stars=el.querySelectorAll('i[data-rating]');
    stars.forEach(function(s){
      var v=parseInt(s.dataset.rating);
      s.className = v<=Math.floor(avg) ? 'fas fa-star' : (v-0.5<=avg ? 'fas fa-star-half-alt' : 'far fa-star');
    });
  }

  fetch('/api/manga/'+MID+'/state',{credentials:'same-origin'})
  .then(function(r){return r.json()})
  .then(function(d){
    // Rating
    updateStars('mobileRatingStars', d.rating_avg);
    updateStars('sidebarRatingStars', d.rating_avg);
    document.querySelectorAll('#detailScore').forEach(function(el){el.textContent=d.rating_avg.toFixed(1);});
    document.querySelectorAll('#detailRatingText').forEach(function(el){el.textContent=d.rating_avg.toFixed(1)+' por '+d.rating_votes+' usuarios';});
    currentRating = d.my_rating || 0;

    // Follow count
    document.querySelectorAll('#detailFollowCount').forEach(function(el){el.textContent=d.follow_count+' usuarios';});

    // Bookmark
    var bmBtn=document.getElementById('bookmarkBtn');
    if(bmBtn && d.is_bookmarked){
      bmBtn.classList.add('active');
      bmBtn.querySelector('i').className='fas fa-bookmark';
    }

    // Likes
    var lc=document.getElementById('mangaLikeCount');
    var dc=document.getElementById('mangaDislikeCount');
    if(lc) lc.textContent=d.likes;
    if(dc) dc.textContent=d.dislikes;
    if(d.my_reaction==='like') document.getElementById('mangaLikeBtn').classList.add('active');
    if(d.my_reaction==='dislike') document.getElementById('mangaDislikeBtn').classList.add('active');

    // Comment form: show if logged in (window.__user set by header)
    if(window.__user && window.__user.logged_in){
      CURRENT_UID = window.__user.id;
      var form=document.getElementById('dc-form');
      var prompt=document.getElementById('dcLoginPrompt');
      if(form) form.style.display='';
      if(prompt) prompt.style.display='none';
    }
  }).catch(function(){});

  // Re-fetch state after actions
  window.reloadMangaState = function(){
    fetch('/api/manga/'+MID+'/state',{credentials:'same-origin'})
    .then(function(r){return r.json()})
    .then(function(d){
      updateStars('mobileRatingStars', d.rating_avg);
      updateStars('sidebarRatingStars', d.rating_avg);
      document.querySelectorAll('#detailScore').forEach(function(el){el.textContent=d.rating_avg.toFixed(1);});
      document.querySelectorAll('#detailRatingText').forEach(function(el){el.textContent=d.rating_avg.toFixed(1)+' por '+d.rating_votes+' usuarios';});
      document.querySelectorAll('#detailFollowCount').forEach(function(el){el.textContent=d.follow_count+' usuarios';});
      var lc=document.getElementById('mangaLikeCount');
      var dc=document.getElementById('mangaDislikeCount');
      if(lc) lc.textContent=d.likes;
      if(dc) dc.textContent=d.dislikes;
    }).catch(function(){});
  };
})();
</script>

<?= $this->endSection() ?>
