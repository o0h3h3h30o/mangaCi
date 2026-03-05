<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
  <main>
    <div class="max-w-7xl mx-auto px-3 w-full mt-6">
      <?php $currentPage = (int) ($_GET['page'] ?? 1); ?>
      <?php if ($currentPage <= 1): ?>

      <div class="mb-4">
        <div class="flex flex-col w-full mt-4">
          <div class="p-2 flex flex-col justify-start">
            <h2 class="text-xl capitalize font-semibold flex items-center justify-between">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                  class="h-6 w-6 text-green-500 inline-block mb-1">
                  <path fill-rule="evenodd"
                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 9a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V15a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V9Z"
                    clip-rule="evenodd" />
                </svg>
                <a href="/search" class="inline-flex items-center ml-2">
                  <span class="text-xl">Newest Manga</span>
                </a>
              </span>
              <a href="/search" class="inline-flex items-center ml-2">
                <span class="p-2 bg-dark-blue rounded-full">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                    <path fill-rule="evenodd"
                      d="M13.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L11.69 12 4.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                      clip-rule="evenodd" />
                    <path fill-rule="evenodd"
                      d="M19.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06L17.69 12l-6.97-6.97a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                      clip-rule="evenodd" />
                  </svg>
                </span>
              </a>
            </h2>
          </div>
        </div>
        <div class="swiper newest-swiper">
          <div class="swiper-wrapper">
            <?php foreach ($newestManga as $manga): ?>
            <div class="swiper-slide">
              <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue
           hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover
           hover:shadow-lg transition-all duration-300
           manga-vertical overflow-hidden">
                <div class="relative">
                  <div class="absolute top-0 right-0 z-10">
                    <span
                      class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-semibold px-2 py-1 rounded-bl-md rounded-tr-md shadow-lg backdrop-blur-sm">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                          clip-rule="evenodd" />
                      </svg>
                      New
                    </span>
                  </div>
                  <a href="manga/<?= esc($manga['slug']) ?>" title="<?= esc($manga['name']) ?>">
                    <div class="cover-frame">
                      <img src="<?= esc(manga_cover_url($manga)) ?>" alt="<?= esc($manga['name']) ?>" class="cover"
                        width="300" height="405" loading="lazy" decoding="async">
                    </div>
                  </a>
                </div>
                <div class="p-3">
                  <h3 class="text-base font-semibold truncate">
                    <a href="manga/<?= esc($manga['slug']) ?>"
                      class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                      <?= esc($manga['name']) ?>
                    </a>
                  </h3>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>
      </div>
      <div class="mb-4">
        <div class="flex flex-col w-full mt-4">
          <div class="p-2 flex flex-col justify-start">
            <h2 class="text-xl capitalize font-semibold">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="h-6 w-6 text-red-500 inline-block mb-1">
                <path fill-rule="evenodd"
                  d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z"
                  clip-rule="evenodd" />
              </svg>
              <span>Today's Hot Manga</span>
            </h2>
          </div>
        </div>
        <div class="swiper hot-today-swiper mt-4">
          <div class="swiper-wrapper">
            <?php foreach ($hotToday as $manga): ?>
            <div class="swiper-slide">
              <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue
           hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover
           hover:shadow-lg transition-all duration-300
           manga-vertical overflow-hidden">
                <div class="relative">
                  <div class="absolute top-0 right-0 z-10">
                    <span
                      class="inline-flex items-center gap-1 bg-gradient-to-r from-red-500 to-pink-600 text-white text-xs font-semibold px-2 py-1 rounded-bl-md rounded-tr-md shadow-lg backdrop-blur-sm">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                          d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      Hot
                    </span>
                  </div>
                  <a href="manga/<?= esc($manga['slug']) ?>" title="<?= esc($manga['name']) ?>">
                    <div class="cover-frame">
                      <img src="<?= esc(manga_cover_url($manga)) ?>" alt="<?= esc($manga['name']) ?>" class="cover"
                        width="300" height="405" loading="lazy" decoding="async">
                    </div>
                  </a>
                </div>
                <div class="p-3">
                  <h3 class="text-base font-semibold mb-2 truncate">
                    <a href="manga/<?= esc($manga['slug']) ?>"
                      class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                      <?= esc($manga['name']) ?>
                    </a>
                  </h3>
                  <div class="flex items-center gap-2 overflow-hidden">
                    <?php if ($manga['chapter_1'] > 0): ?>
                    <h4 class="flex-shrink-0">
                      <a href="manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug']) ?>"
                        class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-xs font-medium whitespace-nowrap">
                        Chapter <?= esc($manga['chapter_1']) ?>
                      </a>
                    </h4>
                    <?php endif; ?>
                    <div class="flex items-center gap-1 text-gray-400 text-xs flex-shrink min-w-0">
                      <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                          clip-rule="evenodd"></path>
                      </svg>
                      <span class="whitespace-nowrap overflow-hidden text-ellipsis"><?= number_format($manga['view_day'] ?? 0) ?> views</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>
      </div>
      <?php endif; ?>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Main -->
        <div class="md:col-span-2">
          <div x-data="{ activeTab: 'new' }">
            <div
              class="flex flex-col rounded-xl w-full border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md border">
              <div class="flex flex-col justify-start">
                <div class="flex border-b border-gray-200 dark:border-gray-700">
                  <button @click="activeTab = 'new'"
                    :class="activeTab === 'new' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-6 py-3 font-semibold text-sm uppercase tracking-wider transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 mb-0.5" fill="none"
                      viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Manga
                  </button>
                  <button @click="activeTab = 'updated'"
                    :class="activeTab === 'updated' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-6 py-3 font-semibold text-sm uppercase tracking-wider transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 mb-0.5" fill="none"
                      viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Recently Updated
                  </button>
                </div>
                <div class="p-4">
                  <div x-show="activeTab === 'new'" x-transition>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                      <?php foreach ($recentlyUpdated as $manga): ?>
                      <div class="w-full">
                        <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue
           hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover
           hover:shadow-lg transition-all duration-300
           manga-vertical overflow-hidden">
                          <div class="relative">
                            <a href="manga/<?= esc($manga['slug']) ?>" title="<?= esc($manga['name']) ?>">
                              <div class="cover-frame">
                                <img src="<?= esc(manga_cover_url($manga)) ?>" alt="<?= esc($manga['name']) ?>" class="cover"
                                  loading="lazy" decoding="async">
                              </div>
                            </a>
                          </div>
                          <div class="p-3">
                            <h3 class="text-base font-semibold mb-2 truncate">
                              <a href="manga/<?= esc($manga['slug']) ?>"
                                class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                                <?= esc($manga['name']) ?>
                              </a>
                            </h3>
                            <div class="flex items-center gap-2 overflow-hidden">
                              <h4 class="flex-shrink-0">
                                <a href="manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug']) ?>"
                                  class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-xs font-medium whitespace-nowrap">
                                  Chapter <?= esc($manga['chapter_1']) ?>
                                </a>
                              </h4>
                              <div class="flex items-center gap-1 text-gray-400 text-xs flex-shrink min-w-0">
                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"></path>
                                </svg>
                                <span class="whitespace-nowrap overflow-hidden text-ellipsis"><?php
                                  $diff = time() - ($manga['update_at'] ?? 0);
                                  if ($diff < 60) echo $diff . 's ago';
                                  elseif ($diff < 3600) echo floor($diff / 60) . 'm ago';
                                  elseif ($diff < 86400) echo floor($diff / 3600) . 'h ago';
                                  elseif ($diff < 2592000) echo floor($diff / 86400) . 'd ago';
                                  else echo date('d/m/Y', $manga['update_at']);
                                ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <div x-show="activeTab === 'updated'" x-transition style="display:none">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                      <?php foreach ($newestManga as $manga): ?>
                      <div class="w-full">
                        <div class="border rounded-lg border-gray-300 dark:border-dark-blue bg-white dark:bg-fire-blue
           hover:dark:bg-light-blue-hover hover:dark:border-dark-blue-hover
           hover:shadow-lg transition-all duration-300
           manga-vertical overflow-hidden">
                          <div class="relative">
                            <a href="manga/<?= esc($manga['slug']) ?>" title="<?= esc($manga['name']) ?>">
                              <div class="cover-frame">
                                <img src="<?= esc(manga_cover_url($manga)) ?>" alt="<?= esc($manga['name']) ?>" class="cover"
                                  loading="lazy" decoding="async">
                              </div>
                            </a>
                          </div>
                          <div class="p-3">
                            <h3 class="text-base font-semibold mb-2 truncate">
                              <a href="manga/<?= esc($manga['slug']) ?>"
                                class="text-blue-700 dark:text-blue-300 hover:text-blue-900 dark:hover:text-blue-100 transition-colors">
                                <?= esc($manga['name']) ?>
                              </a>
                            </h3>
                            <div class="flex items-center gap-2 overflow-hidden">
                              <h4 class="flex-shrink-0">
                                <a href="manga/<?= esc($manga['slug']) ?>/<?= esc($manga['chap_1_slug']) ?>"
                                  class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-xs font-medium whitespace-nowrap">
                                  Chapter <?= esc($manga['chapter_1']) ?>
                                </a>
                              </h4>
                              <div class="flex items-center gap-1 text-gray-400 text-xs flex-shrink min-w-0">
                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd"></path>
                                </svg>
                                <span class="whitespace-nowrap overflow-hidden text-ellipsis"><?php
                                  $diff = time() - ($manga['update_at'] ?? 0);
                                  if ($diff < 60) echo $diff . 's ago';
                                  elseif ($diff < 3600) echo floor($diff / 60) . 'm ago';
                                  elseif ($diff < 86400) echo floor($diff / 3600) . 'h ago';
                                  elseif ($diff < 2592000) echo floor($diff / 86400) . 'd ago';
                                  else echo date('d/m/Y', $manga['update_at']);
                                ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <?= $pager->links() ?>
                </div>
              </div>
            </div>

          </div>
          <!-- Livewire Component wire-end -->
        </div>
        <!-- Sidebar -->
        <div class="md:col-span-1">

          <div
            class="flex flex-col rounded-xl w-full border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md border">
            <div class="flex flex-col justify-start">
              <h2 class="text-xl mb-2 uppercase font-semibold pt-4 pr-4 pl-4">
                <svg class="h-6 w-6 text-blue-500 inline-block mb-1" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                  </path>
                </svg>
                <span>Comments</span>
              </h2>
              <div class="recent-comments pl-4 mb-4">
