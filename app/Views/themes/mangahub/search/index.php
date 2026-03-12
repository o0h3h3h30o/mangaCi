<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.search-page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; padding:24px 0 16px; }
.search-page-title { font-size:22px; font-weight:800; color:var(--txt); }
.search-page-count { font-size:12.5px; color:var(--txt3); background:var(--card); border:1px solid var(--border); padding:4px 12px; border-radius:20px; }
.filter-bar { display:flex; align-items:center; flex-wrap:wrap; gap:8px; padding:14px 16px; margin-bottom:20px; background:var(--card); border:1px solid var(--border); border-radius:12px; }
.filter-search { display:flex; align-items:center; gap:8px; background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:7px 12px; flex:1; min-width:140px; max-width:260px; }
.filter-search input { background:none; border:none; outline:none; font-size:13px; color:var(--txt); width:100%; }
.filter-search input::placeholder { color:var(--txt3); }
.filter-select { appearance:none; background:var(--bg); border:1px solid var(--border); color:var(--txt); font-size:12.5px; padding:7px 28px 7px 11px; border-radius:8px; cursor:pointer; }
.filter-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 16px; border-radius:8px; font-size:12.5px; font-weight:700; background:var(--accent); color:#fff; border:none; cursor:pointer; margin-left:auto; }
.filter-divider { width:1px; height:20px; background:var(--border); }
</style>

<main>
<div class="wrap">
  <div class="search-page-header">
    <h1 class="search-page-title">Search Results</h1>
    <span class="search-page-count"><?= count($results) ?> mangas</span>
  </div>

  <!-- Filter bar -->
  <div class="filter-bar">
    <div class="filter-search">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" placeholder="Search…" id="searchInput" value="<?= esc($_GET['filter']['name'] ?? '') ?>" />
    </div>
    <select class="filter-select" id="sortSelect">
      <option value="-updated_at">Recently updated</option>
      <option value="-views">Most popular</option>
      <option value="-created_at">Newest added</option>
      <option value="name">A–Z</option>
    </select>
    <select class="filter-select" id="genreSelect">
      <option value="">All Genres</option>
      <?php foreach ($categories as $cat): ?>
      <option value="<?= esc($cat['slug']) ?>"><?= esc($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <select class="filter-select" id="statusSelect">
      <option value="">All Status</option>
      <option value="1">Ongoing</option>
      <option value="2">Completed</option>
    </select>
    <button class="filter-btn" id="filterBtn">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      Filter
    </button>
  </div>

  <!-- Results grid -->
  <div class="manga-grid">
    <?php foreach ($results as $_ri => $manga): ?>
    <?php
      $diff = time() - strtotime($manga['update_at'] ?? '');
      if ($diff < 60)         $ago = $diff . 's ago';
      elseif ($diff < 3600)   $ago = floor($diff/60) . 'm ago';
      elseif ($diff < 86400)  $ago = floor($diff/3600) . 'h ago';
      elseif ($diff < 604800) $ago = floor($diff/86400) . 'd ago';
      else                    $ago = floor($diff/604800) . 'w ago';
    ?>
    <a href="/manga/<?= esc($manga['slug']) ?>" class="manga-card">
      <div class="manga-cover">
        <img src="<?= esc(manga_cover_url($manga)) ?>" alt="<?= esc($manga['name']) ?>" <?= $_ri < 6 ? 'loading="eager"' : 'loading="lazy" decoding="async"' ?>>
        <?php if (($manga['chapter_1'] ?? 0) > 0): ?>
        <span class="ch-badge">Ch. <?= rtrim(rtrim(number_format((float)$manga['chapter_1'], 1), '0'), '.') ?></span>
        <?php endif; ?>
      </div>
      <div class="manga-name"><?= esc($manga['name']) ?></div>
      <div class="manga-ch">
        <?php if (($manga['chapter_1'] ?? 0) > 0): ?>
        <span>Chap <?= rtrim(rtrim(number_format((float)$manga['chapter_1'], 1), '0'), '.') ?></span>
        <?php endif; ?>
        <span class="manga-time"><?= $ago ?></span>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if (isset($pager)): ?>
  <?= $pager->links('default', 'mangahub_full') ?>
  <?php endif; ?>
</div>
</main>

<script>
(function(){
  var params = new URLSearchParams(window.location.search);
  var searchInput  = document.getElementById('searchInput');
  var sortSelect   = document.getElementById('sortSelect');
  var genreSelect  = document.getElementById('genreSelect');
  var statusSelect = document.getElementById('statusSelect');
  var filterBtn    = document.getElementById('filterBtn');

  // Set initial values from URL params
  var currentSort = params.get('sort') || '-updated_at';
  for (var i = 0; i < sortSelect.options.length; i++) {
    if (sortSelect.options[i].value === currentSort) {
      sortSelect.selectedIndex = i;
      break;
    }
  }

  var currentGenre = params.get('genre') || params.get('filter[accept_genres]') || '';
  for (var i = 0; i < genreSelect.options.length; i++) {
    if (genreSelect.options[i].value === currentGenre) {
      genreSelect.selectedIndex = i;
      break;
    }
  }

  var currentStatus = params.get('status') || '';
  for (var i = 0; i < statusSelect.options.length; i++) {
    if (statusSelect.options[i].value === currentStatus) {
      statusSelect.selectedIndex = i;
      break;
    }
  }

  function applyFilter() {
    var q = searchInput.value.trim();
    var s = sortSelect.value;
    var g = genreSelect.value;
    var st = statusSelect.value;
    var parts = [];
    if (q) parts.push('filter[name]=' + encodeURIComponent(q));
    if (s && s !== '-updated_at') parts.push('sort=' + encodeURIComponent(s));
    if (g) parts.push('genre=' + encodeURIComponent(g));
    if (st) parts.push('status=' + encodeURIComponent(st));
    window.location.href = '/search' + (parts.length ? '?' + parts.join('&') : '');
  }

  filterBtn.addEventListener('click', applyFilter);
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') applyFilter();
  });
})();
</script>
<?= $this->endSection() ?>
