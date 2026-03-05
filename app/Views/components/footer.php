  </main>

  <?php
    $_flogo      = site_setting('footer_logo') ?: site_setting('site_logo');
    $_ftitle     = site_setting('site_title', 'MangaCI');
    $_fcopyright = site_setting('footer_copyright', '© ' . date('Y') . ' ' . site_setting('site_title', 'MangaCI'));
    $_furl       = site_setting('footer_url', '/');
  ?>
  <footer class="text-center text-white">

    <div class="text-center text-gray-700 p-4 dark:bg-fire-blue dark:text-gray-300" itemscope
      itemtype="http://schema.org/Organization">
      <?php if ($_flogo): ?>
      <a itemprop="url" href="<?= esc($_furl) ?>">
        <img itemprop="logo" src="<?= esc($_flogo) ?>" alt="<?= esc($_ftitle) ?>"
             class="inline-block h-10 w-auto mx-auto mb-1">
      </a>
      <br>
      <?php endif; ?>
      <span class="text-sm"><?= esc($_fcopyright) ?></span>
    </div>

  </footer>

  <button id="backToTop" aria-label="Back to top"
    class="fixed bottom-4 right-4 bg-gray-800/50 dark:bg-gray-700/50 backdrop-blur-sm text-white/80 p-3 rounded-full shadow-lg cursor-pointer opacity-0 transition-all duration-300 hover:bg-gray-700/70 dark:hover:bg-gray-600/70 hover:text-white"
    style="z-index: 9999; position: fixed; bottom: 20px; right: 20px;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
  </button>

  <!-- Alpine.js: stores & data phải đăng ký trước khi Alpine start -->
  <script>
    document.addEventListener('alpine:init', function () {
      Alpine.store('darkMode', {
        on: true,
        init: function () {
          var v = localStorage.getItem('darkMode');
          if (v !== null) this.on = v === 'true';
        },
        toggle: function () {
          this.on = !this.on;
          localStorage.setItem('darkMode', this.on);
        }
      });
      Alpine.store('mobileSidebar', { open: false });
      Alpine.data('listFilers', function () {
        return {
          initialized: false,
          params: { sort: '-updated_at', incomplete: true, complete: true, keyword: '', type: 'name', genres: {} },
          init: function () {
            var _this = this;
            var search = window.location.search;
            if (search[0] === '?') search = decodeURI(search.substring(1));
            search.split('&').forEach(function (val) {
              var key = val.split('=')[0], value = val.split('=')[1];
              switch (key) {
                case 'filter[name]': _this.params.type = 'name'; _this.params.keyword = value; break;
                case 'filter[artist]': _this.params.type = 'artist'; _this.params.keyword = value; break;
                case 'filter[doujinshi]': _this.params.type = 'doujinshi'; _this.params.keyword = value; break;
                case 'filter[status]': _this.params.incomplete = value.includes('2'); _this.params.complete = value.includes('1'); break;
                case 'filter[accept_genres]': value.split(',').forEach(function (id) { _this.params.genres[id] = 1; }); break;
                case 'filter[reject_genres]': value.split(',').forEach(function (id) { _this.params.genres[id] = 2; }); break;
                default: _this.params[key] = value === 'true' ? true : value;
              }
            });
            this.initialized = true;
          },
          toggleGenre: function (i) {
            if (this.params.genres.hasOwnProperty(i)) {
              this.params.genres[i] = this.params.genres[i] < 2 ? this.params.genres[i] + 1 : 0;
            } else { this.params.genres[i] = 1; }
          },
          toQuery: function () {
            var _this = this, queries = [];
            var multi = { 'filter[status]': [], 'filter[accept_genres]': [], 'filter[reject_genres]': [] };
            Object.keys(this.params).forEach(function (key) {
              switch (key) {
                case '': case 'type': break;
                case 'keyword': if (_this.params[key]) queries.push(encodeURI('filter[' + _this.params.type + ']=' + _this.params[key])); break;
                case 'complete': if (_this.params.complete) multi['filter[status]'].push('1'); break;
                case 'incomplete': if (_this.params.incomplete) multi['filter[status]'].push('2'); break;
                case 'genres':
                  Object.keys(_this.params.genres).forEach(function (id) {
                    if (_this.params.genres[id] === 1) multi['filter[accept_genres]'].push(id);
                    else if (_this.params.genres[id] === 2) multi['filter[reject_genres]'].push(id);
                  }); break;
                default: if (_this.params[key]) queries.push(encodeURI(key + '=' + _this.params[key]));
              }
            });
            Object.keys(multi).forEach(function (key) { if (multi[key].length) queries.push(encodeURI(key + '=' + multi[key].join(','))); });
            return queries.join('&');
          },
          doQuery: function (isGlobal) {
            if (!this.initialized) return;
            var qs = this.toQuery();
            var base = isGlobal ? window.location.origin + '/search' : window.location.origin + window.location.pathname;
            window.location.href = base + '?' + qs;
          },
          doFilterStatus: function () { this.params.page = '1'; this.doQuery(); }
        };
      });
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js" defer></script>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Nav scroll hide/show
      var nav = document.querySelector('#main-nav');
      if (nav) {
        var lastScroll = 0, scrollTimer = null;
        window.addEventListener('scroll', function () {
          if (scrollTimer !== null) clearTimeout(scrollTimer);
          scrollTimer = setTimeout(function () {
            var cur = window.pageYOffset;
            if (Math.abs(lastScroll - cur) <= 5) return;
            if (cur > lastScroll && cur > 100) { nav.classList.remove('nav-pinned'); nav.classList.add('nav-unpinned'); }
            else if (cur < lastScroll) { nav.classList.remove('nav-unpinned'); nav.classList.add('nav-pinned'); }
            lastScroll = cur;
          }, 10);
        });
      }

      // Swiper init
      var swiperConfig = {
        slidesPerView: 3, spaceBetween: 12, loop: true,
        autoplay: { delay: 3000, disableOnInteraction: false },
        breakpoints: { 640: { slidesPerView: 3, spaceBetween: 16 }, 1024: { slidesPerView: 6, spaceBetween: 20 } }
      };
      new Swiper('.newest-swiper', Object.assign({}, swiperConfig, {
        navigation: { nextEl: '.newest-swiper .swiper-button-next', prevEl: '.newest-swiper .swiper-button-prev' }
      }));
      new Swiper('.hot-today-swiper', Object.assign({}, swiperConfig, {
        autoplay: { delay: 4000, disableOnInteraction: false },
        navigation: { nextEl: '.hot-today-swiper .swiper-button-next', prevEl: '.hot-today-swiper .swiper-button-prev' }
      }));

      // Back to top
      var btn = document.getElementById('backToTop');
      if (btn) {
        btn.style.display = 'block';
        window.addEventListener('scroll', function () {
          if (window.pageYOffset > 300) { btn.classList.remove('opacity-0'); btn.classList.add('opacity-100'); }
          else { btn.classList.remove('opacity-100'); btn.classList.add('opacity-0'); }
        });
        btn.addEventListener('click', function (e) { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
      }
    });
  </script>

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-RZ5F7JNX7S"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-RZ5F7JNX7S');
  </script>

</body>
</html>
