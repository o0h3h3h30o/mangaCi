<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('head_extra') ?>
<style>
    /* ===== Search Page Styles ===== */
    .search-page {
      padding-top: 24px;
      padding-bottom: 48px;
    }

    /* Search Input Bar */
    .search-main-bar {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }

    .search-main-input {
      flex: 1;
      display: flex;
      align-items: center;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 28px;
      padding: 0 20px;
      height: 48px;
      gap: 10px;
    }

    .search-main-input i {
      color: var(--text-muted);
      font-size: 16px;
      flex-shrink: 0;
    }

    .search-main-input input {
      flex: 1;
      background: transparent;
      border: none;
      outline: none;
      color: var(--text-primary);
      font-family: var(--font);
      font-size: 14px;
    }

    .search-main-input input::placeholder {
      color: var(--text-muted);
    }

    .btn-advanced-filters {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.5px;
      padding: 0 24px;
      height: 48px;
      border-radius: 28px;
      white-space: nowrap;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-advanced-filters:hover {
      background: var(--bg-input);
      color: var(--text-primary);
    }

    /* Filters Panel */
    .search-filters {
      display: none;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 20px;
    }

    .search-filters.open {
      display: block;
    }

    .filters-row {
      display: flex;
      gap: 14px;
      margin-bottom: 14px;
      flex-wrap: wrap;
    }

    .filters-row:last-child {
      margin-bottom: 0;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
      min-width: 140px;
    }

    .filter-group label {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text-muted);
      text-transform: uppercase;
    }

    .filter-group select,
    .filter-group input[type="number"] {
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 14px;
      padding: 10px 14px;
      height: 44px;
      outline: none;
      cursor: pointer;
      width: 100%;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    .filter-group select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236e6890' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      padding-right: 36px;
    }

    .filter-group select:focus,
    .filter-group input[type="number"]:focus {
      border-color: var(--accent);
    }

    /* Filter Action Buttons */
    .filters-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-top: 14px;
      flex-wrap: wrap;
    }

    .btn-reset {
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 600;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      letter-spacing: 0.3px;
      transition: all 0.2s;
    }

    .btn-reset:hover {
      background: var(--bg-card);
      color: var(--text-primary);
    }

    .btn-lucky {
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 600;
      padding: 10px 24px;
      border-radius: 6px;
      cursor: pointer;
      letter-spacing: 0.3px;
      transition: all 0.2s;
    }

    .btn-lucky:hover {
      background: var(--bg-card);
      color: var(--text-primary);
    }

    .btn-apply {
      background: var(--accent);
      border: none;
      color: white;
      font-family: var(--font);
      font-size: 13px;
      font-weight: 700;
      padding: 10px 28px;
      border-radius: 6px;
      cursor: pointer;
      letter-spacing: 0.5px;
      margin-left: auto;
      transition: all 0.2s;
    }

    .btn-apply:hover {
      background: var(--accent-hover);
    }

    /* Results Header */
    .results-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .results-count {
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .results-count strong {
      color: var(--text-primary);
    }

    .view-toggle {
      display: flex;
      gap: 4px;
    }

    .view-toggle-btn {
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 5px;
      color: var(--text-muted);
      font-size: 14px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .view-toggle-btn.active {
      color: var(--accent);
      border-color: var(--accent);
      background: rgba(168, 85, 247, 0.1);
    }

    .view-toggle-btn:hover:not(.active) {
      color: var(--text-secondary);
      background: var(--bg-input);
    }

    /* Results use .grid-container + .card from manhwaread.css */
    /* Pagination uses .pagination from manhwaread.css */

    .no-results {
      grid-column: 1 / -1;
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .search-page { padding-top: 16px; padding-bottom: 36px; }
      .search-main-bar { gap: 8px; }
      .btn-advanced-filters { padding: 0 14px; font-size: 11px; }
      .filters-row { flex-direction: column; gap: 10px; }
      .filter-group { min-width: 100%; }
      .filters-actions { flex-direction: column; gap: 8px; }
      .filters-actions button { width: 100%; text-align: center; }
      .btn-apply { margin-left: 0; }
    }

    @media (max-width: 480px) {
      .search-main-input { height: 40px; }
      .btn-advanced-filters { height: 40px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
helper('text');
function manhwaread_search_time_ago($datetime) {
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

$currentKeyword = $_GET['filter']['name'] ?? '';
$currentSort = $_GET['sort'] ?? '-updated_at';
$currentGenre = $_GET['genre'] ?? ($_GET['filter']['accept_genres'] ?? '');
$currentStatus = $_GET['status'] ?? '';
$resultCount = count($results ?? []);
?>

<div class="container search-page">

  <!-- Main Search Bar -->
  <div class="search-main-bar">
    <div class="search-main-input">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="<?= lang('ComixxSearch.search') ?>" id="searchInput" value="<?= esc($currentKeyword) ?>">
    </div>
    <button class="btn-advanced-filters" id="toggleFilters"><?= lang('ComixxSearch.advanced_filters') ?></button>
  </div>

  <!-- Advanced Filters Panel -->
  <div class="search-filters" id="filtersPanel">
    <div class="filters-row">
      <div class="filter-group">
        <label><?= lang('ComixxSearch.sort_by') ?></label>
        <select id="filterSort">
          <option value=""><?= lang('ComixxSearch.any') ?></option>
          <option value="-updated_at"><?= lang('ComixxSearch.latest_update') ?></option>
          <option value="-views"><?= lang('ComixxSearch.most_popular') ?></option>
          <option value="name">A-Z</option>
          <option value="-name">Z-A</option>
          <option value="-created_at"><?= lang('ComixxSearch.newest_added') ?></option>
        </select>
      </div>
      <div class="filter-group">
        <label><?= lang('ComixxSearch.types') ?></label>
        <select id="filterType">
          <option value=""><?= lang('ComixxSearch.any') ?></option>
          <option value="manga">Manga</option>
          <option value="manhwa">Manhwa</option>
          <option value="manhua">Manhua</option>
          <option value="webtoon">Webtoon</option>
          <option value="one-shot">One-shot</option>
          <option value="doujinshi">Doujinshi</option>
        </select>
      </div>
      <div class="filter-group">
        <label><?= lang('ComixxSearch.genres') ?></label>
        <select id="filterGenre">
          <option value=""><?= lang('ComixxSearch.any') ?></option>
          <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= esc($cat['slug']) ?>"><?= esc($cat['name']) ?></option>
          <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="filter-group">
        <label><?= lang('ComixxSearch.release_status') ?></label>
        <select id="filterStatus">
          <option value=""><?= lang('ComixxSearch.any') ?></option>
          <option value="1"><?= lang('ComixxSearch.ongoing') ?></option>
          <option value="2"><?= lang('ComixxSearch.completed') ?></option>
          <option value="3"><?= lang('ComixxSearch.hiatus') ?></option>
          <option value="4"><?= lang('ComixxSearch.cancelled') ?></option>
        </select>
      </div>
      <div class="filter-group">
        <label>18+</label>
        <select id="filterCaution">
          <option value=""><?= lang('ComixxSearch.any') ?></option>
          <option value="0"><?= lang('ComixxSearch.not_18') ?></option>
          <option value="1"><?= lang('ComixxSearch.only_18') ?></option>
        </select>
      </div>
    </div>

    <div class="filters-actions">
      <button class="btn-reset" id="resetFilters"><?= lang('ComixxSearch.reset_filters') ?></button>
      <button class="btn-lucky" id="luckyBtn"><i class="fas fa-dice"></i> <?= lang('ComixxSearch.feeling_lucky') ?></button>
      <button class="btn-apply" id="applyFilter"><?= lang('ComixxSearch.apply_filter') ?></button>
    </div>
  </div>

  <!-- Results Header -->
  <div class="results-header">
    <div class="results-count">
      <?php if (!empty($currentKeyword)): ?>
      <strong><?= $resultCount ?></strong> <?= lang('ComixxSearch.results_for') ?> "<?= esc($currentKeyword) ?>"
      <?php else: ?>
      <strong><?= $resultCount ?></strong> <?= lang('ComixxSearch.items') ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Results Grid (same layout as home page) -->
  <?php
    $statusMap = [1 => lang('ComixxManga.ongoing'), 2 => lang('ComixxManga.completed')];
  ?>
  <div class="grid-container" id="resultsGrid">
    <?php if (!empty($results)): ?>
    <?php foreach ($results as $manga): ?>
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
            <span class="time"><?= manhwaread_search_time_ago($manga['update_at']) ?></span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="no-results">
      <p><?= lang('ComixxSearch.no_results') ?></p>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($pager)): ?>
  <div class="pagination">
    <?= $pager->links('default', 'manhwaread') ?>
  </div>
  <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

  // --- Toggle Advanced Filters ---
  var toggleBtn = document.getElementById('toggleFilters');
  var filtersPanel = document.getElementById('filtersPanel');
  if (toggleBtn && filtersPanel) {
    toggleBtn.addEventListener('click', function() {
      filtersPanel.classList.toggle('open');
      toggleBtn.textContent = filtersPanel.classList.contains('open') ? '<?= lang('ComixxSearch.hide_filters') ?>' : '<?= lang('ComixxSearch.advanced_filters') ?>';
    });
  }

  // --- Read URL params and set filter values ---
  var params = new URLSearchParams(window.location.search);

  var searchInput = document.getElementById('searchInput');
  var filterSort = document.getElementById('filterSort');
  var filterGenre = document.getElementById('filterGenre');
  var filterStatus = document.getElementById('filterStatus');
  var filterType = document.getElementById('filterType');
  var filterCaution = document.getElementById('filterCaution');

  if (params.get('sort') && filterSort) filterSort.value = params.get('sort');
  if (params.get('genre') && filterGenre) filterGenre.value = params.get('genre');
  if (params.get('status') && filterStatus) filterStatus.value = params.get('status');
  if (params.get('type') && filterType) filterType.value = params.get('type');
  if (params.has('caution') && filterCaution) filterCaution.value = params.get('caution');

  // Open filters panel if any filter is active
  if (params.get('sort') || params.get('genre') || params.get('status') || params.get('type') || params.has('caution')) {
    if (filtersPanel) {
      filtersPanel.classList.add('open');
      if (toggleBtn) toggleBtn.textContent = '<?= lang('ComixxSearch.hide_filters') ?>';
    }
  }

  // --- Apply Filter ---
  function applyFilter() {
    var url = new URL(window.location.href.split('?')[0]);
    var keyword = searchInput ? searchInput.value.trim() : '';
    var sort = filterSort ? filterSort.value : '';
    var genre = filterGenre ? filterGenre.value : '';
    var status = filterStatus ? filterStatus.value : '';
    var type = filterType ? filterType.value : '';
    var caution = filterCaution ? filterCaution.value : '';

    if (keyword) url.searchParams.set('filter[name]', keyword);
    if (sort) url.searchParams.set('sort', sort);
    if (genre) url.searchParams.set('genre', genre);
    if (status) url.searchParams.set('status', status);
    if (type) url.searchParams.set('type', type);
    if (caution !== '') url.searchParams.set('caution', caution);

    window.location.href = url.toString();
  }

  var applyBtn = document.getElementById('applyFilter');
  if (applyBtn) {
    applyBtn.addEventListener('click', applyFilter);
  }

  // Search on Enter key
  if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') applyFilter();
    });
  }

  // --- Reset Filters ---
  var resetBtn = document.getElementById('resetFilters');
  if (resetBtn) {
    resetBtn.addEventListener('click', function() {
      filtersPanel.querySelectorAll('select').forEach(function(sel) { sel.selectedIndex = 0; });
      filtersPanel.querySelectorAll('input[type="number"]').forEach(function(inp) { inp.value = ''; });
      if (searchInput) searchInput.value = '';
      window.location.href = window.location.pathname;
    });
  }

  // --- I'm Feeling Lucky ---
  var luckyBtn = document.getElementById('luckyBtn');
  if (luckyBtn) {
    luckyBtn.addEventListener('click', function() {
      var url = new URL(window.location.href.split('?')[0]);
      url.searchParams.set('sort', '-random');
      window.location.href = url.toString();
    });
  }

  // --- View Toggle (Grid / List) ---
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
