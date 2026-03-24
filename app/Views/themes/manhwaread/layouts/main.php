<!DOCTYPE html>
<html lang="en">

<head itemscope itemtype="http://schema.org/WebPage">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

  <?php $_pt = trim($title ?? ''); $_st = site_setting('site_title', 'ManhwaRead'); $_hh = site_setting('home_heading', ''); ?>
  <title><?= $_pt ? esc($_pt) . ' - ' . esc($_st) : ($_hh ? esc($_hh) : esc($_st)) ?></title>

  <?php $_cdn = rtrim(env('CDN_COVER_URL', ''), '/'); ?>
  <?php if ($_cdn): ?>
  <?php $_cdnOrigin = parse_url($_cdn, PHP_URL_SCHEME) . '://' . parse_url($_cdn, PHP_URL_HOST); ?>
  <link rel="preconnect" href="<?= esc($_cdnOrigin) ?>">
  <link rel="dns-prefetch" href="<?= esc($_cdnOrigin) ?>">
  <?php endif; ?>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Manhwaread stylesheet -->
  <link rel="preload" href="<?= base_url('css/manhwaread.css') ?>?v=<?= time() ?>" as="style">
  <link rel="stylesheet" href="<?= base_url('css/manhwaread.css') ?>?v=<?= time() ?>">

  <!-- SEO meta -->
  <meta name="robots" content="index, follow">
  <meta name="Author" content="<?= esc($_st) ?>">
  <meta name="copyright" content="Copyright &copy; <?= date('Y') ?> <?= esc($_st) ?>">
  <link rel="canonical" href="<?= current_url() ?>">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">

  <?php
    $_desc = !empty($description) ? $description : site_setting('meta_description');
    $_kw   = site_setting('meta_keywords');
    $_ogimg = !empty($og_image) ? $og_image : base_url('dcncc.jpg');
  ?>
  <meta name="description" content="<?= esc($_desc) ?>">
  <?php if ($_kw): ?>
  <meta name="keywords" content="<?= esc($_kw) ?>">
  <?php endif; ?>

  <!-- Open Graph -->
  <meta property="og:title" content="<?= esc($_pt ?: $_st) ?>">
  <meta property="og:description" content="<?= esc($_desc) ?>">
  <meta property="og:image" content="<?= esc($_ogimg) ?>">
  <meta property="og:url" content="<?= current_url() ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?= esc($_st) ?>">

  <?= $this->renderSection('head_extra') ?>

  <?php $_ga = site_setting('ga_id'); if ($_ga): ?>
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($_ga) ?>"></script>
  <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?= esc($_ga) ?>');</script>
  <?php endif; ?>

  <!-- Early theme + sidebar state to prevent FOUC -->
  <script>
  (function(){
    var theme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', theme);
    if (window.innerWidth > 768 && localStorage.getItem('sidebar-collapsed') === 'true') {
      document.documentElement.classList.add('sidebar-pre-collapsed');
    }
  })();
  </script>
</head>

<body>

<?= $this->include('themes/manhwaread/components/sidebar') ?>
<?= $this->include('themes/manhwaread/components/header') ?>

  <main class="main-content">
    <div class="main-content-inner">
      <?= $this->renderSection('content') ?>
    </div>
  </main>

<?= $this->include('themes/manhwaread/components/footer') ?>

<script>
// --- Sidebar + Genre toggle ---
document.addEventListener('DOMContentLoaded', function(){
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  var toggleBtn = document.getElementById('sidebarToggle');

  if(!sidebar || !toggleBtn) return;

  // Desktop: apply saved collapsed state, then remove pre-collapsed (enable transitions)
  if(window.innerWidth > 768 && localStorage.getItem('sidebar-collapsed') === 'true'){
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
  }
  // Remove pre-collapsed class after first paint to enable transitions
  requestAnimationFrame(function(){
    document.documentElement.classList.remove('sidebar-pre-collapsed');
  });

  // Toggle click
  toggleBtn.addEventListener('click', function(e){
    e.stopPropagation();
    if(window.innerWidth <= 768){
      sidebar.classList.toggle('mobile-open');
      if(overlay) overlay.classList.toggle('active');
      document.body.style.overflow = sidebar.classList.contains('mobile-open') ? 'hidden' : '';
    } else {
      sidebar.classList.toggle('collapsed');
      document.body.classList.toggle('sidebar-collapsed');
      localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
    }
  });

  // Overlay close
  if(overlay) overlay.addEventListener('click', function(){
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  });

  // Genre dropdown toggle
  var genreTrigger = document.getElementById('genreDropdownTrigger');
  var genreList = document.getElementById('genreDropdownList');
  if(genreTrigger && genreList){
    genreTrigger.addEventListener('click', function(e){
      e.preventDefault();
      genreList.classList.toggle('open');
      var icon = genreTrigger.querySelector('.dropdown-icon');
      if(icon) icon.style.transform = genreList.classList.contains('open') ? 'rotate(180deg)' : '';
    });
  }
});