<?php if (empty($recentComments)): ?>
                <p class="text-sm text-gray-400 py-4 text-center">No comments yet.</p>
<?php else: ?>
<?php foreach ($recentComments as $i => $cmt):
    $isLast = ($i === count($recentComments) - 1);
    $borderClass = $isLast ? '' : 'border-b-2';
    $mangaName = esc($cmt['manga_name'] ?? '');
    $mangaSlug = esc($cmt['manga_slug'] ?? '');
    $chapName  = $cmt['chapter_name'] ?? '';
    $chapSlug  = $cmt['chapter_slug'] ?? '';
    $title     = $mangaName . ($chapName ? ' - ' . esc($chapName) : '');
    $url       = '/manga/' . $mangaSlug . ($chapSlug ? '/' . esc($chapSlug) : '');
    $commentText = esc(mb_strimwidth($cmt['comment'], 0, 100, '...'));
    $userName  = esc($cmt['user_name'] ?? $cmt['user_username'] ?? 'Anonymous');
    $initial   = mb_strtoupper(mb_substr(strip_tags($userName), 0, 1));
    $ts        = strtotime($cmt['created_at']);
    $diff      = time() - $ts;
    if ($diff < 60) $timeAgo = $diff . 's ago';
    elseif ($diff < 3600) $timeAgo = floor($diff/60) . 'm ago';
    elseif ($diff < 86400) $timeAgo = floor($diff/3600) . 'h ago';
    else $timeAgo = floor($diff/86400) . 'd ago';
