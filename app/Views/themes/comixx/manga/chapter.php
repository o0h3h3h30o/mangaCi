<?= $this->extend('themes/comixx/layouts/reader') ?>

<?= $this->section('head_extra') ?>
<?php
$cdnChapter = rtrim(env('CDN_CHAPTER_URL', ''), '/');
if (!empty($pages) && isset($pages[0])):
    $firstPage = $pages[0];
    $firstPageUrl = !empty($firstPage['image_local'])
        ? ($cdnChapter . '/' . $chapter['id'] . '/' . ltrim($firstPage['image_local'], '/'))
        : trim($firstPage['image']);
?>
<link rel="preload" as="image" href="<?= esc($firstPageUrl) ?>">
<?php endif; ?>
<style>
    /* ===== Reader Page ===== */

    /* Reset body for reader */
    body.reader-body {
      background: #000;
      overflow-x: hidden;
    }

    /* Reader Top Bar */
    .reader-topbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 40px;
      background: var(--bg-header);
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px;
      z-index: 200;
    }

    .reader-topbar-left {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
      flex: 1;
    }

    .reader-back-btn {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-primary);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .reader-back-btn i {
      font-size: 14px;
      color: var(--text-secondary);
      flex-shrink: 0;
    }

    .reader-back-btn span {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .reader-topbar-center {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
      white-space: nowrap;
    }

    .reader-topbar-right {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
    }

    .reader-topbar-btn {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      font-size: 15px;
      color: var(--text-secondary);
      transition: background 0.2s;
    }

    .reader-topbar-btn:hover {
      background: var(--bg-card);
    }

    /* Reader Layout */
    .reader-wrapper {
      display: flex;
      margin-top: 40px;
      min-height: calc(100vh - 40px);
    }

    .reader-main {
      flex: 1;
      display: flex;
      justify-content: center;
      background: #000;
      min-width: 0;
    }

    .reader-pages {
      width: 100%;
      max-width: 810px;
      display: flex;
      flex-direction: column;
    }

    .reader-pages img {
      width: 100%;
      height: auto;
      display: block;
      object-fit: contain;
    }

    /* Single Page Mode */
    .reader-pages.mode-single {
      max-width: 810px;
      align-items: center;
      min-height: calc(100vh - 40px);
      justify-content: center;
    }

    .reader-pages.mode-single img {
      display: none;
    }

    .reader-pages.mode-single img.active-page {
      display: block;
    }

    /* Double Page Mode */
    .reader-pages.mode-double {
      max-width: 1620px;
      flex-direction: row;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-start;
      min-height: calc(100vh - 40px);
    }

    .reader-pages.mode-double img {
      display: none;
    }

    .reader-pages.mode-double img.active-page {
      display: block;
      width: 50%;
    }

    .reader-pages.mode-double.dir-rtl img.active-page {
      direction: rtl;
    }

    /* Long Strip Mode */
    .reader-pages.mode-longstrip {
      max-width: 810px;
      gap: var(--page-gap, 0px);
    }

    .reader-pages.mode-longstrip img {
      display: block;
    }

    /* Page Navigation Controls */
    .reader-page-nav {
      position: fixed;
      bottom: 24px;
      left: calc((100vw - var(--sidebar-width, 525px)) / 2);
      transform: translateX(-50%);
      transition: left 0.3s;
      display: flex;
      align-items: center;
      gap: 12px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 8px 16px;
      z-index: 150;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .reader-page-nav.hidden {
      display: none;
    }

    .reader-page-nav-btn {
      width: 36px;
      height: 36px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      color: var(--text-secondary);
      transition: all 0.2s;
    }

    .reader-page-nav-btn:hover {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .reader-page-nav-btn:disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }

    .reader-page-info {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-primary);
      white-space: nowrap;
      min-width: 60px;
      text-align: center;
    }

    .reader-page-slider {
      -webkit-appearance: none;
      appearance: none;
      width: 120px;
      height: 4px;
      background: var(--border-color);
      border-radius: 2px;
      outline: none;
      cursor: pointer;
    }

    .reader-page-slider::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 14px;
      height: 14px;
      background: #67e8f9;
      border-radius: 50%;
      cursor: pointer;
    }

    .reader-page-slider::-moz-range-thumb {
      width: 14px;
      height: 14px;
      background: #67e8f9;
      border-radius: 50%;
      cursor: pointer;
      border: none;
    }

    /* Reader Sidebar */
    .reader-sidebar {
      width: 525px;
      flex-shrink: 0;
      background: var(--bg-secondary);
      border-left: 1px solid var(--border-color);
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 16px;
      overflow-y: auto;
      max-height: calc(100vh - 40px);
      position: sticky;
      top: 40px;
      transition: margin-right 0.3s, opacity 0.3s;
    }

    .reader-sidebar.collapsed {
      display: none;
    }

    .reader-sidebar-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-primary);
      line-height: 1.4;
    }

    /* Chapter Navigation */
    .reader-chapter-nav {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .reader-chapter-arrow {
      width: 32px;
      height: 32px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      color: var(--text-secondary);
      flex-shrink: 0;
      transition: background 0.2s;
    }

    .reader-chapter-arrow:hover {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .reader-chapter-arrow.disabled {
      opacity: 0.3;
      pointer-events: none;
    }

    .reader-chapter-select {
      width: 100%;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 6px 12px;
      font-family: var(--font);
      font-size: 13px;
      font-weight: 600;
      color: var(--text-primary);
      appearance: none;
      cursor: pointer;
      text-align: center;
      outline: none;
    }

    .reader-chapter-dropdown {
      position: relative;
      flex: 1;
    }

    .reader-chapter-dropdown-btn {
      width: 100%;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 6px 12px;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .reader-chapter-dropdown-btn i {
      font-size: 10px;
      color: var(--text-muted);
    }

    .reader-chapter-dropdown-btn:hover {
      background: var(--bg-card-hover);
    }

    /* Follow + Like Row */
    .reader-action-row {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .reader-like-row {
      display: flex;
      gap: 6px;
    }

    .reader-like-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      padding: 6px 10px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
      transition: all 0.2s;
    }

    .reader-like-btn:hover {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .reader-like-btn.active {
      border-color: var(--accent-blue);
      color: var(--accent-blue);
      background: rgba(52, 211, 153, 0.08);
    }

    .reader-like-btn .like-emoji {
      font-size: 16px;
      display: inline-block;
      transition: transform 0.2s;
    }

    .reader-like-btn:hover .like-emoji {
      transform: scale(1.3);
    }

    .reader-like-btn.active .like-emoji {
      transform: scale(1.2);
    }

    /* Follow Button */
    .reader-bookmark-btn {
      width: 100%;
      padding: 8px 12px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
      transition: all 0.2s;
    }

    .reader-bookmark-btn:hover {
      background: var(--bg-card-hover);
      color: var(--accent-blue);
      border-color: var(--accent-blue);
    }

    .reader-bookmark-btn.active {
      background: rgba(52, 211, 153, 0.1);
      border-color: var(--accent-blue);
      color: var(--accent-blue);
    }

    /* Group Selector */
    .reader-group-select {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .reader-group-label {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .reader-group-dropdown {
      flex: 1;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .reader-group-dropdown i {
      font-size: 10px;
      color: var(--text-muted);
    }

    .reader-group-dropdown:hover {
      background: var(--bg-card-hover);
    }

    /* Sidebar Comments */
    .reader-comments {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 8px;
      min-height: 0;
    }

    .reader-comments-header {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .reader-comments-tabs {
      display: flex;
      gap: 0;
      border-bottom: 1px solid var(--border-color);
    }

    .reader-comments-tab {
      flex: 1;
      padding: 8px 0;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      text-align: center;
      border-bottom: 2px solid transparent;
      cursor: pointer;
      transition: all 0.2s;
    }

    .reader-comments-tab.active {
      color: var(--accent-blue);
      border-bottom-color: var(--accent-blue);
    }

    .reader-comments-tab:hover:not(.active) {
      color: var(--text-secondary);
    }

    .reader-comments .tab-buttons {
      justify-content: flex-end;
    }

    .reader-comments .tab-btn {
      padding: 3px 8px;
      font-size: 11px;
    }

    .reader-comments-title {
      font-size: 13px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .reader-comments-count {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 600;
    }

    .reader-comments-tab-content {
      display: none;
    }

    .reader-comments-tab-content.active {
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
      overflow-y: auto;
      scrollbar-width: thin;
    }

    .reader-comments-list {
      flex: 1;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 6px;
      scrollbar-width: thin;
    }

    .reader-comment {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 10px;
    }

    .reader-comment-top {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
    }

    .reader-comment-avatar {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: var(--bg-card-hover);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      color: var(--text-muted);
      flex-shrink: 0;
    }

    .reader-comment-meta {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
    }

    .reader-comment-user {
      font-size: 12px;
      font-weight: 600;
      color: var(--accent-green);
    }

    .reader-comment-time {
      font-size: 10px;
      color: var(--text-muted);
      margin-left: auto;
    }

    .reader-comment-text {
      font-size: 12px;
      color: var(--text-secondary);
      line-height: 1.5;
    }

    .reader-comment-actions {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 6px;
    }

    .reader-comment-action {
      font-size: 11px;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 4px;
      cursor: pointer;
      transition: color 0.2s;
    }

    .reader-comment-action:hover {
      color: var(--text-secondary);
    }

    .reader-comment-input-wrap {
      display: flex;
      gap: 8px;
      align-items: center;
      padding: 8px 10px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 6px;
    }

    .reader-comment-input-wrap input {
      flex: 1;
      background: transparent;
      border: none;
      outline: none;
      font-family: var(--font);
      font-size: 12px;
      color: var(--text-primary);
    }

    .reader-comment-input-wrap input::placeholder {
      color: var(--text-muted);
    }

    .reader-comment-send {
      font-size: 14px;
      color: var(--accent-blue);
      cursor: pointer;
      transition: opacity 0.2s;
    }

    .reader-comment-send:hover {
      opacity: 0.7;
    }

    .reader-comments-pagination {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      padding: 8px 0 4px;
    }

    .reader-comments-page-btn {
      min-width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 600;
      color: var(--text-muted);
      background: transparent;
      border: 1px solid var(--border-color);
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .reader-comments-page-btn.active {
      color: var(--accent-blue);
      border-color: var(--accent-blue);
      background: rgba(52, 211, 153, 0.1);
    }

    .reader-comments-page-btn:hover:not(.active) {
      color: var(--text-secondary);
      border-color: var(--text-muted);
    }

    .reader-comments-page-btn.nav-arrow {
      font-size: 10px;
    }

    /* ===== Settings Modal ===== */
    .settings-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.7);
      z-index: 300;
      display: none;
      align-items: center;
      justify-content: center;
    }

    .settings-overlay.open {
      display: flex;
    }

    .settings-modal {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      width: 420px;
      max-width: 92vw;
      max-height: 85vh;
      overflow-y: auto;
      position: relative;
    }

    .settings-modal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px 0;
    }

    .settings-mode-icons {
      display: flex;
      gap: 16px;
    }

    .settings-mode-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      font-size: 20px;
      color: var(--text-muted);
      padding-bottom: 8px;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
    }

    .settings-mode-btn.active {
      color: #67e8f9;
      border-bottom-color: #67e8f9;
    }

    .settings-mode-btn:hover:not(.active) {
      color: var(--text-secondary);
    }

    .settings-close-btn {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: var(--text-secondary);
      border-radius: 6px;
      transition: background 0.2s;
    }

    .settings-close-btn:hover {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    /* Settings Tabs */
    .settings-tabs {
      display: flex;
      border-bottom: 1px solid var(--border-color);
      padding: 0 20px;
      margin-top: 12px;
    }

    .settings-tab {
      padding: 10px 16px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text-muted);
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
    }

    .settings-tab.active {
      color: #67e8f9;
      border-bottom-color: #67e8f9;
    }

    .settings-tab:hover:not(.active) {
      color: var(--text-secondary);
    }

    /* Settings Tab Content */
    .settings-tab-content {
      display: none;
      padding: 20px;
    }

    .settings-tab-content.active {
      display: block;
    }

    .settings-group {
      margin-bottom: 20px;
    }

    .settings-group:last-child {
      margin-bottom: 0;
    }

    .settings-group-label {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text-muted);
      text-transform: uppercase;
      margin-bottom: 10px;
    }

    .settings-btn-row {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
    }

    .settings-option-btn {
      padding: 7px 14px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.5px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      color: var(--text-secondary);
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .settings-option-btn.active {
      background: #67e8f9;
      border-color: #67e8f9;
      color: #0d1512;
    }

    .settings-option-btn:hover:not(.active) {
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .settings-option-btn i {
      font-size: 12px;
    }

    /* Small buttons for progress bar */
    .settings-btn-row-small .settings-option-btn {
      padding: 5px 10px;
      font-size: 11px;
    }

    /* Settings placeholder for IMAGE and SHORTCUTS tabs */
    .settings-placeholder {
      font-size: 13px;
      color: var(--text-muted);
      text-align: center;
      padding: 24px 0;
    }

    /* Mobile Top Like Row (visible only on mobile) */
    .reader-mobile-top-likes {
      display: none;
      justify-content: center;
      gap: 6px;
      padding: 10px 16px;
      margin-top: 40px;
      background: var(--bg-secondary);
      border-bottom: 1px solid var(--border-color);
    }

    .reader-mobile-top-likes .reader-like-btn,
    #mobileLikeRow .reader-like-btn {
      flex: none;
      padding: 6px 20px;
    }

    #mobileLikeRow {
      justify-content: center;
    }

    /* Report button */
    .rpt-btn {
      background: none;
      border: 1px solid rgba(239,68,68,.25);
      border-radius: 6px;
      color: #ef4444;
      font-size: 12px;
      padding: 5px 12px;
      cursor: pointer;
      opacity: .7;
      transition: all .2s;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .rpt-btn:hover { opacity: 1; border-color: rgba(239,68,68,.6); }

    /* Report modal */
    .rpt-overlay {
      display: none;
      position: fixed;
      inset: 0;
      z-index: 300;
      background: rgba(0,0,0,.7);
      align-items: center;
      justify-content: center;
      padding: 16px;
    }
    .rpt-overlay.open { display: flex; }
    .rpt-box {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 14px;
      max-width: 420px;
      width: 100%;
      padding: 20px;
      position: relative;
    }
    .rpt-box h3 {
      font-size: 15px;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0 0 4px;
    }
    .rpt-box .rpt-sub {
      font-size: 12px;
      color: var(--text-muted);
      margin: 0 0 14px;
    }
    .rpt-close {
      position: absolute;
      top: 12px;
      right: 14px;
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 20px;
      cursor: pointer;
      line-height: 1;
    }
    .rpt-reasons {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 14px;
    }
    .rpt-reason-label {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 7px 10px;
      border-radius: 8px;
      border: 1px solid var(--border-color);
      cursor: pointer;
      font-size: 13px;
      color: var(--text-secondary);
      transition: border-color .15s;
    }
    .rpt-reason-label:hover,
    .rpt-reason-label.selected { border-color: var(--accent-blue); }
    .rpt-reason-label input { accent-color: var(--accent-blue); }
    .rpt-note {
      width: 100%;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      border-radius: 8px;
      padding: 8px 10px;
      font-size: 13px;
      resize: none;
      outline: none;
      box-sizing: border-box;
      margin-bottom: 14px;
    }
    .rpt-actions {
      display: flex;
      gap: 8px;
      justify-content: flex-end;
    }
    .rpt-cancel-btn {
      background: none;
      border: 1px solid var(--border-color);
      color: var(--text-muted);
      border-radius: 8px;
      padding: 7px 16px;
      font-size: 13px;
      cursor: pointer;
    }
    .rpt-submit-btn {
      background: #ef4444;
      border: none;
      color: #fff;
      border-radius: 8px;
      padding: 7px 18px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: background .2s;
    }
    .rpt-submit-btn:hover { background: #dc2626; }
    .rpt-msg {
      display: none;
      text-align: center;
      font-size: 12px;
      margin-top: 10px;
    }

    /* Mobile Bottom Section */
    .reader-mobile-bottom {
      display: none;
      flex-direction: column;
      gap: 16px;
      padding: 16px;
      background: var(--bg-secondary);
      border-top: 1px solid var(--border-color);
    }

    .reader-mobile-chapter-nav {
      display: flex;
      gap: 10px;
    }

    .reader-mobile-chapter-nav a {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-primary);
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      text-decoration: none;
      transition: all 0.2s;
    }

    .reader-mobile-chapter-nav a:hover {
      background: var(--bg-card-hover);
      border-color: var(--accent-blue);
    }

    .reader-mobile-chapter-nav a.disabled {
      opacity: 0.4;
      pointer-events: none;
    }

    /* ===== Responsive ===== */

    @media (max-width: 900px) {
      .reader-sidebar {
        display: none;
      }

      .reader-mobile-bottom {
        display: flex;
      }

      .reader-mobile-top-likes {
        display: flex;
      }

      .reader-wrapper {
        margin-top: 0;
      }

      .reader-pages {
        max-width: 100%;
      }

      .reader-pages.mode-single,
      .reader-pages.mode-double {
        min-height: auto;
        justify-content: flex-start;
      }

      .reader-pages.mode-single img.active-page {
        max-height: calc(100vh - 120px);
        width: auto;
        max-width: 100%;
        margin: 0 auto;
      }

      .reader-page-nav {
        left: 50%;
      }
    }

    @media (max-width: 768px) {
      .reader-topbar-center {
        font-size: 11px;
      }

      .reader-back-btn span {
        max-width: 180px;
        font-size: 0.8rem;
      }
    }

    /* Comment system extra styles */
    .rc-loading { text-align: center; padding: 16px 0; color: var(--text-muted); font-size: 12px; }
    .rc-count { font-size: 10px; color: var(--text-muted); font-weight: 400; }
    .rc-captcha-wrap {
      display: flex; align-items: center; gap: 6px; padding: 6px 10px;
      background: rgba(255,255,255,.04); border: 1px solid var(--border-color);
      border-radius: 6px; font-size: 12px; font-weight: 600; color: var(--text-primary);
      margin-bottom: 6px;
    }
    .rc-captcha-label { font-size: 11px; color: var(--text-muted); }
    .rc-captcha-ans {
      width: 48px; background: var(--bg-card); border: 1px solid var(--border-color);
      border-radius: 4px; padding: 3px 6px; font-size: 12px; font-family: var(--font);
      color: var(--text-primary); outline: none; text-align: center;
    }
    .rc-captcha-ans:focus { border-color: var(--accent-blue); }
    .rc-login-notice { text-align: center; padding: 10px 0; font-size: 12px; color: var(--text-muted); }
    .rc-login-notice a { color: var(--accent-blue); font-weight: 600; }
    .rc-login-notice a:hover { text-decoration: underline; }

    .rc-item { padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.04); }
    .rc-item:last-child { border-bottom: none; }
    .rc-item-body { display: flex; gap: 8px; }
    .rc-avatar {
      width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #fff;
    }
    .rc-content { flex: 1; min-width: 0; }
    .rc-bubble { background: var(--bg-card); border-radius: 8px; padding: 6px 10px; }
    .rc-user { font-weight: 700; font-size: 11px; color: var(--text-primary); }
    .rc-text { font-size: 11px; color: var(--text-secondary); margin-top: 2px; white-space: pre-wrap; word-break: break-word; line-height: 1.4; }
    .rc-actions { display: flex; align-items: center; gap: 8px; font-size: 11px; margin-top: 3px; padding: 0 2px; }
    .rc-react {
      display: inline-flex; align-items: center; gap: 2px;
      background: none; border: none; font-size: 11px; font-family: var(--font);
      color: var(--text-muted); cursor: pointer; padding: 1px 3px; border-radius: 3px;
    }
    .rc-react:hover { color: var(--accent-blue); }
    .rc-react.liked { color: var(--accent-blue); }
    .rc-reply-btn {
      background: none; border: none; font-size: 11px; font-family: var(--font);
      color: var(--text-muted); cursor: pointer;
    }
    .rc-reply-btn:hover { color: var(--accent-blue); }
    .rc-replies { margin-left: 12px; padding-left: 10px; border-left: 1px solid rgba(52,211,153,.2); margin-top: 4px; }
    .rc-replies .rc-item { padding: 4px 0; }
    .rc-replies .rc-avatar { width: 22px; height: 22px; font-size: 9px; }
    .rc-reply-form { margin-top: 6px; }
    .rc-reply-form textarea {
      width: 100%; background: var(--bg-card); border: 1px solid var(--border-color);
      border-radius: 6px; color: var(--text-primary); font-family: var(--font);
      font-size: 11px; padding: 6px 8px; resize: none; outline: none;
    }
    .rc-reply-form textarea:focus { border-color: var(--accent-blue); }
    .rc-reply-form-actions { display: flex; justify-content: flex-end; gap: 6px; margin-top: 4px; }
    .rc-reply-cancel { background: none; border: none; font-size: 11px; font-family: var(--font); color: var(--text-muted); cursor: pointer; }
    .rc-reply-submit { background: var(--accent-blue); color: var(--bg-primary); border: none; border-radius: 4px; padding: 3px 10px; font-size: 11px; font-family: var(--font); font-weight: 600; cursor: pointer; }
    .rc-toggle-replies { background: none; border: none; font-size: 11px; font-family: var(--font); color: var(--accent-blue); cursor: pointer; padding: 0; }
    .rc-toggle-replies:hover { text-decoration: underline; }
    .rc-show-more { background: none; border: none; font-size: 11px; font-family: var(--font); color: var(--accent-blue); cursor: pointer; padding: 2px 0; }
    .rc-show-more:hover { text-decoration: underline; }

    .rc-pagination {
      display: flex; justify-content: center; align-items: center;
      gap: 3px; flex-wrap: wrap; margin-top: 8px;
    }
    .rc-pagination button {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 24px; height: 24px; padding: 0 4px;
      border-radius: 4px; font-size: 11px; font-family: var(--font);
      border: 1px solid var(--border-color); background: transparent;
      color: var(--text-muted); cursor: pointer;
    }
    .rc-pagination button:hover:not([disabled]):not(.pg-active) { background: var(--bg-card-hover); color: var(--text-primary); }
    .rc-pagination .pg-active { background: var(--accent-blue); border-color: var(--accent-blue); color: var(--bg-primary); font-weight: 700; pointer-events: none; }
    .rc-pagination button[disabled] { opacity: .4; cursor: default; pointer-events: none; }

    /* Back to top button */
    .reader-back-top {
      position: fixed;
      bottom: 24px;
      left: 24px;
      width: 40px;
      height: 40px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 50%;
      display: none;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: var(--text-secondary);
      z-index: 150;
      cursor: pointer;
      transition: all 0.2s;
      box-shadow: 0 2px 8px rgba(0,0,0,.3);
    }
    .reader-back-top:hover {
      background: var(--accent-blue);
      color: #fff;
      border-color: var(--accent-blue);
    }
    .reader-back-top.visible { display: flex; }

    @media (max-width: 480px) {
      .reader-topbar {
        padding: 0 10px;
      }

      .reader-back-btn {
        font-size: 12px;
      }

      .reader-back-btn span {
        max-width: 140px;
      }

      .reader-topbar-center {
        display: none;
      }

      .settings-modal {
        border-radius: 8px;
      }

      .settings-tab {
        padding: 8px 10px;
        font-size: 11px;
      }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<script>document.body.classList.add('reader-body');</script>
<?php
$cdnChapter = rtrim(env('CDN_CHAPTER_URL', ''), '/');
$mangaUrl = site_url('manga/' . $manga['slug']);
$totalPages = count($pages);
$lastChapter = !empty($chapters) ? end($chapters) : null;
?>

<!-- Top Navigation Bar -->
<div class="reader-topbar">
  <div class="reader-topbar-left">
    <a href="<?= esc($mangaUrl) ?>" class="reader-back-btn">
      <i class="fas fa-chevron-left"></i>
      <span><?= esc($manga['name']) ?></span>
    </a>
  </div>
  <div class="reader-topbar-center">Ch. <?= esc($chapter['number']) ?><?php if (!empty($lastChapter)): ?>/<?= esc($lastChapter['number']) ?><?php endif; ?></div>
  <div class="reader-topbar-right">
    <button class="reader-topbar-btn" id="sidebarToggleBtn" title="<?= esc(lang('ComixxManga.toggle_sidebar')) ?>">
      <i class="far fa-comment"></i>
    </button>
    <button class="reader-topbar-btn" id="settingsBtn" title="<?= esc(lang('ComixxManga.settings')) ?>">
      <i class="fas fa-sliders-h"></i>
    </button>
  </div>
</div>

<!-- Mobile Top Like/Dislike (before images) -->
<div class="reader-mobile-top-likes">
  <button class="reader-like-btn mobile-like-btn" data-type="like"><span class="like-emoji">😍</span> <span class="ml-like-count"><?= (int)($likes ?? 0) ?></span></button>
  <button class="reader-like-btn mobile-like-btn" data-type="dislike"><span class="like-emoji">😤</span> <span class="ml-dislike-count"><?= (int)($dislikes ?? 0) ?></span></button>
</div>

<!-- Reader Layout -->
<div class="reader-wrapper">
  <!-- Main Reading Area -->
  <div class="reader-main">
    <div class="reader-pages" id="readerPages">
      <?php if (!empty($pages)): ?>
        <?php foreach ($pages as $index => $page):
          $pageUrl = !empty($page['image_local'])
              ? ($cdnChapter . '/' . $chapter['id'] . '/' . ltrim($page['image_local'], '/'))
              : trim($page['image']);
          $isFirst = $index < 3;
        ?>
        <div class="img-wrap<?= $isFirst ? '' : ' loading' ?>">
        <img
          src="<?= $isFirst ? esc($pageUrl) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E" ?>"
          <?= $isFirst ? '' : 'data-src="' . esc($pageUrl) . '"' ?>
          alt="<?= esc($manga['name']) ?> <?= esc($chapTitle ?? '') ?> - Page <?= (int)$page['slug'] ?>"
          class="<?= $isFirst ? '' : 'lazy' ?>"
          fetchpriority="<?= $index === 0 ? 'high' : 'auto' ?>"
          decoding="async"
          onload="var w=this.parentNode;if(w)w.classList.remove('loading')"
          onerror="var w=this.parentNode;if(w)w.classList.remove('loading')"
        >
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color: var(--text-muted); text-align: center; padding: 40px;"><?= lang('ComixxManga.no_pages') ?></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right Sidebar -->
  <div class="reader-sidebar" id="readerSidebar">
    <div class="reader-sidebar-title"><?= esc($manga['name']) ?></div>

    <!-- Chapter Navigation -->
    <div class="reader-chapter-nav">
      <?php if (!empty($prevChapter)): ?>
      <a href="<?= site_url('manga/' . $manga['slug'] . '/' . $prevChapter['slug']) ?>" class="reader-chapter-arrow" title="<?= esc(lang('ComixxManga.prev_chapter')) ?>">
        <i class="fas fa-chevron-left"></i>
      </a>
      <?php else: ?>
      <button class="reader-chapter-arrow disabled" title="<?= esc(lang('ComixxManga.prev_chapter')) ?>" disabled>
        <i class="fas fa-chevron-left"></i>
      </button>
      <?php endif; ?>

      <div class="reader-chapter-dropdown">
        <select class="reader-chapter-select" id="chapterSelect">
          <?php foreach ($chapters as $ch): ?>
          <option value="<?= esc($ch['slug']) ?>" <?= ($ch['slug'] == $chapter['slug']) ? 'selected' : '' ?>>
            Ch <?= esc($ch['number']) ?><?= !empty($ch['title']) ? ' - ' . esc($ch['title']) : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if (!empty($nextChapter)): ?>
      <a href="<?= site_url('manga/' . $manga['slug'] . '/' . $nextChapter['slug']) ?>" class="reader-chapter-arrow" title="<?= esc(lang('ComixxManga.next_chapter')) ?>">
        <i class="fas fa-chevron-right"></i>
      </a>
      <?php else: ?>
      <button class="reader-chapter-arrow disabled" title="<?= esc(lang('ComixxManga.next_chapter')) ?>" disabled>
        <i class="fas fa-chevron-right"></i>
      </button>
      <?php endif; ?>
    </div>

    <!-- Follow + Like/Dislike -->
    <div class="reader-action-row">
      <button class="reader-bookmark-btn<?= !empty($isBookmarked) ? ' active' : '' ?>" id="readerFollowBtn" data-manga-id="<?= esc($manga['id']) ?>" title="<?= esc(lang('Comixx.follow')) ?>">
        <i class="<?= !empty($isBookmarked) ? 'fas' : 'far' ?> fa-bookmark"></i>
        <span id="readerFollowLabel"><?= !empty($isBookmarked) ? lang('Comixx.following') : lang('Comixx.follow') ?></span>
      </button>
      <div class="reader-like-row">
        <button class="reader-like-btn<?= ($myReaction ?? '') === 'like' ? ' active' : '' ?>" id="chLikeBtn" data-type="like"><span class="like-emoji">😍</span> <span id="chLikeCount"><?= (int)($likes ?? 0) ?></span></button>
        <button class="reader-like-btn<?= ($myReaction ?? '') === 'dislike' ? ' active' : '' ?>" id="chDislikeBtn" data-type="dislike"><span class="like-emoji">😤</span> <span id="chDislikeCount"><?= (int)($dislikes ?? 0) ?></span></button>
      </div>
      <button class="rpt-btn rpt-open-btn"><i class="fas fa-flag"></i> <?= lang('Comixx.report') ?></button>
    </div>

    <!-- Comments Section -->
    <div class="reader-comments" id="sidebarComments">
      <div class="reader-comments-header">
        <div class="reader-comments-tabs">
          <div class="reader-comments-tab active" data-tab="chapter"><?= lang('ComixxManga.chapter_comments') ?> <span class="rc-count" id="sc-ch-count"></span></div>
          <div class="reader-comments-tab" data-tab="all"><?= lang('ComixxManga.all_comments') ?> <span class="rc-count" id="sc-all-count"></span></div>
        </div>
        <div class="tab-buttons">
          <button class="tab-btn active" data-sort="newest"><?= lang('ComixxManga.new') ?></button>
          <button class="tab-btn" data-sort="oldest"><?= lang('ComixxManga.older') ?></button>
          <button class="tab-btn" data-sort="top"><?= lang('ComixxManga.top') ?></button>
        </div>
      </div>

      <div class="reader-comments-tab-content active" data-type="chapter">
        <div class="reader-comments-list" id="sc-ch-list"><p class="rc-loading">...</p></div>
        <div class="rc-pagination" id="sc-ch-pg"></div>
      </div>

      <div class="reader-comments-tab-content" data-type="all">
        <div class="reader-comments-list" id="sc-all-list"><p class="rc-loading">...</p></div>
        <div class="rc-pagination" id="sc-all-pg"></div>
      </div>

      <?php if (!empty($currentUser)): ?>
      <div class="rc-captcha-wrap" id="sc-captcha" style="display:none">
        <span class="rc-captcha-label"><?= lang('ComixxManga.captcha_label') ?></span>
        <span class="rc-captcha-q" id="sc-captcha-q"></span>
        <span>= ?</span>
        <input type="number" class="rc-captcha-ans" id="sc-captcha-ans" min="0" max="99" placeholder="0">
      </div>
      <div class="reader-comment-input-wrap">
        <input type="text" class="reader-comment-input" id="sc-input" maxlength="1000" placeholder="<?= esc(lang('ComixxManga.write_comment')) ?>">
        <button class="reader-comment-send" id="sc-send"><i class="fas fa-paper-plane"></i></button>
      </div>
      <?php else: ?>
      <div class="rc-login-notice">
        <?= lang('ComixxManga.login_to_comment') ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Mobile Bottom: Chapter Nav + Comments -->
<div class="reader-mobile-bottom">
  <div class="reader-mobile-chapter-nav">
    <?php if (!empty($prevChapter)): ?>
    <a href="<?= site_url('manga/' . $manga['slug'] . '/' . $prevChapter['slug']) ?>"><i class="fas fa-chevron-left"></i> <?= lang('ComixxManga.prev_chapter') ?></a>
    <?php else: ?>
    <a href="#" class="disabled"><i class="fas fa-chevron-left"></i> <?= lang('ComixxManga.prev_chapter') ?></a>
    <?php endif; ?>
    <?php if (!empty($nextChapter)): ?>
    <a href="<?= site_url('manga/' . $manga['slug'] . '/' . $nextChapter['slug']) ?>"><?= lang('ComixxManga.next_chapter') ?> <i class="fas fa-chevron-right"></i></a>
    <?php else: ?>
    <a href="#" class="disabled"><?= lang('ComixxManga.next_chapter') ?> <i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
  </div>

  <div class="reader-like-row" id="mobileLikeRow">
    <button class="reader-like-btn mobile-like-btn" data-type="like"><span class="like-emoji">😍</span> <span class="ml-like-count"><?= (int)($likes ?? 0) ?></span></button>
    <button class="reader-like-btn mobile-like-btn" data-type="dislike"><span class="like-emoji">😤</span> <span class="ml-dislike-count"><?= (int)($dislikes ?? 0) ?></span></button>
  </div>
  <div style="text-align:center"><button class="rpt-btn rpt-open-btn"><i class="fas fa-flag"></i> <?= lang('Comixx.report_error') ?></button></div>

  <div class="reader-comments" id="mobileComments">
    <div class="reader-comments-header">
      <div class="reader-comments-tabs">
        <div class="reader-comments-tab active" data-tab="chapter"><?= lang('ComixxManga.chapter_comments') ?> <span class="rc-count" id="mc-ch-count"></span></div>
        <div class="reader-comments-tab" data-tab="all"><?= lang('ComixxManga.all_comments') ?> <span class="rc-count" id="mc-all-count"></span></div>
      </div>
      <div class="tab-buttons">
        <button class="tab-btn active" data-sort="newest"><?= lang('ComixxManga.new') ?></button>
        <button class="tab-btn" data-sort="oldest"><?= lang('ComixxManga.older') ?></button>
        <button class="tab-btn" data-sort="top"><?= lang('ComixxManga.top') ?></button>
      </div>
    </div>

    <div class="reader-comments-tab-content active" data-type="chapter">
      <div class="reader-comments-list" id="mc-ch-list"><p class="rc-loading">...</p></div>
      <div class="rc-pagination" id="mc-ch-pg"></div>
    </div>

    <div class="reader-comments-tab-content" data-type="all">
      <div class="reader-comments-list" id="mc-all-list"><p class="rc-loading">...</p></div>
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
      <?= lang('ComixxManga.login_to_comment') ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Page Navigation Bar (for single/double page modes) -->
<div class="reader-page-nav hidden" id="pageNav">
  <button class="reader-page-nav-btn" id="prevPageBtn" title="<?= esc(lang('ComixxManga.prev_page')) ?>">
    <i class="fas fa-chevron-left"></i>
  </button>
  <input type="range" class="reader-page-slider" id="pageSlider" min="1" max="<?= $totalPages ?>" value="1">
  <span class="reader-page-info" id="pageInfo">1 / <?= $totalPages ?></span>
  <button class="reader-page-nav-btn" id="nextPageBtn" title="<?= esc(lang('ComixxManga.next_page')) ?>">
    <i class="fas fa-chevron-right"></i>
  </button>
</div>

<!-- Back to Top Button -->
<button class="reader-back-top" id="backTopBtn" title="<?= esc(lang('ComixxManga.back_to_top')) ?>">
  <i class="fas fa-chevron-up"></i>
</button>

<!-- Settings Modal -->
<div class="settings-overlay" id="settingsOverlay">
  <div class="settings-modal">
    <!-- Modal Header -->
    <div class="settings-modal-header">
      <div class="settings-mode-icons">
        <button class="settings-mode-btn active" data-mode="palette" title="<?= esc(lang('ComixxManga.color_mode')) ?>">
          <i class="fas fa-palette"></i>
        </button>
        <button class="settings-mode-btn" data-mode="brightness" title="<?= esc(lang('ComixxManga.brightness')) ?>">
          <i class="fas fa-sun"></i>
        </button>
        <button class="settings-mode-btn" data-mode="dark" title="<?= esc(lang('ComixxManga.dark_mode')) ?>">
          <i class="fas fa-moon"></i>
        </button>
      </div>
      <button class="settings-close-btn" id="settingsCloseBtn" title="<?= esc(lang('ComixxManga.close')) ?>">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Settings Tabs -->
    <div class="settings-tabs">
      <button class="settings-tab active" data-tab="layout"><?= lang('ComixxManga.settings_design') ?></button>
      <button class="settings-tab" data-tab="image"><?= lang('ComixxManga.settings_image') ?></button>
      <button class="settings-tab" data-tab="shortcuts"><?= lang('ComixxManga.settings_shortcuts') ?></button>
    </div>

    <!-- LAYOUT Tab -->
    <div class="settings-tab-content active" id="tab-layout">
      <div class="settings-group">
        <div class="settings-group-label"><?= lang('ComixxManga.display_style') ?></div>
        <div class="settings-btn-row" data-group="page-display">
          <button class="settings-option-btn" data-value="single"><?= lang('ComixxManga.single_page') ?></button>
          <button class="settings-option-btn" data-value="double"><?= lang('ComixxManga.double_page') ?></button>
          <button class="settings-option-btn active" data-value="longstrip"><?= lang('ComixxManga.long_strip') ?></button>
        </div>
      </div>

      <div class="settings-group">
        <div class="settings-group-label"><?= lang('ComixxManga.reading_direction') ?></div>
        <div class="settings-btn-row" data-group="reading-dir">
          <button class="settings-option-btn active" data-value="ltr">
            <i class="fas fa-arrow-right"></i> <?= lang('ComixxManga.ltr') ?>
          </button>
          <button class="settings-option-btn" data-value="rtl">
            <i class="fas fa-arrow-left"></i> <?= lang('ComixxManga.rtl') ?>
          </button>
        </div>
      </div>

      <div class="settings-group">
        <div class="settings-group-label"><?= lang('ComixxManga.progress_bar') ?></div>
        <div class="settings-btn-row settings-btn-row-small" data-group="progress-bar">
          <button class="settings-option-btn" data-value="top">TOP</button>
          <button class="settings-option-btn" data-value="bottom">BOTTOM</button>
          <button class="settings-option-btn active" data-value="left">LEFT</button>
          <button class="settings-option-btn" data-value="right">RIGHT</button>
          <button class="settings-option-btn" data-value="none">NONE</button>
        </div>
      </div>

      <div class="settings-group">
        <div class="settings-group-label"><?= lang('ComixxManga.page_gap') ?> <span id="pageGapValue" style="color: var(--text-muted); font-weight: 400;">0px</span></div>
        <input type="range" id="pageGapSlider" min="0" max="50" value="0" style="width: 100%; accent-color: var(--accent-blue); cursor: pointer;">
      </div>
    </div>

    <!-- IMAGE Tab -->
    <div class="settings-tab-content" id="tab-image">
      <div class="settings-placeholder">
        <i class="fas fa-image" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
        <?= lang('ComixxManga.image_settings_soon') ?>
      </div>
    </div>

    <!-- SHORTCUTS Tab -->
    <div class="settings-tab-content" id="tab-shortcuts">
      <div class="settings-placeholder">
        <i class="fas fa-keyboard" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
        <?= lang('ComixxManga.shortcuts_soon') ?>
      </div>
    </div>
  </div>
</div>

<script>
var __chapterLang = {
  reply: <?= json_encode(lang('Comixx.reply')) ?>,
  cancel: <?= json_encode(lang('Comixx.cancel')) ?>,
  send: <?= json_encode(lang('Comixx.send')) ?>,
  follow: <?= json_encode(lang('Comixx.follow')) ?>,
  following: <?= json_encode(lang('Comixx.following')) ?>,
  no_comments: <?= json_encode(lang('ComixxManga.no_comments')) ?>,
  write_comment: <?= json_encode(lang('ComixxManga.write_comment')) ?>,
  post_comment: <?= json_encode(lang('ComixxManga.post_comment')) ?>,
  view_replies: <?= json_encode(lang('ComixxManga.view_replies')) ?>,
  hide_replies: <?= json_encode(lang('ComixxManga.hide_replies')) ?>,
  error: <?= json_encode(lang('ComixxManga.report_error_msg')) ?>,
  submit_report: <?= json_encode(lang('ComixxManga.submit_report')) ?>,
  sending: <?= json_encode(lang('ComixxManga.sending')) ?>,
  select_reason: <?= json_encode(lang('ComixxManga.select_reason')) ?>,
  report_thanks: <?= json_encode(lang('ComixxManga.report_thanks')) ?>,
  login_to_comment: <?= json_encode(lang('ComixxManga.login_to_comment')) ?>,
  report: <?= json_encode(lang('Comixx.report')) ?>,
  report_error: <?= json_encode(lang('Comixx.report_error')) ?>,
  report_chapter_error: <?= json_encode(lang('ComixxManga.report_chapter_error')) ?>,
  close: <?= json_encode(lang('ComixxManga.close')) ?>,
  reason: <?= json_encode(lang('ComixxManga.reason')) ?>,
  wrong_images: <?= json_encode(lang('ComixxManga.wrong_images')) ?>,
  missing_pages: <?= json_encode(lang('ComixxManga.missing_pages')) ?>,
  low_quality: <?= json_encode(lang('ComixxManga.low_quality')) ?>,
  cant_load: <?= json_encode(lang('ComixxManga.cant_load')) ?>,
  wrong_order: <?= json_encode(lang('ComixxManga.wrong_order')) ?>,
  other: <?= json_encode(lang('ComixxManga.other')) ?>,
  additional_details: <?= json_encode(lang('ComixxManga.additional_details')) ?>,
  back_to_top: <?= json_encode(lang('ComixxManga.back_to_top')) ?>,
  js_min: <?= json_encode(lang('ComixxTime.js_min')) ?>,
  js_hour: <?= json_encode(lang('ComixxTime.js_hour')) ?>,
  js_day: <?= json_encode(lang('ComixxTime.js_day')) ?>,
  js_format: <?= json_encode(lang('ComixxTime.js_format')) ?>,
  now: <?= json_encode(lang('ComixxTime.now')) ?>,
  show_more_replies: <?= json_encode(lang('ComixxManga.show_more_replies')) ?>
};
(function() {
    'use strict';

    // ===== Reader State =====
    var readerPages = document.getElementById('readerPages');
    var allImages = Array.from(readerPages.querySelectorAll('img'));
    var totalPages = allImages.length;
    var mangaSlug = '<?= esc($manga['slug'], 'js') ?>';
    var chapterSlug = '<?= esc($chapter['slug'], 'js') ?>';
    var currentMode = 'longstrip'; // single, double, longstrip
    var currentPage = 0; // 0-indexed
    var readingDir = 'ltr';

    // Page nav elements
    var pageNav = document.getElementById('pageNav');
    var prevPageBtn = document.getElementById('prevPageBtn');
    var nextPageBtn = document.getElementById('nextPageBtn');
    var pageSlider = document.getElementById('pageSlider');
    var pageInfo = document.getElementById('pageInfo');

    // ===== Lazy Loading =====
    function lazyLoadImages() {
        var lazyImages = document.querySelectorAll('img.lazy');
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
    }
    lazyLoadImages();

    // ===== Display Mode Logic =====
    function setMode(mode) {
        currentMode = mode;
        readerPages.classList.remove('mode-single', 'mode-double', 'mode-longstrip');
        readerPages.classList.add('mode-' + mode);

        if (mode === 'longstrip') {
            // Show all pages, hide nav
            allImages.forEach(function(img) {
                img.classList.remove('active-page');
                img.style.display = '';
            });
            pageNav.classList.add('hidden');
            window.scrollTo(0, 0);
        } else {
            // Load all images when switching to single/double mode
            document.querySelectorAll('img.lazy').forEach(function(img) {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                }
            });
            // Single or Double: show nav, go to current page
            pageNav.classList.remove('hidden');
            showCurrentPage();
        }
    }

    function showCurrentPage() {
        allImages.forEach(function(img) { img.classList.remove('active-page'); });

        if (currentMode === 'single') {
            allImages[currentPage].classList.add('active-page');
            pageSlider.max = totalPages;
            pageSlider.value = currentPage + 1;
            pageInfo.textContent = (currentPage + 1) + ' / ' + totalPages;
            prevPageBtn.disabled = currentPage === 0;
            nextPageBtn.disabled = currentPage === totalPages - 1;
        } else if (currentMode === 'double') {
            var totalSpreads = Math.ceil(totalPages / 2);
            var spread = Math.floor(currentPage / 2);
            var idx1 = spread * 2;
            var idx2 = spread * 2 + 1;

            if (readingDir === 'rtl') {
                if (idx2 < totalPages) allImages[idx2].classList.add('active-page');
                allImages[idx1].classList.add('active-page');
            } else {
                allImages[idx1].classList.add('active-page');
                if (idx2 < totalPages) allImages[idx2].classList.add('active-page');
            }

            pageSlider.max = totalSpreads;
            pageSlider.value = spread + 1;
            var endPage = Math.min(idx2 + 1, totalPages);
            pageInfo.textContent = (idx1 + 1) + '-' + endPage + ' / ' + totalPages;
            prevPageBtn.disabled = spread === 0;
            nextPageBtn.disabled = spread === totalSpreads - 1;
        }

        window.scrollTo(0, 0);
    }

    function nextPage() {
        if (currentMode === 'single') {
            if (currentPage < totalPages - 1) {
                currentPage++;
                showCurrentPage();
            }
        } else if (currentMode === 'double') {
            var totalSpreads = Math.ceil(totalPages / 2);
            var spread = Math.floor(currentPage / 2);
            if (spread < totalSpreads - 1) {
                currentPage = (spread + 1) * 2;
                showCurrentPage();
            }
        }
    }

    function prevPage() {
        if (currentMode === 'single') {
            if (currentPage > 0) {
                currentPage--;
                showCurrentPage();
            }
        } else if (currentMode === 'double') {
            var spread = Math.floor(currentPage / 2);
            if (spread > 0) {
                currentPage = (spread - 1) * 2;
                showCurrentPage();
            }
        }
    }

    // Page nav buttons
    prevPageBtn.addEventListener('click', function() {
        if (readingDir === 'rtl') nextPage(); else prevPage();
    });
    nextPageBtn.addEventListener('click', function() {
        if (readingDir === 'rtl') prevPage(); else nextPage();
    });

    // Slider
    pageSlider.addEventListener('input', function() {
        var val = parseInt(pageSlider.value);
        if (currentMode === 'single') {
            currentPage = val - 1;
        } else if (currentMode === 'double') {
            currentPage = (val - 1) * 2;
        }
        showCurrentPage();
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === 'Escape') { closeSettings(); return; }
        if (currentMode === 'longstrip') return;
        if (e.key === 'ArrowLeft') {
            if (readingDir === 'rtl') nextPage(); else prevPage();
        } else if (e.key === 'ArrowRight') {
            if (readingDir === 'rtl') prevPage(); else nextPage();
        }
    });

    // Click on image to advance page (single/double)
    readerPages.addEventListener('click', function(e) {
        if (currentMode === 'longstrip') return;
        if (e.target.closest('a') || e.target.closest('button')) return;
        var rect = readerPages.getBoundingClientRect();
        var clickX = e.clientX - rect.left;
        var halfW = rect.width / 2;
        if (readingDir === 'ltr') {
            if (clickX > halfW) nextPage(); else prevPage();
        } else {
            if (clickX > halfW) prevPage(); else nextPage();
        }
    });

    // ===== Settings Modal =====
    var settingsOverlay = document.getElementById('settingsOverlay');
    var settingsBtn = document.getElementById('settingsBtn');
    var settingsCloseBtn = document.getElementById('settingsCloseBtn');

    function openSettings() { settingsOverlay.classList.add('open'); }
    function closeSettings() { settingsOverlay.classList.remove('open'); }

    settingsBtn.addEventListener('click', openSettings);
    settingsCloseBtn.addEventListener('click', closeSettings);
    settingsOverlay.addEventListener('click', function(e) {
        if (e.target === settingsOverlay) closeSettings();
    });

    // Settings Tabs
    var tabs = document.querySelectorAll('.settings-tab');
    var tabContents = document.querySelectorAll('.settings-tab-content');
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) { t.classList.remove('active'); });
            tabContents.forEach(function(c) { c.classList.remove('active'); });
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        });
    });

    // Mode icons toggle
    document.querySelectorAll('.settings-mode-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.settings-mode-btn').forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
        });
    });

    // Settings option buttons - toggle within group + apply setting
    document.querySelectorAll('.settings-btn-row[data-group]').forEach(function(row) {
        var buttons = row.querySelectorAll('.settings-option-btn');
        buttons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                buttons.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');

                var group = row.dataset.group;
                var value = btn.dataset.value;

                if (group === 'page-display') {
                    setMode(value === 'single' ? 'single' : value === 'double' ? 'double' : 'longstrip');
                } else if (group === 'reading-dir') {
                    readingDir = value;
                    readerPages.classList.toggle('dir-rtl', value === 'rtl');
                    if (currentMode !== 'longstrip') showCurrentPage();
                }
            });
        });
    });

    // ===== Sidebar Toggle =====
    var sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    var readerSidebar = document.getElementById('readerSidebar');
    sidebarToggleBtn.addEventListener('click', function() {
        readerSidebar.classList.toggle('collapsed');
        if (readerSidebar.classList.contains('collapsed')) {
            document.documentElement.style.setProperty('--sidebar-width', '0px');
        } else {
            document.documentElement.style.setProperty('--sidebar-width', '525px');
        }
    });

    // ===== Chapter Select =====
    var chapterSelect = document.getElementById('chapterSelect');
    chapterSelect.addEventListener('change', function() {
        var selectedSlug = this.value;
        if (selectedSlug) {
            window.location.href = '<?= site_url('manga') ?>/' + mangaSlug + '/' + selectedSlug;
        }
    });

    // Hide page nav when mobile bottom section is visible
    var mobileBottom = document.querySelector('.reader-mobile-bottom');
    if (mobileBottom) {
        var mobileObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    pageNav.classList.add('hidden');
                } else {
                    var mode = readerPages.classList.contains('mode-longstrip') ? 'longstrip' : 'page';
                    if (mode !== 'longstrip') {
                        pageNav.classList.remove('hidden');
                    }
                }
            });
        }, { threshold: 0.1 });
        mobileObserver.observe(mobileBottom);
    }

    // Page Gap slider
    var pageGapSlider = document.getElementById('pageGapSlider');
    var pageGapValue = document.getElementById('pageGapValue');
    pageGapSlider.addEventListener('input', function() {
        var gap = pageGapSlider.value + 'px';
        document.documentElement.style.setProperty('--page-gap', gap);
        pageGapValue.textContent = gap;
    });

    // Initialize
    setMode('longstrip');

    // ===== Back to Top =====
    var backTopBtn = document.getElementById('backTopBtn');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 600) backTopBtn.classList.add('visible');
        else backTopBtn.classList.remove('visible');
    });
    backTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ===== Follow/Bookmark =====
    var followBtn = document.getElementById('readerFollowBtn');
    if (followBtn) {
      followBtn.addEventListener('click', function() {
        var mid = followBtn.dataset.mangaId;
        fetch('/api/bookmark/toggle', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
          body: 'manga_id=' + mid,
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
            if (label) label.textContent = __chapterLang.following;
          } else {
            followBtn.classList.remove('active');
            if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
            if (label) label.textContent = __chapterLang.follow;
          }
        });
      });
    }

    // ===== Like/Dislike Chapter =====
    (function(){
      var chapterId = <?= (int)$chapter['id'] ?>;
      var allLikeBtns = document.querySelectorAll('[data-type="like"]');
      var allDislikeBtns = document.querySelectorAll('[data-type="dislike"]');

      function updateAllUI(d){
        allLikeBtns.forEach(function(btn){
          btn.querySelector('.ml-like-count,.chLikeCount,#chLikeCount,[class*="Count"]');
          if(d.my_reaction==='like') btn.classList.add('active');
          else btn.classList.remove('active');
        });
        allDislikeBtns.forEach(function(btn){
          if(d.my_reaction==='dislike') btn.classList.add('active');
          else btn.classList.remove('active');
        });
        document.querySelectorAll('.ml-like-count, #chLikeCount').forEach(function(el){el.textContent=d.likes;});
        document.querySelectorAll('.ml-dislike-count, #chDislikeCount').forEach(function(el){el.textContent=d.dislikes;});
      }

      // Initial state loaded from PHP

      function toggle(type){
        fetch('/api/content-like',{
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
          body:'content_type=chapter&content_id='+chapterId+'&type='+type,
          credentials:'same-origin'
        }).then(function(r){
          if(r.status===401){window.location.href='/login';return null;}
          return r.json();
        }).then(function(d){if(d) updateAllUI(d);});
      }

      document.querySelectorAll('[data-type="like"]').forEach(function(btn){
        btn.addEventListener('click',function(){toggle('like');});
      });
      document.querySelectorAll('[data-type="dislike"]').forEach(function(btn){
        btn.addEventListener('click',function(){toggle('dislike');});
      });
    })();

    // ===== Track View (works with Cloudflare cache) =====
    var MANGA_ID = <?= (int)$manga['id'] ?>;
    var CHAPTER_ID = <?= (int)$chapter['id'] ?>;
    fetch('/api/view',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'manga_id='+MANGA_ID+'&chapter_id='+CHAPTER_ID});

    // ===== Comment System =====
    var MANGA_SLUG = <?= json_encode($manga['slug']) ?>;
    var CURRENT_UID = <?= !empty($currentUser) ? (int)$currentUser['id'] : 0 ?>;
    var BG_COLORS = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];

    function escHtml(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function timeAgo(str){
      var d=new Date(str.replace(' ','T'));
      var diff=Math.floor((Date.now()-d.getTime())/1000);
      function fmt(n,unit){
        if(unit.indexOf('{n}')!==-1) return unit.replace('{n}',n);
        return n+unit;
      }
      if(diff<60) return __chapterLang.now;
      if(diff<3600) return fmt(Math.floor(diff/60),__chapterLang.js_min);
      if(diff<86400) return fmt(Math.floor(diff/3600),__chapterLang.js_hour);
      if(diff<604800) return fmt(Math.floor(diff/86400),__chapterLang.js_day);
      return fmt(Math.floor(diff/604800),'w');
    }
    function rcAvatar(name, username, uid, sz){
      sz=sz||28;
      var ch=((name||username||'?')[0]).toUpperCase();
      var bg=BG_COLORS[parseInt(uid||0)%6];
      return '<div class="rc-avatar" style="width:'+sz+'px;height:'+sz+'px;font-size:'+(sz*0.38)+'px;background:'+bg+'">'+ch+'</div>';
    }
    var rcLikeIcon='<svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14zM7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3"/></svg>';

    function rcLikeBtn(c){
      var isLiked=c.my_reaction==='like';
      if(CURRENT_UID>0) return '<button class="rc-react'+(isLiked?' liked':'')+'" data-id="'+c.id+'" data-type="like">'+rcLikeIcon+'<span class="lc">'+c.likes_count+'</span></button>';
      return '<span class="rc-react" style="cursor:default">'+rcLikeIcon+'<span>'+c.likes_count+'</span></span>';
    }

    function rcRenderReply(c, topId){
      var name=c.user_name||c.user_username||'?';
      var rb=(CURRENT_UID>0&&topId)?'<button class="rc-reply-btn" data-id="'+topId+'" data-reply-to="'+c.id+'" data-name="'+escHtml(name)+'">'+__chapterLang.reply+'</button>':'';
      return '<div class="rc-item" data-id="'+c.id+'"><div class="rc-item-body">'+
        rcAvatar(c.user_name,c.user_username,c.user_id,22)+
        '<div class="rc-content"><div class="rc-bubble"><span class="rc-user">'+escHtml(name)+'</span><div class="rc-text">'+escHtml(c.comment)+'</div></div>'+
        '<div class="rc-actions">'+rcLikeBtn(c)+'<small style="color:var(--text-muted)">'+timeAgo(c.created_at)+'</small>'+rb+'</div></div></div></div>';
    }

    function rcReplyFormHtml(parentId, parentName, replyToId){
      var rCa=Math.floor(Math.random()*10)+1, rCb=Math.floor(Math.random()*10)+1;
      return '<div class="rc-reply-form" id="rc-rf-'+parentId+'">'+
        '<input type="hidden" class="rc-reply-to-id" value="'+(replyToId||0)+'">'+
        '<textarea class="rc-reply-input" rows="2" maxlength="1000">@'+escHtml(parentName)+' </textarea>'+
        '<div class="rc-captcha-wrap rc-reply-captcha" data-a="'+rCa+'" data-b="'+rCb+'" style="display:none">'+
          '<span class="rc-captcha-label"><?= lang('ComixxManga.captcha_label') ?></span>'+
          '<span class="rc-captcha-q rc-reply-captcha-q">'+rCa+' + '+rCb+'</span>'+
          '<input type="number" class="rc-captcha-ans rc-reply-captcha-ans" min="0" max="99" placeholder="0">'+
        '</div>'+
        '<div class="rc-reply-form-actions">'+
        '<button class="rc-reply-cancel" data-parent="'+parentId+'">'+__chapterLang.cancel+'</button>'+
        '<button class="rc-reply-submit" data-parent="'+parentId+'">'+__chapterLang.reply+'</button>'+
        '</div></div>';
    }

    function rcFetchReplies(commentId, btn, listEl){
      var container=listEl.querySelector('#rc-replies-'+commentId);
      if(!container) return;
      if(btn){btn.disabled=true;btn.textContent='...';}
      fetch('/api/comments/'+commentId+'/replies')
        .then(function(r){return r.json();})
        .then(function(d){
          if(!d.replies||!d.replies.length){
            if(btn){btn.disabled=false;var cnt=btn.dataset.count;btn.textContent=__chapterLang.view_replies.replace('{n}',' '+cnt+' ');}
            return;
          }
          var LIMIT=3,visible=d.replies.slice(0,LIMIT),hidden=d.replies.slice(LIMIT);
          container.innerHTML=visible.map(function(r){return rcRenderReply(r,commentId);}).join('');
          if(hidden.length>0){
            var mBtn=document.createElement('button');
            mBtn.className='rc-show-more';
            mBtn.textContent=__chapterLang.show_more_replies.replace('{n}',hidden.length);
            mBtn.onclick=function(){mBtn.remove();container.insertAdjacentHTML('beforeend',hidden.map(function(r){return rcRenderReply(r,commentId);}).join(''));};
            container.appendChild(mBtn);
          }
          if(btn){btn.textContent=__chapterLang.hide_replies;btn.disabled=false;btn.dataset.open='1';}
        })
        .catch(function(){if(btn){btn.disabled=false;var cnt=btn.dataset.count;btn.textContent=__chapterLang.view_replies.replace('{n}',' '+cnt+' ');}});
    }

    function rcRenderCmt(c){
      var name=c.user_name||c.user_username||'?';
      var rb=CURRENT_UID>0?'<button class="rc-reply-btn" data-id="'+c.id+'" data-name="'+escHtml(name)+'">'+__chapterLang.reply+'</button>':'';
      var replyCount=parseInt(c.reply_count||0);
      var rt=replyCount>0?'<button class="rc-toggle-replies" data-id="'+c.id+'" data-count="'+replyCount+'">'+__chapterLang.view_replies.replace('{n}',' '+replyCount+' ')+'</button>':'';
      return '<div class="rc-item" data-id="'+c.id+'"><div class="rc-item-body">'+
        rcAvatar(c.user_name,c.user_username,c.user_id)+
        '<div class="rc-content"><div class="rc-bubble"><span class="rc-user">'+escHtml(name)+'</span><div class="rc-text">'+escHtml(c.comment)+'</div></div>'+
        '<div class="rc-actions">'+rcLikeBtn(c)+'<small style="color:var(--text-muted)">'+timeAgo(c.created_at)+'</small>'+rt+rb+'</div>'+
        '<div id="rc-reply-area-'+c.id+'"></div>'+
        '<div id="rc-replies-'+c.id+'" class="rc-replies"></div>'+
        '</div></div></div>';
    }

    function rcRenderPg(pgEl, curPage, totalPg){
      if(!pgEl) return;
      if(totalPg<=1){pgEl.innerHTML='';return;}
      var h='';
      h+=curPage>1?'<button data-page="'+(curPage-1)+'">&#8249;</button>':'<button disabled>&#8249;</button>';
      var s=Math.max(1,curPage-2),e=Math.min(totalPg,s+4);s=Math.max(1,e-4);
      for(var i=s;i<=e;i++) h+=i===curPage?'<button class="pg-active">'+i+'</button>':'<button data-page="'+i+'">'+i+'</button>';
      h+=curPage<totalPg?'<button data-page="'+(curPage+1)+'">&#8250;</button>':'<button disabled>&#8250;</button>';
      pgEl.innerHTML=h;
    }

    // Each panel (sidebar + mobile) has its own state
    function initCommentPanel(prefix, listChId, pgChId, listAllId, pgAllId, countChId, countAllId, inputId, sendId){
      var state={tab:'chapter',order:'newest',chPage:1,chTotal:1,allPage:1,allTotal:1,loadingCh:false,loadingAll:false};
      var listCh=document.getElementById(listChId);
      var pgCh=document.getElementById(pgChId);
      var listAll=document.getElementById(listAllId);
      var pgAll=document.getElementById(pgAllId);
      var countCh=document.getElementById(countChId);
      var countAll=document.getElementById(countAllId);

      function fetchCh(p){
        if(state.loadingCh) return;
        state.loadingCh=true; state.chPage=p;
        fetch('/api/comments/chapter/'+CHAPTER_ID+'?page='+p+'&order='+state.order)
          .then(function(r){return r.json();})
          .then(function(data){
            if(countCh) countCh.textContent=data.total>0?'('+data.total+')':'';
            state.chTotal=data.total>0?Math.ceil(data.total/10):1;
            listCh.innerHTML=(!data.comments||!data.comments.length)
              ?'<p class="rc-loading">'+__chapterLang.no_comments+'</p>'
              :data.comments.map(rcRenderCmt).join('');
            // Replies hidden by default
            rcRenderPg(pgCh,p,state.chTotal);
            state.loadingCh=false;
          }).catch(function(){state.loadingCh=false;});
      }
      function fetchAll(p){
        if(state.loadingAll) return;
        state.loadingAll=true; state.allPage=p;
        fetch('/api/comments/manga/'+MANGA_ID+'/all?page='+p+'&order='+state.order)
          .then(function(r){return r.json();})
          .then(function(data){
            if(countAll) countAll.textContent=data.total>0?'('+data.total+')':'';
            state.allTotal=data.total>0?Math.ceil(data.total/10):1;
            listAll.innerHTML=(!data.comments||!data.comments.length)
              ?'<p class="rc-loading">'+__chapterLang.no_comments+'</p>'
              :data.comments.map(rcRenderCmt).join('');
            // Replies hidden by default
            rcRenderPg(pgAll,p,state.allTotal);
            state.loadingAll=false;
          }).catch(function(){state.loadingAll=false;});
      }

      // Init load
      fetchCh(1); fetchAll(1);

      // Container
      var container=listCh.closest('.reader-comments');
      if(!container) return;

      // Tabs
      container.querySelectorAll('.reader-comments-tab').forEach(function(tab){
        tab.addEventListener('click',function(){
          container.querySelectorAll('.reader-comments-tab').forEach(function(t){t.classList.remove('active');});
          tab.classList.add('active');
          var type=tab.dataset.tab;
          state.tab=type;
          container.querySelectorAll('.reader-comments-tab-content').forEach(function(c){
            c.classList.toggle('active',c.dataset.type===type);
          });
        });
      });

      // Sort
      container.querySelectorAll('.tab-btn[data-sort]').forEach(function(btn){
        btn.addEventListener('click',function(){
          container.querySelectorAll('.tab-btn[data-sort]').forEach(function(b){b.classList.remove('active');});
          btn.classList.add('active');
          state.order=btn.dataset.sort;
          fetchCh(1); fetchAll(1);
        });
      });

      // Pagination clicks
      [pgCh, pgAll].forEach(function(pgEl, idx){
        if(!pgEl) return;
        pgEl.addEventListener('click',function(e){
          var btn=e.target.closest('[data-page]');
          if(!btn) return;
          var p=parseInt(btn.dataset.page);
          if(idx===0) fetchCh(p); else fetchAll(p);
        });
      });

      // Event delegation: like, reply, toggle replies
      [listCh, listAll].forEach(function(listEl){
        if(!listEl) return;
        listEl.addEventListener('click',function(e){
          var target=e.target.closest('button');
          if(!target) return;

          // Like
          if(target.classList.contains('rc-react')){
            var cid=parseInt(target.dataset.id);
            var fd=new FormData(); fd.append('type','like');
            fetch('/api/comments/'+cid+'/react',{method:'POST',body:fd})
              .then(function(r){return r.json();})
              .then(function(d){
                if(d.error) return;
                var item=target.closest('.rc-item');
                var lb=item.querySelector('.rc-react[data-type="like"]');
                if(lb){
                  if(d.my_reaction==='like') lb.classList.add('liked'); else lb.classList.remove('liked');
                  var lc=lb.querySelector('.lc'); if(lc) lc.textContent=d.likes_count;
                }
              });
            return;
          }

          // Toggle replies
          if(target.classList.contains('rc-toggle-replies')){
            var cid=target.dataset.id;
            var rc=listEl.querySelector('#rc-replies-'+cid);
            if(!rc) return;
            if(target.dataset.open==='1'){
              rc.innerHTML=''; target.dataset.open='0';
              var cnt=target.dataset.count; target.textContent=__chapterLang.view_replies.replace('{n}',' '+cnt+' ');
            } else { rcFetchReplies(parseInt(cid),target,listEl); }
            return;
          }

          // Reply button
          if(target.classList.contains('rc-reply-btn')&&!target.classList.contains('rc-toggle-replies')){
            var repliesWrap=target.closest('[id^="rc-replies-"]');
            var parentId=repliesWrap?repliesWrap.id.replace('rc-replies-',''):target.dataset.id;
            var parentName=target.dataset.name;
            var replyToId=target.dataset.replyTo||0;
            var area=listEl.querySelector('#rc-reply-area-'+parentId);
            if(!area) return;
            var existing=listEl.querySelector('#rc-rf-'+parentId);
            if(existing){existing.remove();return;}
            area.innerHTML=rcReplyFormHtml(parentId,parentName,replyToId);
            var ta=area.querySelector('.rc-reply-input');
            ta.focus();ta.setSelectionRange(ta.value.length,ta.value.length);
            return;
          }

          // Cancel reply
          if(target.classList.contains('rc-reply-cancel')){
            var rf=listEl.querySelector('#rc-rf-'+target.dataset.parent);
            if(rf) rf.remove();
            return;
          }

          // Submit reply
          if(target.classList.contains('rc-reply-submit')){
            var parentId=target.dataset.parent;
            var rf=listEl.querySelector('#rc-rf-'+parentId);
            if(!rf) return;
            var ta=rf.querySelector('.rc-reply-input');
            var text=ta?ta.value.trim():'';
            if(!text) return;
            var rcCaptchaWrap=rf.querySelector('.rc-reply-captcha');
            var rcCaptchaAns=rf.querySelector('.rc-reply-captcha-ans');
            if(rcCaptchaWrap&&rcCaptchaWrap.style.display!=='none'&&rcCaptchaAns){
              var rCa=parseInt(rcCaptchaWrap.dataset.a), rCb=parseInt(rcCaptchaWrap.dataset.b);
              if(parseInt(rcCaptchaAns.value)!==rCa+rCb){
                rcCaptchaAns.style.borderColor='red';
                rcCaptchaAns.focus();
                return;
              }
            }
            target.disabled=true;target.textContent='...';
            var body='manga_id='+MANGA_ID+'&chapter_id='+CHAPTER_ID+'&comment='+encodeURIComponent(text)+'&parent_comment='+parentId;
            if(rcCaptchaWrap&&rcCaptchaWrap.style.display!=='none') body+='&captcha_passed=1';
            fetch('/api/comments',{
              method:'POST',
              headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
              body:body,
              credentials:'same-origin'
            })
            .then(function(r){
              if(r.status===429) return r.json().then(function(d){throw d;});
              return r.json();
            })
            .then(function(c){
              if(c.error){alert(c.error);target.disabled=false;target.textContent=__chapterLang.reply;return;}
              rf.remove();
              // Reload replies for parent
              var rc=listEl.querySelector('#rc-replies-'+parentId);
              if(rc){
                fetch('/api/comments/'+parentId+'/replies')
                  .then(function(r){return r.json();})
                  .then(function(rd){
                    if(rd.replies&&rd.replies.length){
                      rc.innerHTML=rd.replies.map(function(r){return rcRenderReply(r,parentId);}).join('');
                    }
                    var toggleBtn=listEl.querySelector('.rc-toggle-replies[data-id="'+parentId+'"]');
                    if(toggleBtn){
                      toggleBtn.dataset.open='1';
                      toggleBtn.textContent=__chapterLang.hide_replies;
                    }
                  });
              }
            })
            .catch(function(d){
              if(d&&d.need_captcha&&rcCaptchaWrap){
                var rCa2=Math.floor(Math.random()*10)+1, rCb2=Math.floor(Math.random()*10)+1;
                rcCaptchaWrap.dataset.a=rCa2; rcCaptchaWrap.dataset.b=rCb2;
                var cq=rf.querySelector('.rc-reply-captcha-q');
                if(cq) cq.textContent=rCa2+' + '+rCb2;
                rcCaptchaWrap.style.display='flex';
                if(rcCaptchaAns){rcCaptchaAns.value='';rcCaptchaAns.focus();}
              }
              target.disabled=false;target.textContent=__chapterLang.reply;
            });
            return;
          }
        });
      });

      // Comment input (send new comment)
      var inp=document.getElementById(inputId);
      var sendBtn=document.getElementById(sendId);
      var captchaWrap=document.getElementById(prefix+'-captcha');
      var captchaQ=document.getElementById(prefix+'-captcha-q');
      var captchaAns=document.getElementById(prefix+'-captcha-ans');
      var captchaA, captchaB;
      var captchaShown=false;

      function newCaptcha(){
        captchaA=Math.floor(Math.random()*10)+1;
        captchaB=Math.floor(Math.random()*10)+1;
        if(captchaQ) captchaQ.textContent=captchaA+' + '+captchaB;
        if(captchaAns) captchaAns.value='';
      }

      if(inp&&sendBtn){
        newCaptcha();
        function postComment(){
          var text=inp.value.trim();
          if(!text) return;
          if(captchaShown&&captchaAns){
            if(parseInt(captchaAns.value)!==captchaA+captchaB){
              captchaAns.style.borderColor='red';
              captchaAns.focus();
              return;
            }
          }
          sendBtn.disabled=true;
          var body='manga_id='+MANGA_ID+'&chapter_id='+CHAPTER_ID+'&comment='+encodeURIComponent(text);
          if(captchaShown) body+='&captcha_passed=1';
          fetch('/api/comments',{
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
            body:body,
            credentials:'same-origin'
          })
          .then(function(r){
            if(r.status===401){window.location.href='/login';return null;}
            return r.json().then(function(d){d._status=r.status;return d;});
          })
          .then(function(d){
            sendBtn.disabled=false;
            if(!d) return;
            if(d.need_captcha){
              captchaShown=true;
              if(captchaWrap){captchaWrap.style.display='flex';newCaptcha();if(captchaAns) captchaAns.focus();}
              return;
            }
            if(d.error){alert(d.error);return;}
            captchaShown=false;
            if(captchaWrap) captchaWrap.style.display='none';
            newCaptcha();
            fetchCh(1);
            inp.value='';
          })
          .catch(function(){sendBtn.disabled=false;});
        }
        sendBtn.addEventListener('click',postComment);
        inp.addEventListener('keydown',function(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();postComment();}});
      }
    }

    // Init both panels
    initCommentPanel('sc','sc-ch-list','sc-ch-pg','sc-all-list','sc-all-pg','sc-ch-count','sc-all-count','sc-input','sc-send');
    initCommentPanel('mc','mc-ch-list','mc-ch-pg','mc-all-list','mc-all-pg','mc-ch-count','mc-all-count','mc-input','mc-send');
})();
</script>

