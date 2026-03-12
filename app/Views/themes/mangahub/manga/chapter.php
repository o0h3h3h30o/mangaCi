<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$cdnChapter = rtrim(env('CDN_CHAPTER_URL', ''), '/');
$prevUrl    = $prevChapter ? '/manga/' . $slug . '/' . $prevChapter['slug'] : null;
$nextUrl    = $nextChapter ? '/manga/' . $slug . '/' . $nextChapter['slug'] : null;
$chapNum    = rtrim(rtrim(number_format((float)$chapter['number'], 1), '0'), '.');
$prevNum    = $prevChapter ? rtrim(rtrim(number_format((float)$prevChapter['number'], 1), '0'), '.') : null;
$nextNum    = $nextChapter ? rtrim(rtrim(number_format((float)$nextChapter['number'], 1), '0'), '.') : null;
$totalPages = count($pages);
$totalChapters = count($chapters);
?>

<style>
/* ══════════════════════════════════════════════════
   READER PAGE OVERRIDES
══════════════════════════════════════════════════ */
body { overflow-x: hidden; }
.main-content { padding-top: 0; padding-bottom: 0; }

/* ── Breadcrumb bar ── */
.reader-breadcrumb {
  background: var(--header); border-bottom: 1px solid var(--border);
  padding: 8px 0; font-size: 12px; color: var(--txt3);
}
.reader-breadcrumb .wrap {
  display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.reader-breadcrumb a { color: var(--txt2); text-decoration: none; transition: color .18s; }
.reader-breadcrumb a:hover { color: var(--accent); }
.reader-breadcrumb .sep { color: var(--txt3); }
.reader-breadcrumb .current { color: var(--txt); font-weight: 600; }

/* ── Reader toolbar ── */
.reader-toolbar {
  background: var(--header); border-bottom: 1px solid var(--border);
  padding: 0; box-shadow: var(--shadow);
  transition: background .3s;
}

/* ── Back-to-top FAB ── */
.fab-top {
  position: fixed; bottom: 24px; right: 20px; z-index: 600;
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--accent); color: #fff;
  border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px rgba(232,25,44,.45);
  opacity: 0; transform: translateY(12px);
  transition: opacity .25s, transform .25s;
  pointer-events: none;
}
.fab-top.show {
  opacity: 1; transform: translateY(0); pointer-events: auto;
}
.fab-top:hover { background: var(--accent2); }

.reader-toolbar-inner {
  max-width: 1200px; margin: 0 auto; padding: 0 16px;
  height: 52px; display: flex; align-items: center; gap: 12px;
}

/* Back button */
.reader-back {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 600; color: var(--txt2);
  background: none; border: none; cursor: pointer;
  padding: 6px 10px; border-radius: 7px;
  transition: background .18s, color .18s; white-space: nowrap;
  text-decoration: none;
}
.reader-back:hover { background: var(--border); color: var(--txt); }

.reader-divider { width: 1px; height: 22px; background: var(--border); flex-shrink: 0; }

/* Chapter selector */
.reader-chapter-nav {
  display: flex; align-items: center; gap: 6px; flex: 1;
  justify-content: center;
}
.ch-nav-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 32px; height: 32px; border-radius: 7px;
  background: var(--card); border: 1px solid var(--border);
  color: var(--txt2); cursor: pointer; font-size: 16px;
  transition: all .18s; flex-shrink: 0;
  text-decoration: none;
}
.ch-nav-btn:hover { color: var(--accent); border-color: var(--accent); }
.ch-nav-btn.disabled { opacity: .35; pointer-events: none; }

.chapter-select-wrap { position: relative; }
.chapter-select {
  appearance: none; -webkit-appearance: none;
  background: var(--card); border: 1px solid var(--border);
  color: var(--txt); font-size: 13px; font-weight: 600;
  padding: 6px 32px 6px 12px; border-radius: 8px;
  cursor: pointer; transition: border-color .18s;
  min-width: 160px; text-align: center;
}
.chapter-select:hover { border-color: var(--accent); }
.chapter-select-wrap::after {
  content: '\25BE'; position: absolute; right: 10px; top: 50%;
  transform: translateY(-50%); font-size: 11px;
  color: var(--txt3); pointer-events: none;
}

/* Page counter */
.reader-page-info {
  font-size: 12px; color: var(--txt3); white-space: nowrap;
  display: flex; align-items: center; gap: 4px;
}
.reader-page-info strong { color: var(--txt); font-weight: 700; }

/* Right toolbar actions */
.reader-actions { display: flex; align-items: center; gap: 6px; }
.reader-icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 8px;
  background: none; border: 1px solid var(--border);
  color: var(--txt2); cursor: pointer; font-size: 16px;
  transition: all .18s;
}
.reader-icon-btn:hover { color: var(--accent); border-color: var(--accent); background: var(--card); }
.reader-icon-btn.active { color: var(--accent); border-color: var(--accent); background: var(--card); }

/* ── Settings dropdown ── */
.settings-dropdown {
  position: fixed; top: auto; right: 16px;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 12px; box-shadow: var(--shadow-h);
  padding: 16px; min-width: 220px; z-index: 500;
  display: none;
}
.settings-dropdown.open { display: block; }
.settings-label { font-size: 11px; font-weight: 700; color: var(--txt3);
  text-transform: uppercase; letter-spacing: .6px; margin-bottom: 10px; }
.settings-row { display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 14px; }
.settings-row:last-child { margin-bottom: 0; }
.settings-row span { font-size: 13px; color: var(--txt); }
.settings-btns { display: flex; gap: 4px; }
.s-btn {
  padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;
  border: 1px solid var(--border); background: var(--card); color: var(--txt2);
  cursor: pointer; transition: all .18s;
}
.s-btn.active, .s-btn:hover { border-color: var(--accent); color: var(--accent); background: rgba(232,25,44,.08); }
.width-slider { width: 100px; accent-color: var(--accent); cursor: pointer; }

