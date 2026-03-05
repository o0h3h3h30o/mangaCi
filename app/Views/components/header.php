<body x-data class="dark bg-stone-800 text-white" :class="{ 'dark bg-stone-800 text-white': $store.darkMode.on }">
  <span class="bg"></span>
  <div x-show="$store.mobileSidebar.open" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @click="$store.mobileSidebar.open = false"
    class="fixed inset-0 z-[99] bg-black/60 backdrop-blur-sm sm:hidden" style="display: none;">
  </div>


  <aside x-show="$store.mobileSidebar.open" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed top-0 left-0 z-[100] h-full w-[80%] max-w-[320px] sm:hidden overflow-y-auto overscroll-contain"
    style="display: none;" @keydown.escape.window="$store.mobileSidebar.open = false">

    <div class="flex flex-col h-full bg-white dark:bg-light-blue border-r border-gray-200 dark:border-gray-800">


      <div class="flex items-center justify-between px-4 py-4 border-b border-gray-100 dark:border-gray-800">
        <a href="index.html" class="flex items-center gap-3 min-w-0">
          <?php $_logo = site_setting('site_logo'); ?>
          <?php if ($_logo): ?>
          <img src="<?= esc($_logo) ?>" alt="<?= esc(site_setting('site_title', 'MangaCI')) ?>" style="width:100%;max-height:52px;object-fit:contain;border-radius:6px">
          <?php else: ?>
          <img src="/favicon.ico" alt="<?= esc(site_setting('site_title', 'MangaCI')) ?>" style="width:100%;max-height:52px;object-fit:contain;border-radius:6px">
          <?php endif; ?>
          <span></span>
        </a>
        <div class="flex items-center gap-2">

          <label class="dm-switch dm-no-transition" x-data
            x-init="$nextTick(() => $el.classList.remove('dm-no-transition'))">
            <input type="checkbox" class="dm-input" checked x-effect="$el.checked = $store.darkMode.on"
              @change="$store.darkMode.toggle()">
            <div class="dm-slider dm-round">

              <div class="dm-sun-moon">

                <svg class="dm-moon-dot dm-moon-dot-1" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-moon-dot dm-moon-dot-2" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-moon-dot dm-moon-dot-3" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>

                <svg class="dm-light-ray dm-light-ray-1" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-light-ray dm-light-ray-2" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-light-ray dm-light-ray-3" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>

                <svg class="dm-cloud-dark dm-cloud-1" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-cloud-dark dm-cloud-2" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-cloud-dark dm-cloud-3" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-cloud-light dm-cloud-4" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-cloud-light dm-cloud-5" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
                <svg class="dm-cloud-light dm-cloud-6" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="50" />
                </svg>
              </div>

              <div class="dm-stars">
                <svg class="dm-star dm-star-1" viewBox="0 0 20 20">
                  <path
                    d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                </svg>
                <svg class="dm-star dm-star-2" viewBox="0 0 20 20">
                  <path
                    d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                </svg>
                <svg class="dm-star dm-star-3" viewBox="0 0 20 20">
                  <path
                    d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                </svg>
                <svg class="dm-star dm-star-4" viewBox="0 0 20 20">
                  <path
                    d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                </svg>
              </div>
            </div>
          </label>
          <button @click="$store.mobileSidebar.open = false"
            class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
            aria-label="Close menu">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>


      <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center gap-3">
          <?php if (!empty($currentUser)): ?>
          <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-base">
            <?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?>
          </div>
          <div class="flex flex-col text-sm">
            <span class="font-semibold text-gray-900 dark:text-white"><?= esc($currentUser['name']) ?></span>
            <a href="/logout" class="text-red-500 hover:text-red-600 text-xs transition-colors">Logout</a>
          </div>
          <?php else: ?>
          <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 4a4 4 0 014 4 4 4 0 01-4 4 4 4 0 01-4-4 4 4 0 014-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4" />
            </svg>
          </div>
          <div class="flex gap-2 text-sm">
            <a href="/login" class="font-medium text-blue-500 hover:text-blue-600 transition-colors">Login</a>
            <span class="text-gray-300 dark:text-gray-600">/</span>
            <a href="/register" class="font-medium text-blue-500 hover:text-blue-600 transition-colors">Register</a>
          </div>
          <?php endif; ?>
        </div>
      </div>


      <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">


        <p
          class="px-3 pt-2 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
          Rankings
        </p>

        <a href="search?sort=-views_day"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-500/10 hover:text-orange-600 dark:hover:text-orange-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-500/10 text-orange-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
              <path d="M9.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z" />
              <path
                d="M0 2a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H2a2 2 0 01-2-2zm15 0a1 1 0 00-1-1H2a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1z" />
            </svg>
          </span>
          Top Day
        </a>

        <a href="search?sort=-views_week"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-50 dark:bg-yellow-500/10 text-yellow-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
              <path
                d="M2 0a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V2a2 2 0 00-2-2zm3.37 5.11V4.001h5.308V5.15L7.42 12H6.025l3.317-6.82v-.07H5.369Z" />
            </svg>
          </span>
          Top Week
        </a>

        <a href="search?sort=-views"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-sky-50 dark:hover:bg-sky-500/10 hover:text-sky-600 dark:hover:text-sky-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
              <path
                d="M4 .5a.5.5 0 00-1 0V1H2a2 2 0 00-2 2v11a2 2 0 002 2h12a2 2 0 002-2V3a2 2 0 00-2-2h-1V.5a.5.5 0 00-1 0V1H4zm-2 4v-1c0-.276.244-.5.545-.5h10.91c.3 0 .545.224.545.5v1c0 .276-.244.5-.546.5H2.545C2.245 5 2 4.776 2 4.5m6 3.493c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132" />
            </svg>
          </span>
          Top Month
        </a>


        <p
          class="px-3 pt-4 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
          Navigation
        </p>

        <a href="search?sort=-updated_at&filter%5Bstatus%5D=2,1&filter%5Breject_genres%5D=340"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 dark:hover:text-blue-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </span>
          Home
        </a>

        <a href="/search"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          Search
        </a>

        <a href="/bookmarks"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-500/10 hover:text-violet-600 dark:hover:text-violet-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-500/10 text-violet-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
            </svg>
          </span>
          Follow
        </a>

        <a href="/history"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors group">
          <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="12 8 12 12 14 14" />
              <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
            </svg>
          </span>
          History
        </a>


        <p
          class="px-3 pt-4 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
          Features
        </p>

        <a href="shop.html"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-500/10 hover:text-amber-600 dark:hover:text-amber-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="21" r="1" />
              <circle cx="20" cy="21" r="1" />
              <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
            </svg>
          </span>
          Shop
        </a>

        <a href="<?= !empty($currentUser) ? '/bookmarks' : '/login' ?>"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-pink-500/10 hover:text-pink-600 dark:hover:text-pink-400 transition-colors group">
          <span
            class="flex items-center justify-center w-8 h-8 rounded-lg bg-pink-50 dark:bg-pink-500/10 text-pink-500 group-hover:scale-105 transition-transform">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </span>
          Bookmarks
        </a>



        <p
          class="px-3 pt-4 pb-1.5 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">
          Category
        </p>

        <div class="grid grid-cols-2 gap-1 px-1">
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <a href="/search?genre=<?= esc($cat['slug']) ?>"
                class="truncate px-2.5 py-2 rounded-md text-xs text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white transition-colors">
                <?= esc($cat['name']) ?>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </nav>


    </div>
  </aside>
  <nav id="main-nav" class="bg-light-blue dark:bg-light-blue w-full">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 shadow-lg md:shadow-none">
      <div class="relative flex items-center justify-between h-16">


        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
          <button type="button" @click="$store.mobileSidebar.open = true"
            class="inline-flex items-center justify-center p-2 rounded-md dark:text-gray-200 text-gray-400 hover:text-white hover:bg-gray-700"
            aria-controls="mobile-sidebar" aria-expanded="false" aria-label="Menu">
            <svg class="block h-6 w-6" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>


        <div class="flex items-center justify-center">
          <a href="/">
            <div class="flex-shrink-0 flex items-center">
              <?php $_dlogo = site_setting('site_logo', 'https://i2.mgcdnxyz.cfd/storage/images/default/dcn-2026.png'); $_dalt = esc(site_setting('site_title','MangaCI')); ?>
              <img class="block lg:hidden h-9 md:h-[6rem] w-auto ml-12 sm:ml-0 object-contain"
                src="<?= esc($_dlogo) ?>" alt="<?= $_dalt ?>"
                style="margin-left: 35px;">
              <img class="hidden lg:block w-auto object-contain"
                src="<?= esc($_dlogo) ?>" alt="<?= $_dalt ?>"
                style="width:500px;margin-left: -30px;">
              <span class="hidden sm:block ml-2 font-semibold text-lg text-gray-900 dark:text-gray-300"></span>
            </div>
          </a>
        </div>

        <!-- Search Form -->
        <div x-data="liveSearchDesktop" @keydown.escape.window="open = false"
          class="hidden items-center md:flex w-full justify-center relative">

          <!-- Container của input và kết quả -->
          <div x-cloak class="flex bg-fire-blue border-dark-blue w-1/2 rounded relative" @click.away="open = false">
            <!-- Input tìm kiếm -->
            <div class="relative flex-grow">
              <input type="text" id="search" x-model.debounce.200ms="searchQuery" @focus="open = true"
                placeholder="Search..."
                class="block border-transparent text-white w-full focus:outline-none focus:ring-0 border-0 bg-transparent pr-10"
                autocomplete="off" aria-label="Search">
              <!-- Loading indicator -->
              <div x-show="loading" class="absolute top-1/2 right-4 transform -translate-y-1/2 animate-spin">
                <svg class="w-5 h-5 text-white" viewBox="0 0 1024 1024" class="icon" xmlns="http://www.w3.org/2000/svg">
                  <path fill="#fff"
                    d="M512 64a32 32 0 0132 32v192a32 32 0 01-64 0V96a32 32 0 0132-32zm0 640a32 32 0 0132 32v192a32 32 0 11-64 0V736a32 32 0 0132-32zm448-192a32 32 0 01-32 32H736a32 32 0 110-64h192a32 32 0 0132 32zm-640 0a32 32 0 01-32 32H96a32 32 0 010-64h192a32 32 0 0132 32zM195.2 195.2a32 32 0 0145.248 0L376.32 331.008a32 32 0 01-45.248 45.248L195.2 240.448a32 32 0 010-45.248zm452.544 452.544a32 32 0 0145.248 0L828.8 783.552a32 32 0 01-45.248 45.248L647.744 692.992a32 32 0 010-45.248zM828.8 195.264a32 32 0 010 45.184L692.992 376.32a32 32 0 01-45.248-45.248l135.808-135.808a32 32 0 0145.248 0zm-452.544 452.48a32 32 0 010 45.248L240.448 828.8a32 32 0 01-45.248-45.248l135.808-135.808a32 32 0 0145.248 0z" />
                </svg>
              </div>
              <!-- Nút X để xóa tìm kiếm -->
              <button x-show="searchQuery.length > 0 && !loading" @click="clearSearch" type="button"
                class="absolute top-1/2 right-4 transform -translate-y-1/2 text-gray-500 hover:text-light-blue focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Kết quả tìm kiếm -->
            <div x-show="open && searchQuery.length >= 2 && results.length > 0"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 transform scale-95"
              x-transition:enter-end="opacity-100 transform scale-100"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 transform scale-100"
              x-transition:leave-end="opacity-0 transform scale-95"
              class="absolute left-0 right-0 top-full mt-1 border-gray-600 border bg-fire-blue shadow-lg rounded-b z-50 max-h-120 overflow-y-auto">
              <template x-for="result in results" :key="result.id">
                <a :href="`/manga/${result.slug}`" class="flex items-start p-2 hover:bg-dark-blue">
                  <img :src="result.cover_full_url" alt="" class="w-14 h-20 object-cover mr-4 rounded">
                  <div class="flex flex-col">
                    <div class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1" x-text="result.name">
                    </div>
                    <div class="text-sm text-gray-400"
                      x-text="result.latest_chapter ? result.latest_chapter.name : 'Updating...'"></div>
                    <div class="flex flex-wrap text-xs text-gray-500 mt-1">
                      <template x-for="(genre, index) in result.genres" :key="index">
                        <span class="mr-1" x-text="genre.name"></span>
                      </template>
                    </div>
                  </div>
                </a>
              </template>

              <!-- Nút xem toàn bộ kết quả -->
              <div class="p-4 border-t border-gray-400">
                <div
                  @click="window.location.href = `/search?sort=-updated_at&filter%5Bname%5D=${encodeURIComponent(searchQuery)}&filter%5Bstatus%5D=2,1`"
                  class="flex items-center justify-center space-x-2 p-2 bg-dark-blue text-white rounded-lg w-full border border-gray-200 dark:border-light-blue cursor-pointer hover:bg-light-blue">
                  <span class="px-4">View all results</span>
                  <span class="border-l border-gray-400 pl-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                      <path fill-rule="evenodd"
                        d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                        clip-rule="evenodd" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>

            <!-- Khi không có kết quả hoặc lỗi -->
            <div x-show="open && ((results.length === 0 && searchQuery.length >= 2 && !loading) || searchError)" x-cloak
              class="absolute left-0 right-0 top-full mt-1 bg-fire-blue shadow-lg rounded-b-lg border border-gray-600 p-4 text-center text-gray-400 z-50">
              <span x-show="results.length === 0 && searchQuery.length >= 2 && !loading">No results found</span>
              <span x-show="searchError">Error fetching results.</span>
            </div>
          </div>
        </div>

        <style>
          .no-default-x::-webkit-search-cancel-button {
            display: none;
          }

          [x-cloak] {
            display: none !important;
          }
        </style>

        <script>
