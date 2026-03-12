<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('head_extra') ?>
<?php $_coverPreload = manga_cover_url($manga); ?>
<link rel="preload" href="<?= esc($_coverPreload) ?>" as="image" fetchpriority="high">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$cdnBase     = rtrim(env('CDN_COVER_URL', ''), '/');
$coverUrl    = manga_cover_url($manga, $cdnBase);
$statusMap   = [1 => 'Ongoing', 2 => 'Completed'];
$statusLabel = $statusMap[$manga['status_id'] ?? 0] ?? 'Unknown';
$statusColor = ($manga['status_id'] ?? 0) == 1 ? '#22c55e' : '#94a3b8';
$typeMap     = [1 => 'Manga', 2 => 'Manhwa', 3 => 'Manhua'];
$typeLabel   = $typeMap[$manga['comictype_id'] ?? 0] ?? 'Manga';

$firstChap = !empty($chapters) ? $chapters[count($chapters) - 1] : null;

function detail_time_ago($ts) {
    $diff = time() - (int)$ts;
    if ($diff < 60)      return $diff . 's ago';
    if ($diff < 3600)    return floor($diff / 60) . 'm ago';
    if ($diff < 86400)   return floor($diff / 3600) . 'h ago';
    if ($diff < 604800)  return floor($diff / 86400) . 'd ago';
    if ($diff < 2592000) return floor($diff / 604800) . 'w ago';
    return date('M d, Y', (int)$ts);
}

$pageUrl = current_url();
?>

<style>
:root { --teal: #3b82f6; --teal-soft: rgba(59,130,246,.12); --star: #f59e0b; }
:root { --card-bg: var(--surface); --card-border: var(--border); }
body.light { --card-bg: var(--surface); --card-border: var(--border); }

.detail-wrap {
  max-width: 1200px; margin: 0 auto; padding: 28px 16px 60px;
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr 300px;
  grid-template-areas:
    "hero     sidebar"
    "chapters sidebar"
    "related  sidebar"
    "comments sidebar";
  gap: 24px;
  align-items: start;
}
.detail-hero { grid-area: hero; }
.detail-chapters { grid-area: chapters; }
.detail-related { grid-area: related; display: none; }
.detail-comments { grid-area: comments; }
.sidebar { grid-area: sidebar; }

/* ── Hero ── */
.hero { display: flex; gap: 24px; align-items: flex-start; margin-bottom: 24px; }
.hero-cover {
  width: 170px; flex-shrink: 0;
  border-radius: 10px; overflow: hidden;
  box-shadow: 0 8px 32px rgba(0,0,0,.5);
}
.hero-cover img { width: 100%; display: block; aspect-ratio: 15/19; object-fit: cover; }
.hero-info { flex: 1; min-width: 0; padding-top: 4px; overflow: hidden; }

.releasing-label {
  font-size: 11px; font-weight: 700; letter-spacing: 1.5px;
  color: var(--teal); text-transform: uppercase;
  margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
}
.releasing-dot {
  width: 7px; height: 7px; border-radius: 50%; background: var(--teal);
  animation: pulse-dot 1.8s ease-in-out infinite;
}
@keyframes pulse-dot { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.4; transform:scale(.7); } }

.hero-title { font-size: 34px; font-weight: 900; color: var(--txt); line-height: 1.1; margin-bottom: 6px; word-break: break-word; overflow-wrap: anywhere; }
.hero-alt { font-size: 12.5px; color: var(--txt3); margin-bottom: 18px; line-height: 1.6; }