/* ── Reader main area ── */
.reader-main {
  display: flex; gap: 0;
  background: var(--bg); min-height: calc(100vh - 104px);
  transition: background .3s;
}

/* Pages column */
.pages-column {
  flex: 1; display: flex; flex-direction: column; align-items: center;
  padding: 20px 16px 48px;
}
.manga-page-wrap {
  width: 100%; max-width: 720px;
  margin-bottom: var(--page-gap, 0px); position: relative;
}
.manga-page-wrap img {
  width: 100%; height: auto; display: block;
  filter: var(--page-filter, none);
}
.page-number-badge {
  position: absolute; bottom: 10px; right: 10px;
  background: rgba(0,0,0,.55); color: #fff;
  font-size: 11px; font-weight: 600;
  padding: 3px 8px; border-radius: 5px;
  backdrop-filter: blur(4px);
}

/* ── Chapter list panel ── */
.chapter-panel {
  width: 0; overflow: hidden; flex-shrink: 0;
  transition: width .3s cubic-bezier(.4,0,.2,1);
  background: var(--surface); border-left: 1px solid var(--border);
}
.chapter-panel.open { width: 260px; }
.panel-inner { width: 260px; padding: 16px; height: 100%; overflow-y: auto; }
.panel-title {
  font-size: 13px; font-weight: 700; color: var(--txt);
  margin-bottom: 4px; display: flex; align-items: center; gap: 8px;
}
.panel-manga-name { font-size: 11.5px; color: var(--txt3); margin-bottom: 14px; }
.panel-search {
  display: flex; align-items: center; gap: 8px;
  background: var(--bg); border: 1px solid var(--border);
  border-radius: 8px; padding: 6px 10px; margin-bottom: 12px;
}
.panel-search svg { color: var(--txt3); flex-shrink: 0; }
.panel-search input {
  background: none; border: none; outline: none; flex: 1;
  font-size: 12.5px; color: var(--txt);
}
.panel-search input::placeholder { color: var(--txt3); }
.ch-list { display: flex; flex-direction: column; gap: 2px; }
.ch-item {
  display: flex; align-items: center; justify-content: space-between;
  padding: 8px 10px; border-radius: 8px; cursor: pointer;
  transition: background .15s; text-decoration: none;
}
.ch-item:hover { background: var(--bg); }
.ch-item.current { background: rgba(232,25,44,.1); }
.ch-item-left { display: flex; flex-direction: column; gap: 2px; }
.ch-item-num { font-size: 13px; font-weight: 600; color: var(--txt); }
.ch-item.current .ch-item-num { color: var(--accent); }
.ch-item-title { font-size: 11px; color: var(--txt3); }
.ch-item-date { font-size: 11px; color: var(--txt3); white-space: nowrap; }

/* ── Bottom navigation ── */
.reader-bottom {
  background: var(--header); border-top: 1px solid var(--border);
  padding: 16px 0;
}
.reader-bottom-inner {
  max-width: 720px; margin: 0 auto; padding: 0 16px;
  display: flex; align-items: center; gap: 12px; justify-content: space-between;
}
.bottom-ch-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 10px 20px; border-radius: 9px; font-size: 13.5px; font-weight: 600;
  cursor: pointer; border: 1px solid var(--border);
  background: var(--card); color: var(--txt);
  transition: all .18s; text-decoration: none;
}
.bottom-ch-btn:hover { border-color: var(--accent); color: var(--accent); }
.bottom-ch-btn.primary {
  background: var(--accent); color: #fff; border-color: var(--accent);
}
.bottom-ch-btn.primary:hover { background: var(--accent2); border-color: var(--accent2); color: #fff; }
.bottom-ch-btn.disabled { opacity: .4; pointer-events: none; }
.back-top-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 16px; border-radius: 9px; font-size: 12.5px;
  font-weight: 600; cursor: pointer; border: 1px solid var(--border);
  background: var(--card); color: var(--txt2);
  transition: all .18s;
}
.back-top-btn:hover { color: var(--accent); border-color: var(--accent); }

/* ── Comments section ── (styles moved inline in comment HTML) */

/* ══════════════════════════════════════════════════
   MOBILE TOOLBAR COMPONENT
══════════════════════════════════════════════════ */
.reader-toolbar-mobile { display: none; }

.rtm-row {
  display: flex; align-items: center;
  border-bottom: 1px solid var(--border);
}
.rtm-row:last-child { border-bottom: none; }

/* Row 1 – Manga title */
.rtm-title-row {
  justify-content: center; gap: 8px;
  padding: 10px 16px;
  color: var(--txt3);
}
.rtm-manga-name {
  font-size: 15px; font-weight: 700; color: var(--txt);
}

/* Row 2 – chapter + bookmark + settings */
.rtm-chapter-row { display: grid; grid-template-columns: 1fr 1fr; }
.rtm-chapter-btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  grid-column: 1; padding: 13px 16px;
  background: none; border: none; border-right: 1px solid var(--border);
  font-size: 14px; font-weight: 700; color: var(--txt);
  cursor: pointer; transition: background .15s;
  text-align: center;
}
.rtm-chapter-btn:hover { background: var(--bg); }
.rtm-chapter-btn svg { color: var(--txt3); flex-shrink: 0; }
.rtm-actions {
  display: grid; grid-template-columns: 1fr 1fr;
  grid-column: 2;
}
.rtm-icon-btn {
  display: inline-flex; align-items: center; justify-content: center;
  height: 48px;
  background: none; border: none; border-left: 1px solid var(--border);
  color: var(--txt2); cursor: pointer; font-size: 18px;
  transition: color .15s, background .15s;
}
.rtm-icon-btn:hover { color: var(--accent); background: var(--bg); }