document.addEventListener('alpine:init', () => {
    Alpine.data('liveSearchDesktop', () => ({
        open: false,
        searchQuery: '',
        results: [],
        loading: false,
        searchError: null,
        _timer: null,
        init() {
            this.$watch('searchQuery', (value) => {
                this.open = value.length >= 1;
                clearTimeout(this._timer);
                if (value.length >= 2) {
                    this.loading = true;
                    this.searchError = null;
                    this._timer = setTimeout(() => this.search(), 300);
                } else {
                    this.results = [];
                    this.loading = false;
                    this.searchError = null;
                }
            });
        },
        async search() {
            const q = this.searchQuery;
            try {
                const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
                if (!res.ok) throw new Error('Network error');
                const data = await res.json();
                if (this.searchQuery === q) {
                    this.results = data.results || [];
                }
            } catch (e) {
                this.searchError = true;
                this.results = [];
            } finally {
                if (this.searchQuery === q) this.loading = false;
            }
        },
        clearSearch() {
            this.searchQuery = '';
            this.results = [];
            this.open = false;
        }
    }));
});
</script>

        <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto">
          <!-- Bao quanh thành phần tìm kiếm bằng một div và áp dụng lớp block md:hidden -->
          <div class="block md:hidden">
            <div x-data="{
            open: false,
            searchQuery: '',
            results: [],
            loading: false,
            searchError: null,
            _timer: null,
            init() {
                this.$watch('searchQuery', (value) => {
                    clearTimeout(this._timer);
                    if (value.length >= 2) {
                        this.loading = true;
                        this.searchError = null;
                        this._timer = setTimeout(() => this.search(), 300);
                    } else {
                        this.results = [];
                        this.loading = false;
                        this.searchError = null;
                    }
                });
            },
            async search() {
                const q = this.searchQuery;
                try {
                    const res = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
                    if (!res.ok) throw new Error();
                    const data = await res.json();
                    if (this.searchQuery === q) this.results = data.results || [];
                } catch(e) {
                    this.searchError = true;
                    this.results = [];
                } finally {
                    if (this.searchQuery === q) this.loading = false;
                }
            },
            clearSearch() {
                this.searchQuery = '';
                this.results = [];
                this.open = false;
            }
        }" @keydown.escape.window="open = false" class="relative">
              <!-- Nút mở tìm kiếm -->
              <button @click="open = true; $nextTick(() => $refs.searchInput.focus());" type="button" id="search-icon"
                class="p-2 rounded-full text-gray-400 hover:text-black dark:hover:text-white" aria-label="Search">
                <!-- Icon tìm kiếm -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="h-6 w-6">
                  <path fill-rule="evenodd"
                    d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                    clip-rule="evenodd" />
                </svg>
              </button>

              <!-- Overlay tìm kiếm -->
              <!-- Thêm x-cloak vào đây -->
              <div x-cloak x-show="open" @click.away="open = false" x-transition
                class="fixed inset-0 z-50 bg-black bg-opacity-50 flex flex-col items-center justify-start">
                <!-- Form tìm kiếm -->
                <form @submit.prevent="search" class="w-full max-w-md px-1 mt-4 relative">
                  <!-- Thêm icon tìm kiếm vào bên trái input -->
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path fill-rule="evenodd"
                          d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                          clip-rule="evenodd" />
                      </svg>
                    </span>
                    <input type="search" x-ref="searchInput" x-model.debounce.200ms="searchQuery"
                      placeholder="Search..." autocomplete="off"
                      class="w-full pl-10 rounded-full bg-white dark:bg-fire-blue p-2 text-lg pr-10 no-default-x focus:outline-none focus:ring-0 focus:border-light-blue" />
                  </div>
                  <!-- Nút X để xóa tìm kiếm -->
                  <button @click="clearSearch" type="button"
                    class="absolute top-1/2 right-6 transform -translate-y-1/2 text-gray-500 hover:text-light-blue focus:outline-none">
                    <!-- Icon X -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </form>

                <!-- Nội dung tìm kiếm -->
                <div class="w-full max-w-md px-1 -mt-3.5 pt-4">
                  <!-- Thông báo khi nhập ít hơn 2 ký tự -->
                  <!-- Thêm x-cloak vào đây -->
                  <div x-cloak x-show="searchQuery.length > 0 && searchQuery.length < 2"
                    class="p-4 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-light-blue rounded-lg shadow-lg">
                    Please enter at least 2 characters to search
                  </div>

                  <!-- Kết quả tìm kiếm -->
                  <template x-if="results.length > 0">
                    <!-- Thêm x-cloak vào đây -->
                    <div x-cloak
                      class="bg-white dark:bg-dark-blue shadow-lg rounded-b-lg -mt-4 pt-3 border-light-blue border-solid"
                      x-show="results.length > 0" x-transition:enter="transition ease-out duration-300"
                      x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-200"
                      x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                      <template x-for="(result, index) in results" :key="result.id">
                        <a :href="`/manga/${result.slug}`"
                          :class="index % 2 === 0 ? 'bg-gray-100 dark:bg-fire-blue' : ''"
                          class="block border-b border-gray-200 dark:border-light-blue p-2 hover:bg-gray-100 dark:hover:bg-light-blue">
                          <div class="flex items-center">
                            <img :src="result.cover_full_url" :alt="result.name"
                              class="w-14 h-20 object-cover mr-4 rounded">
                            <div class="flex-1">
                              <div class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1"
                                x-text="result.name"></div>
                              <div class="text-xs text-gray-600 dark:text-gray-300"
                                x-text="result.latest_chapter ? result.latest_chapter.name : 'Updating...'"></div>
                              <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1"
                                x-text="result.genres.map(g => g.name).join(', ')"></div>
                            </div>
                          </div>
                        </a>
                      </template>
                      <!-- Nút xem toàn bộ kết quả -->
                      <div class="block p-4 bg-fire-blue rounded-b-lg border border-gray-200 dark:border-light-blue">
                        <button
                          @click="window.location.href = `/search?sort=-updated_at&filter%5Bname%5D=${encodeURIComponent(searchQuery)}&filter%5Bstatus%5D=2,1`"
                          class="flex items-center justify-center space-x-2 p-2 bg-dark-blue text-white rounded-lg w-full">
                          <span>View all results</span>
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-6 h-6">
                            <path fill-rule="evenodd"
                              d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                              clip-rule="evenodd" />
                          </svg>
                        </button>
                      </div>
                    </div>
                  </template>

                  <!-- Thông báo khi không có kết quả -->
                  <!-- Thêm x-cloak vào đây -->
                  <div x-cloak x-show="results.length === 0 && searchQuery.length >= 2 && !loading"
                    class="p-4 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-light-blue rounded-lg shadow-lg">
                    No results found
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Thêm CSS để tắt nút X mặc định trong input search và hỗ trợ x-cloak -->
          <style>
            .no-default-x::-webkit-search-cancel-button {
              display: none;
            }

            [x-cloak] {
              display: none !important;
            }
          </style>

          <div class="relative hidden sm:flex items-center mr-2">
            <label class="dm-switch dm-no-transition" x-data
              x-init="$nextTick(() => $el.classList.remove('dm-no-transition'))">
              <input type="checkbox" class="dm-input" checked x-effect="$el.checked = $store.darkMode.on"
                @change="$store.darkMode.toggle()">
              <div class="dm-slider dm-round">

                <div class="dm-sun-moon">

                  <svg class="dm-moon-dot dm-moon-dot-1" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-moon-dot dm-moon-dot-2" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-moon-dot dm-moon-dot-3" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>

                  <svg class="dm-light-ray dm-light-ray-1" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-light-ray dm-light-ray-2" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-light-ray dm-light-ray-3" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>

                  <svg class="dm-cloud-dark dm-cloud-1" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-cloud-dark dm-cloud-2" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-cloud-dark dm-cloud-3" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-cloud-light dm-cloud-4" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-cloud-light dm-cloud-5" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                  <svg class="dm-cloud-light dm-cloud-6" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="50" />
                  </svg>
                </div>

                <div class="dm-stars">
                  <svg class="dm-star dm-star-1" viewBox="0 0 20 20">
                    <path
                      d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                  </svg>
                  <svg class="dm-star dm-star-2" viewBox="0 0 20 20">
                    <path
                      d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                  </svg>
                  <svg class="dm-star dm-star-3" viewBox="0 0 20 20">
                    <path
                      d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                  </svg>
                  <svg class="dm-star dm-star-4" viewBox="0 0 20 20">
                    <path
                      d="M 0 10 C 10 10,10 10,0 10 C 10 10,10 10,10 20 C 10 10,10 10,20 10 C 10 10,10 10,10 0 C 10 10,10 10,0 10 Z" />
                  </svg>
                </div>
              </div>
            </label>
          </div>
          <?php if (!empty($currentUser)): ?>
          <!-- Bell notification -->
          <div id="noti-wrap" class="relative flex items-center mr-1">
            <button id="noti-btn" onclick="toggleNoti(event)" aria-label="Thông báo"
              class="relative p-1.5 rounded-full text-gray-400 hover:text-white transition-colors focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span id="noti-badge" style="display:none;top: 2px;font-size: 12px;"
                class="absolute -top-2 -right-1.5 min-w-[17px] h-[17px] rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center px-0.5 leading-none ring-[#111827]">0</span>
            </button>
            <!-- Dropdown (fixed để thoát stacking context của nav/backdrop-blur) -->
            <div id="noti-panel"
              class="w-80 rounded-xl shadow-2xl overflow-hidden"
              style="display:none;position:fixed;z-index:9999;background:#1a1f2e;border:1px solid #2d3748">
              <div class="flex items-center justify-between px-4 py-3" style="border-bottom:1px solid #2d3748">
                <span class="font-semibold text-white text-sm">Notifications</span>
                <button onclick="notiMarkAll(event)" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Mark all as read</button>
              </div>
              <div id="noti-list" class="overflow-y-auto" style="max-height:360px">
                <div class="text-center text-gray-500 py-8 text-sm">Loading...</div>
              </div>
            </div>
          </div>

          <!-- Mobile: avatar icon → profile -->
          <a class="md:hidden flex items-center justify-center w-8 h-8 rounded-full bg-indigo-500 text-white font-bold text-sm"
            href="/profile" title="Profile">
            <?= mb_strtoupper(mb_substr($currentUser['name'], 0, 1)) ?>
          </a>
          <!-- Desktop: username → profile + logout -->
          <div class="truncate sm:flex hidden items-center gap-3 text-white text-sm">
            <a href="/profile" class="font-semibold hover:text-indigo-300 transition-colors"><?= esc($currentUser['username']) ?></a>
            <a href="/logout" class="text-red-400 hover:text-red-300 transition-colors">Logout</a>
          </div>
          <?php else: ?>
          <!-- Mobile: user icon → login -->
          <a class="md:hidden flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 dark:border-gray-500 text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            href="/login">
            <svg xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24">
              <path fill="currentColor" d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4" />
            </svg>
          </a>
          <!-- Desktop: Login / Register -->
          <div class="truncate sm:block hidden text-white">
            <a href="/login" class="hover:text-indigo-300 transition-colors">Login</a>
            <span class="mx-2">/</span>
            <a href="/register" class="hover:text-indigo-300 transition-colors">Register</a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Desktop Sub nav -->
    <div
      class="hidden md:block backdrop-blur-sm shadow-lg shadow-blue-100/20 dark:shadow-slate-900/30 bg-white/90 dark:bg-fire-blue/70 border-b border-gray-200 dark:border-dark-blue">
      <div class="relative max-w-7xl mx-auto w-full px-2 h-14 flex flex-nowrap items-center rounded-lg"
        x-data="{open: false}">
        <a href="index.html"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-blue-50/80 hover:to-indigo-50/60 dark:hover:from-slate-700/40 dark:hover:to-slate-600/40 hover:shadow-md hover:shadow-blue-100/30 dark:hover:shadow-slate-900/20 whitespace-nowrap">
          <svg class="h-4 w-4 group-hover:scale-105 transition-all duration-300 opacity-70 group-hover:opacity-100"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <span class="pl-2 text-xs uppercase tracking-wider">Home</span>
        </a>
        <a href="/bookmarks"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-violet-500 dark:hover:text-violet-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-violet-50/80 hover:to-purple-50/60 dark:hover:from-slate-700/40 dark:hover:to-slate-600/40 hover:shadow-md hover:shadow-violet-100/30 dark:hover:shadow-slate-900/20 whitespace-nowrap">
          <span class="text-xs uppercase tracking-wider">Follow</span>
        </a>
        <a href="/history"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-indigo-50/80 hover:to-blue-50/60 dark:hover:from-slate-700/40 dark:hover:to-slate-600/40 hover:shadow-md hover:shadow-indigo-100/30 dark:hover:shadow-slate-900/20 whitespace-nowrap">
          <span class="text-xs uppercase tracking-wider">History</span>
        </a>
        <a href="#"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-purple-500 dark:hover:text-purple-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-purple-50/80 hover:to-pink-50/60 dark:hover:from-slate-700/40 dark:hover:to-slate-600/40 hover:shadow-md hover:shadow-purple-100/30 dark:hover:shadow-slate-900/20 relative whitespace-nowrap"
          @click.stop="open = !open">
          <span class="text-xs uppercase tracking-wider">Category</span>
          <svg
            class="h-3 w-3 ml-2 group-hover:rotate-180 transition-all duration-300 opacity-70 group-hover:opacity-100"
            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
            stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" />
            <polyline points="6 9 12 15 18 9" />
          </svg>
        </a>
        <a href="/search"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-teal-500 dark:hover:text-teal-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-teal-50/80 hover:to-cyan-50/60 dark:hover:from-slate-700/40 dark:hover:to-slate-600/40 hover:shadow-md hover:shadow-teal-100/30 dark:hover:shadow-slate-900/20 whitespace-nowrap">
          <span class="text-xs uppercase tracking-wider">Search</span>
        </a>
        <a href="shop.html"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-amber-500 dark:hover:text-amber-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-amber-50/80 hover:to-yellow-50/60 dark:hover:from-slate-700/40 dark:hover:to-slate-600/40 hover:shadow-md hover:shadow-amber-100/30 dark:hover:shadow-slate-900/20 whitespace-nowrap">
          <span class="text-xs uppercase tracking-wider">Shop</span>
        </a>
        <a href="search?sort=-views_day"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-orange-400 dark:hover:text-orange-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-orange-50/70 hover:to-amber-50/50 dark:hover:from-slate-700/30 dark:hover:to-slate-600/30 hover:shadow-lg hover:shadow-orange-100/40 dark:hover:shadow-slate-900/25 whitespace-nowrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="h-3 w-3 text-orange-400 group-hover:scale-110 transition-all duration-300 opacity-80 group-hover:opacity-100"
            viewBox="0 0 16 16">
            <path d="M9.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z" />
            <path
              d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
          </svg>
          <span
            class="pl-2 text-xs font-medium tracking-wider text-orange-500/90 group-hover:text-orange-600 uppercase">Top
            Day</span>
        </a>
        <a href="search?sort=-views_week"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-yellow-400 dark:hover:text-yellow-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-yellow-50/70 hover:to-amber-50/50 dark:hover:from-slate-700/30 dark:hover:to-slate-600/30 hover:shadow-lg hover:shadow-yellow-100/40 dark:hover:shadow-slate-900/25 whitespace-nowrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="h-3 w-3 text-yellow-400 group-hover:scale-110 transition-all duration-300 opacity-80 group-hover:opacity-100"
            viewBox="0 0 16 16">
            <path
              d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm3.37 5.11V4.001h5.308V5.15L7.42 12H6.025l3.317-6.82v-.07H5.369Z" />
          </svg>
          <span
            class="pl-2 text-xs font-medium tracking-wider text-yellow-500/90 group-hover:text-yellow-600 uppercase">Top
            Week</span>
        </a>
        <a href="search?sort=-views"
          class="group inline-flex items-center h-10 mx-1 px-4 text-slate-500 dark:text-slate-400 hover:text-sky-400 dark:hover:text-sky-300 transition-all duration-300 font-medium rounded-full hover:bg-gradient-to-r hover:from-sky-50/70 hover:to-blue-50/50 dark:hover:from-slate-700/30 dark:hover:to-slate-600/30 hover:shadow-lg hover:shadow-sky-100/40 dark:hover:shadow-slate-900/25 whitespace-nowrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="h-3 w-3 text-sky-400 group-hover:scale-110 transition-all duration-300 opacity-80 group-hover:opacity-100"
            viewBox="0 0 16 16">
            <path
              d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zm-2 4v-1c0-.276.244-.5.545-.5h10.91c.3 0 .545.224.545.5v1c0 .276-.244.5-.546.5H2.545C2.245 5 2 4.776 2 4.5m6 3.493c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132" />
          </svg>
          <span class="pl-2 text-xs font-medium tracking-wider text-sky-500/90 group-hover:text-sky-600">TOP
            ALL</span>
        </a>
        <!-- Genres dropdown -->
        <ul x-show="open" x-transition @click.away="open = false"
          class="absolute w-full text-slate-600 dark:text-slate-300 top-full mt-1 py-6 px-6 grid grid-cols-6 bg-white/95 dark:bg-slate-800/95 z-50 gap-2 shadow-2xl shadow-slate-200/50 dark:shadow-slate-900/50 rounded-2xl max-h-[320px] overflow-y-auto backdrop-blur-md border border-slate-100/60 dark:border-slate-700/60"
          style="margin-left: -12px; display: none;">
          <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
              <a href="/search?genre=<?= esc($cat['slug']) ?>">
                <li class="truncate py-2 px-3 rounded hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                  <span class="text-sm text-slate-600 dark:text-slate-300"><?= esc($cat['name']) ?></span>
                </li>
              </a>
            <?php endforeach; ?>
          <?php endif; ?>
            </ul>
      </div>
    </div>
  </nav>