.hero-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.btn-read {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 22px; border-radius: 8px;
  background: var(--teal); color: #fff;
  font-size: 13.5px; font-weight: 700;
  border: none; cursor: pointer; text-decoration: none;
  transition: background .2s, transform .15s;
}
.btn-read:hover { background: #2563eb; transform: translateY(-1px); }
.btn-bookmark {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 20px; border-radius: 8px;
  background: transparent; color: var(--txt2);
  font-size: 13.5px; font-weight: 700;
  border: 1.5px solid var(--card-border);
  cursor: pointer; transition: all .2s;
}
.btn-bookmark:hover, .btn-bookmark.active { border-color: var(--teal); color: var(--teal); }

/* Stats */
.stats-bar { display: flex; flex-wrap: wrap; gap: 0; margin-bottom: 20px; }
.stat-item {
  display: flex; align-items: center; gap: 6px;
  font-size: 13px; color: var(--txt2);
  padding-right: 16px; margin-right: 16px;
  border-right: 1px solid var(--card-border);
}
.stat-item:last-child { border-right: none; padding-right: 0; margin-right: 0; }
.stat-item svg { flex-shrink: 0; color: var(--txt3); }
.stat-val { color: var(--txt); font-weight: 600; }

/* Rating (sidebar) */
.rating-big {
  display: flex; flex-direction: column; align-items: center;
  padding: 10px 0 6px;
}
.rating-score { font-size: 38px; font-weight: 900; color: var(--txt); line-height: 1; }
.rating-max { font-size: 18px; color: var(--txt3); font-weight: 400; }
.rating-stars { display: flex; gap: 3px; margin: 10px 0 6px; }
.rating-star {
  font-size: 20px; cursor: pointer;
  transition: transform .15s;
}
.rating-star:hover { transform: scale(1.2); }
.rating-star.fill { color: var(--star); }
.rating-star.half { color: var(--star); opacity: .5; }
.rating-star.empty { color: var(--card-border); }
.rating-reviews { font-size: 12px; color: var(--txt3); }
.rating-yours { font-size: 12px; color: var(--teal); font-weight: 600; margin-top: 4px; }

/* Description */
.description { font-size: 13.5px; color: var(--txt2); line-height: 1.75; margin-bottom: 6px; }
.read-more {
  font-size: 13px; color: var(--teal); font-weight: 600;
  cursor: pointer; background: none; border: none; padding: 0;
}
.read-more:hover { text-decoration: underline; }

/* Share */
.share-row {
  display: flex; flex-wrap: wrap; align-items: center;
  gap: 8px; margin-top: 20px; padding-top: 20px;
  border-top: 1px solid var(--card-border);
}
.share-label { font-size: 12px; color: var(--txt3); font-weight: 600; margin-right: 4px; }
.share-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 6px 12px; border-radius: 7px;
  font-size: 12.5px; font-weight: 700;
  border: none; cursor: pointer; text-decoration: none;
  transition: opacity .2s;
}
.share-btn:hover { opacity: .85; }
.sh-fb  { background: #1877f2; color: #fff; }
.sh-x   { background: #000; color: #fff; border: 1px solid #333; }
.sh-wa  { background: #25d366; color: #fff; }
.sh-tg  { background: #26a5e4; color: #fff; }

/* ── Chapter section ── */
.chapter-section {
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  border-radius: 12px; overflow: hidden;
}
.chapter-tabs { display: flex; border-bottom: 1px solid var(--card-border); }
.ch-tab {
  padding: 13px 24px; font-size: 13.5px; font-weight: 700;
  color: var(--txt3); cursor: pointer; border: none;
  background: transparent; transition: color .2s;
  border-bottom: 2px solid transparent; margin-bottom: -1px;
}
.ch-tab.active { color: var(--teal); border-bottom-color: var(--teal); }

.chapter-controls {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; border-bottom: 1px solid var(--card-border); flex-wrap: wrap;
}
.ch-count { font-size: 13px; color: var(--txt3); }
.ch-search {
  display: flex; align-items: center; gap: 7px;
  padding: 6px 12px; border-radius: 7px;
  background: var(--bg); border: 1px solid var(--card-border);
  margin-left: auto;
}
.ch-search input {
  background: transparent; border: none; outline: none;
  color: var(--txt); font-size: 13px; width: 120px;
}
.ch-search input::placeholder { color: var(--txt3); }

.chapter-list { padding: 4px 0; max-height: 380px; overflow-y: auto; }
.chapter-list::-webkit-scrollbar { width: 4px; }
.chapter-list::-webkit-scrollbar-thumb { background: var(--card-border); border-radius: 2px; }

.ch-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 11px 16px; cursor: pointer; transition: background .15s;
  border-bottom: 1px solid rgba(255,255,255,.03); text-decoration: none; color: inherit;
}
.ch-row:last-child { border-bottom: none; }
.ch-row:hover { background: rgba(59,130,246,.06); }
.ch-name { font-size: 13.5px; color: var(--txt2); font-weight: 500; transition: color .15s; }
.ch-row:hover .ch-name { color: var(--teal); }
.ch-date { font-size: 12px; color: var(--txt3); flex-shrink: 0; margin-left: 12px; }
.ch-new {
  font-size: 9.5px; font-weight: 700; padding: 2px 6px;
  border-radius: 4px; background: var(--teal); color: #fff;
  margin-left: 8px; letter-spacing: .3px;
}

/* ── Comments ── */
.comments-box {
  margin-top: 16px;
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  border-radius: 12px; padding: 20px;
}
.comments-box h3 { font-size: 15px; font-weight: 700; color: var(--txt); margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.dc-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.dc-head-left { display: flex; align-items: center; gap: 8px; }
.dc-order {
  background: var(--bg); border: 1px solid var(--card-border);
  color: var(--txt2); font-size: 12px; border-radius: 6px;
  padding: 4px 8px; outline: none;
}
.dc-form textarea {
  width: 100%; background: var(--bg); border: 1px solid var(--card-border);
  border-radius: 8px; padding: 10px 12px; font-size: 13px; color: var(--txt);
  resize: none; outline: none; box-sizing: border-box; font-family: inherit;
}
.dc-form textarea:focus { border-color: var(--accent); }
.dc-form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 6px; }
.dc-char { font-size: 11px; color: var(--txt3); }
.dc-submit {
  background: var(--accent); color: #fff; font-size: 12px; font-weight: 600;
  padding: 6px 16px; border-radius: 7px; border: none; cursor: pointer;
  transition: opacity .2s;
}
.dc-submit:hover { opacity: .85; }
.dc-captcha-box {
  display: none; margin-top: 8px; padding: 10px 14px;
  border-radius: 8px; background: rgba(245,158,11,.08);
  border: 1px solid rgba(245,158,11,.25);
}
.dc-captcha-box p { font-size: 12px; color: var(--txt3); margin: 0 0 6px; }
.dc-captcha-box input {
  width: 56px; background: var(--bg); border: 1px solid var(--card-border);
  color: var(--txt); border-radius: 6px; padding: 4px 8px; font-size: 13px; outline: none;
}
.dc-login { text-align: center; font-size: 13px; color: var(--txt3); padding: 10px 0 14px; }
.dc-login a { color: var(--accent); font-weight: 600; text-decoration: none; }
.dc-login a:hover { text-decoration: underline; }
#dc-list { display: flex; flex-direction: column; gap: 4px; }
.dc-item { padding: 4px 0; }
.dc-bubble {
  background: var(--bg); border-radius: 10px;
  padding: 8px 12px;
}
.dc-bubble .dc-name { font-size: 13px; font-weight: 700; color: var(--txt); }
.dc-bubble .dc-chapter-tag {
  font-size: 10px; background: rgba(59,130,246,.12); color: var(--accent);
  border: 1px solid rgba(59,130,246,.2); border-radius: 4px;
  padding: 1px 6px; white-space: nowrap; text-decoration: none;
}
.dc-bubble .dc-chapter-tag:hover { text-decoration: underline; }
.dc-text { margin-top: 3px; font-size: 13px; color: var(--txt2); white-space: pre-wrap; word-break: break-word; }
.dc-meta { display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-top: 4px; padding: 0 4px; }
.dc-meta-left { display: flex; align-items: center; gap: 6px; }
.dc-react {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 12px; background: none; border: none; cursor: pointer;
  color: var(--txt3); transition: color .15s;
}
.dc-react.liked { color: var(--accent); }
.dc-reply-btn {
  background: none; border: none; cursor: pointer;
  font-size: 11px; color: var(--txt3); padding: 2px 6px; border-radius: 4px;
  transition: color .15s;
}
.dc-reply-btn:hover { color: var(--accent); }
.dc-replies { padding-left: 12px; margin-top: 6px; border-left: 1px solid rgba(59,130,246,.2); }
.dc-show-more {
  font-size: 12px; color: var(--accent); background: none; border: none;
  cursor: pointer; padding: 2px 4px; margin-top: 4px;
}
.dc-show-more:hover { text-decoration: underline; }
.dc-reply-input {
  width: 100%; background: var(--bg); border: 1px solid var(--card-border);
  border-radius: 8px; padding: 8px 10px; font-size: 12px; color: var(--txt);
  resize: none; outline: none; box-sizing: border-box; font-family: inherit;
}
.dc-reply-input:focus { border-color: var(--accent); }
.dc-rf-cancel { background: none; border: none; cursor: pointer; font-size: 12px; color: var(--txt3); padding: 4px 10px; }
.dc-rf-submit {
  background: var(--accent); color: #fff; font-size: 12px;
  padding: 4px 14px; border-radius: 6px; border: none; cursor: pointer;
}
#dc-pg { display: flex; justify-content: center; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 12px; }
#dc-pg button {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 30px; height: 30px; padding: 0 6px; border-radius: 6px;
  font-size: 12px; border: 1px solid var(--card-border);
  background: var(--bg); color: var(--txt2); cursor: pointer; transition: background .15s;
}
#dc-pg button:hover:not([disabled]):not(.pg-active) { background: var(--card-border); }
#dc-pg .pg-active { background: var(--accent) !important; border-color: var(--accent) !important; color: #fff !important; font-weight: 700; pointer-events: none; }
#dc-pg button[disabled] { opacity: .4; cursor: default; pointer-events: none; }
.dc-av {
  position: relative; display: inline-flex; align-items: center; justify-content: center;
  width: var(--av-size, 40px); height: var(--av-size, 40px); border-radius: 50%; flex-shrink: 0;
}
.dc-av-inner {
  width: 100%; height: 100%; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; color: #fff;
}

/* ── Sidebar ── */
.sidebar { display: flex; flex-direction: column; gap: 16px; }
.sidebar-card {
  background: var(--card-bg);
  border: 1px solid var(--card-border);
  border-radius: 12px; padding: 18px;
}
.sidebar-card h4 {
  font-size: 13px; font-weight: 700; color: var(--txt3);
  text-transform: uppercase; letter-spacing: .8px;
  margin-bottom: 14px; padding-bottom: 10px;
  border-bottom: 1px solid var(--card-border);
}
.meta-row { display: flex; gap: 8px; margin-bottom: 10px; font-size: 13px; }
.meta-row:last-child { margin-bottom: 0; }
.meta-key { color: var(--txt3); font-weight: 600; min-width: 76px; flex-shrink: 0; }
.meta-val { color: var(--txt); display: flex; flex-wrap: wrap; gap: 4px 6px; }
.meta-link { color: var(--teal); }
.meta-link:hover { text-decoration: underline; }

.related-item {
  display: flex; gap: 10px; align-items: center;
  padding: 8px 0; border-bottom: 1px solid var(--card-border);
  text-decoration: none; color: inherit;
}
.related-item:last-child { border-bottom: none; padding-bottom: 0; }
.related-item:hover .related-title { color: var(--teal); }
.related-thumb {
  width: 44px; height: 58px; border-radius: 6px;
  overflow: hidden; flex-shrink: 0;
}
.related-thumb img { width: 100%; height: 100%; object-fit: cover; }
.related-info { flex: 1; min-width: 0; }
.related-title {
  font-size: 13px; font-weight: 700; color: var(--txt); margin-bottom: 3px;
  overflow: hidden; text-overflow: ellipsis; transition: color .15s;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.related-meta { font-size: 11.5px; color: var(--txt3); }

/* ── Responsive ── */
@media (max-width: 900px) {
  .detail-wrap {
    grid-template-columns: 1fr;
    grid-template-areas:
      "hero"
      "sidebar"
      "chapters"
      "related"
      "comments";
  }
  .sidebar { display: grid; grid-template-columns: 1fr 1fr; }
  .sidebar .sidebar-card-related { display: none; }
  .detail-related { display: block; }
}
@media (max-width: 600px) {
  .hero { flex-direction: column; align-items: center; text-align: center; }
  .hero-cover { width: 140px; }
  .hero-actions { justify-content: center; }
  .stats-bar { justify-content: center; }
  .share-row { justify-content: center; }
  .hero-title { font-size: 22px; }
  .hero-alt { font-size: 11.5px; }
  .sidebar { grid-template-columns: 1fr; }
}
</style>

<div class="detail-wrap">

  <!-- Hero -->
  <div class="detail-hero">
    <div class="hero">
      <div class="hero-cover">
        <img src="<?= esc($coverUrl) ?>" alt="<?= esc($manga['name']) ?>" width="340" height="430" loading="eager" fetchpriority="high">
      </div>
      <div class="hero-info">
        <div class="releasing-label">
          <span class="releasing-dot"></span>
          <?= esc($statusLabel) ?>
        </div>
        <h1 class="hero-title"><?= esc($manga['name']) ?></h1>
        <?php if (!empty($manga['otherNames'])): ?>
          <div class="hero-alt"><?= esc($manga['otherNames']) ?></div>
        <?php endif; ?>

        <div class="hero-actions">
          <?php if ($firstChap): ?>
            <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($firstChap['slug']) ?>" class="btn-read">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg>
              START READING
            </a>
          <?php endif; ?>
          <button class="btn-bookmark" id="bookmarkBtn" data-manga="<?= (int)$manga['id'] ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
            BOOKMARK
          </button>
        </div>

        <div class="stats-bar">
          <div class="stat-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
            <span class="stat-val"><?= esc($typeLabel) ?></span>
          </div>
          <div class="stat-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <span class="stat-val"><?= number_format((int)($manga['views'] ?? 0)) ?></span>
          </div>
          <div class="stat-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span class="stat-val"><?= number_format((int)($manga['view_day'] ?? 0)) ?> daily</span>
          </div>
          <div class="stat-item">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span class="stat-val"><?= count($chapters) ?> chaps</span>
          </div>
        </div>

        <div class="description" id="descText"><?= $manga['summary'] ?? '' ?></div>
        <button class="read-more" id="readMoreBtn">Read more +</button>

        <!-- Share -->
        <div class="share-row">
          <span class="share-label">Share:</span>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($pageUrl) ?>" target="_blank" rel="noopener" class="share-btn sh-fb">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            Facebook
          </a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($manga['name']) ?>" target="_blank" rel="noopener" class="share-btn sh-x">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            X
          </a>
          <a href="https://wa.me/?text=<?= urlencode($manga['name'] . ' ' . $pageUrl) ?>" target="_blank" rel="noopener" class="share-btn sh-wa">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
            WhatsApp
          </a>
          <a href="https://t.me/share/url?url=<?= urlencode($pageUrl) ?>&text=<?= urlencode($manga['name']) ?>" target="_blank" rel="noopener" class="share-btn sh-tg">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
            Telegram
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Chapter List -->
  <div class="detail-chapters">
    <div class="chapter-section">
      <div class="chapter-tabs">
        <button class="ch-tab active">CHAPTER</button>
      </div>
      <div class="chapter-controls">
        <span class="ch-count"><?= count($chapters) ?> chapters</span>
        <div class="ch-search">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" placeholder="Chap number…" id="chapterSearch">
        </div>
      </div>
      <div class="chapter-list" id="chapterList">
        <?php if (!empty($chapters)): ?>
          <?php foreach ($chapters as $idx => $ch): ?>
            <?php if (empty($ch['is_show'])) continue; ?>
            <?php
              $chNum = rtrim(rtrim(number_format((float)$ch['number'], 1), '0'), '.');
              $chLabel = 'Chapter ' . $chNum;
              if (!empty($ch['name'])) $chLabel .= ': ' . $ch['name'];
              $chTs = $ch['update_at'] ?? $manga['update_at'] ?? time();
              $chTimestamp = is_numeric($chTs) ? (int)$chTs : strtotime($chTs);
              $chTimeAgo = detail_time_ago($chTimestamp);
              $isNew = $idx === 0 && (time() - $chTimestamp) < 86400 * 3;
            ?>
            <a href="/manga/<?= esc($manga['slug']) ?>/<?= esc($ch['slug']) ?>" class="ch-row" data-chnum="<?= esc($chNum) ?>">
              <span class="ch-name">
                <?= esc($chLabel) ?>
                <?php if ($isNew): ?><span class="ch-new">NEW</span><?php endif; ?>
              </span>
              <span class="ch-date"><?= esc($chTimeAgo) ?></span>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="padding:16px;color:var(--txt3);font-size:.875rem;">No chapters available yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Comments -->
  <div class="detail-comments">
    <div class="comments-box" id="dc-section">
      <div class="dc-head">
        <div class="dc-head-left">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent);flex-shrink:0">
            <path d="M8 9h8"/><path d="M8 13h6"/>
            <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3h-5l-5 3v-3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h12z"/>
          </svg>
          <h3 style="margin:0">Comments <span id="dc-count" style="font-size:12px;color:var(--txt3);font-weight:400"></span></h3>
        </div>
        <select id="dc-order" class="dc-order">
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="top">Top</option>
        </select>
      </div>

      <div class="dc-login" id="dc-login-prompt">
        <a href="/login">Login</a> or <a href="/register">Register</a> to join the conversation
      </div>
      <form id="dc-form" class="dc-form" style="margin-bottom:14px;display:none">
        <textarea id="dc-input" rows="3" maxlength="1000" placeholder="Write a comment..."></textarea>
        <div id="dc-captcha-box" class="dc-captcha-box">
          <p>You commented recently. Solve this to continue:</p>
          <div style="display:flex;align-items:center;gap:8px">
            <span id="dc-captcha-q" style="font-size:13px;font-weight:600;color:var(--txt)"></span>
            <span style="color:var(--txt3);font-size:13px">= ?</span>
            <input id="dc-captcha-ans" type="number" min="0" max="99" placeholder="0">
          </div>
        </div>
        <div class="dc-form-footer">
          <span id="dc-char" class="dc-char">0 / 1000</span>
          <button type="submit" class="dc-submit">Post comment</button>
        </div>
      </form>

      <div id="dc-list">
        <p style="text-align:center;color:var(--txt3);padding:20px 0;font-size:13px">Loading...</p>
      </div>

      <div id="dc-pg"></div>
    </div>
  </div>

  <!-- Sidebar -->
  <aside class="sidebar">

    <!-- Information -->
    <div class="sidebar-card">
      <h4>Information</h4>

      <?php if (!empty($authors)): ?>
      <div class="meta-row">
        <span class="meta-key">Author:</span>
        <span class="meta-val">
          <?php foreach ($authors as $a): ?>
            <a href="/search?filter[artist]=<?= urlencode($a['name']) ?>" class="meta-link"><?= esc($a['name']) ?></a>
          <?php endforeach; ?>
        </span>
      </div>
      <?php endif; ?>

      <?php if (!empty($mangaCats)): ?>
      <div class="meta-row">
        <span class="meta-key">Genres:</span>
        <span class="meta-val">
          <?php foreach ($mangaCats as $cat): ?>
            <a href="/search?genre=<?= urlencode($cat['slug']) ?>" class="meta-link"><?= esc($cat['name']) ?></a>
          <?php endforeach; ?>
        </span>
      </div>
      <?php endif; ?>

      <div class="meta-row">
        <span class="meta-key">Status:</span>
        <span class="meta-val" style="color:<?= $statusColor ?>;font-weight:600;"><?= esc($statusLabel) ?></span>
      </div>

      <div class="meta-row">
        <span class="meta-key">Type:</span>
        <span class="meta-val"><?= esc($typeLabel) ?></span>
      </div>
    </div>

    <!-- Rating -->
    <div class="sidebar-card">
      <h4>Rating</h4>
      <div class="rating-big">
        <div>
          <span class="rating-score" id="ratingScore"><?= number_format($ratingAvg ?? 0, 2) ?></span>
          <span class="rating-max"> / 10</span>
        </div>
        <div class="rating-stars" id="ratingStars">
          <?php
            $score10 = $ratingAvg ?? 0;
            for ($i = 1; $i <= 10; $i++):
              if ($i <= floor($score10)):
                $cls = 'fill';
              elseif ($i <= ceil($score10) && ($score10 - floor($score10)) >= 0.3):
                $cls = 'half';
              else:
                $cls = 'empty';
              endif;
          ?>
          <span class="rating-star <?= $cls ?>" data-star="<?= $i ?>">★</span>
          <?php endfor; ?>
        </div>
        <div class="rating-reviews" id="ratingReviews">by <?= number_format((int)($ratingVotes ?? 0)) ?> reviews</div>
        <?php if (($myRating ?? 0) > 0): ?>
        <div class="rating-yours" id="ratingYours">Your rating: <?= (int)$myRating ?>/10</div>
        <?php else: ?>
        <div class="rating-yours" id="ratingYours">Click a star to rate!</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Related Manga (desktop sidebar) -->
    <?php if (!empty($recommended)): ?>
    <div class="sidebar-card sidebar-card-related">
      <h4>You May Also Like</h4>
      <?php foreach ($recommended as $rel): ?>
        <?php $relCover = manga_cover_url($rel, $cdnBase); ?>
        <a href="/manga/<?= esc($rel['slug']) ?>" class="related-item">
          <div class="related-thumb">
            <img src="<?= esc($relCover) ?>" alt="<?= esc($rel['name']) ?>" loading="lazy" decoding="async">
          </div>
          <div class="related-info">
            <div class="related-title"><?= esc($rel['name']) ?></div>
            <?php if (!empty($rel['chapter_1'])): ?>
              <div class="related-meta">Ch. <?= esc($rel['chapter_1']) ?></div>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </aside>

  <!-- Related Manga (mobile - after chapters) -->
  <?php if (!empty($recommended)): ?>
  <div class="detail-related">
    <div class="sidebar-card">
      <h4>You May Also Like</h4>
      <?php foreach ($recommended as $rel): ?>
        <?php $relCover = manga_cover_url($rel, $cdnBase); ?>
        <a href="/manga/<?= esc($rel['slug']) ?>" class="related-item">
          <div class="related-thumb">
            <img src="<?= esc($relCover) ?>" alt="<?= esc($rel['name']) ?>" loading="lazy" decoding="async">
          </div>
          <div class="related-info">
            <div class="related-title"><?= esc($rel['name']) ?></div>
            <?php if (!empty($rel['chapter_1'])): ?>
              <div class="related-meta">Ch. <?= esc($rel['chapter_1']) ?></div>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
(function() {
  'use strict';

  /* Read more toggle */
  var descEl = document.getElementById('descText');
  var btnEl  = document.getElementById('readMoreBtn');
  if (descEl && btnEl) {
    descEl.style.display = '-webkit-box';
    descEl.style.webkitLineClamp = '3';
    descEl.style.webkitBoxOrient = 'vertical';
    descEl.style.overflow = 'hidden';
    if (descEl.scrollHeight <= descEl.clientHeight + 5) {
      btnEl.style.display = 'none';
    }
    var expanded = false;
    btnEl.addEventListener('click', function() {
      expanded = !expanded;
      descEl.style.webkitLineClamp = expanded ? 'unset' : '3';
      descEl.style.overflow = expanded ? 'visible' : 'hidden';
      btnEl.textContent = expanded ? 'Read less −' : 'Read more +';
    });
  }

  /* Chapter search */
  var chSearch = document.getElementById('chapterSearch');
  var chList   = document.getElementById('chapterList');
  if (chSearch && chList) {
    chSearch.addEventListener('input', function() {
      var q = this.value.trim();
      chList.querySelectorAll('.ch-row').forEach(function(row) {
        row.style.display = (!q || row.getAttribute('data-chnum').indexOf(q) !== -1) ? '' : 'none';
      });
    });
  }

  /* Bookmark toggle */
  var bookmarkBtn = document.getElementById('bookmarkBtn');
  if (bookmarkBtn) {
    var bmSvg = bookmarkBtn.querySelector('svg');
    var bmPath = bmSvg ? bmSvg.querySelector('path') : null;
    bookmarkBtn.addEventListener('click', function() {
      var mangaId = bookmarkBtn.getAttribute('data-manga');
      var fd = new FormData();
      fd.append('manga_id', mangaId);
      fetch('/api/bookmark/toggle', {
        method: 'POST',
        body: fd
      })
      .then(function(res) {
        if (res.status === 401) { window.location.href = '/login'; return; }
        return res.json();
      })
      .then(function(data) {
        if (!data) return;
        if (data.bookmarked) {
          bookmarkBtn.classList.add('active');
          if (bmSvg) bmSvg.setAttribute('fill', 'currentColor');
          bookmarkBtn.childNodes.forEach(function(n) { if (n.nodeType === 3 && n.textContent.trim()) n.textContent = '\n            BOOKMARKED\n          '; });
        } else {
          bookmarkBtn.classList.remove('active');
          if (bmSvg) bmSvg.setAttribute('fill', 'none');
          bookmarkBtn.childNodes.forEach(function(n) { if (n.nodeType === 3 && n.textContent.trim()) n.textContent = '\n            BOOKMARK\n          '; });
        }
      })
      .catch(function(e) { console.error('Bookmark error:', e); });
    });
  }
})();
</script>

<!-- Comment System -->
<script>
(window.__MH_AUTH || Promise.resolve(null)).then(function(__u) {
  var MANGA_ID    = <?= (int) $manga['id'] ?>;
  var MANGA_SLUG  = <?= json_encode($manga['slug']) ?>;
  var CURRENT_UID = (__u && __u.id) || 0;

  /* Hydrate comment form */
  if (CURRENT_UID > 0) {
    var loginPrompt = document.getElementById('dc-login-prompt');
    var dcForm = document.getElementById('dc-form');
    if (loginPrompt) loginPrompt.style.display = 'none';
    if (dcForm) dcForm.style.display = '';
  }
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
    var sz=size||40;
    var ch=((name||username||'?')[0]).toUpperCase();
    var bg=BG[parseInt(uid||0)%6];
    if(sz<40){
      return '<div style="width:'+sz+'px;height:'+sz+'px;border-radius:50%;background:'+bg+';display:flex;align-items:center;justify-content:center;font-size:'+(sz*0.4)+'px;font-weight:700;color:#fff;flex-shrink:0">'+ch+'</div>';
    }
    return '<div class="dc-av" style="--av-size:40px"><div class="dc-av-inner" style="background:'+bg+';font-size:16px">'+ch+'</div></div>';
  }

  var likeIconSvg='<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>';

  function likeBtnHtml(c){
    var isLiked=c.my_reaction==='like';
    if(CURRENT_UID>0){
      return '<button class="dc-react'+(isLiked?' liked':'')+'" data-id="'+c.id+'" data-type="like">'+likeIconSvg+'<span class="dc-lc">'+c.likes_count+'</span></button>';
    }
    return '<span class="dc-react" style="cursor:default">'+likeIconSvg+'<span>'+c.likes_count+'</span></span>';
  }

  function renderReply(c, topParentId){
    var name=c.user_name||c.user_username||'?';
    var replyBtn=(CURRENT_UID>0&&topParentId)
      ?'<button class="dc-reply-btn" data-id="'+topParentId+'" data-reply-to="'+c.id+'" data-name="'+esc(name)+'">↩ Reply</button>'
      :'';
    return '<div class="dc-item" data-id="'+c.id+'">'+
      '<div style="display:flex;gap:8px;align-items:flex-start">'+
        avatar(c.user_name,c.user_username,c.user_id,30)+
        '<div style="flex:1;min-width:0">'+
          '<div class="dc-bubble">'+
            '<span class="dc-name">'+esc(name)+'</span>'+
            '<div class="dc-text">'+esc(c.comment)+'</div>'+
          '</div>'+
          '<div class="dc-meta">'+
            '<div class="dc-meta-left">'+likeBtnHtml(c)+'<span style="color:var(--txt3);font-size:11px">'+timeAgo(c.created_at)+'</span></div>'+
            (replyBtn?'<div>'+replyBtn+'</div>':'')+
          '</div>'+
        '</div>'+
      '</div>'+
    '</div>';
  }

  function replyFormHtml(parentId, parentName, replyToId){
    return '<div style="margin-top:8px" id="dc-rf-'+parentId+'">'+
      '<input type="hidden" class="dc-reply-to-id" value="'+(replyToId||0)+'">'+
      '<textarea class="dc-reply-input" rows="2" maxlength="1000">@'+esc(parentName)+' </textarea>'+
      '<div class="dc-rf-captcha-box" style="display:none;margin-top:6px;padding:8px 12px;border-radius:8px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25)">'+
        '<p style="font-size:12px;color:var(--txt3);margin:0 0 6px">You commented recently. Solve this to continue:</p>'+
        '<div style="display:flex;align-items:center;gap:8px">'+
          '<span class="dc-rf-captcha-q" style="font-size:13px;font-weight:600;color:var(--txt)"></span>'+
          '<span style="color:var(--txt3);font-size:13px">= ?</span>'+
          '<input class="dc-rf-captcha-ans" type="number" min="0" max="99" style="width:52px;background:var(--bg);border:1px solid var(--card-border);color:var(--txt);border-radius:6px;padding:4px 8px;font-size:13px;outline:none" placeholder="0">'+
        '</div>'+
      '</div>'+
      '<div style="display:flex;justify-content:flex-end;gap:8px;margin-top:4px">'+
        '<button class="dc-rf-cancel dc-reply-cancel" data-parent="'+parentId+'">Cancel</button>'+
        '<button class="dc-rf-submit dc-reply-submit" data-parent="'+parentId+'">Reply</button>'+
      '</div>'+
    '</div>';
  }

  function fetchReplies(commentId, btn){
    var container=document.getElementById('dc-replies-'+commentId);
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
          mBtn.className='dc-show-more';
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
      ?'<button class="dc-reply-btn" data-id="'+c.id+'" data-name="'+esc(name)+'">↩ Reply</button>'
      :'';
    return '<div class="dc-item" data-id="'+c.id+'">'+
      '<div style="display:flex;gap:12px;align-items:flex-start">'+
        avatar(c.user_name,c.user_username,c.user_id)+
        '<div style="flex:1;min-width:0">'+
          '<div class="dc-bubble">'+
            '<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:2px">'+
              '<span class="dc-name">'+esc(name)+'</span>'+
              (c.chapter_slug?'<a href="/manga/'+MANGA_SLUG+'/'+esc(c.chapter_slug)+'" class="dc-chapter-tag">'+esc(c.chapter_name||c.chapter_slug)+'</a>':'')+
            '</div>'+
            '<div class="dc-text">'+esc(c.comment)+'</div>'+
          '</div>'+
          '<div class="dc-meta">'+
            '<div class="dc-meta-left">'+likeBtnHtml(c)+'<span style="color:var(--txt3);font-size:11px">'+timeAgo(c.created_at)+'</span></div>'+
            (replyBtn?'<div>'+replyBtn+'</div>':'')+
          '</div>'+
          '<div id="dc-reply-area-'+c.id+'"></div>'+
          '<div id="dc-replies-'+c.id+'" class="dc-replies" style="display:none"></div>'+
        '</div>'+
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
    if(s>1){h+='<button data-page="1">1</button>';if(s>2)h+='<span style="padding:0 4px;color:var(--txt3)">…</span>';}
    for(var i=s;i<=e;i++) h+=i===page?'<button class="pg-active">'+i+'</button>':'<button data-page="'+i+'">'+i+'</button>';
    if(e<totalPages){if(e<totalPages-1)h+='<span style="padding:0 4px;color:var(--txt3)">…</span>';h+='<button data-page="'+totalPages+'">'+totalPages+'</button>';}
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
          ?'<p style="text-align:center;color:var(--txt3);padding:20px 0;font-size:13px">No comments yet. Be the first!</p>'
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
      document.getElementById('dc-captcha-box').style.display='none';
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
        fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){showCaptcha(d.question);});
        return;
      }
      var fd=new FormData();
      fd.append('manga_id',MANGA_ID);
      fd.append('comment',text);
      if(captchaReady){
        var ans=document.getElementById('dc-captcha-ans');
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

    if(target.classList.contains('dc-react')){
      var cid=parseInt(target.dataset.id);
      var fd=new FormData();
      fd.append('type',target.dataset.type);
      fetch('/api/comments/'+cid+'/react',{method:'POST',body:fd})
        .then(function(r){return r.json();})
        .then(function(d){
          if(d.error) return;
          var item=target.closest('.dc-item');
          if(!item) return;
          var lb=item.querySelector('.dc-react[data-type="like"]');
          if(lb){
            lb.classList.toggle('liked',d.my_reaction==='like');
            var lc=lb.querySelector('.dc-lc');
            if(lc) lc.textContent=d.likes_count;
          }
        });
      return;
    }

    if(target.classList.contains('dc-reply-btn')){
      var parentId=target.dataset.id;
      var parentName=target.dataset.name;
      var replyToId=target.dataset.replyTo||0;
      var area=document.getElementById('dc-reply-area-'+parentId);
      if(!area) return;
      var existing=document.getElementById('dc-rf-'+parentId);
      if(existing){existing.remove();return;}
      area.innerHTML=replyFormHtml(parentId,parentName,replyToId);
      var ta=area.querySelector('.dc-reply-input');
      ta.focus();
      ta.setSelectionRange(ta.value.length,ta.value.length);
      return;
    }

    if(target.classList.contains('dc-reply-cancel')){
      var rf=document.getElementById('dc-rf-'+target.dataset.parent);
      if(rf) rf.remove();
      return;
    }

    if(target.classList.contains('dc-reply-submit')){
      var parentId=target.dataset.parent;
      var rf=document.getElementById('dc-rf-'+parentId);
      if(!rf) return;
      var ta=rf.querySelector('.dc-reply-input');
      var text=ta?ta.value.trim():'';
      if(!text) return;
      var captchaBox=rf.querySelector('.dc-rf-captcha-box');
      var captchaVisible=captchaBox&&captchaBox.style.display!=='none';
      if(captchaVisible){
        var captchaAns=rf.querySelector('.dc-rf-captcha-ans');
        if(!captchaAns||!captchaAns.value.trim()){captchaAns&&captchaAns.focus();return;}
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
            fetch('/api/captcha').then(function(r){return r.json();}).then(function(d){
              var box=rf.querySelector('.dc-rf-captcha-box');
              var q=rf.querySelector('.dc-rf-captcha-q');
              var ans=rf.querySelector('.dc-rf-captcha-ans');
              if(box) box.style.display='block';
              if(q) q.textContent=d.question;
              if(ans){ans.value='';ans.focus();}
            });
            target.disabled=false; target.textContent='Reply';
            return;
          }
          if(c.error){alert(c.error);target.disabled=false;target.textContent='Reply';return;}
          rf.remove();
          var container=document.getElementById('dc-replies-'+parentId);
          if(container){
            var moreBtn=container.querySelector('.dc-show-more');
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

<script>
(function(){
  var mangaId = <?= (int)$manga['id'] ?>;
  var stars = document.querySelectorAll('#ratingStars .rating-star');
  var scoreEl = document.getElementById('ratingScore');
  var reviewsEl = document.getElementById('ratingReviews');
  var yoursEl = document.getElementById('ratingYours');

  // Update 10-star display from a score on 10-scale
  function renderStars(score10) {
    stars.forEach(function(s) {
      var v = parseInt(s.dataset.star);
      s.className = 'rating-star';
      if (v <= Math.floor(score10)) {
        s.classList.add('fill');
      } else if (v <= Math.ceil(score10) && (score10 - Math.floor(score10)) >= 0.3) {
        s.classList.add('half');
      } else {
        s.classList.add('empty');
      }
    });
  }

  // Hover: highlight stars up to hovered position
  stars.forEach(function(star) {
    star.addEventListener('mouseenter', function() {
      var v = parseInt(this.dataset.star);
      stars.forEach(function(s) {
        var sv = parseInt(s.dataset.star);
        s.className = 'rating-star ' + (sv <= v ? 'fill' : 'empty');
      });
    });
    star.addEventListener('mouseleave', function() {
      // Restore from current score
      var cur = parseFloat(scoreEl.textContent) || 0;
      renderStars(cur);
    });
    star.addEventListener('click', function() {
      var starNum = parseInt(this.dataset.star);

      var fd = new FormData();
      fd.append('item_id', mangaId);
      fd.append('score', starNum);
      fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

      fetch('/api/rating', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (d.error) { alert(d.error); return; }
          var avg = parseFloat(d.avg).toFixed(2);
          scoreEl.textContent = avg;
          reviewsEl.textContent = 'by ' + d.votes.toLocaleString() + ' reviews';
          yoursEl.textContent = 'Your rating: ' + d.your_score + '/10';
          renderStars(parseFloat(avg));
        })
        .catch(function() { alert('Failed to submit rating.'); });
    });
  });
})();
</script>

<?= $this->endSection() ?>