// --- Theme toggle ---
(function(){
  var btns = document.querySelectorAll('[data-theme-toggle]');
  btns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var current = document.documentElement.getAttribute('data-theme');
      var next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('theme', next);
      // Update icon
      var icon = btn.querySelector('i');
      if(icon){
        icon.className = next === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
      }
    });
  });
  // Set initial icon
  var theme = document.documentElement.getAttribute('data-theme') || 'dark';
  btns.forEach(function(btn){
    var icon = btn.querySelector('i');
    if(icon) icon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
  });
})();

// --- Search typeahead (desktop) ---
(function(){
  var input = document.getElementById('headerSearchInput');
  var dropdown = document.getElementById('headerSearchDropdown');
  if(!input || !dropdown) return;
  var timer = null;

  input.addEventListener('input', function(){
    clearTimeout(timer);
    var q = input.value.trim();
    if(q.length < 2){ dropdown.classList.remove('open'); dropdown.innerHTML = ''; return; }
    timer = setTimeout(function(){
      fetch('/api/search?q=' + encodeURIComponent(q)).then(function(r){ return r.json(); }).then(function(d){
        if(!d.results || !d.results.length){ dropdown.classList.remove('open'); return; }
        dropdown.innerHTML = d.results.slice(0,5).map(function(m){
          var chLabel = m.latest_chapter ? m.latest_chapter.name : '';
          return '<a href="/manga/' + m.slug + '" class="search-result-item">'
            + '<div class="search-result-thumb"><img src="' + (m.cover_full_url || '') + '" width="40" height="56" alt=""></div>'
            + '<div class="search-result-info"><h4>' + m.name + '</h4>' + (chLabel ? '<span>' + chLabel + '</span>' : '') + '</div>'
            + '</a>';
        }).join('');
        dropdown.classList.add('open');
      }).catch(function(){});
    }, 300);
  });

  input.addEventListener('keydown', function(e){
    if(e.key === 'Enter'){
      e.preventDefault();
      window.location.href = '/search?filter[name]=' + encodeURIComponent(input.value.trim());
    }
  });

  document.addEventListener('click', function(e){
    if(!e.target.closest('.top-search')) dropdown.classList.remove('open');
  });
})();

// --- Mobile search toggle ---
(function(){
  var openBtn = document.getElementById('mobileSearchBtn');
  var closeBtn = document.getElementById('mobileSearchCloseBtn');
  var dropdown = document.getElementById('mobileSearchDropdown');
  var input = document.getElementById('mobileSearchInput');
  var results = document.getElementById('mobileSearchResults');

  if(!openBtn || !dropdown) return;

  openBtn.addEventListener('click', function(){
    dropdown.classList.add('open');
    if(input) input.focus();
  });

  if(closeBtn) closeBtn.addEventListener('click', function(){
    dropdown.classList.remove('open');
  });

  // Search autocomplete for mobile
  if(input && results){
    var timer = null;
    input.addEventListener('input', function(){
      clearTimeout(timer);
      var q = input.value.trim();
      if(q.length < 2){ results.classList.remove('open'); results.innerHTML = ''; return; }
      timer = setTimeout(function(){
        fetch('/api/search?q=' + encodeURIComponent(q)).then(function(r){ return r.json(); }).then(function(d){
          if(!d.results || !d.results.length){ results.classList.remove('open'); return; }
          results.innerHTML = d.results.slice(0,5).map(function(m){
            var chLabel = m.latest_chapter ? m.latest_chapter.name : '';
            return '<a href="/manga/' + m.slug + '" class="search-result-item">'
              + '<div class="search-result-thumb"><img src="' + (m.cover_full_url || '') + '" width="40" height="56" alt=""></div>'
              + '<div class="search-result-info"><h4>' + m.name + '</h4>' + (chLabel ? '<span>' + chLabel + '</span>' : '') + '</div>'
              + '</a>';
          }).join('');
          results.classList.add('open');
        }).catch(function(){});
      }, 300);
    });

    input.addEventListener('keydown', function(e){
      if(e.key === 'Enter'){
        e.preventDefault();
        window.location.href = '/search?filter[name]=' + encodeURIComponent(input.value.trim());
      }
    });
  }
})();
</script>
</body>
</html>
