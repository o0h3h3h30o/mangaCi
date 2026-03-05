<?= $this->extend('themes/default/layouts/main') ?>

<?= $this->section('head_extra') ?>
<link rel="preconnect" href="https://cdn2.manhwaraw18.com" crossorigin>
<link rel="dns-prefetch" href="https://cdn2.manhwaraw18.com">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $cdnChapter = rtrim(env('CDN_CHAPTER_URL', ''), '/');
    $prevUrl    = $prevChapter ? '/manga/' . $slug . '/' . $prevChapter['slug'] : null;
    $nextUrl = $nextChapter ? '/manga/' . $slug . '/' . $nextChapter['slug'] : null;
    $chapNum = rtrim(rtrim(number_format((float) $chapter['number'], 1), '0'), '.');
?>

<style>
  html, body { overflow-x: hidden; max-width: 100%; }
  @media(max-width:767px) { #chapter-content img { width: 100% !important; } }
  @media(max-width:767px) { #backToTop { bottom: 76px !important; } }
  @media(max-width:767px) { #bottom-nav { display: none; } }
  body { background: #0a0a0a !important; }
  body > span.bg { display: none !important; }

  .nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.06);
    color: #d1d5db;
    cursor: pointer;
    transition: all .2s;
    flex-shrink: 0;
  }
  .nav-btn:hover { background: rgba(99,102,241,0.2); border-color: rgba(99,102,241,0.4); color: #fff; }
  .nav-btn.disabled { opacity: 0.25; cursor: not-allowed; pointer-events: none; }

  .page-img { display: block; width: 100%; line-height: 0; }
  .page-img img { width: 100%; height: auto; display: block; }

  #btt {
    position: fixed;
    bottom: 68px;
    right: 20px;
    z-index: 100;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    border: none;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(99,102,241,.4);
    transition: opacity .3s;
  }
  #btt.show { display: flex; }

  .cover-sm {
    width: 50px;
    height: 67px;
    background-size: cover;
    background-position: center;
    border-radius: 4px;
    flex-shrink: 0;
  }

  /* Sticky mobile bottom nav */
  @media(min-width:768px) { #mobile-bottom-nav { display: none !important; } }
  #mobile-bottom-nav {
    position: fixed;
    bottom: 12px;
    left: 12px;
    right: 12px;
    z-index: 48;
    border-radius: .5rem;
    overflow: hidden;
    padding-bottom: env(safe-area-inset-bottom, 0px);
    transition: bottom .3s ease, opacity .3s ease;
  }
  #mobile-bottom-nav.hide {
    bottom: -100px;
    opacity: 0;
    pointer-events: none;
  }

  /* Image skeleton loader */
  .img-wrap {
    position: relative;
    line-height: 0;
  }
  .img-wrap.loading {
    min-height: 560px;
  }
  .img-wrap.loading::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, #161616 0%, #2a2a2a 40%, #161616 100%);
    background-size: 400% 100%;
    animation: shimmer 1.4s ease-in-out infinite;
  }
  /* spinner icon giữa skeleton */
  .img-wrap.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 1;
    width: 32px;
    height: 32px;
    margin: -16px 0 0 -16px;
    border: 3px solid rgba(255,255,255,0.08);
    border-top-color: rgba(99,102,241,0.7);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }
  @keyframes shimmer {
    0%   { background-position: 100% 0; }
    100% { background-position: -100% 0; }
  }
  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  /* Comment section - new UI */
  #cc-section { background: #111827; border: 1px solid #1f2937; border-radius: 12px; padding: 16px; margin-top: 16px; }
  #cc-section textarea { background: #1f2937; border: 1px solid #374151; color: #e5e7eb; border-radius: 8px; width: 100%; padding: 8px 12px; font-size: 13px; resize: none; outline: none; }
  #cc-section textarea:focus { border-color: #6366f1; }
  #cc-section select { background: #1f2937; border: 1px solid #374151; color: #9ca3af; border-radius: 6px; padding: 3px 8px; font-size: 12px; outline: none; }
  /* Avatar frames */
  .cc-av { position:relative; display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:50%; flex-shrink:0; }
  .cc-av-inner { width:100%; height:100%; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; color:#fff; }
  .cc-av-t2 .cc-av-inner { border:2px solid #6b7280; }
  .cc-av-t3 .cc-av-inner { border:2px solid #3b82f6; box-shadow:0 0 8px rgba(59,130,246,.6); }
  .cc-av-t4 { background:linear-gradient(135deg,#8b5cf6,#a855f7,#7c3aed); padding:2px; }
  .cc-av-t4 .cc-av-inner { border-radius:50%; }
  .cc-av-t5 { background:linear-gradient(135deg,#f59e0b,#eab308,#d97706); padding:3px; box-shadow:0 0 10px rgba(245,158,11,.4); }
  .cc-av-t5 .cc-av-inner { border-radius:50%; }
  .cc-av-t6 { background:linear-gradient(135deg,#f59e0b,#ef4444,#8b5cf6,#3b82f6,#f59e0b); background-size:300% 300%; animation:cc-glow 3s ease infinite; padding:3px; box-shadow:0 0 12px rgba(139,92,246,.5); }
  .cc-av-t6 .cc-av-inner { border-radius:50%; }
  @keyframes cc-glow { 0%,100%{background-position:0% 50%}50%{background-position:100% 50%} }
  @media(prefers-reduced-motion:reduce){.cc-av-t6{animation:none}}
  /* Comment items */
  .cc-item { padding: 8px 0; border-bottom: 1px solid #1f2937; }
  .cc-item:last-child { border-bottom: none; }
  .cc-bubble { background: #1f2937; border-radius: 12px; padding: 8px 12px; }
  /* Tabs */
  .cc-tab { padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid #374151; background: #1f2937; color: #9ca3af; transition: all .15s; }
  .cc-tab.cc-tab-active { background: #6366f1; border-color: #6366f1; color: #fff; }
  /* Pagination */
  #cc-pg { display: flex; justify-content: center; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 12px; }
  .ccpg { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 8px; border-radius: 6px; font-size: 13px; font-weight: 500; border: 1px solid #374151; background: #1f2937; color: #9ca3af; cursor: pointer; transition: background .15s; user-select: none; }
  .ccpg:hover:not(.ccpg-active):not(.ccpg-disabled) { background: #374151; color: #e5e7eb; }
  .ccpg-active { background: #6366f1; border-color: #6366f1; color: #fff; font-weight: 700; pointer-events: none; }
  .ccpg-disabled { color: #374151; cursor: default; pointer-events: none; }
</style>

<main style="background:#0a0a0a;min-height:100vh" x-data="{ listOpen: false }">
<div class="max-w-7xl mx-auto px-3 w-full pt-6" style="padding-bottom:64px">

    <!-- Mobile bottom nav -->
    <div id="mobile-bottom-nav" class="bg-white dark:bg-light-blue border border-gray-200 dark:border-gray-700 shadow-lg">
        <nav class="grid grid-cols-4">
            <?php if ($prevUrl): ?>
            <a href="<?= esc($prevUrl) ?>" class="border-r border-gray-200 dark:border-gray-700">
                <button class="flex justify-center py-3 cursor-pointer w-full hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                    </svg>
                </button>
            </a>
            <?php else: ?>
            <span class="border-r border-gray-200 dark:border-gray-700 flex justify-center py-3 opacity-30">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
            </span>
            <?php endif; ?>

            <a href="/manga/<?= esc($slug) ?>" class="border-r border-gray-200 dark:border-gray-700">
                <button class="flex justify-center py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
                    </svg>
                </button>
            </a>

            <button @click="listOpen = !listOpen" class="border-r border-gray-200 dark:border-gray-700 flex justify-center py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                </svg>
            </button>

            <?php if ($nextUrl): ?>
            <a href="<?= esc($nextUrl) ?>">
                <button class="flex justify-center py-3 cursor-pointer w-full hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                </button>
            </a>
            <?php else: ?>
            <span class="flex justify-center py-3 opacity-30">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                </svg>
            </span>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Breadcrumb -->
    <div class="flex py-3 px-5 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 dark:bg-light-blue dark:border-gray-700 shadow-md truncate mb-4" style="margin-top:15px">
        <ol class="flex flex-wrap items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="/" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">Home</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    <a href="/manga/<?= esc($slug) ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white truncate"><?= esc($manga['name']) ?></a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400"><?= esc($chapTitle) ?></span>
                </div>
            </li>
        </ol>
    </div>

    <!-- Title -->
    <div class="flex py-3 px-5 text-gray-700 bg-gray-50 rounded-lg border border-gray-200 dark:bg-light-blue dark:border-gray-700 shadow-md truncate mb-4">
        <a href="/manga/<?= esc($slug) ?>" class="ml-1 text-xl font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white truncate"><?= esc($manga['name']) ?></a>
        <span class="ml-1 text-xl font-medium text-gray-700 md:ml-2 dark:text-gray-400 truncate"> - <?= esc($chapTitle) ?></span>
    </div>

    <!-- Desktop nav (top) -->
    <nav class="grid grid-cols-4 mb-4 bg-white dark:bg-light-blue rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <?php if ($prevUrl): ?>
        <a href="<?= esc($prevUrl) ?>">
            <button class="flex justify-center items-center gap-2 py-3 cursor-pointer w-full hover:bg-gray-200 dark:hover:bg-gray-600">
                <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="11 7 6 12 11 17"/><polyline points="17 7 12 12 17 17"/>
                </svg>
                <span class="hidden sm:block text-sm dark:text-white">Prev</span>
            </button>
        </a>
        <?php else: ?>
        <span class="flex justify-center items-center gap-2 py-3 opacity-30">
            <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="11 7 6 12 11 17"/><polyline points="17 7 12 12 17 17"/>
            </svg>
            <span class="hidden sm:block text-sm dark:text-white">Prev</span>
        </span>
        <?php endif; ?>

        <a href="/manga/<?= esc($slug) ?>">
            <button class="flex justify-center items-center gap-2 py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 w-full">
                <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                    <rect x="10" y="12" width="4" height="4"/>
                </svg>
                <span class="hidden sm:block text-sm dark:text-white">Home</span>
            </button>
        </a>

        <button @click="listOpen = !listOpen" class="flex justify-center items-center gap-2 py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 w-full">
            <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
            <span class="hidden sm:block text-sm dark:text-white">Chapters</span>
        </button>

        <?php if ($nextUrl): ?>
        <a href="<?= esc($nextUrl) ?>">
            <button class="flex justify-center items-center gap-2 py-3 cursor-pointer w-full hover:bg-gray-200 dark:hover:bg-gray-600">
                <span class="hidden sm:block text-sm dark:text-white">Next</span>
                <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/>
                </svg>
            </button>
        </a>
        <?php else: ?>
        <span class="flex justify-center items-center gap-2 py-3 opacity-30">
            <span class="hidden sm:block text-sm dark:text-white">Next</span>
            <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/>
            </svg>
        </span>
        <?php endif; ?>
    </nav>

    <!-- Report Error Button (top) -->
    <div style="text-align:center;margin:4px 0 8px">
      <button class="rpt-open-btn" style="background:none;border:1px solid rgba(239,68,68,0.3);border-radius:8px;color:#ef4444;font-size:12px;padding:4px 12px;cursor:pointer;opacity:.7;transition:all .2s" onmouseover="this.style.opacity=1;this.style.borderColor='rgba(239,68,68,0.7)'" onmouseout="this.style.opacity=.7;this.style.borderColor='rgba(239,68,68,0.3)'">
        <span style="display:inline-flex;align-items:center;gap:5px">
          <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
          Report error
        </span>
      </button>
    </div>

    <!-- Chapter images -->
    <div class="text-center" id="chapter-content">
        <?php foreach ($pages as $i => $page):
            $imgSrc = !empty($page['image_local'])
                ? ($cdnChapter . '/' . $chapter['id'] . '/' . ltrim($page['image_local'], '/'))
                : trim($page['image']);
            $pageNum = (int) $page['slug'];
            $isFirst = $i < 3;
        ?>
        <div class="img-wrap loading">
        <img class="my-0 mx-auto<?= $isFirst ? '' : ' lazy' ?>" style="width:90%"
             src="<?= $isFirst ? esc($imgSrc) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" ?>"
             <?= $isFirst ? '' : 'data-src="' . esc($imgSrc) . '"' ?>
             fetchpriority="<?= $i === 0 ? 'high' : 'auto' ?>"
             decoding="async"
             onload="var w=this.parentNode;if(w)w.classList.remove('loading')"
             onerror="var w=this.parentNode;if(w)w.classList.remove('loading')"
             alt="<?= esc($manga['name']) ?> <?= esc($chapTitle) ?> - Page <?= $pageNum ?>">
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Bottom nav (desktop) -->
    <nav id="bottom-nav" class="grid grid-cols-4 mt-4 mb-4 bg-white dark:bg-light-blue rounded-lg shadow-md border border-gray-200 dark:border-gray-700">
        <?php if ($prevUrl): ?>
        <a href="<?= esc($prevUrl) ?>">
            <button class="flex justify-center items-center gap-2 py-3 cursor-pointer w-full hover:bg-gray-200 dark:hover:bg-gray-600">
                <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="11 7 6 12 11 17"/><polyline points="17 7 12 12 17 17"/>
                </svg>
                <span class="hidden sm:block text-sm dark:text-white">Prev</span>
            </button>
        </a>
        <?php else: ?>
        <span class="flex justify-center items-center gap-2 py-3 opacity-30">
            <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="11 7 6 12 11 17"/><polyline points="17 7 12 12 17 17"/>
            </svg>
            <span class="hidden sm:block text-sm dark:text-white">Prev</span>
        </span>
        <?php endif; ?>

        <a href="/manga/<?= esc($slug) ?>">
            <button class="flex justify-center items-center gap-2 py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 w-full">
                <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="5 12 3 12 12 3 21 12 19 12"/>
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"/>
                    <rect x="10" y="12" width="4" height="4"/>
                </svg>
                <span class="hidden sm:block text-sm dark:text-white">Home</span>
            </button>
        </a>

        <button @click="listOpen = !listOpen" class="flex justify-center items-center gap-2 py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 w-full">
            <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
            <span class="hidden sm:block text-sm dark:text-white">Chapters</span>
        </button>

        <?php if ($nextUrl): ?>
        <a href="<?= esc($nextUrl) ?>">
            <button class="flex justify-center items-center gap-2 py-3 cursor-pointer w-full hover:bg-gray-200 dark:hover:bg-gray-600">
                <span class="hidden sm:block text-sm dark:text-white">Next</span>
                <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/>
                </svg>
            </button>
        </a>
        <?php else: ?>
        <span class="flex justify-center items-center gap-2 py-3 opacity-30">
            <span class="hidden sm:block text-sm dark:text-white">Next</span>
            <svg class="h-6 w-6 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/>
            </svg>
        </span>
        <?php endif; ?>
    </nav>

  <!-- Report Error Button -->
  <div style="text-align:center;margin:12px 0">
    <button class="rpt-open-btn" style="background:none;border:1px solid rgba(239,68,68,0.3);border-radius:8px;color:#ef4444;font-size:12px;padding:5px 14px;cursor:pointer;transition:all .2s;opacity:.7" onmouseover="this.style.opacity=1;this.style.borderColor='rgba(239,68,68,0.7)'" onmouseout="this.style.opacity=.7;this.style.borderColor='rgba(239,68,68,0.3)'">
      <span style="display:inline-flex;align-items:center;gap:5px">
        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
        Report error
      </span>
    </button>
  </div>

  <!-- Report Modal -->
  <div id="rpt-modal" style="display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,0.7);align-items:center;justify-content:center;padding:16px">
    <div style="background:#1f2937;border:1px solid #374151;border-radius:14px;max-width:440px;width:100%;padding:20px;position:relative">
      <button id="rpt-close-btn" style="position:absolute;top:12px;right:14px;background:none;border:none;color:#6b7280;cursor:pointer;font-size:20px;line-height:1">×</button>
      <h3 style="color:#e5e7eb;font-size:15px;font-weight:600;margin:0 0 4px">Report Chapter Error</h3>
      <p style="color:#6b7280;font-size:12px;margin:0 0 14px"><?= esc($manga['name']) ?> — <?= esc($chapTitle) ?></p>

      <p style="color:#9ca3af;font-size:12px;margin:0 0 8px;font-weight:500">Reason <span style="color:#ef4444">*</span></p>
      <div id="rpt-reasons" style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px">
        <?php foreach ([
          'wrong_images'    => 'Wrong images / Not related to this chapter',
          'missing_pages'   => 'Missing pages',
          'low_quality'     => 'Low quality / Blurry images',
          'cant_load'       => 'Images not loading',
          'wrong_order'     => 'Pages in wrong order',
          'other'           => 'Other',
        ] as $val => $label): ?>
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:7px 10px;border-radius:8px;border:1px solid #374151;transition:border-color .15s" onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor=this.querySelector('input').checked?'#6366f1':'#374151'">
          <input type="radio" name="rpt-reason" value="<?= $val ?>" style="accent-color:#6366f1">
          <span style="color:#d1d5db;font-size:13px"><?= $label ?></span>
        </label>
        <?php endforeach; ?>
      </div>

      <textarea id="rpt-note" rows="2" maxlength="300" placeholder="Additional details (optional)" style="width:100%;background:#111827;border:1px solid #374151;color:#e5e7eb;border-radius:8px;padding:8px 10px;font-size:13px;resize:none;outline:none;box-sizing:border-box;margin-bottom:14px"></textarea>

      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button id="rpt-cancel-btn" style="background:none;border:1px solid #374151;color:#9ca3af;border-radius:8px;padding:7px 16px;font-size:13px;cursor:pointer">Cancel</button>
        <button id="rpt-submit-btn" style="background:#ef4444;border:none;color:#fff;border-radius:8px;padding:7px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">Submit Report</button>
      </div>
      <p id="rpt-msg" style="display:none;text-align:center;font-size:12px;margin-top:10px"></p>
    </div>
  </div>

  <!-- Comment Section -->
  <div id="cc-section">
    <!-- Header -->
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
      <svg style="width:20px;height:20px;color:#6366f1;flex-shrink:0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path d="M8 9h8"/><path d="M8 13h6"/>
        <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-5l-5 3v-3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h12z"/>
      </svg>
      <span style="font-size:15px;font-weight:600;color:#e5e7eb;flex:1">Comments <span id="cc-count" style="font-size:12px;color:#6b7280;font-weight:400"></span></span>
      <select id="cc-order">
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
        <option value="top">Top</option>
      </select>
    </div>

    <!-- Tabs -->
    <div style="display:flex;gap:8px;justify-content:center;margin-bottom:14px">
      <button id="cc-tab-chapter" class="cc-tab cc-tab-active" data-tab="chapter">Chapter Comments</button>
      <button id="cc-tab-all" class="cc-tab" data-tab="all">All Comments</button>
    </div>

    <?php if ($currentUser): ?>
    <!-- Form -->
    <form id="cc-form" style="margin-bottom:14px">
      <textarea id="cc-input" rows="3" maxlength="1000" placeholder="Write a comment..."></textarea>
      <!-- Captcha box -->
      <div id="cc-captcha-box" style="display:none;margin-top:8px;padding:8px 12px;border-radius:8px;background:#2a2000;border:1px solid #4d3800">
        <p style="font-size:12px;color:#d4a800;margin:0 0 6px">You commented recently. Solve this to continue:</p>
        <div style="display:flex;align-items:center;gap:8px">
          <span id="cc-captcha-q" style="font-size:13px;font-weight:600;color:#e5e7eb"></span>
          <span style="color:#9ca3af;font-size:13px">= ?</span>
          <input id="cc-captcha-ans" type="number" min="0" max="99"
                 style="width:56px;background:#1f2937;border:1px solid #374151;color:#e5e7eb;border-radius:6px;padding:4px 8px;font-size:13px;outline:none"
                 placeholder="0">
        </div>
      </div>
      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px">
        <span id="cc-char" style="font-size:11px;color:#6b7280">0 / 1000</span>
        <button type="submit" style="background:#6366f1;color:#fff;font-size:13px;padding:5px 16px;border-radius:7px;border:none;cursor:pointer">Post comment</button>
      </div>
    </form>
    <?php else: ?>
    <p style="text-align:center;font-size:13px;color:#9ca3af;padding:10px 0 14px">
      <a href="/login" style="color:#6366f1;font-weight:600">Login</a>
      or
      <a href="/register" style="color:#6366f1;font-weight:600">Register</a>
      to comment
    </p>
    <?php endif; ?>

    <!-- List -->
    <div id="cc-list">
      <p style="text-align:center;color:#6b7280;padding:24px 0;font-size:13px">Loading...</p>
    </div>

    <!-- Pagination -->
    <div id="cc-pg"></div>
  </div>

</div>

<!-- Chapter list side panel -->
<div x-show="listOpen" @click.self="listOpen = false" class="fixed inset-0 z-50" style="background:rgba(0,0,0,0.6);display:none">
    <div class="absolute left-0 top-0 h-full w-full sm:w-80 border-r-2 border-gray-100 dark:border-dark-blue bg-white dark:bg-fire-blue flex flex-col">
        <!-- Panel header -->
        <div class="flex gap-3 p-3 border-b-2 border-gray-100 dark:border-dark-blue">
            <div class="cover-sm" style="background-image: url('<?= esc(manga_cover_url($manga)) ?>')"></div>
            <div class="flex-auto w-0">
                <div class="flex items-start justify-between">
                    <a href="/manga/<?= esc($slug) ?>" class="font-semibold hover:text-blue-500 dark:text-white truncate pr-2"><?= esc($manga['name']) ?></a>
                    <button @click="listOpen = false" class="flex-shrink-0">
                        <svg class="h-7 w-7 text-red-500" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400"><?= esc($chapTitle) ?></span>
            </div>
        </div>
        <!-- Chapter list -->
        <ul class="flex-1 overflow-y-auto p-3">
            <?php foreach ($chapters as $i => $ch):
                $isActive = $ch['slug'] === $chapter['slug'];
                $chNum    = rtrim(rtrim(number_format((float)$ch['number'], 1), '0'), '.');
                $chTitle  = $ch['name'] ?: 'Chapter ' . $chNum;
                $rowBg    = $i % 2 === 0 ? 'bg-gray-100 dark:bg-light-blue' : '';
            ?>
            <a href="/manga/<?= esc($slug) ?>/<?= esc($ch['slug']) ?>">
                <li class="py-2 px-2 hover:bg-gray-200 dark:hover:bg-light-blue flex gap-3 <?= $rowBg ?><?= $isActive ? ' border-2 border-dashed border-blue-400' : '' ?>">
                    <?= esc($chTitle) ?>
                </li>
            </a>
            <?php endforeach; ?>
        </ul>
        <!-- Close button -->
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button @click="listOpen = false" class="w-full py-2 rounded-lg text-sm font-semibold" style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);color:#f87171">
                ✕ Close
            </button>
        </div>
    </div>
</div>


</main>

<!-- Back to top -->


<script>
var bnav = document.getElementById('mobile-bottom-nav');
if (bnav) bnav.classList.add('hide');
var lastScrollY = 0;
window.addEventListener('scroll', function() {
  var y = window.scrollY;
  if (bnav) {
    // hiện khi đã scroll xuống, ẩn khi về gần đầu trang
    bnav.classList.toggle('hide', y < 80);
  }
  lastScrollY = y;
}, {passive: true});

// ===== Comment section =====
(function() {
  var CHAPTER_ID  = <?= (int) $chapter['id'] ?>;
  var MANGA_ID    = <?= (int) $manga['id'] ?>;
  var MANGA_SLUG  = <?= json_encode($slug) ?>;
  var CURRENT_UID = <?= $currentUser ? (int)$currentUser['id'] : 0 ?>;
  var page = 1, totalPages = 1, loading = false, order = 'newest', activeTab = 'chapter';
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
    var sz=size||40;
    var ch=((name||username||'?')[0]).toUpperCase();
    var t=(parseInt(uid||0)%6)+1;
    var bg=BG[parseInt(uid||0)%6];
    if(sz<40){
      return '<div style="width:'+sz+'px;height:'+sz+'px;border-radius:50%;background:'+bg+';display:flex;align-items:center;justify-content:center;font-size:'+(sz*0.4)+'px;font-weight:700;color:#fff;flex-shrink:0">'+ch+'</div>';
    }
    return '<div class="cc-av cc-av-t'+t+'"><div class="cc-av-inner" style="background:'+bg+'">'+ch+'</div></div>';
  }

  var likeIconSvg='<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>';

  function likeBtnHtml(c){
    var isLiked=c.my_reaction==='like';
    var col=isLiked?'#6366f1':'#6b7280';
    if(CURRENT_UID>0){
      return '<button class="cc-react" data-id="'+c.id+'" data-type="like" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;background:none;border:none;cursor:pointer;color:'+col+'">'+likeIconSvg+'<span class="cc-lc">'+c.likes_count+'</span></button>';
    }
    return '<span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#6b7280">'+likeIconSvg+c.likes_count+'</span>';
  }

  function renderReply(c, topParentId){
    var name=c.user_name||c.user_username||'?';
    var replyBtn=(CURRENT_UID>0&&topParentId)
      ?'<button class="cc-reply-btn" data-id="'+topParentId+'" data-reply-to="'+c.id+'" data-name="'+esc(name)+'" style="background:none;border:none;cursor:pointer;font-size:11px;color:#6b7280;padding:2px 6px;border-radius:4px">↩ Reply</button>'
      :'';
    return '<div class="cc-item" style="margin-top:6px;padding:2px 0" data-id="'+c.id+'">'+
      '<div style="display:flex;gap:8px;align-items:flex-start">'+
        avatar(c.user_name,c.user_username,c.user_id,30)+
        '<div style="flex:1;min-width:0">'+
          '<div class="cc-bubble">'+
            '<span style="font-size:12px;font-weight:700;color:#e5e7eb">'+esc(name)+'</span>'+
            '<div style="margin-top:3px;font-size:12px;color:#d1d5db;white-space:pre-wrap;word-break:break-word">'+esc(c.comment)+'</div>'+
          '</div>'+
          '<div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-top:3px;padding-left:4px">'+
            '<div style="display:flex;align-items:center;gap:6px">'+likeBtnHtml(c)+'<span style="font-size:11px;color:#6b7280">'+timeAgo(c.created_at)+'</span></div>'+
            (replyBtn?'<div>'+replyBtn+'</div>':'')+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>';
  }

  function replyFormHtml(parentId, parentName, replyToId){
    return '<div style="margin-top:8px" id="cc-rf-'+parentId+'">'+
      '<input type="hidden" class="cc-reply-to-id" value="'+(replyToId||0)+'">'+
      '<textarea class="cc-reply-input" rows="2" maxlength="1000" style="width:100%;background:#1f2937;border:1px solid #374151;border-radius:8px;padding:8px 10px;font-size:12px;color:#e5e7eb;resize:none;outline:none;box-sizing:border-box">@'+esc(parentName)+' </textarea>'+
      '<div class="cc-rf-captcha-box" style="display:none;margin-top:6px;padding:8px 12px;border-radius:8px;background:#2a2000;border:1px solid #4d3800">'+
        '<p style="font-size:12px;color:#d4a800;margin:0 0 6px">You commented recently. Solve this to continue:</p>'+
        '<div style="display:flex;align-items:center;gap:8px">'+
          '<span class="cc-rf-captcha-q" style="font-size:13px;font-weight:600;color:#e5e7eb"></span>'+
          '<span style="color:#9ca3af;font-size:13px">= ?</span>'+
          '<input class="cc-rf-captcha-ans" type="number" min="0" max="99" style="width:52px;background:#1f2937;border:1px solid #374151;color:#e5e7eb;border-radius:6px;padding:4px 8px;font-size:13px;outline:none" placeholder="0">'+
        '</div>'+
      '</div>'+
      '<div style="display:flex;justify-content:flex-end;gap:8px;margin-top:4px">'+
        '<button class="cc-reply-cancel" data-parent="'+parentId+'" style="background:none;border:none;cursor:pointer;font-size:12px;color:#6b7280;padding:4px 10px">Cancel</button>'+
        '<button class="cc-reply-submit" data-parent="'+parentId+'" style="background:#6366f1;color:#fff;font-size:12px;padding:4px 14px;border-radius:6px;border:none;cursor:pointer">Reply</button>'+
      '</div>'+
    '</div>';
  }

  function fetchReplies(commentId, btn){
    var container=document.getElementById('cc-replies-'+commentId);
    if(!container) return;
    if(btn){btn.disabled=true;btn.textContent='Loading…';}
    fetch('/api/comments/'+commentId+'/replies')
      .then(function(r){return r.json();})
      .then(function(d){
        if(!d.replies||!d.replies.length){
          if(btn){btn.disabled=false;var cnt=btn.dataset.count;btn.textContent=cnt+' repl'+(parseInt(cnt)===1?'y':'ies')+' ▾';}
          return;
        }
        var LIMIT=5,visible=d.replies.slice(0,LIMIT),hidden=d.replies.slice(LIMIT);
        container.innerHTML=visible.map(function(r){return renderReply(r,commentId);}).join('');
        if(hidden.length>0){
          var mBtn=document.createElement('button');
          mBtn.className='cc-show-more text-xs text-indigo-500 hover:text-indigo-400 mt-1 px-1 py-0.5';
          mBtn.textContent='Show '+hidden.length+' more repl'+(hidden.length===1?'y':'ies')+'…';
          mBtn.onclick=function(){mBtn.remove();container.insertAdjacentHTML('beforeend',hidden.map(function(r){return renderReply(r,commentId);}).join(''));};
          container.appendChild(mBtn);
        }
        container.style.display='block';
        if(btn){btn.textContent='▴ hide';btn.disabled=false;btn.dataset.open='1';}
      })
      .catch(function(){
        if(btn){btn.disabled=false;var cnt=btn.dataset.count;btn.textContent=cnt+' repl'+(parseInt(cnt)===1?'y':'ies')+' ▾';}
      });
  }

  function renderCmt(c){
    var name=c.user_name||c.user_username||'?';
    var replyBtn=CURRENT_UID>0
      ?'<button class="cc-reply-btn" data-id="'+c.id+'" data-name="'+esc(name)+'" style="background:none;border:none;cursor:pointer;font-size:11px;color:#6b7280;padding:2px 6px;border-radius:4px">↩ Reply</button>'
      :'';

    return '<div class="cc-item" data-id="'+c.id+'">'+
      '<div style="display:flex;gap:12px;align-items:flex-start">'+
        avatar(c.user_name,c.user_username,c.user_id)+
        '<div style="flex:1;min-width:0">'+
          '<div class="cc-bubble">'+
            '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:2px">'+
              '<span style="font-size:13px;font-weight:700;color:#e5e7eb">'+esc(name)+'</span>'+
              (c.chapter_slug ? '<a href="/manga/'+MANGA_SLUG+'/'+esc(c.chapter_slug)+'" style="font-size:10px;background:rgba(99,102,241,0.2);color:#a5b4fc;border:1px solid rgba(99,102,241,0.3);border-radius:4px;padding:1px 6px;white-space:nowrap">'+esc(c.chapter_name||c.chapter_slug)+'</a>' : '')+
            '</div>'+
            '<div class="cc-text" style="margin-top:4px;font-size:13px;color:#d1d5db;white-space:pre-wrap;word-break:break-word">'+esc(c.comment)+'</div>'+
          '</div>'+
          '<div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-top:4px;padding-left:4px">'+
            '<div style="display:flex;align-items:center;gap:6px">'+likeBtnHtml(c)+'<span style="color:#4b5563;font-size:11px">-</span><span style="font-size:11px;color:#6b7280">'+timeAgo(c.created_at)+'</span></div>'+
            (replyBtn?'<div>'+replyBtn+'</div>':'')+
          '</div>'+
          '<div id="cc-reply-area-'+c.id+'"></div>'+
          '<div id="cc-replies-'+c.id+'" style="display:none;padding-left:12px;margin-top:6px;border-left:1px solid rgba(99,102,241,0.3)"></div>'+
        '</div>'+
      '</div>'+
    '</div>';
  }

  function renderPg(){
    var el=document.getElementById('cc-pg');
    if(!el) return;
    if(totalPages<=1){el.innerHTML='';return;}
    function btn(p,label,extra){return '<button class="ccpg'+(extra?' '+extra:'')+'" data-page="'+p+'">'+(label!==undefined?label:p)+'</button>';}
    var h='';
    h+= page>1?btn(page-1,'&#8249;'):'<button class="ccpg ccpg-disabled">&#8249;</button>';
    var s=Math.max(1,page-2),e=Math.min(totalPages,s+4);s=Math.max(1,e-4);
    if(s>1){h+=btn(1);if(s>2)h+='<span class="ccpg" style="pointer-events:none;border:none;background:transparent;color:#6b7280">…</span>';}
    for(var i=s;i<=e;i++) h+=i===page?'<button class="ccpg ccpg-active">'+i+'</button>':btn(i);
    if(e<totalPages){if(e<totalPages-1)h+='<span class="ccpg" style="pointer-events:none;border:none;background:transparent;color:#6b7280">…</span>';h+=btn(totalPages);}
    h+= page<totalPages?btn(page+1,'&#8250;'):'<button class="ccpg ccpg-disabled">&#8250;</button>';
    el.innerHTML=h;
  }

  function fetchComments(p){
    if(loading) return;
    loading=true; page=p;
    var url = activeTab==='chapter'
      ? '/api/comments/chapter/'+CHAPTER_ID+'?page='+p+'&order='+order
      : '/api/comments/manga/'+MANGA_ID+'/all?page='+p+'&order='+order;
    fetch(url)
      .then(function(r){return r.json();})
      .then(function(data){
        var list=document.getElementById('cc-list');
        var countEl=document.getElementById('cc-count');
        if(countEl) countEl.textContent=data.total>0?'('+data.total+')':'';
        totalPages=data.total>0?Math.ceil(data.total/10):1;
        list.innerHTML=(!data.comments||!data.comments.length)
          ?'<p style="text-align:center;color:#6b7280;padding:20px 0;font-size:13px">No comments yet.</p>'
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

  // Tab switching
  var secEl=document.getElementById('cc-section');
  if(secEl) secEl.addEventListener('click',function(e){
    var btn=e.target.closest('[data-tab]');
    if(!btn) return;
    var tab=btn.dataset.tab;
    if(tab===activeTab) return;
    activeTab=tab;
    document.querySelectorAll('.cc-tab').forEach(function(t){t.classList.toggle('cc-tab-active',t.dataset.tab===tab);});
    fetchComments(1);
  });

  var orderSel=document.getElementById('cc-order');
  if(orderSel) orderSel.addEventListener('change',function(){order=this.value;fetchComments(1);});

  var pgEl=document.getElementById('cc-pg');
  if(pgEl) pgEl.addEventListener('click',function(e){
    var btn=e.target.closest('[data-page]');
    if(!btn) return;
    var p=parseInt(btn.dataset.page);
    if(p&&p!==page){fetchComments(p);document.getElementById('cc-section').scrollIntoView({behavior:'smooth',block:'start'});}
  });

  var form=document.getElementById('cc-form');
  if(form){
    var inp=document.getElementById('cc-input');
    var charEl=document.getElementById('cc-char');
    var captchaReady=false;
    var LAST_KEY='lct_'+CURRENT_UID;
    inp.addEventListener('input',function(){charEl.textContent=inp.value.length+' / 1000';});

    function showCaptcha(question){
      document.getElementById('cc-captcha-q').textContent=question;
      document.getElementById('cc-captcha-box').style.display='block';
      captchaReady=true;
    }
    function hideCaptcha(){
      document.getElementById('cc-captcha-box').style.display='none';
      var ans=document.getElementById('cc-captcha-ans');
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
      if(activeTab==='chapter') fd.append('chapter_id',CHAPTER_ID);
      fd.append('comment',text);
      if(captchaReady){
        var ans=document.getElementById('cc-captcha-ans');
        if(!ans||!ans.value.trim()){ ans&&ans.focus(); return; }
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
          var list=document.getElementById('cc-list');
          var ph=list.querySelector('p');
          if(ph) ph.remove();
          c.reply_count=0;
          list.insertAdjacentHTML('afterbegin',renderCmt(c));
          inp.value=''; charEl.textContent='0 / 1000';
          var countEl=document.getElementById('cc-count');
          if(countEl){var cur=parseInt((countEl.textContent||'').replace(/\D/g,''))||0;countEl.textContent='('+(cur+1)+')';}
        })
        .catch(function(){alert('Something went wrong, please try again.');});
    });
  }

  document.getElementById('cc-list').addEventListener('click',function(e){
    var target=e.target.closest('button');
    if(!target) return;
    var cid=parseInt(target.dataset.id);

    // Like / react
    if(target.classList.contains('cc-react')){
      var type=target.dataset.type;
      var fd=new FormData();
      fd.append('type',type);
      fetch('/api/comments/'+cid+'/react',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(d){
          if(d.error) return;
          var item=target.closest('.cc-item');
          if(!item) return;
          var lb=item.querySelector('.cc-react[data-type="like"]');
          if(lb){
            lb.style.color=d.my_reaction==='like'?'#6366f1':'#6b7280';
            var lc=lb.querySelector('.cc-lc');
            if(lc) lc.textContent=d.likes_count;
          }
        });
      return;
    }

    // Reply button
    if(target.classList.contains('cc-reply-btn')){
      var parentId=target.dataset.id;
      var parentName=target.dataset.name;
      var replyToId=target.dataset.replyTo||0;
      var area=document.getElementById('cc-reply-area-'+parentId);
      if(!area) return;
      var existing=document.getElementById('cc-rf-'+parentId);
      if(existing){existing.remove();return;}
      area.innerHTML=replyFormHtml(parentId,parentName,replyToId);
      var ta=area.querySelector('.cc-reply-input');
      ta.focus();
      ta.setSelectionRange(ta.value.length,ta.value.length);
      return;
    }

    // Cancel reply
    if(target.classList.contains('cc-reply-cancel')){
      var rf=document.getElementById('cc-rf-'+target.dataset.parent);
      if(rf) rf.remove();
      return;
    }

    // Submit reply
    if(target.classList.contains('cc-reply-submit')){
      var parentId=target.dataset.parent;
      var rf=document.getElementById('cc-rf-'+parentId);
      if(!rf) return;
      var ta=rf.querySelector('.cc-reply-input');
      var text=ta?ta.value.trim():'';
      if(!text) return;
      var captchaBox=rf.querySelector('.cc-rf-captcha-box');
      var captchaVisible=captchaBox&&captchaBox.style.display!=='none';
      if(captchaVisible){
        var captchaAns=rf.querySelector('.cc-rf-captcha-ans');
        if(!captchaAns||!captchaAns.value.trim()){captchaAns&&captchaAns.focus();return;}
      }
      target.disabled=true; target.textContent='Sending…';
      var replyToInput=rf.querySelector('.cc-reply-to-id');
      var fd=new FormData();
      fd.append('manga_id',MANGA_ID);
      if(activeTab==='chapter') fd.append('chapter_id',CHAPTER_ID);
      fd.append('comment',text);
      fd.append('parent_comment',parentId);
      fd.append('reply_to_id',replyToInput?replyToInput.value:0);
      if(captchaVisible){
        var captchaAns=rf.querySelector('.cc-rf-captcha-ans');
        if(captchaAns&&captchaAns.value.trim()) fd.append('captcha_answer',captchaAns.value.trim());
      }
      fetch('/api/comments',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(c){
          if(c.need_captcha){
            fetch('/api/captcha')
              .then(function(r){return r.json();})
              .then(function(d){
                var box=rf.querySelector('.cc-rf-captcha-box');
                var q=rf.querySelector('.cc-rf-captcha-q');
                var ans=rf.querySelector('.cc-rf-captcha-ans');
                if(box) box.style.display='block';
                if(q) q.textContent=d.question;
                if(ans){ans.value='';ans.focus();}
              });
            target.disabled=false; target.textContent='Reply';
            return;
          }
          if(c.error){alert(c.error);target.disabled=false;target.textContent='Reply';return;}
          rf.remove();
          var container=document.getElementById('cc-replies-'+parentId);
          if(container){
            var moreBtn=container.querySelector('.cc-show-more');
            if(moreBtn) moreBtn.insertAdjacentHTML('beforebegin',renderReply(c,parentId));
            else container.insertAdjacentHTML('beforeend',renderReply(c,parentId));
            container.style.display='block';
          }
        })
        .catch(function(){target.disabled=false;target.textContent='Reply';alert('Something went wrong, please try again.');});
      return;
    }
  });
})();

// Lazy load images
if ('IntersectionObserver' in window) {
  var lazyImgs = document.querySelectorAll('img.lazy');
  var obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) {
        var img = e.target;
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        obs.unobserve(img);
      }
    });
  }, { rootMargin: '600px' });
  lazyImgs.forEach(function(img) { obs.observe(img); });
}
</script>

<script>
(function(){
  var CHAPTER_ID = <?= (int)$chapter['id'] ?>;
  var modal   = document.getElementById('rpt-modal');
  var closeBtn= document.getElementById('rpt-close-btn');
  var cancelBtn=document.getElementById('rpt-cancel-btn');
  var submitBtn=document.getElementById('rpt-submit-btn');
  var msg     = document.getElementById('rpt-msg');

  function openModal(){
    modal.style.display='flex';
    document.body.style.overflow='hidden';
  }
  function closeModal(){
    modal.style.display='none';
    document.body.style.overflow='';
    msg.style.display='none';
    msg.textContent='';
    submitBtn.style.display='';
    document.querySelectorAll('input[name="rpt-reason"]').forEach(function(r){ r.checked=false; });
    document.getElementById('rpt-note').value='';
    submitBtn.disabled=false; submitBtn.textContent='Submit Report';
  }

  document.querySelectorAll('.rpt-open-btn').forEach(function(btn){
    btn.addEventListener('click', openModal);
  });
  closeBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });

  // Highlight selected radio label
  document.querySelectorAll('input[name="rpt-reason"]').forEach(function(r){
    r.addEventListener('change', function(){
      document.querySelectorAll('#rpt-reasons label').forEach(function(l){
        l.style.borderColor = '#374151';
      });
      r.closest('label').style.borderColor = '#6366f1';
    });
  });

  submitBtn.addEventListener('click', function(){
    var reason = document.querySelector('input[name="rpt-reason"]:checked');
    if(!reason){
      msg.style.display='block'; msg.style.color='#ef4444';
      msg.textContent='Please select a reason.'; return;
    }
    submitBtn.disabled=true; submitBtn.textContent='Sending…';
    msg.style.display='none';

    var fd = new FormData();
    fd.append('reason', reason.value);
    fd.append('note', document.getElementById('rpt-note').value.trim());

    fetch('/api/chapters/'+CHAPTER_ID+'/report', {
      method:'POST', credentials:'same-origin', body: fd
    })
    .then(function(r){ return r.json(); })
    .then(function(d){
      msg.style.display='block';
      if(d.ok){
        msg.style.color='#22c55e';
        msg.textContent='Thank you! Your report has been submitted.';
        submitBtn.style.display='none';
        setTimeout(closeModal, 2500);
      } else {
        msg.style.color='#ef4444';
        msg.textContent = d.error||'Something went wrong, please try again.';
        submitBtn.disabled=false; submitBtn.textContent='Submit Report';
      }
    })
    .catch(function(){
      msg.style.display='block'; msg.style.color='#ef4444';
      msg.textContent='Something went wrong, please try again.';
      submitBtn.disabled=false; submitBtn.textContent='Submit Report';
    });
  });
})();
</script>

<?= $this->endSection() ?>
