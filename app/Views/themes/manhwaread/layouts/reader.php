<!DOCTYPE html>
<html lang="en">

<head itemscope itemtype="http://schema.org/WebPage">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <?php $_pt = trim($title ?? ''); $_st = site_setting('site_title', 'ManhwaRead'); ?>
  <title><?= $_pt ? esc($_pt) . ' - ' . esc($_st) : esc($_st) ?></title>

  <?php $_cdn = rtrim(env('CDN_COVER_URL', ''), '/'); ?>
  <?php if ($_cdn): ?>
  <?php $_cdnOrigin = parse_url($_cdn, PHP_URL_SCHEME) . '://' . parse_url($_cdn, PHP_URL_HOST); ?>
  <link rel="preconnect" href="<?= esc($_cdnOrigin) ?>">
  <link rel="dns-prefetch" href="<?= esc($_cdnOrigin) ?>">
  <?php endif; ?>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Manhwaread stylesheet -->
  <link rel="stylesheet" href="<?= base_url('css/manhwaread.css') ?>?v=<?= time() ?>">

  <link rel="canonical" href="<?= current_url() ?>">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

  <?php
    $_desc = !empty($description) ? $description : site_setting('meta_description');
    $_ogimg = !empty($og_image) ? $og_image : base_url('dcncc.jpg');
  ?>
  <meta name="description" content="<?= esc($_desc) ?>">
  <meta property="og:title" content="<?= esc($_pt ?: $_st) ?>">
  <meta property="og:description" content="<?= esc($_desc) ?>">
  <meta property="og:image" content="<?= esc($_ogimg) ?>">
  <meta property="og:url" content="<?= current_url() ?>">

  <?= $this->renderSection('head_extra') ?>

  <?php $_ga = site_setting('ga_id'); if ($_ga): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($_ga) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= esc($_ga) ?>');</script>
  <?php endif; ?>

  <script>
  (function(){
    var theme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', theme);
  })();
  </script>
</head>

