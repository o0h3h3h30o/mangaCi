  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <a href="/" class="logo">
      <?php $_logo = site_setting('site_logo'); ?>
      <?php if ($_logo): ?>
      <img src="<?= esc($_logo) ?>" alt="<?= esc(site_setting('site_title', 'ManhwaRead')) ?>" class="logo-img" style="height:36px">
      <?php else: ?>
      <div class="logo-icon"><i class="fas fa-circle-notch"></i></div>
      <?php endif; ?>
      <span class="logo-text"><?= esc(site_setting('site_title', 'ManhwaRead')) ?></span>
    </a>
    <nav class="nav-menu">
      <?php
        $_uri = service('uri')->getPath();
        $_isHome = ($_uri === '/' || $_uri === '');
        $_isSearch = str_starts_with($_uri, '/search');
        $_isTop = str_starts_with($_uri, '/search') && str_contains($_uri, 'sort=-views');
      ?>
      <a href="/" class="nav-item<?= $_isHome ? ' active' : '' ?>">
        <i class="fas fa-home"></i>
        <span><?= lang('Comixx.home') ?></span>
      </a>
      <a href="/search" class="nav-item<?= ($_isSearch && !$_isTop) ? ' active' : '' ?>">
        <i class="fas fa-th-large"></i>
        <span><?= lang('Comixx.explore') ?></span>
      </a>
      <a href="/random" class="nav-item">
        <i class="fas fa-random"></i>
        <span><?= lang('Comixx.random') ?></span>
      </a>
      <a href="/search?sort=-views" class="nav-item<?= $_isTop ? ' active' : '' ?>">
        <i class="fas fa-trophy"></i>
        <span><?= lang('Comixx.popular') ?></span>
      </a>

      <?php if (!empty($categories)): ?>
      <a href="javascript:void(0)" class="nav-item" id="genreDropdownTrigger">
        <i class="fas fa-list"></i>
        <span><?= lang('Comixx.genres') ?></span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </a>
      <div class="nav-group-list" id="genreDropdownList">
        <?php foreach ($categories as $cat): ?>
        <a href="/search?genre=<?= esc($cat['slug'], 'url') ?>" class="nav-sub-item"><?= esc($cat['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </nav>
  </aside>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar JS handled in main.php layout -->