?>
                <div class="w-full border-gray-200 dark:border-gray-700 py-3 <?= $borderClass ?>">
                  <div class="w-full truncate">
                    <a href="<?= $url ?>" class="text-ellipsis font-semibold"><?= $title ?></a>
                  </div>
                  <p class="py-2 text-sm dark:text-[#d5d5d5]"><?= $commentText ?></p>
                  <div class="flex mt-2 items-center">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
                      <?= $initial ?>
                    </div>
                    <p class="grow pl-2 text-sm"><?= $userName ?></p>
                    <p class="text-xs text-gray-400" style="margin-right:15px"><?= $timeAgo ?></p>
                  </div>
                </div>
<?php endforeach; ?>
<?php endif; ?>
              </div>
            </div>
          </div>


          <div x-data="{ topTab: 'day' }"
            class="flex flex-col rounded-xl w-full border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue shadow-md mt-6 border">
            <div class="flex flex-col justify-start">
              <h2 class="text-xl mb-2 uppercase font-semibold pt-4 pr-4 pl-4">
                <svg class="h-6 w-6 text-blue-500 inline-block mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span>Top Manga</span>
              </h2>
              <div class="flex border-b border-gray-200 dark:border-gray-700 mx-4">
                <button @click="topTab = 'day'"
                  :class="topTab === 'day' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                  class="px-4 py-2 font-semibold text-sm tracking-wider transition-colors">
                  Day
                </button>
                <button @click="topTab = 'month'"
                  :class="topTab === 'month' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                  class="px-4 py-2 font-semibold text-sm tracking-wider transition-colors">
                  Month
                </button>
                <button @click="topTab = 'all'"
                  :class="topTab === 'all' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                  class="px-4 py-2 font-semibold text-sm tracking-wider transition-colors">
                  All
                </button>
              </div>

              <?php
              function renderTopList($items, $viewField) {
                $html = '';
                foreach ($items as $i => $manga) {
                  $rank = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                  if ($i === 0) {
                    $badgeClass = 'text-red-500 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900 dark:to-red-800';
                  } elseif ($i === 1) {
                    $badgeClass = 'text-yellow-500 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900 dark:to-yellow-800';
                  } elseif ($i === 2) {
                    $badgeClass = 'text-green-500 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800';
                  } else {
                    $badgeClass = 'text-gray-500 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600';
                  }
                  $coverUrl = esc(manga_cover_url($manga));
                  $views = number_format($manga[$viewField] ?? 0);
                  $html .= '<div class="w-full hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 rounded-lg p-3 mb-2 border border-gray-200 dark:border-gray-700">';
                  $html .= '<div class="flex gap-3 w-full items-start">';
                  $html .= '<div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ' . $badgeClass . '">' . $rank . '</div>';
                  $html .= '<a href="manga/' . esc($manga['slug']) . '" class="flex-shrink-0 group" style="width:60px">';
                  $html .= '<div class="relative overflow-hidden rounded-lg shadow-sm group-hover:shadow-md transition-all duration-300">';
                  $html .= '<img src="' . $coverUrl . '" alt="' . esc($manga['name']) . '" class="rounded-lg w-full h-16 object-cover transform group-hover:scale-105 transition-all duration-300" loading="lazy" decoding="async">';
                  $html .= '</div></a>';
                  $html .= '<div class="flex-1 min-w-0 flex flex-col justify-between">';
                  $html .= '<div class="mb-2"><a href="manga/' . esc($manga['slug']) . '" class="font-semibold text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 line-clamp-2 leading-tight">' . esc($manga['name']) . '</a></div>';
                  $html .= '<div class="flex items-center justify-between text-sm">';
                  $html .= '<div class="flex-1 min-w-0 mr-3"><a href="manga/' . esc($manga['slug']) . '/' . esc($manga['chap_1_slug']) . '" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 truncate block text-xs">Chapter ' . esc($manga['chapter_1']) . '</a></div>';
                  $html .= '<div class="flex items-center space-x-1 text-gray-400 dark:text-gray-500 flex-shrink-0">';
                  $html .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
                  $html .= '<span class="text-xs">' . $views . '</span>';
                  $html .= '</div></div></div></div></div>';
                }
                return $html;
              }
              ?>

              <div class="px-4 py-3">
                <div x-show="topTab === 'day'" x-transition class="gap-2 grid">
                  <?= renderTopList($topDay, 'view_day') ?>
                </div>
                <div x-show="topTab === 'month'" x-transition style="display:none" class="gap-2 grid">
                  <?= renderTopList($topMonth, 'view_month') ?>
                </div>
                <div x-show="topTab === 'all'" x-transition style="display:none" class="gap-2 grid">
                  <?= renderTopList($topAll, 'views') ?>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
<?= $this->endSection() ?>