<!-- Report Modal -->
<div class="rpt-overlay" id="rptModal">
  <div class="rpt-box">
    <button class="rpt-close" id="rptClose">&times;</button>
    <h3><?= lang('ComixxManga.report_chapter_error') ?></h3>
    <p class="rpt-sub"><?= esc($manga['name']) ?> — <?= esc($chapTitle) ?></p>
    <p style="font-size:12px;color:var(--text-muted);margin:0 0 8px;font-weight:500"><?= lang('ComixxManga.reason') ?> <span style="color:#ef4444">*</span></p>
    <div class="rpt-reasons" id="rptReasons">
      <?php foreach ([
        'wrong_images'  => lang('ComixxManga.wrong_images'),
        'missing_pages' => lang('ComixxManga.missing_pages'),
        'low_quality'   => lang('ComixxManga.low_quality'),
        'cant_load'     => lang('ComixxManga.cant_load'),
        'wrong_order'   => lang('ComixxManga.wrong_order'),
        'other'         => lang('ComixxManga.other'),
      ] as $val => $label): ?>
      <label class="rpt-reason-label">
        <input type="radio" name="rpt-reason" value="<?= $val ?>">
        <span><?= $label ?></span>
      </label>
      <?php endforeach; ?>
    </div>
    <textarea class="rpt-note" id="rptNote" rows="2" maxlength="300" placeholder="<?= esc(lang('ComixxManga.additional_details')) ?>"></textarea>
    <div class="rpt-actions">
      <button class="rpt-cancel-btn" id="rptCancel"><?= lang('Comixx.cancel') ?></button>
      <button class="rpt-submit-btn" id="rptSubmit"><?= lang('ComixxManga.submit_report') ?></button>
    </div>
    <p class="rpt-msg" id="rptMsg"></p>
  </div>
