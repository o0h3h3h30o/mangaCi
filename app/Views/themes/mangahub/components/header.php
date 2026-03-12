<?php
  $_st   = site_setting('site_title', 'MangaCI');
  $_logo = site_setting('site_logo');
?>
<body><script>
if(localStorage.getItem('mangahub-theme')==='light')document.body.classList.add('light');
</script>

<header>
  <div class="wrap header-inner">

    <!-- Logo -->
    <a href="/" class="logo">
      <?php if ($_logo): ?>
        <img src="<?= esc($_logo) ?>" alt="<?= esc($_st) ?>" style="height:36px">
      <?php else: ?>
        <div class="logo-badge">M</div>
        <span><?= esc($_st) ?><sup>v2</sup></span>
      <?php endif; ?>
    </a>

    <!-- Center nav links -->
    <nav class="header-links" id="headerLinks">
      <a href="/search?sort=-created_at" class="nav-link">Newest</a>
      <a href="/search?sort=-updated_at" class="nav-link">Updated</a>
      <a href="/search?sort=-views" class="nav-link">Popular</a>
      <a href="/search" class="nav-link nav-link-random">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/>
          <polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/>
        </svg>
        Random
      </a>
    </nav>

    <!-- Search -->
    <div class="search" id="searchWrap">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="headerSearch" placeholder="Search manga…" autocomplete="off">
      <div class="search-dropdown" id="searchDropdown"></div>
    </div>

    <!-- Right action buttons -->
    <div class="header-actions">
      <div id="authDesktop"></div>
      <script>
      (function(){
        var u=localStorage.getItem('mh_user'),el=document.getElementById('authDesktop'),
            arrow='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
        if(u){el.innerHTML='<a href="/profile" class="btn-login">'+u.replace(/</g,'&lt;')+' '+arrow+'</a>';}
        else{el.innerHTML='<a href="/login" class="btn-login">Login '+arrow+'</a>';}
      })();
      </script>

      <button class="dark-btn" id="darkBtn" title="Toggle dark mode">🌙</button>

      <button class="burger-btn" id="burgerBtn" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>

  </div>

  <!-- Mobile drawer -->
  <div class="mobile-menu" id="mobileMenu">
    <nav class="mobile-nav">
      <a href="/search?sort=-created_at" class="mobile-link">Newest</a>
      <a href="/search?sort=-updated_at" class="mobile-link">Updated</a>
      <a href="/search?sort=-views" class="mobile-link">Popular</a>
      <a href="/search" class="mobile-link">Advanced Filter</a>
    </nav>
    <div class="mobile-menu-footer">
      <div id="authMobile"></div>
      <script>
      (function(){
        var u=localStorage.getItem('mh_user'),el=document.getElementById('authMobile'),
            arrow='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
        if(u){el.innerHTML='<a href="/profile" class="btn-login mobile-login">'+u.replace(/</g,'&lt;')+' '+arrow+'</a>';}
        else{el.innerHTML='<a href="/login" class="btn-login mobile-login">Login '+arrow+'</a>';}
      })();
      </script>
      <button class="dark-btn mobile-dark" id="darkBtnMobile" title="Toggle dark mode">🌙</button>
    </div>
  </div>
</header>

