<?= $this->extend('themes/comixx/layouts/main') ?>

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
      border-radius: 6px;
      padding: 0 14px;
      height: 44px;
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
      padding: 0 20px;
      height: 44px;
      border-radius: 6px;
      white-space: nowrap;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-advanced-filters:hover {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    /* Filters Panel */
    .search-filters {
      display: none;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 20px;
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
      border-radius: 5px;
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 13px;
      padding: 8px 10px;
      outline: none;
      cursor: pointer;
      appearance: auto;
      width: 100%;
    }

    .filter-group select:focus,
    .filter-group input[type="number"]:focus {
      border-color: var(--accent-blue);
    }

    .filter-year-row {
      display: flex;
      gap: 6px;
      align-items: center;
    }

    .filter-year-row select {
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 5px;
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 13px;
      padding: 8px 10px;
      outline: none;
      cursor: pointer;
      flex: 1;
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
      background: var(--bg-card);
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
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .btn-lucky {
      background: var(--bg-card);
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
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .btn-apply {
      background: var(--accent-blue);
      border: none;
      color: #0d1512;
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
      background: #2bc48d;
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
      color: var(--accent-blue);
      border-color: var(--accent-blue);
      background: rgba(52, 211, 153, 0.1);
    }

    .view-toggle-btn:hover:not(.active) {
      color: var(--text-secondary);
      background: var(--bg-card-hover);
    }

    /* Results Grid */
    .results-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 16px 14px;
    }

    .result-card {
      cursor: pointer;
      transition: transform 0.2s;
    }

    .result-card:hover {
      transform: translateY(-4px);
    }

    .result-card-image {
      position: relative;
      aspect-ratio: 3/4;
      border-radius: 6px;
      overflow: hidden;
      background: var(--bg-card);
      margin-bottom: 6px;
    }

    .result-card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s;
    }

    .result-card:hover .result-card-image img {
      transform: scale(1.05);
    }

    .result-card-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 11px;
      color: var(--text-muted);
      margin-bottom: 4px;
    }

    .result-card-meta .ch-tag {
      color: var(--accent-blue);
      font-weight: 600;
    }

    .result-card-title {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
      line-height: 1.3;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* List detail info - hidden in grid view */
    .result-card-detail {
      display: none;
    }

    /* Results List View */
    .results-grid.list-view {
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }

    .results-grid.list-view .result-card {
      display: flex;
      align-items: flex-start;
      gap: 18px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 14px 18px;
    }

    .results-grid.list-view .result-card:hover {
      transform: none;
      background: var(--bg-card-hover);
    }

    .results-grid.list-view .result-card-image {
      width: 100px;
      min-width: 100px;
      aspect-ratio: 3/4;
      margin-bottom: 0;
      border-radius: 6px;
    }

    .results-grid.list-view .result-card-detail {
      display: flex;
      flex-direction: column;
      flex: 1;
      min-width: 0;
      gap: 6px;
    }

    .results-grid.list-view .result-card-meta,
    .results-grid.list-view .result-card-title {
      display: none;
    }

    .result-card-detail-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text-primary);
      line-height: 1.3;
    }

    .result-card-detail-tags {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      font-size: 11px;
      font-weight: 600;
      color: var(--accent-blue);
      letter-spacing: 0.5px;
    }

    .result-card-detail-tags span {
      display: inline-flex;
      align-items: center;
      gap: 3px;
    }

    .result-card-detail-tags .tag-muted {
      color: var(--text-muted);
    }

    .result-card-detail-desc {
      font-size: 12px;
      color: var(--text-secondary);
      line-height: 1.6;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      margin-top: 4px;
    }

    .result-card-detail-time {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    /* Pagination */
    .search-pagination {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      margin-top: 36px;
    }

    .page-btn {
      min-width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 5px;
      color: var(--text-secondary);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      padding: 0 6px;
    }

    .page-btn:hover:not(.active):not(:disabled) {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .page-btn.active {
      background: var(--accent-blue);
      border-color: var(--accent-blue);
      color: #0d1512;
    }

    .page-btn:disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }

    /* ===== Responsive ===== */
    @media (max-width: 1200px) {
      .results-grid {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    @media (max-width: 1024px) {
      .results-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 768px) {
      .search-page {
        padding-top: 16px;
        padding-bottom: 36px;
      }

      .search-main-bar {
        gap: 8px;
      }

      .btn-advanced-filters {
        padding: 0 14px;
        font-size: 11px;
      }

      .filters-row {
        flex-direction: column;
        gap: 10px;
      }

      .filter-group {
        min-width: 100%;
      }

      .filter-year-group {
        min-width: 100% !important;
      }

      .filters-actions {
        flex-direction: column;
        gap: 8px;
      }

      .filters-actions button {
        width: 100%;
        text-align: center;
      }

      .btn-apply {
        margin-left: 0;
      }

      .results-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 10px;
      }

      .result-card-title {
        font-size: 0.8rem;
      }

      .result-card-meta {
        font-size: 10px;
      }

      .results-grid.list-view {
        grid-template-columns: 1fr;
      }

      .results-grid.list-view .result-card-image {
        width: 72px;
        min-width: 72px;
      }

      .results-grid.list-view .result-card {
        padding: 10px 12px;
        gap: 12px;
      }

      .result-card-detail-title {
        font-size: 14px;
      }

      .result-card-detail-desc {
        -webkit-line-clamp: 2;
      }
    }

    @media (max-width: 480px) {
      .results-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 8px;
      }

      .search-main-input {
        height: 40px;
      }

      .btn-advanced-filters {
        height: 40px;
      }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
helper('text');
function comixx_search_time_ago($datetime) {
    if (empty($datetime)) return '';
    $now = new \DateTime();
    if (is_numeric($datetime)) {
        $ago = (new \DateTime())->setTimestamp((int)$datetime);
    } else {
        $ago = new \DateTime($datetime);
    }
    $diff = $now->diff($ago);

    if ($diff->y > 0) return 'hace ' . $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) return 'hace ' . $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
    if ($diff->d > 0) {
        if ($diff->d >= 7) {
            $weeks = floor($diff->d / 7);
            return 'hace ' . $weeks . ' semana' . ($weeks > 1 ? 's' : '');
        }
        return 'hace ' . $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
    }
    if ($diff->h > 0) return 'hace ' . $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    if ($diff->i > 0) return 'hace ' . $diff->i . ' min';
    return 'Ahora';
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
      <input type="text" placeholder="Buscar.." id="searchInput" value="<?= esc($currentKeyword) ?>">
    </div>
    <button class="btn-advanced-filters" id="toggleFilters">FILTROS AVANZADOS</button>
  </div>

  <!-- Advanced Filters Panel -->
  <div class="search-filters" id="filtersPanel">
    <div class="filters-row">
      <div class="filter-group">
        <label>Ordenar Por</label>
        <select id="filterSort">
          <option value="">Cualquiera</option>
          <option value="-updated_at">Última Actualización</option>
          <option value="-views">Más Popular</option>
          <option value="name">A-Z</option>
          <option value="-name">Z-A</option>
          <option value="-created_at">Más Recientes</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Tipos</label>
        <select id="filterType">
          <option value="">Cualquiera</option>
          <option value="manga">Manga</option>
          <option value="manhwa">Manhwa</option>
          <option value="manhua">Manhua</option>
          <option value="webtoon">Webtoon</option>
          <option value="one-shot">One-shot</option>
          <option value="doujinshi">Doujinshi</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Géneros</label>
        <select id="filterGenre">
          <option value="">Cualquiera</option>
          <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= esc($cat['slug']) ?>"><?= esc($cat['name']) ?></option>
          <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
      <div class="filter-group">
        <label>Estado</label>
        <select id="filterStatus">
          <option value="">Cualquiera</option>
          <option value="1">En Curso</option>
          <option value="2">Completado</option>
          <option value="3">En Pausa</option>
          <option value="4">Cancelado</option>
        </select>
      </div>
    </div>

    <div class="filters-actions">
      <button class="btn-reset" id="resetFilters">LIMPIAR FILTROS</button>
      <button class="btn-lucky" id="luckyBtn"><i class="fas fa-dice"></i> TENGO SUERTE</button>
      <button class="btn-apply" id="applyFilter">APLICAR FILTRO</button>
    </div>
  </div>

  <!-- Results Header -->
  <div class="results-header">
    <div class="results-count">
      <?php if (!empty($currentKeyword)): ?>
      <strong><?= $resultCount ?></strong> resultados para "<?= esc($currentKeyword) ?>"
      <?php else: ?>
      <strong><?= $resultCount ?></strong> Elementos
      <?php endif; ?>
    </div>
    <div class="view-toggle">
      <button class="view-toggle-btn" data-view="list" title="Vista lista"><i class="fas fa-list"></i></button>
      <button class="view-toggle-btn active" data-view="grid" title="Vista cuadrícula"><i class="fas fa-th"></i></button>
    </div>
  </div>

  <!-- Results Grid -->
  <div class="results-grid" id="resultsGrid">
    <?php if (!empty($results)): ?>
    <?php foreach ($results as $result): ?>
    <a href="<?= base_url('manga/' . esc($result['slug'])) ?>" class="result-card">
      <div class="result-card-image"><img src="<?= manga_cover_url($result) ?>" alt="<?= esc($result['name']) ?>" loading="lazy"></div>
      <div class="result-card-meta">
        <?php if (!empty($result['chapter_1'])): ?>
        <span class="ch-tag">Ch. <?= esc($result['chapter_1']) ?></span>
        <?php endif; ?>
        <?php if (!empty($result['update_at'])): ?>
        <span><?= comixx_search_time_ago($result['update_at']) ?></span>
        <?php endif; ?>
      </div>
      <div class="result-card-title"><?= esc($result['name']) ?></div>
      <div class="result-card-detail">
        <div class="result-card-detail-title"><?= esc($result['name']) ?></div>
        <div class="result-card-detail-tags">
          <?php if (!empty($result['chapter_1'])): ?>
          <span>Ch. <?= esc($result['chapter_1']) ?></span>
          <?php endif; ?>
        </div>
        <div class="result-card-detail-desc"><?= esc(character_limiter(strip_tags($result['summary'] ?? ''), 150)) ?></div>
        <?php if (!empty($result['update_at'])): ?>
        <div class="result-card-detail-time"><?= comixx_search_time_ago($result['update_at']) ?></div>
        <?php endif; ?>
      </div>
    </a>
    <?php endforeach; ?>
    <?php else: ?>
    <div class="no-results">
      <p>No se encontraron resultados. Intenta ajustar tu búsqueda o filtros.</p>
    </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($pager)): ?>
  <div class="search-pagination">
    <?= $pager->links('default', 'comixx_full') ?>
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
      toggleBtn.textContent = filtersPanel.classList.contains('open') ? 'OCULTAR FILTROS' : 'FILTROS AVANZADOS';
    });
  }

  // --- Read URL params and set filter values ---
  var params = new URLSearchParams(window.location.search);

  var searchInput = document.getElementById('searchInput');
  var filterSort = document.getElementById('filterSort');
  var filterGenre = document.getElementById('filterGenre');
  var filterStatus = document.getElementById('filterStatus');
  var filterType = document.getElementById('filterType');

  if (params.get('sort') && filterSort) filterSort.value = params.get('sort');
  if (params.get('genre') && filterGenre) filterGenre.value = params.get('genre');
  if (params.get('status') && filterStatus) filterStatus.value = params.get('status');
  if (params.get('type') && filterType) filterType.value = params.get('type');

  // Open filters panel if any filter is active
  if (params.get('sort') || params.get('genre') || params.get('status') || params.get('type')) {
    if (filtersPanel) {
      filtersPanel.classList.add('open');
      if (toggleBtn) toggleBtn.textContent = 'OCULTAR FILTROS';
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

    if (keyword) url.searchParams.set('filter[name]', keyword);
    if (sort) url.searchParams.set('sort', sort);
    if (genre) url.searchParams.set('genre', genre);
    if (status) url.searchParams.set('status', status);
    if (type) url.searchParams.set('type', type);

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