</div>
<script>
(function(){
  var modal=document.getElementById('rptModal');
  var submitBtn=document.getElementById('rptSubmit');
  var msg=document.getElementById('rptMsg');

  function openRpt(){modal.classList.add('open');document.body.style.overflow='hidden';}
  function closeRpt(){
    modal.classList.remove('open');document.body.style.overflow='';
    msg.style.display='none';submitBtn.style.display='';submitBtn.disabled=false;submitBtn.textContent=__chapterLang.submit_report;
    document.querySelectorAll('input[name="rpt-reason"]').forEach(function(r){r.checked=false;});
    document.querySelectorAll('.rpt-reason-label').forEach(function(l){l.classList.remove('selected');});
    document.getElementById('rptNote').value='';
  }

  document.querySelectorAll('.rpt-open-btn').forEach(function(b){b.addEventListener('click',openRpt);});
  document.getElementById('rptClose').addEventListener('click',closeRpt);
  document.getElementById('rptCancel').addEventListener('click',closeRpt);
  modal.addEventListener('click',function(e){if(e.target===modal)closeRpt();});

  document.querySelectorAll('input[name="rpt-reason"]').forEach(function(r){
    r.addEventListener('change',function(){
      document.querySelectorAll('.rpt-reason-label').forEach(function(l){l.classList.remove('selected');});
      this.closest('.rpt-reason-label').classList.add('selected');
    });
  });

  submitBtn.addEventListener('click',function(){
    var reason=document.querySelector('input[name="rpt-reason"]:checked');
    if(!reason){msg.style.display='block';msg.style.color='#ef4444';msg.textContent=__chapterLang.select_reason;return;}
    submitBtn.disabled=true;submitBtn.textContent=__chapterLang.sending;msg.style.display='none';
    var fd=new FormData();
    fd.append('reason',reason.value);
    fd.append('note',document.getElementById('rptNote').value.trim());
    fetch('/api/chapters/'+<?= (int)$chapter['id'] ?>+'/report',{method:'POST',credentials:'same-origin',body:fd})
    .then(function(r){return r.json();})
    .then(function(d){
      msg.style.display='block';
      if(d.ok){
        msg.style.color='#22c55e';msg.textContent=__chapterLang.report_thanks;
        submitBtn.style.display='none';setTimeout(closeRpt,2500);
      }else{
        msg.style.color='#ef4444';msg.textContent=d.error||__chapterLang.error;
        submitBtn.disabled=false;submitBtn.textContent=__chapterLang.submit_report;
      }
    })
    .catch(function(){
      msg.style.display='block';msg.style.color='#ef4444';msg.textContent=__chapterLang.error;
      submitBtn.disabled=false;submitBtn.textContent=__chapterLang.submit_report;
    });
  });
})();
</script>
<?= $this->endSection() ?>