<style>
.search { position: relative; }
.search-dropdown {
  display: none; position: fixed; z-index: 999;
  background: var(--card); border: 1px solid var(--border); border-radius: 12px;
  box-shadow: 0 12px 32px rgba(0,0,0,.35); max-height: 400px;
  overflow-y: auto; min-width: 320px;
}
@media (max-width: 720px) {
  .search-dropdown {
    left: 8px; right: 8px; min-width: 0; max-height: calc(100vh - 80px);
  }
}
.search-dropdown.active { display: block; }
.sd-item {
  display: flex; align-items: center; gap: 10px; padding: 8px 12px;
  text-decoration: none; color: var(--txt); transition: background .15s;
}
.sd-item:hover { background: var(--surface); }
.sd-item + .sd-item { border-top: 1px solid var(--border); }
.sd-cover {
  width: 40px; height: 54px; border-radius: 6px; overflow: hidden; flex-shrink: 0;
  background: var(--surface);
}
.sd-cover img { width: 100%; height: 100%; object-fit: cover; }
.sd-info { flex: 1; min-width: 0; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.sd-name {
  font-size: 13px; font-weight: 700; color: var(--txt);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; min-width: 0;
}
.sd-chapter { font-size: 11.5px; color: var(--txt3); white-space: nowrap; flex-shrink: 0; }
.sd-empty { padding: 20px; text-align: center; font-size: 13px; color: var(--txt3); }
.sd-loading { padding: 16px; text-align: center; font-size: 12px; color: var(--txt3); }
.sd-all {
  display: block; text-align: center; padding: 10px; font-size: 12.5px;
  font-weight: 700; color: var(--accent); text-decoration: none;
  border-top: 1px solid var(--border);
}
.sd-all:hover { background: var(--surface); }
</style>

<script>
(function(){
  var input = document.getElementById('headerSearch');
  var dropdown = document.getElementById('searchDropdown');
  if (!input || !dropdown) return;

  var timer = null, lastQ = '';
  var wrap = document.getElementById('searchWrap');

  function positionDropdown() {
    var r = wrap.getBoundingClientRect();
    dropdown.style.top = (r.bottom + 6) + 'px';
    if (window.innerWidth > 720) {
      dropdown.style.right = (window.innerWidth - r.right) + 'px';
      dropdown.style.left = '';
    }
  }

  function showDropdown() {
    positionDropdown();
    dropdown.classList.add('active');
  }

  input.addEventListener('input', function() {
    var q = input.value.trim();
    clearTimeout(timer);
    if (q.length < 2) { dropdown.classList.remove('active'); dropdown.innerHTML = ''; lastQ = ''; return; }
    if (q === lastQ) return;
    dropdown.innerHTML = '<div class="sd-loading">Searching...</div>';
    showDropdown();
    timer = setTimeout(function() { doSearch(q); }, 250);
  });

  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      var q = input.value.trim();
      if (q) window.location.href = '/search?filter[name]=' + encodeURIComponent(q);
    }
  });

  document.addEventListener('click', function(e) {
    if (!e.target.closest('#searchWrap')) { dropdown.classList.remove('active'); }
  });

  input.addEventListener('focus', function() {
    if (dropdown.innerHTML && input.value.trim().length >= 2) showDropdown();
  });

  function doSearch(q) {
    lastQ = q;
    fetch('/api/search?q=' + encodeURIComponent(q))
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (input.value.trim() !== q) return;
        var results = data.results || [];
        if (!results.length) {
          dropdown.innerHTML = '<div class="sd-empty">No results found</div>';
          return;
        }
        var html = '';
        results.forEach(function(m) {
          html += '<a href="/manga/' + m.slug + '" class="sd-item">' +
            '<div class="sd-cover"><img src="' + (m.cover_full_url || '') + '" alt="" loading="lazy"></div>' +
            '<div class="sd-info"><div class="sd-name">' + escHtml(m.name) + '</div>' +
            (m.latest_chapter ? '<div class="sd-chapter">' + escHtml(m.latest_chapter.name) + '</div>' : '') +
            '</div></a>';
        });
        html += '<a href="/search?filter[name]=' + encodeURIComponent(q) + '" class="sd-all">View all results</a>';
        dropdown.innerHTML = html;
      })
      .catch(function() { dropdown.innerHTML = '<div class="sd-empty">Search failed</div>'; });
  }

  function escHtml(s) {
    var d = document.createElement('div'); d.textContent = s; return d.innerHTML;
  }
})();
</script>

<script>
window.__MH_AUTH = fetch('/api/me', { credentials: 'same-origin' })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (!d.logged_in) { localStorage.removeItem('mh_user'); return null; }
    window.__MH_USER = d;
    var prev = localStorage.getItem('mh_user');
    localStorage.setItem('mh_user', d.username);
    /* Only re-render if username changed or was missing */
    if (prev !== d.username) {
      var arrow = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
      var esc = d.username.replace(/</g,'&lt;');
      var desktop = document.getElementById('authDesktop');
      var mobile = document.getElementById('authMobile');
      if (desktop) desktop.innerHTML = '<a href="/profile" class="btn-login">' + esc + ' ' + arrow + '</a>';
      if (mobile) mobile.innerHTML = '<a href="/profile" class="btn-login mobile-login">' + esc + ' ' + arrow + '</a>';
    }
    return d;
  })
  .catch(function() { return null; })
  .then(function(d) {
    if (!d && localStorage.getItem('mh_user')) {
      localStorage.removeItem('mh_user');
      /* Session expired – show login buttons */
      var arrow = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';
      var desktop = document.getElementById('authDesktop');
      var mobile = document.getElementById('authMobile');
      if (desktop) desktop.innerHTML = '<a href="/login" class="btn-login">Login ' + arrow + '</a>';
      if (mobile) mobile.innerHTML = '<a href="/login" class="btn-login mobile-login">Login ' + arrow + '</a>';
    }
    return d;
  });
</script>