/* Row 3 – PREV + NEXT */
.rtm-nav-row { display: grid; grid-template-columns: 1fr 1fr; }
.rtm-nav-btn {
  display: inline-flex; align-items: center; justify-content: center;
  gap: 8px; padding: 14px 16px;
  background: none; border: none;
  font-size: 13px; font-weight: 700; letter-spacing: .5px;
  color: var(--txt2); cursor: pointer;
  transition: color .15s, background .15s;
  text-decoration: none;
}
.rtm-nav-btn:first-child { border-right: 1px solid var(--border); }
.rtm-nav-btn:hover { color: var(--accent); background: var(--bg); }
.rtm-nav-btn.disabled { opacity: .35; pointer-events: none; }

/* ══════════════════════════════════════════════════
   RESPONSIVE – MOBILE
══════════════════════════════════════════════════ */
@media (max-width: 768px) {
  .reader-breadcrumb { display: none; }
  .reader-toolbar-inner { display: none; }
  .reader-toolbar-mobile { display: block; }
  .pages-column { padding: 0 0 40px; }
  .manga-page-wrap { max-width: 100%; margin-bottom: 0; }

  .chapter-panel {
    position: fixed; top: 0; right: 0; bottom: 0;
    width: 0 !important; height: 100vh; z-index: 400;
    box-shadow: none; border-left: none;
    transition: width .3s cubic-bezier(.4,0,.2,1), box-shadow .3s;
  }
  .chapter-panel.open {
    width: 80vw !important; max-width: 300px !important;
    box-shadow: -8px 0 32px rgba(0,0,0,.45);
  }
  .panel-inner { width: 100%; min-width: 0; }

  .panel-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.55); z-index: 399;
    backdrop-filter: blur(2px);
  }
  .panel-backdrop.show { display: block; }

  .settings-dropdown {
    position: fixed; bottom: 0; left: 0; right: 0; top: auto;
    border-radius: 16px 16px 0 0;
    min-width: 0; width: 100%;
    padding: 20px 20px 32px;
    box-shadow: 0 -8px 32px rgba(0,0,0,.35);
    z-index: 500;
  }
  .settings-dropdown::before {
    content: ''; display: block; width: 36px; height: 4px;
    background: var(--border); border-radius: 2px;
    margin: 0 auto 16px;
  }

  .reader-bottom-inner { padding: 0 12px; gap: 8px; }
  .bottom-ch-btn { padding: 10px 14px; font-size: 12.5px; }
  .back-top-btn { padding: 10px 12px; font-size: 12px; }
}
</style>

<!-- Breadcrumb -->
<div class="reader-breadcrumb">
  <div class="wrap">
    <a href="/">Home</a>
    <span class="sep">&rsaquo;</span>
    <a href="/manga/<?= esc($slug) ?>"><?= esc($manga['name']) ?></a>
    <span class="sep">&rsaquo;</span>
    <span class="current">Chapter <?= esc($chapNum) ?><?= !empty($chapter['name']) ? ' – ' . esc($chapter['name']) : '' ?></span>
  </div>
</div>

<!-- ── Reader Toolbar ── -->
<div class="reader-toolbar">

  <!-- ══ MOBILE TOOLBAR (≤ 768px) ══ -->
  <div class="reader-toolbar-mobile">

    <!-- Row 1: Manga title (links to detail) -->
    <a href="/manga/<?= esc($slug) ?>" class="rtm-row rtm-title-row" style="text-decoration:none;color:inherit">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
      </svg>
      <span class="rtm-manga-name"><?= esc($manga['name']) ?></span>
    </a>

    <!-- Row 2: Chapter btn + Comments + Settings -->
    <div class="rtm-row rtm-chapter-row">
      <button class="rtm-chapter-btn" id="mobileChapterBtn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
          <line x1="8" y1="18" x2="21" y2="18"/>
          <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/>
          <line x1="3" y1="18" x2="3.01" y2="18"/>
        </svg>
        Chapter <?= esc($chapNum) ?>
      </button>
      <div class="rtm-actions">
        <button class="rtm-icon-btn" title="Jump to comments" onclick="document.getElementById('cc-section').scrollIntoView({behavior:'smooth'})">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </button>
        <button class="rtm-icon-btn" id="settingsBtnMobile" title="Settings">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Row 3: Prev / Next chapter -->
    <div class="rtm-row rtm-nav-row">
      <a href="<?= $prevUrl ? esc($prevUrl) : '#' ?>" class="rtm-nav-btn<?= $prevUrl ? '' : ' disabled' ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="19 20 9 12 19 4"/><line x1="5" y1="19" x2="5" y2="5"/>
        </svg>
        PREV
      </a>
      <a href="<?= $nextUrl ? esc($nextUrl) : '#' ?>" class="rtm-nav-btn<?= $nextUrl ? '' : ' disabled' ?>">
        NEXT
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="5 4 15 12 5 20"/><line x1="19" y1="4" x2="19" y2="20"/>
        </svg>
      </a>
    </div>

  </div><!-- /reader-toolbar-mobile -->

  <!-- Settings dropdown – shared -->
  <div class="settings-dropdown" id="settingsDropdown">
    <div class="settings-label">Reading settings</div>
    <div class="settings-row">
      <span>Page width</span>
      <input type="range" class="width-slider" id="widthSlider" min="50" max="100" value="100" />
    </div>
    <div class="settings-row">
      <span>Background</span>
      <div class="settings-btns">
        <button class="s-btn active" data-bg="default">Default</button>
        <button class="s-btn" data-bg="black">Black</button>
        <button class="s-btn" data-bg="white">White</button>
      </div>
    </div>
    <div class="settings-row">
      <span>Page tint</span>
      <div class="settings-btns">
        <button class="s-btn active" data-filter="none">Normal</button>
        <button class="s-btn" data-filter="grayscale(100%)">B&amp;W</button>
      </div>
    </div>
    <div class="settings-row">
      <span>Page gap</span>
      <div class="settings-btns">
        <button class="s-btn active" data-gap="0">None</button>
        <button class="s-btn" data-gap="4">Small</button>
        <button class="s-btn" data-gap="12">Medium</button>
      </div>
    </div>
  </div>

  <!-- ══ DESKTOP TOOLBAR ══ -->
  <div class="reader-toolbar-inner">

    <!-- Back to manga -->
    <a href="/manga/<?= esc($slug) ?>" class="reader-back">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      <span><?= esc(mb_strimwidth($manga['name'], 0, 40, '...')) ?></span>
    </a>

    <div class="reader-divider"></div>

    <!-- Chapter navigation -->
    <div class="reader-chapter-nav">
      <a href="<?= $prevUrl ? esc($prevUrl) : '#' ?>" class="ch-nav-btn<?= $prevUrl ? '' : ' disabled' ?>" title="Previous chapter">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </a>

      <div class="chapter-select-wrap">
        <select class="chapter-select" id="chapterSelect" onchange="if(this.value)location.href=this.value">
          <?php foreach ($chapters as $ch): ?>
            <?php $chN = rtrim(rtrim(number_format((float)$ch['number'], 1), '0'), '.'); ?>
            <option value="/manga/<?= esc($slug) ?>/<?= esc($ch['slug']) ?>"<?= $ch['id'] == $chapter['id'] ? ' selected' : '' ?>>
              Chapter <?= esc($chN) ?><?= !empty($ch['name']) ? ' – ' . esc($ch['name']) : '' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <a href="<?= $nextUrl ? esc($nextUrl) : '#' ?>" class="ch-nav-btn<?= $nextUrl ? '' : ' disabled' ?>" title="Next chapter">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </a>
    </div>

    <!-- Page counter -->
    <div class="reader-page-info" id="pageInfo">
      Page <strong id="curPage">1</strong> / <strong><?= $totalPages ?></strong>
    </div>

    <!-- Right actions -->
    <div class="reader-actions">
      <button class="reader-icon-btn" title="Comments" onclick="document.getElementById('cc-section').scrollIntoView({behavior:'smooth'})">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
      </button>

      <button class="reader-icon-btn" id="settingsBtn" title="Reading settings">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="3"/>
          <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
        </svg>
      </button>

      <button class="reader-icon-btn" id="panelToggleBtn" title="Chapter list">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
          <line x1="8" y1="18" x2="21" y2="18"/>
          <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/>
          <line x1="3" y1="18" x2="3.01" y2="18"/>
        </svg>
      </button>
    </div>

  </div>
