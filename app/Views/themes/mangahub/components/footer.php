<?php
  $_flogo      = site_setting('footer_logo') ?: site_setting('site_logo');
  $_ftitle     = site_setting('site_title', 'MangaCI');
  $_fcopyright = site_setting('footer_copyright', '© ' . date('Y') . ' ' . site_setting('site_title', 'MangaCI'));
  $_furl       = site_setting('footer_url', '/');
?>
  </main>

  <footer>
    <div class="wrap">
      <div class="footer-cols">
        <div class="footer-col">
          <h5>Site Info</h5>
          <a href="/">About Us</a>
          <a href="/search">Sitemap</a>
        </div>
        <div class="footer-col">
          <h5>Browse</h5>
          <a href="/search?sort=-updated_at">Latest Updates</a>
          <a href="/search?sort=-views">Popular</a>
          <a href="/search?sort=-created_at">New Releases</a>
        </div>
        <div class="footer-col">
          <h5>Support</h5>
          <a href="/search">Advanced Search</a>
        </div>
      </div>
      <div class="footer-bottom">
        <?= esc($_fcopyright) ?>
      </div>
    </div>
  </footer>

  <!-- Back to top FAB -->
  <button class="fab-top" id="fabTop" title="Back to top">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="18 15 12 9 6 15"/>
    </svg>
  </button>

  <!-- Vanilla JS: dark mode, burger, back-to-top, search -->
  <script>
  (function() {
    'use strict';

    /* ── Dark Mode Toggle ─────────────────────────────── */
    var darkBtns = [document.getElementById('darkBtn'), document.getElementById('darkBtnMobile')];

    function applyTheme(isDark) {
      document.body.classList.toggle('light', !isDark);
      darkBtns.forEach(function(b) { if (b) b.textContent = isDark ? '☀️' : '🌙'; });
    }

    function toggleDark() {
      var isDark = document.body.classList.contains('light');
      applyTheme(isDark);
      localStorage.setItem('mangahub-theme', isDark ? 'dark' : 'light');
    }

    var saved = localStorage.getItem('mangahub-theme');
    applyTheme(saved !== 'light');

    darkBtns.forEach(function(b) { if (b) b.addEventListener('click', toggleDark); });

    /* ── Burger / Mobile Menu ─────────────────────────── */
    var burgerBtn  = document.getElementById('burgerBtn');
    var mobileMenu = document.getElementById('mobileMenu');

    if (burgerBtn && mobileMenu) {
      burgerBtn.addEventListener('click', function() {
        var isOpen = mobileMenu.classList.toggle('open');
        burgerBtn.classList.toggle('open', isOpen);
      });

      mobileMenu.querySelectorAll('.mobile-link, .mobile-nav a, a').forEach(function(l) {
        l.addEventListener('click', function() {
          mobileMenu.classList.remove('open');
          burgerBtn.classList.remove('open');
        });
      });

      document.addEventListener('click', function(e) {
        if (!burgerBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
          mobileMenu.classList.remove('open');
          burgerBtn.classList.remove('open');
        }
      });
    }

    /* ── Back to Top FAB ──────────────────────────────── */
    var fabTop = document.getElementById('fabTop');
    if (fabTop) {
      window.addEventListener('scroll', function() {
        fabTop.classList.toggle('show', window.scrollY > 400);
      }, { passive: true });

      fabTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    /* ── Header Search ────────────────────────────────── */
    var searchInput = document.getElementById('headerSearch');
    if (searchInput) {
      searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
          var q = searchInput.value.trim();
          if (q) {
            window.location.href = '/search?filter[name]=' + encodeURIComponent(q);
          }
        }
      });
    }

  })();
  </script>

</body>
</html>
