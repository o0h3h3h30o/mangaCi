<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Admin') ?> — Admin Panel</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <style>
    body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .nav-link { display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:6px; font-size:13px; transition:background .15s; color:#9ca3af; white-space:nowrap; overflow:hidden; }
    .nav-link:hover { background:#374151; color:#f9fafb; }
    .nav-link.active { background:#4f46e5; color:#fff; }
    .nav-link svg { flex-shrink:0; }
    #admin-sidebar { transition: transform .22s cubic-bezier(.4,0,.2,1); }
    #admin-main    { transition: margin-left .22s cubic-bezier(.4,0,.2,1); }
    #sidebar-backdrop { transition: opacity .22s; }
  </style>
  <!-- Đặt class trước khi render để tránh flash -->
  <script>
    (function(){
      var stored = localStorage.getItem('admin-sb');
      // Desktop mặc định open, mobile mặc định closed
      var isDesktop = window.innerWidth >= 1024;
      var open = stored !== null ? stored === '1' : isDesktop;
      if (open) document.documentElement.classList.add('sb-init-open');
    })();
  </script>
  <style>
    /* Áp dụng ngay khi load (trước JS) */
    html.sb-init-open #admin-sidebar { transform: translateX(0) !important; }
    @media (min-width: 1024px) {
      html.sb-init-open #admin-main { margin-left: 224px !important; }
    }
  </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen overflow-x-hidden">

<!-- Backdrop (mobile) -->
<div id="sidebar-backdrop"
     class="fixed inset-0 z-30 bg-black/60 opacity-0 pointer-events-none lg:hidden"
     onclick="sbToggle(false)"></div>

<!-- Sidebar -->
<aside id="admin-sidebar"
       class="fixed inset-y-0 left-0 z-40 w-56 bg-gray-900 border-r border-gray-800 flex flex-col overflow-y-auto"
       style="transform: translateX(-100%)">

  <!-- Brand + close btn -->
  <div class="flex items-center justify-between px-4 py-4 border-b border-gray-800 shrink-0">
    <a href="/admin" class="flex items-center gap-2.5 text-white font-bold text-sm hover:text-indigo-300 transition-colors truncate">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-400 shrink-0">
        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
      </svg>
      Admin Panel
    </a>
    <button onclick="sbToggle(false)"
            class="lg:hidden ml-2 p-1 text-gray-500 hover:text-gray-300 hover:bg-gray-800 rounded transition-colors shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>

  <!-- Nav -->
  <nav class="flex-1 px-3 py-3 space-y-0.5">

    <a href="/admin" class="nav-link <?= ($activePage??'')==='dashboard' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      Dashboard
    </a>

    <div class="pt-3 pb-1 px-2">
      <span class="text-[10px] font-semibold text-gray-600 uppercase tracking-widest">Content</span>
    </div>

    <a href="/admin/manga" class="nav-link <?= ($activePage??'')==='manga' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
      </svg>
      Manga
    </a>

    <a href="/admin/categories" class="nav-link <?= ($activePage??'')==='categories' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
      </svg>
      Categories
    </a>

    <a href="/admin/tags" class="nav-link <?= ($activePage??'')==='tags' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
        <line x1="7" y1="7" x2="7.01" y2="7"/>
      </svg>
      Tags
    </a>

    <a href="/admin/authors" class="nav-link <?= ($activePage??'')==='authors' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <line x1="23" y1="11" x2="17" y2="11"/><line x1="20" y1="8" x2="20" y2="14"/>
      </svg>
      Authors / Artists
    </a>

    <?php
      try { $pendingReports = \Config\Database::connect()->table('chapter_reports')->where('status','pending')->countAllResults(); }
      catch (\Throwable $e) { $pendingReports = 0; }
    ?>
    <a href="/admin/reports" class="nav-link <?= ($activePage??'')==='reports' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/>
      </svg>
      Reports
      <?php if ($pendingReports > 0): ?>
      <span class="ml-auto bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.5 min-w-[18px] text-center leading-none"><?= $pendingReports ?></span>
      <?php endif; ?>
    </a>

    <div class="pt-3 pb-1 px-2">
      <span class="text-[10px] font-semibold text-gray-600 uppercase tracking-widest">System</span>
    </div>

    <a href="/admin/users" class="nav-link <?= ($activePage??'')==='users' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      Users
    </a>

    <a href="/admin/groups" class="nav-link <?= ($activePage??'')==='groups' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
        <line x1="7" y1="7" x2="7.01" y2="7"/>
      </svg>
      Groups
    </a>

    <a href="/admin/settings" class="nav-link <?= ($activePage??'')==='settings' ? 'active' : '' ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
      </svg>
      Settings
    </a>

  </nav>

  <!-- Footer: username -->
  <div class="px-4 py-3 border-t border-gray-800 text-xs text-gray-500 truncate shrink-0">
    <?= esc($currentUser['username'] ?? '') ?>
  </div>
</aside>

<!-- Main area -->
<div id="admin-main" class="flex flex-col min-h-screen" style="margin-left:0">

  <!-- Top bar -->
  <header class="bg-gray-900 border-b border-gray-800 px-4 py-3 flex items-center gap-3 sticky top-0 z-20 shrink-0">
    <!-- Hamburger -->
    <button onclick="sbToggle()"
            class="p-1.5 text-gray-400 hover:text-gray-200 hover:bg-gray-800 rounded-lg transition-colors shrink-0"
            title="Toggle sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>

    <h1 class="text-sm font-semibold text-gray-200 flex-1 truncate"><?= esc($title ?? 'Admin') ?></h1>

    <div class="flex items-center gap-4 text-xs text-gray-400 shrink-0">
      <a href="/" class="hidden sm:inline hover:text-gray-200 transition-colors">← Site</a>
      <span class="hidden sm:inline text-gray-700">|</span>
      <span class="hidden sm:inline text-gray-500 truncate max-w-[100px]"><?= esc($currentUser['username'] ?? '') ?></span>
      <a href="/logout" class="text-red-400 hover:text-red-300 transition-colors">Logout</a>
    </div>
  </header>

  <!-- Content -->
  <main class="flex-1 p-4 sm:p-6 overflow-auto">
    <?= $content ?>
  </main>

</div>

<script>
(function () {
  var SIDEBAR_W  = 224; // w-56 = 224px
  var sidebar    = document.getElementById('admin-sidebar');
  var main       = document.getElementById('admin-main');
  var backdrop   = document.getElementById('sidebar-backdrop');
  var isDesktop  = function () { return window.innerWidth >= 1024; };
  var stored     = localStorage.getItem('admin-sb');
  var sbOpen     = stored !== null ? stored === '1' : isDesktop();

  function applyState(animate) {
    if (!animate) {
      sidebar.style.transition = 'none';
      main.style.transition    = 'none';
    }

    if (sbOpen) {
      sidebar.style.transform = 'translateX(0)';
      if (isDesktop()) {
        main.style.marginLeft = SIDEBAR_W + 'px';
        backdrop.style.opacity        = '0';
        backdrop.style.pointerEvents  = 'none';
      } else {
        main.style.marginLeft         = '0';
        backdrop.style.opacity        = '1';
        backdrop.style.pointerEvents  = 'auto';
      }
    } else {
      sidebar.style.transform       = 'translateX(-' + SIDEBAR_W + 'px)';
      main.style.marginLeft         = '0';
      backdrop.style.opacity        = '0';
      backdrop.style.pointerEvents  = 'none';
    }

    if (!animate) {
      // Re-enable transitions after a tick
      requestAnimationFrame(function () {
        sidebar.style.transition = '';
        main.style.transition    = '';
      });
    }
  }

  window.sbToggle = function (force) {
    if (force === undefined) sbOpen = !sbOpen;
    else sbOpen = !!force;
    localStorage.setItem('admin-sb', sbOpen ? '1' : '0');
    applyState(true);
  };

  // Remove the css pre-init class now that JS takes over
  document.documentElement.classList.remove('sb-init-open');

  // Apply initial state without animation
  applyState(false);

  // Handle resize
  window.addEventListener('resize', function () { applyState(true); });
})();
</script>
</body>
</html>