</div>

<!-- ── Main reader area ── -->
<div class="reader-main" id="readerMain">

  <!-- Pages -->
  <div class="pages-column" id="pagesColumn">

    <?php foreach ($pages as $idx => $page): ?>
      <?php
        $imgUrl = !empty($page['image_local'])
            ? ($cdnChapter . '/' . $chapter['id'] . '/' . ltrim($page['image_local'], '/'))
            : trim($page['image']);
        $pageNum = $idx + 1;
      ?>
      <div class="manga-page-wrap" data-page="<?= $pageNum ?>">
        <img src="<?= esc($imgUrl) ?>" alt="Page <?= $pageNum ?>" width="900" <?= $pageNum <= 2 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy" decoding="async"' ?> />
        <span class="page-number-badge"><?= $pageNum ?></span>
      </div>
    <?php endforeach; ?>

    <!-- Comments section -->
    <div class="reader-comments" id="cc-section">
      <style>
      .reader-comments { width: 100%; max-width: 900px; margin: 40px auto 0; padding: 28px 24px; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; box-sizing: border-box; align-self: center; }
      @media (max-width: 600px) { .reader-comments { padding: 20px 14px; margin-top: 24px; } }
      .cc-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
      .cc-head-left { display: flex; align-items: center; gap: 8px; }
      .cc-head-left h3 { font-size: 17px; font-weight: 700; color: var(--txt); margin: 0; }
      .cc-order { background: var(--bg); border: 1px solid var(--border); color: var(--txt2); font-size: 12px; border-radius: 6px; padding: 4px 8px; outline: none; }
      .cc-tabs { display: flex; gap: 8px; justify-content: center; margin-bottom: 14px; }
      .cc-tab { padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1px solid var(--border); background: var(--bg); color: var(--txt3); cursor: pointer; transition: all .15s; }
      .cc-tab-active { background: var(--accent); border-color: var(--accent); color: #fff; }
      .cc-form textarea { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 10px; padding: 12px 14px; font-size: 14px; color: var(--txt); resize: none; outline: none; box-sizing: border-box; font-family: inherit; }
      .cc-form textarea:focus { border-color: var(--accent); }
      .cc-form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 6px; }
      .cc-char { font-size: 11px; color: var(--txt3); }
      .cc-submit { background: var(--accent); color: #fff; font-size: 13px; font-weight: 600; padding: 8px 20px; border-radius: 8px; border: none; cursor: pointer; transition: opacity .2s; }
      .cc-submit:hover { opacity: .85; }
      .cc-captcha-box { display: none; margin-top: 8px; padding: 10px 14px; border-radius: 8px; background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.25); }
      .cc-captcha-box p { font-size: 12px; color: var(--txt3); margin: 0 0 6px; }
      .cc-captcha-box input { width: 56px; background: var(--bg); border: 1px solid var(--border); color: var(--txt); border-radius: 6px; padding: 4px 8px; font-size: 13px; outline: none; }
      .cc-login { text-align: center; font-size: 13px; color: var(--txt3); padding: 10px 0 14px; }
      .cc-login a { color: var(--accent); font-weight: 600; text-decoration: none; }
      .cc-login a:hover { text-decoration: underline; }
      #cc-list { display: flex; flex-direction: column; gap: 6px; }
      .cc-item { padding: 6px 0; }
      .cc-bubble { background: var(--bg); border-radius: 12px; padding: 10px 14px; }
      .cc-name { font-size: 14px; font-weight: 700; color: var(--txt); }
      .cc-chapter-tag { font-size: 10px; background: rgba(59,130,246,.12); color: var(--accent); border: 1px solid rgba(59,130,246,.2); border-radius: 4px; padding: 1px 6px; white-space: nowrap; text-decoration: none; }
      .cc-chapter-tag:hover { text-decoration: underline; }
      .cc-text { margin-top: 4px; font-size: 14px; color: var(--txt2); white-space: pre-wrap; word-break: break-word; line-height: 1.5; }
      .cc-meta { display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-top: 4px; padding: 0 4px; }
      .cc-meta-left { display: flex; align-items: center; gap: 6px; }
      .cc-react { display: inline-flex; align-items: center; gap: 5px; font-size: 13px; background: none; border: none; cursor: pointer; color: var(--txt3); transition: color .15s; }
      .cc-react.liked { color: var(--accent); }
      .cc-reply-btn { background: none; border: none; cursor: pointer; font-size: 12px; color: var(--txt3); padding: 2px 8px; border-radius: 4px; }
      .cc-reply-btn:hover { color: var(--accent); }
      .cc-replies { padding-left: 12px; margin-top: 6px; border-left: 1px solid rgba(59,130,246,.2); }
      .cc-show-more { font-size: 12px; color: var(--accent); background: none; border: none; cursor: pointer; padding: 2px 4px; margin-top: 4px; }
      .cc-reply-input { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 8px 10px; font-size: 12px; color: var(--txt); resize: none; outline: none; box-sizing: border-box; font-family: inherit; }
      .cc-reply-input:focus { border-color: var(--accent); }
      .cc-rf-cancel { background: none; border: none; cursor: pointer; font-size: 12px; color: var(--txt3); padding: 4px 10px; }
      .cc-rf-submit { background: var(--accent); color: #fff; font-size: 12px; padding: 4px 14px; border-radius: 6px; border: none; cursor: pointer; }
      .cc-av { display: inline-flex; align-items: center; justify-content: center; width: var(--av-size, 40px); height: var(--av-size, 40px); border-radius: 50%; flex-shrink: 0; }
      .cc-av-inner { width: 100%; height: 100%; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; }
      #cc-pg { display: flex; justify-content: center; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 12px; }
      #cc-pg button { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 8px; border-radius: 7px; font-size: 13px; border: 1px solid var(--border); background: var(--bg); color: var(--txt2); cursor: pointer; transition: background .15s; }
      #cc-pg button:hover:not([disabled]):not(.pg-active) { background: var(--border); }
      #cc-pg .pg-active { background: var(--accent) !important; border-color: var(--accent) !important; color: #fff !important; font-weight: 700; pointer-events: none; }
      #cc-pg button[disabled] { opacity: .4; cursor: default; pointer-events: none; }
      </style>

      <div class="cc-head">
        <div class="cc-head-left">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent);flex-shrink:0">
            <path d="M8 9h8"/><path d="M8 13h6"/>
            <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-5l-5 3v-3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h12z"/>
          </svg>
          <h3>Comments <span id="cc-count" style="font-size:13px;color:var(--txt3);font-weight:400"></span></h3>
        </div>
        <select id="cc-order" class="cc-order">
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="top">Top</option>
        </select>
      </div>

      <div class="cc-tabs">
        <button class="cc-tab cc-tab-active" data-tab="chapter">Chapter Comments</button>
        <button class="cc-tab" data-tab="all">All Comments</button>
      </div>

      <div class="cc-login" id="cc-login-prompt">
        <a href="/login">Login</a> or <a href="/register">Register</a> to comment
      </div>
      <form id="cc-form" class="cc-form" style="margin-bottom:14px;display:none">
        <textarea id="cc-input" rows="3" maxlength="1000" placeholder="Write a comment..."></textarea>
        <div id="cc-captcha-box" class="cc-captcha-box">
          <p>You commented recently. Solve this to continue:</p>
          <div style="display:flex;align-items:center;gap:8px">
            <span id="cc-captcha-q" style="font-size:13px;font-weight:600;color:var(--txt)"></span>
            <span style="color:var(--txt3);font-size:13px">= ?</span>
            <input id="cc-captcha-ans" type="number" min="0" max="99" placeholder="0">
          </div>
        </div>
        <div class="cc-form-footer">
          <span id="cc-char" class="cc-char">0 / 1000</span>
          <button type="submit" class="cc-submit">Post comment</button>
        </div>
      </form>

      <div id="cc-list">
        <p style="text-align:center;color:var(--txt3);padding:20px 0;font-size:13px">Loading...</p>
      </div>

      <div id="cc-pg"></div>
    </div>

  </div><!-- /pages-column -->

  <!-- Backdrop for mobile panel -->
  <div class="panel-backdrop" id="panelBackdrop"></div>

  <!-- Chapter list panel -->
  <div class="chapter-panel" id="chapterPanel">
    <div class="panel-inner">
      <div class="panel-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        Chapter List
      </div>
      <div class="panel-manga-name"><?= esc($manga['name']) ?> &middot; <?= $totalChapters ?> chapters</div>

      <div class="panel-search">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="panelSearchInput" placeholder="Search chapter..." />
      </div>

      <div class="ch-list" id="chList">
        <?php foreach ($chapters as $ch): ?>
          <?php
            $chN = rtrim(rtrim(number_format((float)$ch['number'], 1), '0'), '.');
            $isCurrent = ($ch['id'] == $chapter['id']);
            $chDate = !empty($ch['created_at']) ? date('M d', strtotime($ch['created_at'])) : '';
          ?>
          <a href="/manga/<?= esc($slug) ?>/<?= esc($ch['slug']) ?>" class="ch-item<?= $isCurrent ? ' current' : '' ?>" data-chnum="<?= esc($chN) ?>">
            <div class="ch-item-left">
              <span class="ch-item-num">Chapter <?= esc($chN) ?></span>
              <?php if (!empty($ch['name'])): ?>
                <span class="ch-item-title"><?= esc($ch['name']) ?></span>
              <?php endif; ?>
            </div>
            <span class="ch-item-date"><?= esc($chDate) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div><!-- /reader-main -->

