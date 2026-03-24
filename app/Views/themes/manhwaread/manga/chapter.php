<?= $this->extend('themes/manhwaread/layouts/reader') ?>

<?= $this->section('content') ?>

<?php
$cdnChapter = rtrim(env('CDN_CHAPTER_URL', ''), '/');
$mangaUrl = site_url('manga/' . $manga['slug']);
$totalPages = count($pages);
?>

<!-- Reader Content -->
<main class="reader-content" id="readerContent">
  <div class="reader-images" id="readerImages">
    <?php if (!empty($pages)): ?>
      <?php foreach ($pages as $index => $page):
        $pageUrl = !empty($page['image_local'])
            ? ($cdnChapter . '/' . $chapter['id'] . '/' . ltrim($page['image_local'], '/'))
            : trim($page['image']);
        $isFirst = $index < 3;
      ?>
      <img
        src="<?= $isFirst ? esc($pageUrl) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" ?>"
        <?= $isFirst ? '' : 'data-src="' . esc($pageUrl) . '"' ?>
        alt="<?= esc($manga['name']) ?> <?= esc($chapTitle ?? '') ?> - Page <?= (int)($page['slug'] ?? $index + 1) ?>"
        class="<?= $isFirst ? '' : 'lazy' ?>"
        loading="<?= $isFirst ? 'eager' : 'lazy' ?>"
        decoding="async"
      >
      <?php endforeach; ?>
    <?php else: ?>
      <p style="color: var(--text-muted); text-align: center; padding: 40px;"><?= lang('ComixxManga.no_pages') ?></p>
    <?php endif; ?>
  </div>

  <!-- End of chapter -->
  <div class="reader-end">
    <p class="reader-end-text"><?= lang('ComixxManga.end_of_chapter') ?> <?= esc($chapTitle ?? '') ?></p>
    <div class="reader-end-nav">
      <?php if (!empty($prevChapter)): ?>
      <a href="<?= site_url('manga/' . $manga['slug'] . '/' . $prevChapter['slug']) ?>" class="reader-end-btn">
        <i class="fas fa-chevron-left"></i> <?= lang('ComixxManga.prev_chapter') ?>
      </a>
      <?php else: ?>
      <span class="reader-end-btn disabled"><i class="fas fa-chevron-left"></i> <?= lang('ComixxManga.prev_chapter') ?></span>
      <?php endif; ?>

      <?php if (!empty($nextChapter)): ?>
      <a href="<?= site_url('manga/' . $manga['slug'] . '/' . $nextChapter['slug']) ?>" class="reader-end-btn primary">
        <?= lang('ComixxManga.next_chapter') ?> <i class="fas fa-chevron-right"></i>
      </a>
      <?php else: ?>
      <span class="reader-end-btn disabled"><?= lang('ComixxManga.next_chapter') ?> <i class="fas fa-chevron-right"></i></span>
      <?php endif; ?>
    </div>

    <!-- Follow button -->
    <button class="reader-follow-btn<?= !empty($isBookmarked) ? ' active' : '' ?>" id="readerFollowBtn" data-manga-id="<?= esc($manga['id']) ?>">
      <i class="<?= !empty($isBookmarked) ? 'fas' : 'far' ?> fa-bookmark"></i>
      <span id="readerFollowLabel"><?= !empty($isBookmarked) ? lang('Comixx.following') : lang('Comixx.follow') ?></span>
    </button>

    <!-- Like/Dislike -->
    <div class="reader-end-likes">
      <button class="reader-like-btn" id="chLikeBtn" data-type="like"><span class="like-emoji">&#x1F60D;</span> <span class="ch-like-count">0</span></button>
      <button class="reader-like-btn" id="chDislikeBtn" data-type="dislike"><span class="like-emoji">&#x1F624;</span> <span class="ch-dislike-count">0</span></button>
    </div>
  </div>

  <!-- Report Button -->
  <div style="text-align:center;padding:12px 0">
    <button class="rpt-btn rpt-open-btn"><i class="fas fa-flag"></i> <?= lang('Comixx.report_error') ?></button>
  </div>

  <!-- Mobile Comments Section -->
  <div class="reader-mobile-comments" id="mobileComments">
    <!-- Comment Type Tabs -->
    <div class="reader-comments-tabs" style="display:flex;gap:0;margin-bottom:12px;border-bottom:2px solid var(--border-color,#333);">
      <button class="rc-type-tab active" data-type="chapter" style="flex:1;padding:10px;font-size:14px;font-weight:600;background:none;border:none;color:var(--text-muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .2s;"><?= lang('ComixxManga.chapter_comments') ?> <span class="rc-count" id="mc-ch-count"></span></button>
      <button class="rc-type-tab" data-type="all" style="flex:1;padding:10px;font-size:14px;font-weight:600;background:none;border:none;color:var(--text-muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .2s;"><?= lang('ComixxManga.all_comments') ?> <span class="rc-count" id="mc-all-count"></span></button>
    </div>
    <style>
    .rc-type-tab.active { color: var(--accent, #a855f7) !important; border-bottom-color: var(--accent, #a855f7) !important; }
    </style>

    <!-- Sort buttons -->
    <div class="reader-comments-header">
      <div class="tab-buttons">
        <button class="tab-btn active" data-sort="newest"><?= lang('ComixxManga.new') ?></button>
        <button class="tab-btn" data-sort="oldest"><?= lang('ComixxManga.older') ?></button>
        <button class="tab-btn" data-sort="top"><?= lang('ComixxManga.top') ?></button>
      </div>
    </div>

    <!-- Chapter Comments List -->
    <div id="mc-ch-panel">
      <div class="reader-comments-list" id="mc-ch-list">
        <p class="rc-loading">...</p>
      </div>
      <div class="rc-pagination" id="mc-ch-pg"></div>
    </div>

    <!-- All Comments List -->
    <div id="mc-all-panel" style="display:none;">
      <div class="reader-comments-list" id="mc-all-list">
        <p class="rc-loading">...</p>
      </div>
      <div class="rc-pagination" id="mc-all-pg"></div>
    </div>

    <?php if (!empty($currentUser)): ?>
    <div class="rc-captcha-wrap" id="mc-captcha" style="display:none">
      <span class="rc-captcha-label"><?= lang('ComixxManga.captcha_label') ?></span>
      <span class="rc-captcha-q" id="mc-captcha-q"></span>
      <span>= ?</span>
      <input type="number" class="rc-captcha-ans" id="mc-captcha-ans" min="0" max="99" placeholder="0">
    </div>
    <div class="reader-comment-input-wrap">
      <input type="text" class="reader-comment-input" id="mc-input" maxlength="1000" placeholder="<?= esc(lang('ComixxManga.write_comment')) ?>">
      <button class="reader-comment-send" id="mc-send"><i class="fas fa-paper-plane"></i></button>
    </div>
    <?php else: ?>
    <div class="rc-login-notice">
      <a href="/login"><?= lang('ComixxManga.login_to_comment') ?></a>
    </div>
    <?php endif; ?>
  </div>
</main>

<!-- Report Modal -->
<div class="rpt-overlay" id="rptOverlay">
  <div class="rpt-box">
    <button class="rpt-close" id="rptClose">&times;</button>
    <h3><?= lang('ComixxManga.report_chapter_error') ?></h3>
    <p class="rpt-sub"><?= lang('ComixxManga.reason') ?></p>
    <div class="rpt-reasons">
      <label class="rpt-reason-label"><input type="radio" name="rpt-reason" value="wrong_images"> <?= lang('ComixxManga.wrong_images') ?></label>
      <label class="rpt-reason-label"><input type="radio" name="rpt-reason" value="missing_pages"> <?= lang('ComixxManga.missing_pages') ?></label>
      <label class="rpt-reason-label"><input type="radio" name="rpt-reason" value="low_quality"> <?= lang('ComixxManga.low_quality') ?></label>
      <label class="rpt-reason-label"><input type="radio" name="rpt-reason" value="cant_load"> <?= lang('ComixxManga.cant_load') ?></label>
      <label class="rpt-reason-label"><input type="radio" name="rpt-reason" value="wrong_order"> <?= lang('ComixxManga.wrong_order') ?></label>
      <label class="rpt-reason-label"><input type="radio" name="rpt-reason" value="other"> <?= lang('ComixxManga.other') ?></label>
    </div>
    <textarea class="rpt-note" rows="3" placeholder="<?= lang('ComixxManga.additional_details') ?>" id="rptNote"></textarea>
    <div class="rpt-actions">
      <button class="rpt-cancel-btn" id="rptCancelBtn"><?= lang('Comixx.cancel') ?></button>
      <button class="rpt-submit-btn" id="rptSubmitBtn"><?= lang('ComixxManga.submit_report') ?></button>
    </div>
    <p class="rpt-msg" id="rptMsg"></p>
  </div>
</div>

<style>
/* Reader-specific inline styles */
.reader-end { text-align: center; padding: 30px 16px; background: var(--bg-secondary, #1a1a2e); max-width: 60%; margin: 0 auto; width: 100%; }
.reader-end-text { font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px; }
.reader-end-nav { display: flex; gap: 12px; justify-content: center; margin-bottom: 16px; flex-wrap: wrap; }
.reader-end-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; font-size: 14px; font-weight: 600; color: var(--text-primary); background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 8px; text-decoration: none; transition: all 0.2s; }
.reader-end-btn:hover { background: var(--bg-card-hover); border-color: var(--accent, #a855f7); }
.reader-end-btn.primary { background: var(--accent, #a855f7); color: #fff; border-color: var(--accent, #a855f7); }
.reader-end-btn.primary:hover { opacity: 0.9; }
.reader-end-btn.disabled { opacity: 0.4; pointer-events: none; }
.reader-follow-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: var(--text-secondary); background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 8px; cursor: pointer; transition: all 0.2s; margin-bottom: 12px; }
.reader-follow-btn:hover { border-color: var(--accent, #a855f7); color: var(--accent, #a855f7); }
.reader-follow-btn.active { background: rgba(168,85,247,.1); border-color: var(--accent, #a855f7); color: var(--accent, #a855f7); }
.reader-end-likes { display: flex; gap: 8px; justify-content: center; margin-bottom: 12px; }
.reader-like-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; font-size: 13px; font-weight: 600; color: var(--text-secondary); background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 8px; cursor: pointer; transition: all 0.2s; }
.reader-like-btn:hover { background: var(--bg-card-hover); }
.reader-like-btn.active { border-color: var(--accent, #a855f7); color: var(--accent, #a855f7); }
.reader-like-btn .like-emoji { font-size: 18px; }
.reader-mobile-comments { padding: 16px 16px 80px; background: var(--bg-secondary, #1a1a2e); max-width: 60%; margin: 0 auto; width: 100%; }
@media (max-width: 768px) {
  .reader-end, .reader-mobile-comments { max-width: 100%; }
}
.reader-comments-header { display: flex; align-items: center; justify-content: flex-end; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
.reader-comments-title { font-size: 15px; font-weight: 700; color: var(--text-primary); }
.reader-comments-list { min-height: 40px; }
.reader-comment-input-wrap { display: flex; gap: 8px; align-items: center; padding: 8px 10px; background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 8px; margin-top: 8px; }
.reader-comment-input { flex: 1; background: transparent; border: none; outline: none; font-size: 16px; color: var(--text-primary); }
.reader-comment-input::placeholder { color: var(--text-muted); }
.reader-comment-send { font-size: 16px; color: var(--accent, #a855f7); cursor: pointer; background: none; border: none; }
.rpt-btn { background: none; border: 1px solid rgba(239,68,68,.25); border-radius: 6px; color: #ef4444; font-size: 12px; padding: 5px 12px; cursor: pointer; opacity: .7; transition: all .2s; display: inline-flex; align-items: center; gap: 5px; }
.rpt-btn:hover { opacity: 1; border-color: rgba(239,68,68,.6); }
.rpt-overlay { display: none; position: fixed; inset: 0; z-index: 300; background: rgba(0,0,0,.7); align-items: center; justify-content: center; padding: 16px; }
.rpt-overlay.open { display: flex; }
.rpt-box { background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 14px; max-width: 420px; width: 100%; padding: 20px; position: relative; }
.rpt-box h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px; }
.rpt-box .rpt-sub { font-size: 12px; color: var(--text-muted); margin: 0 0 14px; }
.rpt-close { position: absolute; top: 12px; right: 14px; background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer; }
.rpt-reasons { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
.rpt-reason-label { display: flex; align-items: center; gap: 8px; padding: 7px 10px; border-radius: 8px; border: 1px solid var(--border-color, #333); cursor: pointer; font-size: 13px; color: var(--text-secondary); transition: border-color .15s; }
.rpt-reason-label:hover { border-color: var(--accent, #a855f7); }
.rpt-reason-label input { accent-color: var(--accent, #a855f7); }
.rpt-note { width: 100%; background: var(--bg-secondary); border: 1px solid var(--border-color, #333); color: var(--text-primary); border-radius: 8px; padding: 8px 10px; font-size: 13px; resize: none; outline: none; box-sizing: border-box; margin-bottom: 14px; }
.rpt-actions { display: flex; gap: 8px; justify-content: flex-end; }
.rpt-cancel-btn { background: none; border: 1px solid var(--border-color, #333); color: var(--text-muted); border-radius: 8px; padding: 7px 16px; font-size: 13px; cursor: pointer; }
.rpt-submit-btn { background: #ef4444; border: none; color: #fff; border-radius: 8px; padding: 7px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }
.rpt-submit-btn:hover { background: #dc2626; }
.rpt-msg { display: none; text-align: center; font-size: 12px; margin-top: 10px; }
.rc-loading { text-align: center; padding: 16px 0; color: var(--text-muted); font-size: 12px; }
.rc-count { font-size: 11px; color: var(--text-muted); font-weight: 400; }
.rc-captcha-wrap { display: flex; align-items: center; gap: 6px; padding: 6px 10px; background: rgba(255,255,255,.04); border: 1px solid var(--border-color, #333); border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
.rc-captcha-label { font-size: 11px; color: var(--text-muted); }
.rc-captcha-ans { width: 48px; background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 4px; padding: 3px 6px; font-size: 12px; color: var(--text-primary); outline: none; text-align: center; }
.rc-login-notice { text-align: center; padding: 10px 0; font-size: 12px; color: var(--text-muted); }
.rc-login-notice a { color: var(--accent, #a855f7); font-weight: 600; }
.rc-pagination { display: flex; justify-content: center; align-items: center; gap: 3px; flex-wrap: wrap; margin-top: 8px; }
.rc-pagination button { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 28px; padding: 0 4px; border-radius: 4px; font-size: 11px; border: 1px solid var(--border-color, #333); background: transparent; color: var(--text-muted); cursor: pointer; }
.rc-pagination .pg-active { background: var(--accent, #a855f7); border-color: var(--accent, #a855f7); color: #000; font-weight: 700; }
.rc-pagination button[disabled] { opacity: .4; cursor: default; pointer-events: none; }
.rc-item { padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
.rc-item:last-child { border-bottom: none; }
.rc-item-body { display: flex; gap: 8px; }
.rc-avatar { width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; }
.rc-content { flex: 1; min-width: 0; }
.rc-bubble { background: var(--bg-card); border-radius: 8px; padding: 6px 10px; }
.rc-user { font-weight: 700; font-size: 13px; color: var(--text-primary); }
.rc-text { font-size: 13px; color: var(--text-primary, #fff); font-weight: 500; margin-top: 2px; white-space: pre-wrap; word-break: break-word; line-height: 1.5; }
.rc-actions { display: flex; align-items: center; gap: 10px; font-size: 13px; margin-top: 5px; padding: 0 2px; }
.rc-react { display: inline-flex; align-items: center; gap: 4px; background: none; border: none; font-size: 13px; color: var(--text-muted); cursor: pointer; padding: 2px 4px; border-radius: 3px; }
.rc-react:hover { color: var(--accent, #a855f7); }
.rc-react.liked { color: var(--accent, #a855f7); }
.rc-reply-btn { background: none; border: none; font-size: 13px; color: var(--text-muted); cursor: pointer; }
.rc-reply-btn:hover { color: var(--accent, #a855f7); }
.rc-replies { margin-left: 12px; padding-left: 10px; border-left: 1px solid rgba(168,85,247,.2); margin-top: 4px; }
.rc-toggle-replies { background: none; border: none; font-size: 11px; color: var(--accent, #a855f7); cursor: pointer; padding: 0; }
.rc-toggle-replies:hover { text-decoration: underline; }
.rc-reply-form textarea { width: 100%; background: var(--bg-card); border: 1px solid var(--border-color, #333); border-radius: 6px; color: var(--text-primary); font-size: 11px; padding: 6px 8px; resize: none; outline: none; }
.rc-reply-form-actions { display: flex; justify-content: flex-end; gap: 6px; margin-top: 4px; }
.rc-reply-cancel { background: none; border: none; font-size: 11px; color: var(--text-muted); cursor: pointer; }
.rc-reply-submit { background: var(--accent, #a855f7); color: #000; border: none; border-radius: 4px; padding: 3px 10px; font-size: 11px; font-weight: 600; cursor: pointer; }
</style>

<script>
(function() {
  'use strict';

  var mangaSlug = <?= json_encode($manga['slug']) ?>;
  var chapterSlug = <?= json_encode($chapter['slug']) ?>;
  var mangaId = <?= (int)$manga['id'] ?>;
  var chapterId = <?= (int)$chapter['id'] ?>;
  var CURRENT_UID = <?= !empty($currentUser) ? (int)$currentUser['id'] : 0 ?>;
  var BG_COLORS = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];

  var __lang = {
    reply: <?= json_encode(lang('Comixx.reply')) ?>,
    cancel: <?= json_encode(lang('Comixx.cancel')) ?>,
    follow: <?= json_encode(lang('Comixx.follow')) ?>,
    following: <?= json_encode(lang('Comixx.following')) ?>,
    no_comments: <?= json_encode(lang('ComixxManga.no_comments')) ?>,
    now: <?= json_encode(lang('ComixxTime.now')) ?>,
    js_min: <?= json_encode(lang('ComixxTime.js_min')) ?>,
    js_hour: <?= json_encode(lang('ComixxTime.js_hour')) ?>,
    js_day: <?= json_encode(lang('ComixxTime.js_day')) ?>,
    view_replies: <?= json_encode(lang('ComixxManga.view_replies')) ?>,
    hide_replies: <?= json_encode(lang('ComixxManga.hide_replies')) ?>,
    show_more_replies: <?= json_encode(lang('ComixxManga.show_more_replies')) ?>,
    select_reason: <?= json_encode(lang('ComixxManga.select_reason')) ?>,
    report_thanks: <?= json_encode(lang('ComixxManga.report_thanks')) ?>,
    sending: <?= json_encode(lang('ComixxManga.sending')) ?>
  };

  // ===== Lazy Loading =====
  (function() {
    var lazyImages = document.querySelectorAll('img.lazy');
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            var img = entry.target;
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.classList.remove('lazy');
              observer.unobserve(img);
            }
          }
        });
      }, { rootMargin: '500px' });
      lazyImages.forEach(function(img) { observer.observe(img); });
    } else {
      lazyImages.forEach(function(img) {
        if (img.dataset.src) { img.src = img.dataset.src; img.classList.remove('lazy'); }
      });
    }
  })();

  // ===== Track View =====
  fetch('/api/view', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'manga_id=' + mangaId + '&chapter_id=' + chapterId
  });

  // ===== Follow/Bookmark =====
  var followBtn = document.getElementById('readerFollowBtn');
  if (followBtn) {
    followBtn.addEventListener('click', function() {
      fetch('/api/bookmark/toggle', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'manga_id=' + mangaId,
        credentials: 'same-origin'
      })
      .then(function(r) {
        if (r.status === 401) { window.location.href = '/login'; return null; }
        return r.json();
      })
      .then(function(d) {
        if (!d) return;
        var icon = followBtn.querySelector('i');
        var label = document.getElementById('readerFollowLabel');
        if (d.bookmarked) {
          followBtn.classList.add('active');
          if (icon) { icon.classList.remove('far'); icon.classList.add('fas'); }
          if (label) label.textContent = __lang.following;
        } else {
          followBtn.classList.remove('active');
          if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
          if (label) label.textContent = __lang.follow;
        }
      });
    });
  }

  // ===== Like/Dislike Chapter =====
  (function(){
    function updateAllUI(d) {
      document.querySelectorAll('.ch-like-count, #chLikeCount').forEach(function(el) { el.textContent = d.likes; });
      document.querySelectorAll('.ch-dislike-count, #chDislikeCount').forEach(function(el) { el.textContent = d.dislikes; });
      document.querySelectorAll('[data-type="like"]').forEach(function(btn) {
        btn.classList.toggle('active', d.my_reaction === 'like');
      });
      document.querySelectorAll('[data-type="dislike"]').forEach(function(btn) {
        btn.classList.toggle('active', d.my_reaction === 'dislike');
      });
    }

    // Fetch initial state
    fetch('/api/content-like?content_type=chapter&content_id=' + chapterId)
      .then(function(r) { return r.json(); }).then(updateAllUI).catch(function(){});

    function toggle(type) {
      fetch('/api/content-like', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'content_type=chapter&content_id=' + chapterId + '&type=' + type,
        credentials: 'same-origin'
      })
      .then(function(r) {
        if (r.status === 401) { window.location.href = '/login'; return null; }
        return r.json();
      })
      .then(function(d) { if (d) updateAllUI(d); });
    }

    document.querySelectorAll('[data-type="like"]').forEach(function(btn) {
      btn.addEventListener('click', function() { toggle('like'); });
    });
    document.querySelectorAll('[data-type="dislike"]').forEach(function(btn) {
      btn.addEventListener('click', function() { toggle('dislike'); });
    });
  })();

  // ===== Report Modal =====
  (function(){
    var overlay = document.getElementById('rptOverlay');
    var closeBtn = document.getElementById('rptClose');
    var cancelBtn = document.getElementById('rptCancelBtn');
    var submitBtn = document.getElementById('rptSubmitBtn');
    var msgEl = document.getElementById('rptMsg');

    document.querySelectorAll('.rpt-open-btn').forEach(function(btn) {
      btn.addEventListener('click', function() { overlay.classList.add('open'); });
    });

    function closeReport() { overlay.classList.remove('open'); }
    if (closeBtn) closeBtn.addEventListener('click', closeReport);
    if (cancelBtn) cancelBtn.addEventListener('click', closeReport);
    if (overlay) overlay.addEventListener('click', function(e) {
      if (e.target === overlay) closeReport();
    });

    if (submitBtn) {
      submitBtn.addEventListener('click', function() {
        var checked = document.querySelector('input[name="rpt-reason"]:checked');
        if (!checked) {
          msgEl.style.display = 'block';
          msgEl.style.color = '#ef4444';
          msgEl.textContent = __lang.select_reason;
          return;
        }
        submitBtn.disabled = true;
        submitBtn.textContent = __lang.sending;
        var note = document.getElementById('rptNote');
        fetch('/api/report', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
          body: 'manga_id=' + mangaId + '&chapter_id=' + chapterId + '&reason=' + checked.value + '&note=' + encodeURIComponent((note ? note.value : '')),
          credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function() {
          msgEl.style.display = 'block';
          msgEl.style.color = '#10b981';
          msgEl.textContent = __lang.report_thanks;
          setTimeout(closeReport, 1500);
        })
        .catch(function() {
          msgEl.style.display = 'block';
          msgEl.style.color = '#ef4444';
          msgEl.textContent = 'Error';
        })
        .finally(function() {
          submitBtn.disabled = false;
          submitBtn.textContent = __lang.sending;
        });
      });
    }
  })();

  // ===== Comment System (Mobile) =====
  (function(){
    var currentSort = 'newest';
    var currentPage = 1;
    var currentType = 'chapter'; // 'chapter' or 'all'
    var allPage = 1;
    var allSort = 'newest';

    function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

    function timeAgo(str) {
      var d = new Date(str.replace(' ','T'));
      var diff = Math.floor((Date.now() - d.getTime()) / 1000);
      if (diff < 60) return __lang.now;
      if (diff < 3600) return __lang.js_min.replace('{n}', Math.floor(diff/60));
      if (diff < 86400) return __lang.js_hour.replace('{n}', Math.floor(diff/3600));
      if (diff < 604800) return __lang.js_day.replace('{n}', Math.floor(diff/86400));
      return Math.floor(diff/604800) + 'w';
    }

    function avatar(name, uid, sz) {
      sz = sz || 28;
      var ch = ((name||'?')[0]).toUpperCase();
      var bg = BG_COLORS[parseInt(uid||0) % 6];
      return '<div class="rc-avatar" style="width:'+sz+'px;height:'+sz+'px;font-size:'+(sz*0.38)+'px;background:'+bg+'">'+ch+'</div>';
    }

    var rcLikeIcon = '<i class="fas fa-thumbs-up" style="font-size:10px"></i>';

    function renderComment(c) {
      var name = c.user_name || c.user_username || '?';
      var isLiked = c.my_reaction === 'like';
      var likeBtn = CURRENT_UID > 0
        ? '<button class="rc-react'+(isLiked?' liked':'')+'" data-id="'+c.id+'" data-type="like">'+rcLikeIcon+' <span class="lc">'+c.likes_count+'</span></button>'
        : '<span class="rc-react" style="cursor:default">'+rcLikeIcon+' '+c.likes_count+'</span>';
      var replyBtn = CURRENT_UID > 0
        ? '<button class="rc-reply-btn" data-id="'+c.id+'" data-name="'+escHtml(name)+'">'+__lang.reply+'</button>'
        : '';
      var toggleReplies = c.reply_count > 0
        ? '<button class="rc-toggle-replies" data-id="'+c.id+'" data-count="'+c.reply_count+'">'+__lang.view_replies.replace('{n}', ' ' + c.reply_count + ' ')+'</button>'
        : '';

      var chapLabel = c.chapter_name ? '<span class="rc-chap-label" style="font-size:11px;color:var(--accent,#a855f7);font-weight:600;margin-left:6px;">'+escHtml(c.chapter_name)+'</span>' : '';

      return '<div class="rc-item" data-id="'+c.id+'"><div class="rc-item-body">'
        + avatar(name, c.user_id)
        + '<div class="rc-content"><div class="rc-bubble"><span class="rc-user">'+escHtml(name)+chapLabel+'</span><div class="rc-text">'+escHtml(c.comment)+'</div></div>'
        + '<div class="rc-actions">'+likeBtn+'<small style="color:var(--text-muted)">'+timeAgo(c.created_at)+'</small>'+replyBtn+'</div>'
        + toggleReplies
        + '<div class="rc-replies" id="rc-replies-'+c.id+'"></div>'
        + '</div></div></div>';
    }

    function loadComments(page, sort) {
      var listEl = document.getElementById('mc-ch-list');
      var pgEl = document.getElementById('mc-ch-pg');
      if (!listEl) return;
      listEl.innerHTML = '<p class="rc-loading">...</p>';

      fetch('/api/comments/chapter/' + chapterId + '?order=' + sort + '&page=' + page)
        .then(function(r) { return r.json(); })
        .then(function(d) {
          var countEl = document.getElementById('mc-ch-count');
          if (countEl) countEl.textContent = '(' + (d.total || 0) + ')';

          if (!d.comments || !d.comments.length) {
            listEl.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:16px 0;font-size:12px">' + __lang.no_comments + '</p>';
            if (pgEl) pgEl.innerHTML = '';
            return;
          }

          listEl.innerHTML = d.comments.map(renderComment).join('');

          // Pagination
          var totalPages = d.last_page || 1;
          if (!pgEl || totalPages <= 1) { if (pgEl) pgEl.innerHTML = ''; return; }
          var html = '';
          html += '<button' + (page <= 1 ? ' disabled' : '') + ' data-page="' + (page-1) + '"><i class="fas fa-chevron-left"></i></button>';
          for (var i = 1; i <= totalPages; i++) {
            html += '<button class="' + (i === page ? 'pg-active' : '') + '" data-page="'+i+'">'+i+'</button>';
          }
          html += '<button' + (page >= totalPages ? ' disabled' : '') + ' data-page="' + (page+1) + '"><i class="fas fa-chevron-right"></i></button>';
          pgEl.innerHTML = html;
          pgEl.querySelectorAll('button:not([disabled])').forEach(function(b) {
            b.addEventListener('click', function() {
              currentPage = parseInt(this.dataset.page);
              loadComments(currentPage, currentSort);
            });
          });
        })
        .catch(function() { listEl.innerHTML = ''; });
    }

    // All Comments loader
    function loadAllComments(page, sort) {
      var listEl = document.getElementById('mc-all-list');
      var pgEl = document.getElementById('mc-all-pg');
      if (!listEl) return;
      listEl.style.minHeight = listEl.offsetHeight + 'px';
      listEl.style.opacity = '0.5';

      fetch('/api/comments/manga/' + mangaId + '/all?order=' + sort + '&page=' + page)
        .then(function(r) { return r.json(); })
        .then(function(d) {
          var countEl = document.getElementById('mc-all-count');
          if (countEl) countEl.textContent = '(' + (d.total || 0) + ')';

          if (!d.comments || !d.comments.length) {
            listEl.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:16px 0;font-size:13px">' + __lang.no_comments + '</p>';
            listEl.style.minHeight = ''; listEl.style.opacity = '1';
            if (pgEl) pgEl.innerHTML = '';
            return;
          }
          listEl.innerHTML = d.comments.map(renderComment).join('');
          listEl.style.minHeight = ''; listEl.style.opacity = '1';

          var totalPages = d.last_page || 1;
          if (!pgEl || totalPages <= 1) { if (pgEl) pgEl.innerHTML = ''; return; }
          var html = '';
          html += '<button' + (page <= 1 ? ' disabled' : '') + ' data-page="' + (page-1) + '"><i class="fas fa-chevron-left"></i></button>';
          for (var i = 1; i <= totalPages; i++) {
            html += '<button class="' + (i === page ? 'pg-active' : '') + '" data-page="'+i+'">'+i+'</button>';
          }
          html += '<button' + (page >= totalPages ? ' disabled' : '') + ' data-page="' + (page+1) + '"><i class="fas fa-chevron-right"></i></button>';
          pgEl.innerHTML = html;
          pgEl.querySelectorAll('button:not([disabled])').forEach(function(b) {
            b.addEventListener('click', function() {
              allPage = parseInt(this.dataset.page);
              loadAllComments(allPage, allSort);
            });
          });
        })
        .catch(function() { listEl.innerHTML = ''; listEl.style.minHeight = ''; listEl.style.opacity = '1'; });
    }

    // Type tabs (Chapter / All)
    var chPanel = document.getElementById('mc-ch-panel');
    var allPanel = document.getElementById('mc-all-panel');
    var allLoaded = false;
    document.querySelectorAll('.rc-type-tab').forEach(function(tab) {
      tab.addEventListener('click', function() {
        document.querySelectorAll('.rc-type-tab').forEach(function(t) { t.classList.remove('active'); });
        tab.classList.add('active');
        currentType = tab.dataset.type;
        if (currentType === 'chapter') {
          chPanel.style.display = '';
          allPanel.style.display = 'none';
        } else {
          chPanel.style.display = 'none';
          allPanel.style.display = '';
          if (!allLoaded) { allLoaded = true; loadAllComments(1, allSort); }
        }
      });
    });

    // Sort tabs
    var commentSection = document.getElementById('mobileComments');
    if (commentSection) {
      commentSection.querySelectorAll('[data-sort]').forEach(function(btn) {
        btn.addEventListener('click', function() {
          commentSection.querySelectorAll('[data-sort]').forEach(function(b) { b.classList.remove('active'); });
          btn.classList.add('active');
          if (currentType === 'chapter') {
            currentSort = btn.dataset.sort;
            currentPage = 1;
            loadComments(currentPage, currentSort);
          } else {
            allSort = btn.dataset.sort;
            allPage = 1;
            loadAllComments(allPage, allSort);
          }
        });
      });
    }

    // Comment input
    var mcInput = document.getElementById('mc-input');
    var mcSend = document.getElementById('mc-send');
    var mcCaptcha = document.getElementById('mc-captcha');
    var mcCaptchaQ = document.getElementById('mc-captcha-q');
    var mcCaptchaAns = document.getElementById('mc-captcha-ans');
    var captchaA, captchaB;

    var captchaShown = false;

    function newCaptcha() {
      captchaA = Math.floor(Math.random() * 10) + 1;
      captchaB = Math.floor(Math.random() * 10) + 1;
      if (mcCaptchaQ) mcCaptchaQ.textContent = captchaA + ' + ' + captchaB;
      if (mcCaptchaAns) mcCaptchaAns.value = '';
    }

    if (mcInput && mcSend) {
      newCaptcha();
      mcSend.addEventListener('click', function() {
        if (!mcInput.value.trim()) return;
        if (captchaShown) {
          if (mcCaptchaAns && parseInt(mcCaptchaAns.value) !== captchaA + captchaB) {
            mcCaptchaAns.style.borderColor = 'red';
            mcCaptchaAns.focus();
            return;
          }
        }
        fetch('/api/comments', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
          body: 'manga_id=' + mangaId + '&chapter_id=' + chapterId + '&comment=' + encodeURIComponent(mcInput.value.trim()) + (captchaShown ? '&captcha_passed=1' : ''),
          credentials: 'same-origin'
        })
        .then(function(r) {
          if (r.status === 401) { window.location.href = '/login'; return null; }
          return r.json().then(function(d) { d._status = r.status; return d; });
        })
        .then(function(d) {
          if (!d) return;
          if (d.need_captcha) {
            captchaShown = true;
            if (mcCaptcha) { mcCaptcha.style.display = ''; newCaptcha(); if (mcCaptchaAns) mcCaptchaAns.focus(); }
            return;
          }
          if (d.error) return;
          mcInput.value = '';
          captchaShown = false;
          if (mcCaptcha) mcCaptcha.style.display = 'none';
          newCaptcha();
          loadComments(1, currentSort);
        });
      });

      mcInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); mcSend.click(); }
      });
    }

    // Delegated events: like, reply, toggle replies
    function bindListEvents(listContainer) {
      if (!listContainer) return;
      listContainer.addEventListener('click', function(e) {
        // Like
        var reactBtn = e.target.closest('.rc-react[data-id]');
        if (reactBtn) {
          fetch('/api/comments/' + reactBtn.dataset.id + '/react', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
            body: 'type=like',
            credentials: 'same-origin'
          })
          .then(function(r) {
            if (r.status === 401) { window.location.href = '/login'; return null; }
            return r.json();
          })
          .then(function(d) {
            if (!d) return;
            var lc = reactBtn.querySelector('.lc');
            if (lc) lc.textContent = d.likes_count;
            reactBtn.classList.toggle('liked', d.my_reaction === 'like');
          });
          return;
        }

        // Toggle replies
        var toggleBtn = e.target.closest('.rc-toggle-replies');
        if (toggleBtn) {
          var cid = toggleBtn.dataset.id;
          var container = document.getElementById('rc-replies-' + cid);
          if (toggleBtn.dataset.open === '1') {
            if (container) container.innerHTML = '';
            toggleBtn.dataset.open = '0';
            toggleBtn.textContent = __lang.view_replies.replace('{n}', ' ' + toggleBtn.dataset.count + ' ');
            return;
          }
          toggleBtn.disabled = true;
          toggleBtn.textContent = '...';
          fetch('/api/comments/' + cid + '/replies')
            .then(function(r) { return r.json(); })
            .then(function(d) {
              if (d.replies && d.replies.length && container) {
                container.innerHTML = d.replies.map(renderComment).join('');
              }
              toggleBtn.dataset.open = '1';
              toggleBtn.textContent = __lang.hide_replies;
              toggleBtn.disabled = false;
            });
          return;
        }

        // Reply button
        var replyBtn = e.target.closest('.rc-reply-btn');
        if (replyBtn && CURRENT_UID > 0) {
          var repliesWrap = replyBtn.closest('[id^="rc-replies-"]');
          var parentId = repliesWrap ? repliesWrap.id.replace('rc-replies-', '') : replyBtn.dataset.id;
          var existing = document.getElementById('rc-rf-' + parentId);
          if (existing) { existing.remove(); return; }
          var replyHtml = '<div class="rc-reply-form" id="rc-rf-' + parentId + '">'
            + '<textarea rows="2" maxlength="1000">@' + escHtml(replyBtn.dataset.name) + ' </textarea>'
            + '<div class="rc-reply-form-actions">'
            + '<button class="rc-reply-cancel" data-parent="'+parentId+'">'+__lang.cancel+'</button>'
            + '<button class="rc-reply-submit" data-parent="'+parentId+'">'+__lang.reply+'</button>'
            + '</div></div>';
          replyBtn.closest('.rc-content').insertAdjacentHTML('beforeend', replyHtml);
        }
      });
    }
    bindListEvents(document.getElementById('mc-ch-list'));
    bindListEvents(document.getElementById('mc-all-list'));

    // Delegated: cancel/submit reply
    document.addEventListener('click', function(e) {
      var cancelBtn = e.target.closest('.rc-reply-cancel');
      if (cancelBtn) {
        var wrap = document.getElementById('rc-rf-' + cancelBtn.dataset.parent);
        if (wrap) wrap.remove();
        return;
      }
      var submitBtn2 = e.target.closest('.rc-reply-submit');
      if (submitBtn2) {
        var pid = submitBtn2.dataset.parent;
        var wrap2 = document.getElementById('rc-rf-' + pid);
        var ta = wrap2 ? wrap2.querySelector('textarea') : null;
        if (!ta || !ta.value.trim()) return;
        var rCa = Math.floor(Math.random()*10)+1, rCb = Math.floor(Math.random()*10)+1;
        var captchaEl = wrap2.querySelector('.rc-reply-captcha');
        if (!captchaEl) {
          var captchaHtml = '<div class="rc-reply-captcha" data-a="'+rCa+'" data-b="'+rCb+'" style="display:none;margin-top:6px;padding:8px;background:var(--bg-card);border-radius:6px;border:1px solid var(--border-color,#333);font-size:13px;">'
            + '<span style="font-weight:600;">'+rCa+' + '+rCb+' = ? </span>'
            + '<input type="number" class="rc-reply-captcha-ans" min="0" max="99" style="width:50px;padding:4px 8px;border-radius:4px;border:1px solid var(--border-color,#333);background:var(--bg-card);color:var(--text-primary);font-size:13px;text-align:center;">'
            + '</div>';
          ta.insertAdjacentHTML('afterend', captchaHtml);
          captchaEl = wrap2.querySelector('.rc-reply-captcha');
        }
        if (captchaEl && captchaEl.style.display !== 'none') {
          var ca2 = parseInt(captchaEl.dataset.a), cb2 = parseInt(captchaEl.dataset.b);
          var ans2 = captchaEl.querySelector('.rc-reply-captcha-ans');
          if (parseInt(ans2.value) !== ca2 + cb2) {
            ans2.style.borderColor = 'red';
            ans2.focus();
            return;
          }
        }
        fetch('/api/comments', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
          body: 'manga_id=' + mangaId + '&chapter_id=' + chapterId + '&comment=' + encodeURIComponent(ta.value.trim()) + '&parent_comment=' + pid + (captchaEl && captchaEl.style.display !== 'none' ? '&captcha_passed=1' : ''),
          credentials: 'same-origin'
        })
        .then(function(r) {
          return r.json().then(function(d) { d._status = r.status; return d; });
        })
        .then(function(d) {
          if (d.need_captcha && captchaEl) {
            captchaEl.style.display = '';
            var ans3 = captchaEl.querySelector('.rc-reply-captcha-ans');
            if (ans3) ans3.focus();
            return;
          }
          if (wrap2) wrap2.remove();
          loadComments(currentPage, currentSort);
        });
      }
    });

    // Initial load
    loadComments(1, 'newest');
  })();
})();
</script>

<?= $this->endSection() ?>