<?php if (!empty($currentUser)): ?>
<script>
(function(){
  var open = false;

  // Đóng khi click ngoài
  document.addEventListener('click', function(e){
    if(open && !document.getElementById('noti-wrap').contains(e.target)){
      open = false;
      document.getElementById('noti-panel').style.display = 'none';
    }
  });

  window.toggleNoti = function(e){
    e.stopPropagation();
    open = !open;
    var panel = document.getElementById('noti-panel');
    if(open){
      var btn = document.getElementById('noti-btn');
      var r = btn.getBoundingClientRect();
      panel.style.top  = (r.bottom + 6) + 'px';
      panel.style.right = (window.innerWidth - r.right) + 'px';
      panel.style.display = 'block';
      loadNotiList();
    } else {
      panel.style.display = 'none';
    }
  };

  function _esc(s){
    if(!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function _timeDiff(s){
    if(!s) return '';
    var d = new Date(s.replace(' ','T')), now = new Date();
    var sec = Math.floor((now - d) / 1000);
    if(sec < 60) return 'Just now';
    if(sec < 3600) return Math.floor(sec/60) + ' minutes ago';
    if(sec < 86400) return Math.floor(sec/3600) + ' hours ago';
    return Math.floor(sec/86400) + ' days ago';
  }

  function updateBadge(n){
    var b = document.getElementById('noti-badge');
    if(!b) return;
    if(n > 0){ b.textContent = n > 99 ? '99+' : n; b.style.display = 'flex'; }
    else { b.style.display = 'none'; }
  }

  function loadNotiList(){
    fetch('/api/notifications', {credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(function(d){
        updateBadge(d.unread || 0);
        var list = document.getElementById('noti-list');
        if(!d.notifications || d.notifications.length === 0){
          list.innerHTML = '<div style="text-align:center;color:#6b7280;padding:32px 0;font-size:13px">No notifications</div>';
          return;
        }
        list.innerHTML = d.notifications.map(function(n){
          var isResolved = n.type === 'report_resolved';
          var avatar, msgHtml;
          if(isResolved){
            avatar = '<div style="width:32px;height:32px;border-radius:50%;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0">'+
              '<svg style="width:16px;height:16px;color:#fff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></div>';
            msgHtml = '<p style="font-size:13px;color:#e5e7eb;margin:0">Your error report for <span style="font-weight:500;color:#fff">'+_esc(n.manga_name)+'</span> has been <span style="color:#4ade80;font-weight:600">resolved</span></p>';
          } else {
            var actor = n.actor_name || n.actor_username || '?';
            var initial = actor.charAt(0).toUpperCase();
            avatar = '<div style="width:32px;height:32px;border-radius:50%;background:#4f46e5;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">'+_esc(initial)+'</div>';
            msgHtml = '<p style="font-size:13px;color:#e5e7eb;margin:0"><span style="font-weight:600;color:#a5b4fc">'+_esc(actor)+'</span> replied to your comment in <span style="font-weight:500;color:#fff">'+_esc(n.manga_name)+'</span></p>';
          }
          var dotColor = isResolved ? '#4ade80' : '#818cf8';
          return '<div class="noti-item" data-id="'+n.id+'" data-slug="'+_esc(n.manga_slug)+'" data-chapter="'+_esc(n.chapter_slug||'')+'" onclick="notiClick(this)"'
            +' style="display:flex;align-items:flex-start;gap:10px;padding:12px 16px;cursor:pointer;background:rgba(99,102,241,0.12)"'
            +' onmouseover="this.style.background=\'rgba(255,255,255,0.07)\'" onmouseout="this.style.background=\'rgba(99,102,241,0.12)\'">'+
            avatar+
            '<div style="flex:1;min-width:0">'+
            msgHtml+
            '<p style="font-size:12px;color:#d1d5db;margin:3px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">'+_esc(n.preview)+'</p>'+
            '<p style="font-size:11px;color:#9ca3af;margin:4px 0 0">'+_timeDiff(n.created_at)+'</p>'+
            '</div>'+
            '<div style="width:8px;height:8px;border-radius:50%;background:'+dotColor+';flex-shrink:0;margin-top:6px"></div>'+
            '</div>';
        }).join('');
      })
      .catch(function(){});
  }

  window.notiClick = function(el){
    var id = el.dataset.id, slug = el.dataset.slug, chapter = el.dataset.chapter;
    fetch('/api/notifications/'+id+'/read', {method:'POST', credentials:'same-origin'});
    // Xóa khỏi DOM
    el.remove();
    var remaining = document.querySelectorAll('.noti-item').length;
    updateBadge(remaining);
    if(remaining === 0){
      document.getElementById('noti-list').innerHTML = '<div style="text-align:center;color:#6b7280;padding:32px 0;font-size:13px">No notifications</div>';
    }
    if(slug){
      var url = chapter ? '/manga/'+slug+'/'+chapter+'#cc-section' : '/manga/'+slug+'#comment-section';
      window.location.href = url;
    }
  };

  window.notiMarkAll = function(e){
    e.stopPropagation();
    fetch('/api/notifications/read-all', {method:'POST', credentials:'same-origin'})
      .then(function(){
        document.getElementById('noti-list').innerHTML = '<div style="text-align:center;color:#6b7280;padding:32px 0;font-size:13px">No notifications</div>';
        updateBadge(0);
      });
  };

  // Tự fetch count khi load trang
  document.addEventListener('DOMContentLoaded', function(){
    fetch('/api/notifications', {credentials:'same-origin'})
      .then(function(r){ return r.json(); })
      .then(function(d){ updateBadge(d.unread || 0); })
      .catch(function(){});
  });
})();
</script>
<?php endif; ?>