<!-- ── Bottom navigation ── -->
<div class="reader-bottom">
  <div class="reader-bottom-inner">
    <a href="<?= $prevUrl ? esc($prevUrl) : '#' ?>" class="bottom-ch-btn<?= $prevUrl ? '' : ' disabled' ?>">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
      <span><?= $prevUrl ? 'Chapter ' . esc($prevNum) : 'No prev' ?></span>
    </a>

    <a href="/manga/<?= esc($slug) ?>" class="back-top-btn" style="text-decoration:none">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
      </svg>
      <?= esc(mb_strimwidth($manga['name'], 0, 30, '...')) ?>
    </a>

    <a href="<?= $nextUrl ? esc($nextUrl) : '#' ?>" class="bottom-ch-btn primary<?= $nextUrl ? '' : ' disabled' ?>">
      <span><?= $nextUrl ? 'Chapter ' . esc($nextNum) : 'No next' ?></span>
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </a>
  </div>
</div>

<!-- Back-to-top FAB -->
<button class="fab-top" id="fabTop" title="Back to top">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="18 15 12 9 6 15"/>
  </svg>
</button>

<script>
(function() {
  'use strict';

  /* ── Chapter panel toggle ── */
  var panelBtn      = document.getElementById('panelToggleBtn');
  var panel         = document.getElementById('chapterPanel');
  var panelBackdrop = document.getElementById('panelBackdrop');

  function openPanel() {
    panel.classList.add('open');
    if (panelBtn) panelBtn.classList.add('active');
    if (panelBackdrop) panelBackdrop.classList.add('show');
    if (window.innerWidth <= 768) document.body.style.overflow = 'hidden';
  }
  function closePanel() {
    panel.classList.remove('open');
    if (panelBtn) panelBtn.classList.remove('active');
    if (panelBackdrop) panelBackdrop.classList.remove('show');
    document.body.style.overflow = '';
  }
  if (panelBtn) panelBtn.addEventListener('click', function() { panel.classList.contains('open') ? closePanel() : openPanel(); });
  if (panelBackdrop) panelBackdrop.addEventListener('click', closePanel);

  /* Mobile chapter button → open panel */
  var mobileChBtn = document.getElementById('mobileChapterBtn');
  if (mobileChBtn) mobileChBtn.addEventListener('click', function() { panel.classList.contains('open') ? closePanel() : openPanel(); });

  /* ── Settings dropdown ── */
  var settingsBtn       = document.getElementById('settingsBtn');
  var settingsBtnMobile = document.getElementById('settingsBtnMobile');
  var settingsDropdown  = document.getElementById('settingsDropdown');

  function openSettings(triggerBtn) {
    var rect = triggerBtn.getBoundingClientRect();
    if (window.innerWidth <= 768) {
      settingsDropdown.style.top = '';
      settingsDropdown.style.right = '';
    } else {
      settingsDropdown.style.top   = (rect.bottom + 8) + 'px';
      settingsDropdown.style.right = (window.innerWidth - rect.right) + 'px';
    }
    settingsDropdown.classList.toggle('open');
  }

  if (settingsBtn) settingsBtn.addEventListener('click', function(e) { e.stopPropagation(); openSettings(settingsBtn); });
  if (settingsBtnMobile) settingsBtnMobile.addEventListener('click', function(e) { e.stopPropagation(); openSettings(settingsBtnMobile); });

  document.addEventListener('click', function(e) {
    var inDropdown = settingsDropdown.contains(e.target);
    var inBtn      = (settingsBtn && settingsBtn.contains(e.target)) ||
                     (settingsBtnMobile && settingsBtnMobile.contains(e.target));
    if (!inDropdown && !inBtn) settingsDropdown.classList.remove('open');
  });

  /* ── Reading settings with localStorage ── */
  var widthSlider = document.getElementById('widthSlider');
  var pagesColumn = document.getElementById('pagesColumn');
  var readerMain  = document.getElementById('readerMain');
  var STORAGE_KEY = 'mangahub-reader';

  function loadSettings() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch(e) { return {}; }
  }
  function saveSettings(obj) {
    var cur = loadSettings();
    for (var k in obj) cur[k] = obj[k];
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cur));
  }

  function applyWidth(v) {
    var wraps = pagesColumn.querySelectorAll('.manga-page-wrap');
    for (var i = 0; i < wraps.length; i++) wraps[i].style.maxWidth = v + '%';
    widthSlider.value = v;
  }
  function applyBg(bg) {
    if (bg === 'black') readerMain.style.background = '#000';
    else if (bg === 'white') readerMain.style.background = '#fff';
    else readerMain.style.background = '';
    activateBtn('[data-bg]', 'data-bg', bg);
  }
  function applyFilter(f) {
    var imgs = document.querySelectorAll('.manga-page-wrap img');
    for (var i = 0; i < imgs.length; i++) imgs[i].style.filter = (f === 'none' || !f) ? '' : f;
    activateBtn('[data-filter]', 'data-filter', f || 'none');
  }
  function applyGap(g) {
    pagesColumn.style.setProperty('--page-gap', g + 'px');
    activateBtn('[data-gap]', 'data-gap', String(g));
  }
  function activateBtn(sel, attr, val) {
    var btns = document.querySelectorAll(sel);
    for (var i = 0; i < btns.length; i++) {
      btns[i].classList.toggle('active', btns[i].getAttribute(attr) === val);
    }
  }

  /* Restore saved settings */
  var saved = loadSettings();
  applyWidth(saved.width || 100);
  applyBg(saved.bg || 'default');
  applyFilter(saved.filter || 'none');
  applyGap(saved.gap != null ? saved.gap : 0);

  /* Page width slider */
  widthSlider.addEventListener('input', function() {
    applyWidth(this.value);
    saveSettings({ width: this.value });
  });

  /* Background buttons */
  var bgBtns = document.querySelectorAll('[data-bg]');
  for (var i = 0; i < bgBtns.length; i++) {
    bgBtns[i].addEventListener('click', function() {
      var bg = this.getAttribute('data-bg');
      applyBg(bg);
      saveSettings({ bg: bg });
    });
  }

  /* Filter buttons */
  var filterBtns = document.querySelectorAll('[data-filter]');
  for (var i = 0; i < filterBtns.length; i++) {
    filterBtns[i].addEventListener('click', function() {
      var f = this.getAttribute('data-filter');
      applyFilter(f);
      saveSettings({ filter: f });
    });
  }

  /* Gap buttons */
  var gapBtns = document.querySelectorAll('[data-gap]');
  for (var i = 0; i < gapBtns.length; i++) {
    gapBtns[i].addEventListener('click', function() {
      var g = this.getAttribute('data-gap');
      applyGap(g);
      saveSettings({ gap: g });
    });
  }

  /* ── Page counter (IntersectionObserver) ── */
  var curPageEl = document.getElementById('curPage');
  var pageWraps = document.querySelectorAll('.manga-page-wrap[data-page]');
  if (curPageEl && pageWraps.length > 0 && 'IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function(entries) {
      for (var i = 0; i < entries.length; i++) {
        if (entries[i].isIntersecting) {
          curPageEl.textContent = entries[i].target.getAttribute('data-page');
        }
      }
    }, { threshold: 0.4 });
    for (var i = 0; i < pageWraps.length; i++) observer.observe(pageWraps[i]);
  }

  /* ── Panel search ── */
  var panelSearch = document.getElementById('panelSearchInput');
  if (panelSearch) {
    panelSearch.addEventListener('input', function() {
      var q = this.value.trim().toLowerCase();
      var items = document.querySelectorAll('#chList .ch-item');
      for (var i = 0; i < items.length; i++) {
        var num = items[i].getAttribute('data-chnum') || '';
        items[i].style.display = (!q || num.indexOf(q) !== -1) ? '' : 'none';
      }
    });
  }

  /* ── Back-to-top FAB ── */
  var fabTop = document.getElementById('fabTop');
  if (fabTop) {
    window.addEventListener('scroll', function() {
      fabTop.classList.toggle('show', window.pageYOffset > 400);
    }, { passive: true });
    fabTop.addEventListener('click', function() { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }

  /* ── Keyboard navigation ── */
  var prevUrl = <?= $prevUrl ? "'" . esc($prevUrl, 'js') . "'" : 'null' ?>;
  var nextUrl = <?= $nextUrl ? "'" . esc($nextUrl, 'js') . "'" : 'null' ?>;
  document.addEventListener('keydown', function(e) {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') return;
    if (e.key === 'ArrowLeft' && prevUrl) {
      window.location.href = prevUrl;
    } else if (e.key === 'ArrowRight' && nextUrl) {
      window.location.href = nextUrl;
    }
  });

})();
</script>

