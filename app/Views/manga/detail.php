<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$cdnBase   = rtrim(env('CDN_COVER_URL', ''), '/');
$coverUrl  = manga_cover_url($manga, $cdnBase);
$statusMap = [1 => 'Ongoing', 2 => 'Completed'];
$statusLabel = $statusMap[$manga['status_id'] ?? 0] ?? 'Unknown';
$statusClass = ($manga['status_id'] ?? 0) == 2
    ? 'bg-green-100/60 text-green-800 dark:bg-green-900/40 dark:text-green-300'
    : 'bg-yellow-100/60 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300';

$diff = time() - ($manga['update_at'] ?? 0);
if ($diff < 60)          $updatedAgo = $diff . 's ago';
elseif ($diff < 3600)    $updatedAgo = floor($diff/60) . 'm ago';
elseif ($diff < 86400)   $updatedAgo = floor($diff/3600) . 'h ago';
elseif ($diff < 604800)  $updatedAgo = floor($diff/86400) . 'd ago';
else                     $updatedAgo = floor($diff/604800) . 'w ago';

$firstChap = !empty($chapters) ? $chapters[count($chapters)-1] : null;
$lastChap  = !empty($chapters) ? $chapters[0] : null;
?>
<main>
  <div class="max-w-7xl mx-auto px-3 w-full mt-6">

    <!-- Breadcrumb -->
    <div class="flex py-3 px-5 text-gray-700 bg-gray-50 rounded-xl border border-gray-100 dark:bg-fire-blue dark:border-dark-blue dark:shadow-gray-900 shadow-md truncate" aria-label="Breadcrumb">
      <ol class="flex flex-wrap items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
          <a href="/" class="inline-flex items-center text-sm text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <svg class="mr-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
            Home
          </a>
        </li>
        <li aria-current="page">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
            <span class="ml-1 text-sm font-medium text-gray-400 md:ml-2 dark:text-gray-500 truncate"><?= esc($manga['name']) ?></span>
          </div>
        </li>
      </ol>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

      <!-- Left column (2/3) -->
      <div class="md:col-span-2">

        <!-- Manga Info Card -->
        <div class="justify-between border border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded-xl dark:shadow-gray-900 mb-4">
          <div class="flex flex-row truncate mb-4 justify-center md:justify-start items-center">
            <svg class="h-6 w-6 text-indigo-500 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a2.5 2.5 0 0 1 0-5H20" /><path d="M8 7h8" /><path d="M8 11h6" />
            </svg>
            <h1 class="text-xl ml-1 font-semibold text-center md:text-left truncate"><?= esc($manga['name']) ?></h1>
          </div>

          <div class="flex flex-col sm:flex-row gap-4 pb-4">
            <!-- Cover -->
            <div class="relative mx-auto my-0" style="min-width:200px;max-width:280px">
              <div class="cover-frame overflow-hidden rounded-lg shadow-2xl">
                <img src="<?= $coverUrl ?>" alt="<?= esc($manga['name']) ?>"
                  class="rounded-lg cover w-full h-auto drop-shadow-2xl"
                  onerror="this.src='https://via.placeholder.com/280x400?text=No+Cover'">
              </div>
            </div>

            <!-- Info -->
            <div class="grow flex flex-col min-w-0">

              <?php if (!empty($manga['otherNames'])): ?>
              <div class="flex items-start mb-3">
                <svg class="w-5 h-5 text-purple-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v2h4a1 1 0 0 1 0 2h-1v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6H3a1 1 0 1 1 0-2h4z" />
                </svg>
                <span class="text-gray-700 dark:text-gray-300 mr-2 font-bold whitespace-nowrap">Other names:</span>
                <span class="font-semibold text-sm leading-relaxed"><?= esc($manga['otherNames']) ?></span>
              </div>
              <?php endif; ?>

              <!-- Genres -->
              <?php if (!empty($mangaCats)): ?>
              <div class="mt-2 w-full max-w-full">
                <div class="flex items-center gap-2 w-full">
                  <svg class="w-5 h-5 text-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  <span class="text-gray-700 dark:text-gray-300 font-bold whitespace-nowrap flex-shrink-0">Genres:</span>
                  <div class="relative flex-1 min-w-0 overflow-hidden">
                    <div class="flex gap-2 overflow-x-auto pb-1 w-full cursor-grab" style="scrollbar-width:none">
                      <?php foreach ($mangaCats as $cat): ?>
                      <a href="/search?genre=<?= esc($cat['slug']) ?>"
                        class="bg-green-100/60 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full dark:bg-green-900/40 dark:text-green-300 whitespace-nowrap hover:bg-green-200/80 dark:hover:bg-green-800/60 transition-colors duration-200 flex-shrink-0">
                        <?= esc($cat['name']) ?>
                      </a>
                      <?php endforeach; ?>
                    </div>
                    <div class="absolute top-0 right-0 bottom-0 w-8 bg-gradient-to-l from-white dark:from-fire-blue to-transparent pointer-events-none"></div>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <!-- Author -->
              <?php if (!empty($authors)): ?>
              <div class="mt-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-gray-700 dark:text-gray-300 mr-2 font-bold">Author:</span>
                <?php foreach ($authors as $i => $author): ?>
                  <?= $i > 0 ? ' , ' : '' ?>
                  <a class="font-semibold text-blue-500 hover:text-blue-700 transition-colors" href="/search?author=<?= esc($author['slug']) ?>">
                    &nbsp; <?= esc($author['name']) ?> &nbsp;
                  </a>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>

              <!-- Status -->
              <div class="mt-3 flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-gray-700 dark:text-gray-300 mr-2 font-bold">Status:</span>
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full <?= $statusClass ?>">
                  <?= $statusLabel ?>
                </span>
              </div>

              <!-- Updated -->
              <div class="mt-3 flex items-center">
                <svg class="w-5 h-5 text-amber-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-gray-700 dark:text-gray-300 mr-2 font-bold">Updated:</span>
                <span class="font-semibold"><?= $updatedAgo ?></span>
              </div>

              <!-- Views -->
              <div class="mt-3 flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-gray-700 dark:text-gray-300 mr-2 font-bold">Views:</span>
                <span class="font-semibold"><?= number_format($manga['views'] ?? 0) ?></span>
              </div>

              <!-- Action buttons -->
              <div class="flex flex-wrap gap-3 mt-auto pt-4">
                <button id="followBtn"
                  data-manga-id="<?= (int)$manga['id'] ?>"
                  data-bookmarked="<?= !empty($isBookmarked) ? '1' : '0' ?>"
                  data-login-url="/login"
                  data-loggedin="<?= !empty($currentUser) ? '1' : '0' ?>"
                  class="group flex items-center gap-2 h-12 px-4 rounded-2xl cursor-pointer select-none bg-white dark:bg-[#2e2e43] border border-gray-200 dark:border-gray-700/50 shadow-md hover:shadow-lg transition-all duration-300">
                  <svg id="followIcon" fill="<?= !empty($isBookmarked) ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-violet-500 group-hover:scale-110 transition-transform duration-200">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                  </svg>
                  <span id="followText" class="text-sm font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">
                    <?= !empty($isBookmarked) ? 'Following' : 'Follow' ?>
                  </span>
                </button>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                  var btn = document.getElementById('followBtn');
                  if (!btn) return;
                  btn.addEventListener('click', function() {
                    if (btn.dataset.loggedin !== '1') {
                      window.location.href = btn.dataset.loginUrl;
                      return;
                    }
                    var mangaId = btn.dataset.mangaId;
                    fetch('/api/bookmark/toggle', {
                      method: 'POST',
                      headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                      body: '<?= csrf_token() ?>=' + encodeURIComponent('<?= csrf_hash() ?>') + '&manga_id=' + mangaId
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                      var icon = document.getElementById('followIcon');
                      var text = document.getElementById('followText');
                      if (data.bookmarked) {
                        btn.dataset.bookmarked = '1';
                        icon.setAttribute('fill', 'currentColor');
                        text.textContent = 'Following';
                      } else {
                        btn.dataset.bookmarked = '0';
                        icon.setAttribute('fill', 'none');
                        text.textContent = 'Follow';
                      }
                    })
                    .catch(function() {});
                  });
                });
                </script>

                <?php if ($firstChap): ?>
                <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($firstChap['slug']) ?>">
                  <div class="group flex items-center gap-2 h-12 px-4 rounded-2xl cursor-pointer select-none bg-white dark:bg-[#2e2e43] border border-gray-200 dark:border-gray-700/50 shadow-md hover:shadow-lg transition-all duration-300">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-emerald-500 group-hover:scale-110 transition-transform duration-200">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">Read First</span>
                  </div>
                </a>
                <?php endif; ?>

                <?php if ($lastChap): ?>
                <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($lastChap['slug']) ?>">
                  <div class="group flex items-center gap-2 h-12 px-4 rounded-2xl cursor-pointer select-none bg-white dark:bg-[#272f3f] border border-gray-200 dark:border-gray-700/50 shadow-md hover:shadow-lg transition-all duration-300">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-rose-500 group-hover:scale-110 transition-transform duration-200">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.689c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061A1.125 1.125 0 0 1 3 16.811V8.69ZM12.75 8.689c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061a1.125 1.125 0 0 1-1.683-.977V8.69Z" />
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-100 whitespace-nowrap">Read Latest</span>
                  </div>
                </a>
                <?php endif; ?>
              </div>

            </div>
          </div>
        </div>

        <!-- Description -->
        <?php if (!empty($manga['summary'])): ?>
        <div class="justify-between border border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded-xl dark:shadow-gray-900 mb-4">
          <div class="flex flex-row truncate mb-4">
            <svg class="h-6 w-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h2 class="grow text-lg ml-1 font-semibold">Description</h2>
          </div>
          <div class="relative">
            <div id="pilot-content" class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed overflow-hidden transition-all duration-500 ease-in-out" style="max-height:4.5rem;line-height:1.5rem;">
              <?= nl2br(esc($manga['summary'])) ?>
            </div>
            <div id="pilot-gradient" class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-fire-blue to-transparent pointer-events-none transition-opacity duration-500"></div>
            <button id="pilot-toggle" class="mt-3 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm transition-all duration-300 hover:scale-105 rounded px-2 py-1">
              <span id="toggle-text">Show more</span>
              <svg id="toggle-icon" class="inline-block w-4 h-4 ml-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
          </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          const el = document.getElementById('pilot-content');
          const grad = document.getElementById('pilot-gradient');
          const btn = document.getElementById('pilot-toggle');
          const txt = document.getElementById('toggle-text');
          const ico = document.getElementById('toggle-icon');
          if (!el || !btn) return;
          const fullH = el.scrollHeight;
          if (fullH <= 72) { btn.style.display='none'; grad.style.display='none'; el.style.maxHeight='none'; return; }
          let open = false;
          btn.addEventListener('click', function() {
            open = !open;
            el.style.maxHeight = open ? fullH + 'px' : '4.5rem';
            grad.style.opacity = open ? '0' : '1';
            txt.textContent = open ? 'Show less' : 'Show more';
            ico.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
          });
        });
        </script>
        <?php endif; ?>

        <!-- Chapters List -->
        <div class="justify-between border border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded-xl dark:shadow-gray-900 mb-4">
          <div class="flex flex-row items-center mb-4">
            <svg class="h-6 w-6 text-emerald-500 dark:text-emerald-400 pt-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 12h6" /><path d="M9 16h6" /><path d="M9 8h6" />
              <path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16l-2.5-1.5L14 21l-2.5-1.5L9 21l-2.5-1.5L5 21z" />
            </svg>
            <h2 class="grow text-lg ml-1 font-semibold">Chapter List</h2>
            <span class="text-sm text-gray-400"><?= count($chapters) ?> chapters</span>
          </div>

          <div class="mb-4">
            <input id="chapterSearch" type="number" pattern="[0-9]*" inputmode="numeric"
              placeholder="Jump to chapter..."
              class="border border-gray-300 dark:border-gray-600 rounded py-2 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-fire-blue">
          </div>

          <?php if (empty($chapters)): ?>
            <p class="text-center text-gray-400 py-4">No chapters available.</p>
          <?php else: ?>
          <ul id="chapterList" class="overflow-y-auto overflow-x-hidden" style="max-height:500px">
            <?php foreach ($chapters as $i => $chap):
              $rowBg = $i % 2 === 0 ? 'bg-gray-50 dark:bg-light-blue' : '';
              $chapAgo = format_chapter_date($chap['updated_at'] ?? $chap['created_at'] ?? null);
            ?>
            <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($chap['slug']) ?>" class="block" data-num="<?= (float)$chap['number'] ?>">
              <li class="py-1 px-2 hover:bg-gray-200 dark:hover:bg-light-blue flex gap-3 relative <?= $rowBg ?>">
                <div class="grow truncate flex items-center">
                  <span class="text-ellipsis"><?= esc($chap['name']) ?></span>
                </div>
                <div class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                  <?php if ($chap['view'] > 0): ?>
                  <span><?= number_format($chap['view']) ?></span>
                  <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <?php endif; ?>
                  <span class="ml-1 whitespace-nowrap"><?= $chapAgo ?></span>
                </div>
              </li>
            </a>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
          const input = document.getElementById('chapterSearch');
          const list  = document.getElementById('chapterList');
          if (!input || !list) return;
          input.addEventListener('input', function() {
            const q = parseFloat(this.value);
            const items = list.querySelectorAll('a[data-num]');
            items.forEach(function(a) {
              if (!this.value || parseFloat(a.dataset.num) === q) {
                a.style.display = '';
              } else {
                a.style.display = 'none';
              }
            }, this);
          });
        });
        </script>
        <!-- Comment Section -->
        <div id="dc-section" class="justify-between border-2 border-gray-100 dark:border-dark-blue p-3 bg-white dark:bg-fire-blue shadow-md rounded dark:shadow-gray-900">
        <style>
        .av-frame{position:relative;display:inline-flex;align-items:center;justify-content:center;width:var(--frame-size,40px);height:var(--frame-size,40px);border-radius:50%;flex-shrink:0}
        .av-frame-img{width:100%;height:100%;border-radius:50%;object-fit:cover}
        .av-t2 .av-frame-img{border:2px solid #9ca3af}
        .dark .av-t2 .av-frame-img{border-color:#6b7280}
        .av-t3 .av-frame-img{border:2px solid #3b82f6;box-shadow:0 0 6px rgba(59,130,246,.5)}
        .av-t4{background:linear-gradient(135deg,#8b5cf6,#a855f7,#7c3aed);padding:2px}
        .av-t4 .av-frame-img{border:none}
        .av-t5{background:linear-gradient(135deg,#f59e0b,#eab308,#d97706);padding:3px;box-shadow:0 0 10px rgba(245,158,11,.4)}
        .av-t5 .av-frame-img{border:none}
        .av-t6{background:linear-gradient(135deg,#f59e0b,#ef4444,#8b5cf6,#3b82f6,#f59e0b);background-size:300% 300%;animation:av-glow 3s ease infinite;padding:3px;box-shadow:0 0 12px rgba(139,92,246,.5)}
        .av-t6 .av-frame-img{border:none}
        @keyframes av-glow{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
        @media(prefers-reduced-motion:reduce){.av-t6{animation:none}}
        #dc-pg{display:flex;justify-content:center;align-items:center;gap:4px;flex-wrap:wrap;margin-top:12px}
        #dc-pg button{display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 6px;border-radius:6px;font-size:13px;border:1px solid #e5e7eb;background:#fff;color:#374151;cursor:pointer;transition:background .15s}
        .dark #dc-pg button{border-color:#374151;background:#1f2937;color:#9ca3af}
        #dc-pg button:hover:not([disabled]):not(.pg-active){background:#f3f4f6}
        .dark #dc-pg button:hover:not([disabled]):not(.pg-active){background:#374151;color:#e5e7eb}
        #dc-pg .pg-active{background:#6366f1!important;border-color:#6366f1!important;color:#fff!important;font-weight:700;pointer-events:none}
        #dc-pg button[disabled]{opacity:.4;cursor:default;pointer-events:none}
        </style>
        <div class="flex flex-row items-center truncate mb-4">
            <svg class="h-6 w-6 dark:text-white flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z"/>
                <path d="M3 20l1.3-3.9a9 8 0 1 1 3.4 2.9l-4.7 1"/>
                <line x1="12" y1="12" x2="12" y2="12.01"/>
                <line x1="8" y1="12" x2="8" y2="12.01"/>
                <line x1="16" y1="12" x2="16" y2="12.01"/>
            </svg>
            <span class="grow text-lg ml-1 font-semibold truncate">Comments <span id="dc-count" class="text-sm font-normal text-gray-400"></span></span>
            <select id="dc-order" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 text-xs rounded px-2 py-1 outline-none ml-2 flex-shrink-0">
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
                <option value="top">Top</option>
            </select>
        </div>

        <?php if (!$currentUser): ?>
        <p class="py-4 text-center font-normal"><a class="font-semibold hover:underline" href="/login">Login</a> or <a class="font-semibold hover:underline" href="/register">Register</a> to join the conversation</p>
        <?php else: ?>
        <form id="dc-form" class="mb-4">
            <div class="w-full border border-gray-200 dark:border-dark-blue rounded px-2 py-3 shadow">
                <textarea id="dc-input" rows="3" maxlength="1000" placeholder="Write a comment..." class="bg-gray-100 rounded border border-gray-400 dark:border-dark-blue dark:bg-light-blue leading-normal resize-none w-full py-2 px-3 placeholder-gray-700 dark:placeholder-gray-400 focus:outline-none focus:bg-white dark:focus:bg-gray-700 text-sm"></textarea>
                <!-- Captcha box -->
                <div id="dc-captcha-box" style="display:none" class="mt-2 p-3 rounded-lg bg-gray-800 border border-gray-600">
                    <p class="text-xs text-gray-400 mb-2 font-medium">You just commented. Solve the captcha to continue:</p>
                    <div class="flex items-center gap-3">
                        <span id="dc-captcha-q" class="text-base font-bold text-white tracking-wide"></span>
                        <span class="text-sm font-semibold text-gray-400">= ?</span>
                        <input id="dc-captcha-ans" type="number" min="0" max="99"
                               class="w-16 border border-gray-500 rounded-lg px-2 py-1 text-sm font-bold outline-none focus:border-indigo-400"
                               style="background:#1f2937;color:#f9fafb"
                               placeholder="0">
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2 px-1">
                    <span id="dc-char" class="text-xs text-gray-400">0 / 1000</span>
                    <button type="submit" class="bg-gray-300 dark:bg-light-blue px-3 py-2 border-2 rounded-lg dark:border-dark-blue hover:bg-gray-200 dark:hover:bg-gray-700 text-xs cursor-pointer">Post comment</button>
                </div>
            </div>
        </form>
        <?php endif; ?>

        <!-- Comment List -->
        <div class="w-full py-2 px-3 flex flex-col space-y-2" id="dc-list">
            <p class="py-4 text-center text-sm text-gray-400">Loading...</p>
        </div>

        <!-- Pagination -->
        <div id="dc-pg"></div>
        </div>

      <script>
      (function() {
        var MANGA_ID    = <?= (int) $manga['id'] ?>;
        var MANGA_SLUG  = <?= json_encode($manga['slug']) ?>;
        var CURRENT_UID = <?= $currentUser ? (int)$currentUser['id'] : 0 ?>;
        var page = 1, totalPages = 1, loading = false, order = 'newest';
        var BG = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];

        function esc(s){ return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

        function timeAgo(str){
          var d=new Date(str.replace(' ','T'));
          var diff=Math.floor((Date.now()-d.getTime())/1000);
          if(diff<60)      return diff+'s ago';
          if(diff<3600)    return Math.floor(diff/60)+'m ago';
          if(diff<86400)   return Math.floor(diff/3600)+'h ago';
          if(diff<604800)  return Math.floor(diff/86400)+'d ago';
          return Math.floor(diff/604800)+'w ago';
        }

        function avatar(name, username, uid, size){
          var sz = size||40;
          var ch = ((name||username||'?')[0]).toUpperCase();
          var bg = BG[parseInt(uid||0)%6];
          if(sz < 40){
            return '<div style="width:'+sz+'px;height:'+sz+'px;border-radius:50%;background:'+bg+
              ';display:flex;align-items:center;justify-content:center;font-size:'+(sz*0.4)+'px'+
              ';font-weight:700;color:#fff;flex-shrink:0">'+ch+'</div>';
          }
          var t=(parseInt(uid||0)%6)+1;
          return '<div class="av-frame av-t'+t+'" style="--frame-size:40px">'+
            '<div class="av-frame-img" style="background:'+bg+';display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff">'+ch+'</div>'+
            '</div>';
        }

        var likeIconSvg = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>';

        function likeBtnHtml(c){
          var isLiked = c.my_reaction==='like';
          if(CURRENT_UID>0){
            return '<button class="dc-react flex items-center space-x-1 px-0.5 py-0.5 transition-colors '+(isLiked?'text-blue-500':'text-gray-400 dark:text-gray-500 hover:text-blue-400')+'" data-id="'+c.id+'" data-type="like">'+likeIconSvg+'<span class="text-xs like-count">'+c.likes_count+'</span></button>';
          }
          return '<span class="flex items-center space-x-1 text-gray-400 dark:text-gray-500">'+likeIconSvg+'<span class="text-xs">'+c.likes_count+'</span></span>';
        }

        function renderReply(c, topParentId){
          var name = c.user_name||c.user_username||'?';
          var replyBtn = (CURRENT_UID>0 && topParentId)
            ? '<button class="dc-reply-btn text-xs text-gray-400 dark:text-gray-500 hover:text-indigo-400 transition-colors px-1.5 py-0.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700" data-id="'+topParentId+'" data-reply-to="'+c.id+'" data-name="'+esc(name)+'">↩ Reply</button>'
            : '';
          return '<div class="dc-item mt-1.5 py-0.5" data-id="'+c.id+'">'+
            '<div class="flex space-x-2">'+
            '<div class="flex-shrink-0 self-start mt-0.5">'+avatar(c.user_name,c.user_username,c.user_id,30)+'</div>'+
            '<div class="flex-auto w-0">'+
            '<div class="bg-gray-100 dark:bg-gray-600 rounded-xl px-3 pb-1.5 pt-1">'+
            '<div class="mb-0.5"><span class="font-bold text-sm text-gray-900 dark:text-gray-100">'+esc(name)+'</span></div>'+
            '<p class="text-sm break-words whitespace-pre-wrap text-gray-800 dark:text-gray-200">'+esc(c.comment)+'</p>'+
            '</div>'+
            '<div class="flex items-center justify-between text-xs px-1 mt-0.5">'+
            '<div class="flex items-center gap-x-2 font-semibold text-gray-700 dark:text-gray-400">'+
            likeBtnHtml(c)+'<small>·</small><small>'+timeAgo(c.created_at)+'</small>'+
            '</div>'+
            (replyBtn ? '<div>'+replyBtn+'</div>' : '')+
            '</div>'+
            '</div>'+
            '</div>'+
            '</div>';
        }

        function replyFormHtml(parentId, parentName, replyToId){
          return '<div class="mt-2" id="dc-rf-'+parentId+'">'+
            '<input type="hidden" class="dc-reply-to-id" value="'+(replyToId||0)+'">'+
            '<textarea class="dc-reply-input w-full bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-xl px-3 py-2 text-sm resize-none focus:outline-none focus:border-indigo-500" rows="2" maxlength="1000">@'+esc(parentName)+' </textarea>'+
            '<div class="dc-rf-captcha-box hidden mt-2 p-3 rounded-lg bg-gray-800 border border-gray-600">'+
            '<p class="text-xs text-gray-400 mb-2 font-medium">You just commented. Solve the captcha to continue:</p>'+
            '<div class="flex items-center gap-3">'+
            '<span class="dc-rf-captcha-q text-base font-bold text-white tracking-wide"></span>'+
            '<span class="text-sm font-semibold text-gray-400">= ?</span>'+
            '<input class="dc-rf-captcha-ans w-16 border border-gray-500 rounded-lg px-2 py-1 text-sm font-bold outline-none focus:border-indigo-400" style="background:#1f2937;color:#f9fafb" type="number" min="0" max="99" placeholder="0">'+
            '</div>'+
            '</div>'+
            '<div class="flex justify-end gap-2 mt-1">'+
            '<button class="dc-reply-cancel text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 px-2 py-1" data-parent="'+parentId+'">Cancel</button>'+
            '<button class="dc-reply-submit bg-indigo-600 hover:bg-indigo-500 text-white text-xs px-3 py-1.5 rounded-lg transition-colors" data-parent="'+parentId+'">Reply</button>'+
            '</div>'+
            '</div>';
        }

        function fetchReplies(commentId, btn){
          var container = document.getElementById('dc-replies-'+commentId);
          if(!container) return;
          if(btn){ btn.disabled=true; btn.textContent='Loading…'; }
          fetch('/api/comments/'+commentId+'/replies')
            .then(function(r){return r.json();})
            .then(function(d){
              if(!d.replies||!d.replies.length){
                if(btn){ btn.disabled=false; var cnt=btn.dataset.count; btn.textContent=cnt+' repl'+(parseInt(cnt)===1?'y':'ies')+' ▾'; }
                return;
              }
              var LIMIT=5, visible=d.replies.slice(0,LIMIT), hidden=d.replies.slice(LIMIT);
              container.innerHTML = visible.map(function(r){ return renderReply(r, commentId); }).join('');
              if(hidden.length>0){
                var mBtn=document.createElement('button');
                mBtn.className='dc-show-more text-xs text-indigo-500 hover:text-indigo-400 mt-1 px-1 py-0.5';
                mBtn.textContent='Show '+hidden.length+' more repl'+(hidden.length===1?'y':'ies')+'…';
                mBtn.onclick=function(){ mBtn.remove(); container.insertAdjacentHTML('beforeend',hidden.map(function(r){return renderReply(r,commentId);}).join('')); };
                container.appendChild(mBtn);
              }
              container.classList.remove('hidden');
              if(btn){ btn.textContent='▴ hide'; btn.disabled=false; btn.dataset.open='1'; }
            })
            .catch(function(){
              if(btn){ btn.disabled=false; var cnt=btn.dataset.count; btn.textContent=cnt+' repl'+(parseInt(cnt)===1?'y':'ies')+' ▾'; }
            });
        }

        function renderCmt(c){
          var name       = c.user_name||c.user_username||'?';

          var rightBtns = '';
          if(CURRENT_UID>0){
            rightBtns += '<button class="dc-reply-btn text-xs text-gray-400 dark:text-gray-500 hover:text-indigo-400 transition-colors px-1.5 py-0.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700" data-id="'+c.id+'" data-name="'+esc(name)+'">↩ Reply</button>';
          }
          return '<div class="dc-item py-1" data-id="'+c.id+'">'+
            '<div class="flex space-x-3">'+
            '<div class="flex flex-shrink-0 self-start">'+avatar(c.user_name,c.user_username,c.user_id)+'</div>'+
            '<div class="flex-auto items-center justify-center w-0"><div class="block">'+
            '<div class="bg-gray-100 dark:bg-gray-600 w-auto rounded-xl px-3 pb-2 pt-1">'+
            '<div class="mb-1"><div class="flex items-center flex-wrap gap-2">'+
            '<span class="font-bold text-gray-900 dark:text-gray-100">'+esc(name)+'</span>'+
            (c.chapter_slug?'<a href="/manga/'+MANGA_SLUG+'/'+esc(c.chapter_slug)+'" class="text-xs bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded px-1.5 py-0.5 font-medium hover:underline" onclick="event.stopPropagation()">'+esc(c.chapter_name)+'</a>':'')+
            '</div></div>'+
            '<div class="dc-text"><p class="text-sm break-words whitespace-pre-wrap text-gray-800 dark:text-gray-200">'+esc(c.comment)+'</p></div>'+
            '</div>'+
            '<div class="flex items-center justify-between text-xs w-full px-1 mt-0.5">'+
            '<div class="flex items-center gap-x-2 font-semibold text-gray-700 dark:text-gray-400">'+
            likeBtnHtml(c)+
            '<small>·</small>'+
            '<small>'+timeAgo(c.created_at)+'</small>'+
            '</div>'+
            (rightBtns ? '<div class="flex items-center gap-x-1">'+rightBtns+'</div>' : '')+
            '</div>'+
            '<div id="dc-reply-area-'+c.id+'"></div>'+
            '<div id="dc-replies-'+c.id+'" class="pl-3 ml-1 mt-2 space-y-0.5" style="border-left:1px solid rgba(129,140,248,0.3)"></div>'+
            '</div></div>'+
            '</div>'+
            '</div>';
        }

        function renderPg(){
          var el=document.getElementById('dc-pg');
          if(!el) return;
          if(totalPages<=1){el.innerHTML='';return;}
          var h='';
          h+= page>1?'<button data-page="'+(page-1)+'">&#8249;</button>':'<button disabled>&#8249;</button>';
          var s=Math.max(1,page-2),e=Math.min(totalPages,s+4);s=Math.max(1,e-4);
          if(s>1){h+='<button data-page="1">1</button>';if(s>2)h+='<span style="padding:0 4px;color:#9ca3af">…</span>';}
          for(var i=s;i<=e;i++) h+=i===page?'<button class="pg-active">'+i+'</button>':'<button data-page="'+i+'">'+i+'</button>';
          if(e<totalPages){if(e<totalPages-1)h+='<span style="padding:0 4px;color:#9ca3af">…</span>';h+='<button data-page="'+totalPages+'">'+totalPages+'</button>';}
          h+= page<totalPages?'<button data-page="'+(page+1)+'">&#8250;</button>':'<button disabled>&#8250;</button>';
          el.innerHTML=h;
        }

        function fetchComments(p){
          if(loading) return;
          loading=true; page=p;
          fetch('/api/comments/manga/'+MANGA_ID+'/all?page='+p+'&order='+order)
            .then(function(r){return r.json();})
            .then(function(data){
              var list=document.getElementById('dc-list');
              var countEl=document.getElementById('dc-count');
              if(countEl) countEl.textContent=data.total>0?'('+data.total+')':'';
              totalPages=data.total>0?Math.ceil(data.total/10):1;
              list.innerHTML=(!data.comments||!data.comments.length)
                ?'<p class="py-4 text-center text-sm text-gray-400">No comments yet. Be the first!</p>'
                :data.comments.map(renderCmt).join('');
              if(data.comments) data.comments.forEach(function(c){
                if(parseInt(c.reply_count||0)>0) fetchReplies(c.id, null);
              });
              renderPg();
              loading=false;
            })
            .catch(function(){loading=false;});
        }

        fetchComments(1);

        var orderSel=document.getElementById('dc-order');
        if(orderSel) orderSel.addEventListener('change',function(){order=this.value;fetchComments(1);});

        var pgEl=document.getElementById('dc-pg');
        if(pgEl) pgEl.addEventListener('click',function(e){
          var btn=e.target.closest('[data-page]');
          if(!btn) return;
          var p=parseInt(btn.dataset.page);
          if(p&&p!==page){fetchComments(p);document.getElementById('dc-section').scrollIntoView({behavior:'smooth',block:'start'});}
        });

        var form=document.getElementById('dc-form');
        if(form){
          var inp=document.getElementById('dc-input');
          var charEl=document.getElementById('dc-char');
          var captchaReady=false;
          var LAST_KEY='lct_'+CURRENT_UID;
          inp.addEventListener('input',function(){charEl.textContent=inp.value.length+' / 1000';});

          function showCaptcha(question){
            document.getElementById('dc-captcha-q').textContent=question;
            document.getElementById('dc-captcha-box').style.display='block';
            captchaReady=true;
          }
          function hideCaptcha(){
            var box=document.getElementById('dc-captcha-box');
            if(box) box.style.display='none';
            var ans=document.getElementById('dc-captcha-ans');
            if(ans) ans.value='';
            captchaReady=false;
          }

          form.addEventListener('submit',function(e){
            e.preventDefault();
            var text=inp.value.trim();
            if(!text) return;

            var last=parseInt(localStorage.getItem(LAST_KEY)||'0');
            var withinLimit=(CURRENT_UID>0)&&((Date.now()-last)<300000);

            if(withinLimit&&!captchaReady){
              fetch('/api/captcha')
                .then(function(r){return r.json();})
                .then(function(d){showCaptcha(d.question);});
              return;
            }

            var fd=new FormData();
            fd.append('manga_id',MANGA_ID);
            fd.append('comment',text);
            if(captchaReady){
              var ans=document.getElementById('dc-captcha-ans');
              if(!ans||!ans.value.trim()){
                ans&&ans.focus();
                return;
              }
              fd.append('captcha_answer',ans.value.trim());
            }

            fetch('/api/comments',{method:'POST',body:fd})
              .then(function(r){return r.json();})
              .then(function(c){
                if(c.need_captcha){
                  fetch('/api/captcha')
                    .then(function(r){return r.json();})
                    .then(function(d){showCaptcha(d.question);});
                  return;
                }
                if(c.error){alert(c.error);return;}
                localStorage.setItem(LAST_KEY,Date.now());
                hideCaptcha();
                var list=document.getElementById('dc-list');
                var ph=list.querySelector('p');
                if(ph) ph.remove();
                c.reply_count=0;
                list.insertAdjacentHTML('afterbegin',renderCmt(c));
                inp.value=''; charEl.textContent='0 / 1000';
                var countEl=document.getElementById('dc-count');
                if(countEl){var cur=parseInt((countEl.textContent||'').replace(/\D/g,''))||0;countEl.textContent='('+(cur+1)+')';}
              })
              .catch(function(){alert('Something went wrong, please try again.');});
          });
        }

        document.getElementById('dc-list').addEventListener('click',function(e){
          var target=e.target.closest('button');
          if(!target) return;

          // Like / react
          if(target.classList.contains('dc-react')){
            var cid=parseInt(target.dataset.id);
            var type=target.dataset.type;
            var fd=new FormData();
            fd.append('type',type);
            fetch('/api/comments/'+cid+'/react',{method:'POST',body:fd})
              .then(function(r){return r.json();})
              .then(function(d){
                if(d.error) return;
                var item=target.closest('.dc-item');
                if(!item) return;
                var lb=item.querySelector('.dc-react[data-type="like"]');
                if(lb){
                  var isActive=d.my_reaction==='like';
                  lb.className=lb.className.replace(/text-blue-500|text-gray-400|dark:text-gray-500|hover:text-blue-400/g,'').trim();
                  lb.className+=' '+(isActive?'text-blue-500':'text-gray-400 dark:text-gray-500 hover:text-blue-400');
                  var lc=lb.querySelector('.like-count');
                  if(lc) lc.textContent=d.likes_count;
                }
              });
            return;
          }

          // Reply button → hiển thị form inline
          if(target.classList.contains('dc-reply-btn')){
            var parentId=target.dataset.id;
            var parentName=target.dataset.name;
            var replyToId=target.dataset.replyTo||0;
            var area=document.getElementById('dc-reply-area-'+parentId);
            if(!area) return;
            var existing=document.getElementById('dc-rf-'+parentId);
            if(existing){ existing.remove(); return; }
            area.innerHTML=replyFormHtml(parentId,parentName,replyToId);
            var ta=area.querySelector('.dc-reply-input');
            ta.focus();
            ta.setSelectionRange(ta.value.length, ta.value.length);
            return;
          }

          // Cancel reply
          if(target.classList.contains('dc-reply-cancel')){
            var rf=document.getElementById('dc-rf-'+target.dataset.parent);
            if(rf) rf.remove();
            return;
          }

          // Submit reply
          if(target.classList.contains('dc-reply-submit')){
            var parentId=target.dataset.parent;
            var rf=document.getElementById('dc-rf-'+parentId);
            if(!rf) return;
            var ta=rf.querySelector('.dc-reply-input');
            var text=ta?ta.value.trim():'';
            if(!text) return;
            // Kiểm tra captcha nếu đang hiển thị
            var captchaBox=rf.querySelector('.dc-rf-captcha-box');
            var captchaVisible=captchaBox&&!captchaBox.classList.contains('hidden');
            if(captchaVisible){
              var captchaAns=rf.querySelector('.dc-rf-captcha-ans');
              if(!captchaAns||!captchaAns.value.trim()){ captchaAns&&captchaAns.focus(); return; }
            }
            target.disabled=true; target.textContent='Sending…';
            var replyToInput=rf.querySelector('.dc-reply-to-id');
            var fd=new FormData();
            fd.append('manga_id',MANGA_ID);
            fd.append('comment',text);
            fd.append('parent_comment',parentId);
            fd.append('reply_to_id',replyToInput?replyToInput.value:0);
            if(captchaVisible){
              var captchaAns=rf.querySelector('.dc-rf-captcha-ans');
              if(captchaAns&&captchaAns.value.trim()) fd.append('captcha_answer',captchaAns.value.trim());
            }
            fetch('/api/comments',{method:'POST',body:fd})
              .then(function(r){return r.json();})
              .then(function(c){
                if(c.need_captcha){
                  fetch('/api/captcha')
                    .then(function(r){return r.json();})
                    .then(function(d){
                      var box=rf.querySelector('.dc-rf-captcha-box');
                      var q=rf.querySelector('.dc-rf-captcha-q');
                      var ans=rf.querySelector('.dc-rf-captcha-ans');
                      if(box) box.classList.remove('hidden');
                      if(q) q.textContent=d.question;
                      if(ans){ ans.value=''; ans.focus(); }
                    });
                  target.disabled=false; target.textContent='Reply';
                  return;
                }
                if(c.error){ alert(c.error); target.disabled=false; target.textContent='Reply'; return; }
                rf.remove();
                var container=document.getElementById('dc-replies-'+parentId);
                if(container){
                  var moreBtn=container.querySelector('.dc-show-more');
                  if(moreBtn) moreBtn.insertAdjacentHTML('beforebegin',renderReply(c,parentId));
                  else container.insertAdjacentHTML('beforeend',renderReply(c,parentId));
                }
              })
              .catch(function(){ target.disabled=false; target.textContent='Reply'; alert('Something went wrong, please try again.'); });
            return;
          }

        });
      })();
      </script>

      </div><!-- end left col -->

      <!-- Right column (1/3) - Sidebar -->
      <div class="md:col-span-1">

        <!-- Recommended manga -->
        <?php if (!empty($recommended)): ?>
        <div class="mb-4 flex flex-col rounded-xl w-full border border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md">
          <div class="p-4 flex flex-col justify-start">
            <h3 class="text-xl capitalize font-semibold flex items-center mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-red-500 inline-block mr-2 flex-shrink-0">
                <path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd" />
              </svg>
              Hot Today
            </h3>
            <div class="gap-3 grid">
              <?php foreach ($recommended as $i => $rec):
                $recCover = manga_cover_url($rec, $cdnBase);
                $rank = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
              ?>
              <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-light-blue transition-colors">
                <span class="text-2xl font-black text-gray-200 dark:text-gray-700 w-8 text-center flex-shrink-0"><?= $rank ?></span>
                <a href="/manga/<?= esc($rec['slug']) ?>" class="flex-shrink-0">
                  <img src="<?= $recCover ?>" alt="<?= esc($rec['name']) ?>" class="w-12 h-16 object-cover rounded-lg shadow"
                    onerror="this.src='https://via.placeholder.com/48x64?text=<?= $rank ?>'">
                </a>
                <div class="flex-1 min-w-0">
                  <a href="/manga/<?= esc($rec['slug']) ?>" class="text-sm font-semibold hover:text-blue-500 transition-colors block" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.35">
                    <?= esc($rec['name']) ?>
                  </a>
                  <?php if ($rec['chapter_1'] > 0): ?>
                  <span class="text-xs text-gray-400 mt-0.5 block">Ch. <?= rtrim(rtrim(number_format((float)$rec['chapter_1'], 1), '0'), '.') ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</main>
<?= $this->endSection() ?>