<body class="reader-body">

  <?php
    $_mangaTitle = esc($manga['name'] ?? '');
    $_mangaSlug  = esc($manga['slug'] ?? '');
    $_chapTitle  = esc($chapTitle ?? $chapter['name'] ?? '');
    $_prevUrl    = !empty($prevChapter) ? '/manga/' . $_mangaSlug . '/' . esc($prevChapter['slug']) : '';
    $_nextUrl    = !empty($nextChapter) ? '/manga/' . $_mangaSlug . '/' . esc($nextChapter['slug']) : '';
  ?>

  <!-- Reader Header -->
  <header class="reader-header" id="readerHeader">
    <div class="reader-header-left">
      <a href="/manga/<?= $_mangaSlug ?>" class="reader-back"><i class="fas fa-chevron-left"></i></a>
      <div class="reader-title-info">
        <a href="/manga/<?= $_mangaSlug ?>" class="reader-manga-title" style="text-decoration:none;color:inherit;"><?= $_mangaTitle ?></a>
        <span class="reader-chapter-name"><?= $_chapTitle ?></span>
      </div>
    </div>
    <div class="reader-header-right">
      <button class="reader-icon-btn" id="settingsBtn" aria-label="<?= lang('Comixx.settings') ?>"><i class="fas fa-ellipsis-v"></i></button>
    </div>
  </header>

  <!-- Settings Panel -->
  <div class="reader-settings" id="settingsPanel">
    <div class="settings-group">
      <span class="settings-label"><?= lang('Comixx.reading_mode') ?></span>
      <div class="settings-options">
        <button class="setting-btn active" data-mode="longstrip"><?= lang('ComixxManga.long_strip') ?></button>
        <button class="setting-btn" data-mode="single"><?= lang('ComixxManga.single_page') ?></button>
      </div>
    </div>
    <div class="settings-group">
      <span class="settings-label"><?= lang('Comixx.image_width') ?></span>
      <div class="settings-options">
        <button class="setting-btn active" data-width="60">60%</button>
        <button class="setting-btn" data-width="80">80%</button>
        <button class="setting-btn" data-width="100">100%</button>
      </div>
    </div>
  </div>

  <!-- Reader Content -->
  <?= $this->renderSection('content') ?>

  <!-- Bottom Navigation Bar -->
  <nav class="reader-bottom-bar" id="readerBottomBar">
    <?php if ($_prevUrl): ?>
    <a href="<?= $_prevUrl ?>" class="reader-nav-btn" id="prevChapter"><i class="fas fa-arrow-left"></i></a>
    <?php else: ?>
    <button class="reader-nav-btn" disabled><i class="fas fa-arrow-left"></i></button>
    <?php endif; ?>

    <button class="reader-chapter-select" id="chapterSelect">
      <i class="fas fa-list"></i>
      <span><?= $_chapTitle ?></span>
    </button>

    <?php if ($_nextUrl): ?>
    <a href="<?= $_nextUrl ?>" class="reader-nav-btn" id="nextChapter"><i class="fas fa-arrow-right"></i></a>
    <?php else: ?>
    <button class="reader-nav-btn" disabled><i class="fas fa-arrow-right"></i></button>
    <?php endif; ?>
  </nav>

  <!-- Chapter Select Modal -->
  <div class="chapter-modal-overlay" id="chapterModal">
    <div class="chapter-modal">
      <div class="chapter-modal-header">
        <h3><?= lang('Comixx.select_chapter') ?></h3>
        <button class="chapter-modal-close" id="closeModal"><i class="fas fa-times"></i></button>
      </div>
      <div class="chapter-modal-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="<?= lang('Comixx.search_chapter') ?>" id="chapterSearch">
      </div>
      <div class="chapter-modal-list" id="chapterModalList">
        <?php if (!empty($chapters)): ?>
          <?php foreach ($chapters as $_ch): ?>
          <a href="/manga/<?= $_mangaSlug ?>/<?= esc($_ch['slug']) ?>"
             class="chapter-modal-item<?= ($_ch['slug'] ?? '') === ($chapter['slug'] ?? '') ? ' active' : '' ?>">
            <span><?= esc($_ch['name'] ?? '') ?></span>
            <span class="chapter-modal-date"><?= !empty($_ch['created_at']) ? date('M d, Y', strtotime($_ch['created_at'])) : '' ?></span>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
  // Header and bottom bar always visible

  // Settings toggle
  document.getElementById('settingsBtn').addEventListener('click', function(e){
    e.stopPropagation();
    document.getElementById('settingsPanel').classList.toggle('open');
  });

  // Reading mode: longstrip / single
  var currentMode = 'longstrip';
  var currentPage = 0;
  var totalImgPages = 0;

  function applyMode(mode) {
    currentMode = mode;
    var images = document.getElementById('readerImages');
    if (!images) return;
    var imgs = images.querySelectorAll('img');
    totalImgPages = imgs.length;

    if (mode === 'single') {
      images.classList.add('single-page-mode');
      currentPage = 0;
      imgs.forEach(function(img, i) { img.style.display = i === 0 ? 'block' : 'none'; });
      window.scrollTo(0, 0);
    } else {
      images.classList.remove('single-page-mode');
      imgs.forEach(function(img) { img.style.display = 'block'; });
    }
    localStorage.setItem('reader-mode', mode);
  }

  function showPage(idx) {
    var images = document.getElementById('readerImages');
    if (!images) return;
    var imgs = images.querySelectorAll('img');
    if (idx < 0 || idx >= imgs.length) return;
    currentPage = idx;
    imgs.forEach(function(img, i) { img.style.display = i === idx ? 'block' : 'none'; });
    // Lazy load
    if (imgs[idx].dataset.src && !imgs[idx].src.includes(imgs[idx].dataset.src)) {
      imgs[idx].src = imgs[idx].dataset.src;
    }
    images.scrollIntoView({behavior: 'instant', block: 'start'});
  }

  document.querySelectorAll('.setting-btn[data-mode]').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.setting-btn[data-mode]').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      applyMode(btn.dataset.mode);
    });
  });

  // Image width buttons
  document.querySelectorAll('.setting-btn[data-width]').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('.setting-btn[data-width]').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      var images = document.getElementById('readerImages');
      if(images) images.style.maxWidth = btn.dataset.width + '%';
      localStorage.setItem('reader-width', btn.dataset.width);
    });
  });

  // Restore saved reader settings
  (function(){
    var savedMode = localStorage.getItem('reader-mode') || 'longstrip';
    var savedWidth = localStorage.getItem('reader-width');
    document.querySelectorAll('.setting-btn[data-mode]').forEach(function(b){
      b.classList.toggle('active', b.dataset.mode === savedMode);
    });
    applyMode(savedMode);
    if(savedWidth){
      document.querySelectorAll('.setting-btn[data-width]').forEach(function(b){
        b.classList.toggle('active', b.dataset.width === savedWidth);
      });
      var images = document.getElementById('readerImages');
      if(images) images.style.maxWidth = savedWidth + '%';
    }
  })();

  // Keyboard navigation for single page mode
  document.addEventListener('keydown', function(e) {
    if (currentMode !== 'single') return;
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    if (e.key === 'ArrowLeft') { showPage(currentPage - 1); e.preventDefault(); }
    if (e.key === 'ArrowRight') { showPage(currentPage + 1); e.preventDefault(); }
  });

  // Click on image to go next page in single mode
  var readerImages = document.getElementById('readerImages');
  if (readerImages) {
    readerImages.addEventListener('click', function(e) {
      if (currentMode !== 'single') return;
      if (e.target.tagName !== 'IMG') return;
      var rect = e.target.getBoundingClientRect();
      var x = e.clientX - rect.left;
      if (x < rect.width / 3) { showPage(currentPage - 1); }
      else { showPage(currentPage + 1); }
    });
  }

  // Chapter modal
  document.getElementById('chapterSelect').addEventListener('click', function(e){
    e.stopPropagation();
    document.getElementById('chapterModal').classList.add('open');
    document.body.style.overflow = 'hidden';
  });

  document.getElementById('closeModal').addEventListener('click', function(){
    document.getElementById('chapterModal').classList.remove('open');
    document.body.style.overflow = '';
  });

  document.getElementById('chapterModal').addEventListener('click', function(e){
    if(e.target === e.currentTarget){
      e.currentTarget.classList.remove('open');
      document.body.style.overflow = '';
    }
  });

  // Chapter search filter
  document.getElementById('chapterSearch').addEventListener('input', function(e){
    var query = e.target.value.toLowerCase();
    document.querySelectorAll('.chapter-modal-item').forEach(function(item){
      item.style.display = item.textContent.toLowerCase().includes(query) ? '' : 'none';
    });
  });

  // Scroll progress
  window.addEventListener('scroll', function(){
    var scrollTop = window.scrollY;
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    var progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    document.documentElement.style.setProperty('--scroll-progress', progress + '%');
  });

  // Theme
  var theme = localStorage.getItem('theme') || 'dark';
  document.documentElement.setAttribute('data-theme', theme);
  </script>
</body>
</html>