<!-- Comment System -->
<script>
(window.__MH_AUTH || Promise.resolve(null)).then(function(__u) {
  var CHAPTER_ID  = <?= (int) $chapter['id'] ?>;
  var MANGA_ID    = <?= (int) $manga['id'] ?>;
  var MANGA_SLUG  = <?= json_encode($slug) ?>;
  var CURRENT_UID = (__u && __u.id) || 0;

  /* Hydrate comment form */
  if (CURRENT_UID > 0) {
    var loginPrompt = document.getElementById('cc-login-prompt');
    var ccForm = document.getElementById('cc-form');
    if (loginPrompt) loginPrompt.style.display = 'none';
    if (ccForm) ccForm.style.display = '';
  }
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
    var bg=BG[parseInt(uid||0)%6];
    if(sz<40){
      return '<div style="width:'+sz+'px;height:'+sz+'px;border-radius:50%;background:'+bg+';display:flex;align-items:center;justify-content:center;font-size:'+(sz*0.4)+'px;font-weight:700;color:#fff;flex-shrink:0">'+ch+'</div>';
    }
    return '<div class="cc-av" style="--av-size:40px"><div class="cc-av-inner" style="background:'+bg+';font-size:16px">'+ch+'</div></div>';
  }

  var likeIconSvg='<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>';

  function likeBtnHtml(c){
    var isLiked=c.my_reaction==='like';
    if(CURRENT_UID>0){
      return '<button class="cc-react'+(isLiked?' liked':'')+'" data-id="'+c.id+'" data-type="like">'+likeIconSvg+'<span class="cc-lc">'+c.likes_count+'</span></button>';
    }
    return '<span class="cc-react" style="cursor:default">'+likeIconSvg+'<span>'+c.likes_count+'</span></span>';
  }

  function renderReply(c, topParentId){
    var name=c.user_name||c.user_username||'?';
    var replyBtn=(CURRENT_UID>0&&topParentId)
      ?'<button class="cc-reply-btn" data-id="'+topParentId+'" data-reply-to="'+c.id+'" data-name="'+esc(name)+'">↩ Reply</button>'
      :'';
    return '<div class="cc-item" data-id="'+c.id+'">'+
      '<div style="display:flex;gap:8px;align-items:flex-start">'+
        avatar(c.user_name,c.user_username,c.user_id,30)+
        '<div style="flex:1;min-width:0">'+
          '<div class="cc-bubble">'+
            '<span class="cc-name">'+esc(name)+'</span>'+
            '<div class="cc-text">'+esc(c.comment)+'</div>'+
          '</div>'+
          '<div class="cc-meta">'+
            '<div class="cc-meta-left">'+likeBtnHtml(c)+'<span style="color:var(--txt3);font-size:11px">'+timeAgo(c.created_at)+'</span></div>'+
            (replyBtn?'<div>'+replyBtn+'</div>':'')+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>';
  }

  function replyFormHtml(parentId, parentName, replyToId){
    return '<div style="margin-top:8px" id="cc-rf-'+parentId+'">'+
      '<input type="hidden" class="cc-reply-to-id" value="'+(replyToId||0)+'">'+
      '<textarea class="cc-reply-input" rows="2" maxlength="1000">@'+esc(parentName)+' </textarea>'+
      '<div class="cc-rf-captcha-box" style="display:none;margin-top:6px;padding:8px 12px;border-radius:8px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25)">'+
        '<p style="font-size:12px;color:var(--txt3);margin:0 0 6px">You commented recently. Solve this to continue:</p>'+
        '<div style="display:flex;align-items:center;gap:8px">'+
          '<span class="cc-rf-captcha-q" style="font-size:13px;font-weight:600;color:var(--txt)"></span>'+
          '<span style="color:var(--txt3);font-size:13px">= ?</span>'+
          '<input class="cc-rf-captcha-ans" type="number" min="0" max="99" style="width:52px;background:var(--bg);border:1px solid var(--border);color:var(--txt);border-radius:6px;padding:4px 8px;font-size:13px;outline:none" placeholder="0">'+
        '</div>'+
      '</div>'+
      '<div style="display:flex;justify-content:flex-end;gap:8px;margin-top:4px">'+
        '<button class="cc-rf-cancel cc-reply-cancel" data-parent="'+parentId+'">Cancel</button>'+
        '<button class="cc-rf-submit cc-reply-submit" data-parent="'+parentId+'">Reply</button>'+
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
          mBtn.className='cc-show-more';
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
      ?'<button class="cc-reply-btn" data-id="'+c.id+'" data-name="'+esc(name)+'">↩ Reply</button>'
      :'';
    return '<div class="cc-item" data-id="'+c.id+'">'+
      '<div style="display:flex;gap:12px;align-items:flex-start">'+
        avatar(c.user_name,c.user_username,c.user_id)+
        '<div style="flex:1;min-width:0">'+
          '<div class="cc-bubble">'+
            '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:2px">'+
              '<span class="cc-name">'+esc(name)+'</span>'+
              (c.chapter_slug?'<a href="/manga/'+MANGA_SLUG+'/'+esc(c.chapter_slug)+'" class="cc-chapter-tag">'+esc(c.chapter_name||c.chapter_slug)+'</a>':'')+
            '</div>'+
            '<div class="cc-text">'+esc(c.comment)+'</div>'+
          '</div>'+
          '<div class="cc-meta">'+
            '<div class="cc-meta-left">'+likeBtnHtml(c)+'<span style="color:var(--txt3);font-size:11px">'+timeAgo(c.created_at)+'</span></div>'+
            (replyBtn?'<div>'+replyBtn+'</div>':'')+
          '</div>'+
          '<div id="cc-reply-area-'+c.id+'"></div>'+
          '<div id="cc-replies-'+c.id+'" class="cc-replies" style="display:none"></div>'+
        '</div>'+
      '</div>'+
    '</div>';
  }

  function renderPg(){
    var el=document.getElementById('cc-pg');
    if(!el) return;
    if(totalPages<=1){el.innerHTML='';return;}
    var h='';
    h+= page>1?'<button data-page="'+(page-1)+'">&#8249;</button>':'<button disabled>&#8249;</button>';
    var s=Math.max(1,page-2),e=Math.min(totalPages,s+4);s=Math.max(1,e-4);
    if(s>1){h+='<button data-page="1">1</button>';if(s>2)h+='<span style="padding:0 4px;color:var(--txt3)">…</span>';}
    for(var i=s;i<=e;i++) h+=i===page?'<button class="pg-active">'+i+'</button>':'<button data-page="'+i+'">'+i+'</button>';
    if(e<totalPages){if(e<totalPages-1)h+='<span style="padding:0 4px;color:var(--txt3)">…</span>';h+='<button data-page="'+totalPages+'">'+totalPages+'</button>';}
    h+= page<totalPages?'<button data-page="'+(page+1)+'">&#8250;</button>':'<button disabled>&#8250;</button>';
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
          ?'<p style="text-align:center;color:var(--txt3);padding:20px 0;font-size:13px">No comments yet.</p>'
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
        fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){showCaptcha(d.question);});
        return;
      }
      var fd=new FormData();
      fd.append('manga_id',MANGA_ID);
      if(activeTab==='chapter') fd.append('chapter_id',CHAPTER_ID);
      fd.append('comment',text);
      if(captchaReady){
        var ans=document.getElementById('cc-captcha-ans');
        if(!ans||!ans.value.trim()){ans&&ans.focus();return;}
        fd.append('captcha_answer',ans.value.trim());
      }
      fetch('/api/comments',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(c){
          if(c.need_captcha){
            fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){showCaptcha(d.question);});
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

    if(target.classList.contains('cc-react')){
      var cid=parseInt(target.dataset.id);
      var fd=new FormData();
      fd.append('type',target.dataset.type);
      fetch('/api/comments/'+cid+'/react',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(d){
          if(d.error) return;
          var item=target.closest('.cc-item');
          if(!item) return;
          var lb=item.querySelector('.cc-react[data-type="like"]');
          if(lb){
            lb.classList.toggle('liked',d.my_reaction==='like');
            var lc=lb.querySelector('.cc-lc');
            if(lc) lc.textContent=d.likes_count;
          }
        });
      return;
    }

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

    if(target.classList.contains('cc-reply-cancel')){
      var rf=document.getElementById('cc-rf-'+target.dataset.parent);
      if(rf) rf.remove();
      return;
    }

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
      var replyToInput=rf.querySelector('.dc-reply-to-id, .cc-reply-to-id');
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
            fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){
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
});
</script>

<?= $this->endSection() ?>
