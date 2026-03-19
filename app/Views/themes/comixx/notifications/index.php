<?= $this->extend('themes/comixx/layouts/main') ?>

<?= $this->section('head_extra') ?>
<style>
    /* ===== Profile Banner ===== */
    .profile-banner {
      position: relative;
      background: linear-gradient(135deg, #0a1f18 0%, #0d2a1f 30%, #112e24 60%, #0a1f18 100%);
      padding: 40px 0 24px;
      overflow: hidden;
    }

    .profile-banner::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        repeating-conic-gradient(from 0deg at 50% 50%, transparent 0deg 80deg, rgba(52, 211, 153, 0.015) 80deg 90deg),
        radial-gradient(ellipse at 20% 80%, rgba(52, 211, 153, 0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(52, 211, 153, 0.04) 0%, transparent 60%);
      pointer-events: none;
    }

    .profile-banner::after {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        url("data:image/svg+xml,%3Csvg width='400' height='200' viewBox='0 0 400 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 80 Q100 40 200 80 T400 80' fill='none' stroke='rgba(52,211,153,0.04)' stroke-width='1'/%3E%3Cpath d='M0 120 Q100 80 200 120 T400 120' fill='none' stroke='rgba(52,211,153,0.03)' stroke-width='1'/%3E%3Cpath d='M0 160 Q100 120 200 160 T400 160' fill='none' stroke='rgba(52,211,153,0.025)' stroke-width='1'/%3E%3C/svg%3E");
      background-size: 400px 200px;
      opacity: 0.8;
      pointer-events: none;
    }

    .profile-banner-inner {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .profile-avatar {
      position: relative;
      width: 96px;
      height: 96px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1a4a3a, #0d2a1f);
      border: 3px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .profile-avatar i.avatar-icon {
      font-size: 38px;
      color: var(--accent-blue);
    }

    .profile-avatar-edit {
      position: absolute;
      bottom: 2px;
      right: 2px;
      width: 28px;
      height: 28px;
      background: var(--accent-blue);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      color: white;
      cursor: pointer;
      border: 2px solid var(--bg-primary);
    }

    .profile-avatar-edit:hover {
      background: #059669;
    }

    .profile-user-info {
      flex: 1;
    }

    .profile-username {
      font-size: 22px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .profile-handle {
      font-size: 14px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .profile-logout-btn {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      padding: 8px 20px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 700;
      color: var(--text-secondary);
      letter-spacing: 0.5px;
      transition: all 0.2s;
      flex-shrink: 0;
    }

    .profile-logout-btn:hover {
      background: var(--accent-red);
      border-color: var(--accent-red);
      color: white;
    }

    /* ===== Profile Tabs ===== */
    .profile-tabs {
      background: var(--bg-secondary);
      border-bottom: 1px solid var(--border-color);
      position: sticky;
      top: 52px;
      z-index: 50;
    }

    .profile-tabs-inner {
      display: flex;
      gap: 0;
    }

    .profile-tab {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 14px 20px;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-muted);
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
      cursor: pointer;
      white-space: nowrap;
      background: none;
      border-top: none;
      border-left: none;
      border-right: none;
      font-family: var(--font);
      text-decoration: none;
    }

    .profile-tab:hover {
      color: var(--text-secondary);
    }

    .profile-tab.active {
      color: var(--accent-blue);
      border-bottom-color: var(--accent-blue);
    }

    .profile-tab .tab-text {
      display: inline;
    }

    /* ===== Profile Content ===== */
    .profile-content {
      padding-top: 24px;
      padding-bottom: 40px;
    }

    /* ===== Edit Profile Form ===== */
    .profile-form {
      max-width: 600px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 6px;
      letter-spacing: 0.3px;
    }

    .form-input {
      width: 100%;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 10px 14px;
      font-family: var(--font);
      font-size: 14px;
      color: var(--text-primary);
      outline: none;
      transition: border-color 0.2s;
    }

    .form-input:focus {
      border-color: var(--accent-blue);
    }

    .form-input.readonly {
      color: var(--text-muted);
      cursor: not-allowed;
    }

    .form-input::placeholder {
      color: var(--text-muted);
    }

    .change-password-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 14px;
      font-weight: 600;
      color: var(--accent-blue);
      margin-bottom: 24px;
      cursor: pointer;
      transition: color 0.2s;
    }

    .change-password-link:hover {
      color: #059669;
    }

    .save-btn {
      background: var(--accent-blue);
      color: white;
      padding: 10px 28px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.5px;
      transition: background 0.2s;
    }

    .save-btn:hover {
      background: #059669;
    }

    /* ===== Bookmarks Tab ===== */
    .bookmarks-subtabs {
      display: flex;
      gap: 0;
      margin-bottom: 16px;
      border-bottom: 1px solid var(--border-color);
    }

    .bookmark-subtab {
      padding: 10px 20px;
      font-size: 13px;
      font-weight: 700;
      color: var(--text-muted);
      letter-spacing: 0.5px;
      border-bottom: 2px solid transparent;
      transition: all 0.2s;
      cursor: pointer;
      background: none;
      border-top: none;
      border-left: none;
      border-right: none;
      font-family: var(--font);
    }

    .bookmark-subtab:hover {
      color: var(--text-secondary);
    }

    .bookmark-subtab.active {
      color: var(--accent-blue);
      border-bottom-color: var(--accent-blue);
    }

    .bookmarks-toolbar {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }

    .bookmarks-search {
      flex: 1;
      min-width: 200px;
      display: flex;
      align-items: center;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      position: relative;
    }

    .bookmarks-search i {
      position: absolute;
      left: 12px;
      color: var(--text-muted);
      font-size: 14px;
    }

    .bookmarks-search input {
      width: 100%;
      background: transparent;
      border: none;
      outline: none;
      font-family: var(--font);
      font-size: 13px;
      color: var(--text-primary);
      padding: 8px 12px 8px 36px;
    }

    .bookmarks-search input::placeholder {
      color: var(--text-muted);
    }

    .advanced-filters-btn {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      padding: 8px 16px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
      letter-spacing: 0.3px;
      display: flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
      transition: all 0.2s;
    }

    .advanced-filters-btn:hover {
      background: var(--bg-card-hover);
    }

    /* Desktop Table */
    .bookmarks-table {
      width: 100%;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      overflow: hidden;
    }

    .bookmarks-table-header {
      display: grid;
      grid-template-columns: 2fr 100px 100px 110px 90px 90px 90px 90px;
      padding: 10px 16px;
      background: var(--bg-card);
      border-bottom: 1px solid var(--border-color);
      font-size: 12px;
      font-weight: 700;
      color: var(--text-muted);
      letter-spacing: 0.3px;
      gap: 8px;
    }

    .bookmarks-table-row {
      display: grid;
      grid-template-columns: 2fr 100px 100px 110px 90px 90px 90px 90px;
      padding: 10px 16px;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      color: var(--text-secondary);
      border-bottom: 1px solid var(--border-color);
      transition: background 0.15s;
    }

    .bookmarks-table-row:last-child {
      border-bottom: none;
    }

    .bookmarks-table-row:hover {
      background: var(--bg-card-hover);
    }

    .bookmark-title-cell {
      display: flex;
      align-items: center;
      gap: 10px;
      min-width: 0;
    }

    .bookmark-thumb {
      width: 40px;
      height: 56px;
      border-radius: 4px;
      overflow: hidden;
      flex-shrink: 0;
      background: var(--bg-card);
    }

    .bookmark-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .bookmark-title-text {
      font-weight: 500;
      color: var(--text-primary);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .bookmark-select {
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 4px;
      padding: 4px 8px;
      font-family: var(--font);
      font-size: 12px;
      color: var(--text-secondary);
      outline: none;
      cursor: pointer;
    }

    .bookmark-select:focus {
      border-color: var(--accent-blue);
    }

    .bookmarks-count {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 14px;
    }

    /* Mobile Card Layout */
    .bookmarks-cards {
      display: none;
    }

    .bookmark-card {
      display: flex;
      gap: 12px;
      padding: 14px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      margin-bottom: 8px;
    }

    .bookmark-card-thumb {
      width: 64px;
      height: 88px;
      border-radius: 6px;
      overflow: hidden;
      flex-shrink: 0;
      background: var(--bg-card);
    }

    .bookmark-card-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .bookmark-card-info {
      flex: 1;
      min-width: 0;
    }

    .bookmark-card-title {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 8px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .bookmark-card-meta {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px 16px;
      font-size: 12px;
    }

    .bookmark-card-meta-item {
      display: flex;
      gap: 4px;
    }

    .bookmark-card-meta-label {
      color: var(--text-muted);
    }

    .bookmark-card-meta-value {
      color: var(--text-secondary);
    }

    /* ===== History Tab ===== */
    .history-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .history-item {
      display: flex;
      gap: 12px;
      padding: 12px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      transition: background 0.2s;
    }

    .history-item:hover {
      background: var(--bg-card);
    }

    .history-thumb {
      width: 48px;
      height: 64px;
      border-radius: 4px;
      overflow: hidden;
      flex-shrink: 0;
      background: var(--bg-card);
    }

    .history-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .history-info {
      flex: 1;
      min-width: 0;
    }

    .history-title {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 4px;
    }

    .history-chapter {
      font-size: 13px;
      color: var(--accent-blue);
      font-weight: 500;
      margin-bottom: 4px;
    }

    .history-time {
      font-size: 12px;
      color: var(--text-muted);
    }

    /* ===== Notifications Tab ===== */
    .notification-list {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .notification-item {
      display: flex;
      gap: 12px;
      padding: 14px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      transition: background 0.2s;
    }

    .notification-item:hover {
      background: var(--bg-card);
    }

    .notification-item.unread {
      border-left: 3px solid var(--accent-blue);
    }

    .notification-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--bg-card);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 16px;
      color: var(--accent-blue);
    }

    .notification-content {
      flex: 1;
    }

    .notification-text {
      font-size: 14px;
      color: var(--text-primary);
      margin-bottom: 4px;
      line-height: 1.5;
    }

    .notification-text strong {
      color: var(--accent-blue);
    }

    .notification-time {
      font-size: 12px;
      color: var(--text-muted);
    }

    /* ===== Settings Tab ===== */
    .settings-section {
      margin-bottom: 28px;
    }

    .settings-section-title {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 16px;
      color: var(--text-primary);
    }

    .settings-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 16px;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      margin-bottom: 8px;
    }

    .settings-row-label {
      font-size: 14px;
      font-weight: 500;
      color: var(--text-primary);
    }

    .settings-row-desc {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .toggle-switch {
      position: relative;
      width: 44px;
      height: 24px;
      flex-shrink: 0;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      inset: 0;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .toggle-slider::before {
      content: '';
      position: absolute;
      top: 3px;
      left: 3px;
      width: 16px;
      height: 16px;
      background: var(--text-muted);
      border-radius: 50%;
      transition: all 0.3s;
    }

    .toggle-switch input:checked + .toggle-slider {
      background: var(--accent-blue);
      border-color: var(--accent-blue);
    }

    .toggle-switch input:checked + .toggle-slider::before {
      transform: translateX(20px);
      background: white;
    }

    .settings-select {
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 6px 12px;
      font-family: var(--font);
      font-size: 13px;
      color: var(--text-secondary);
      outline: none;
    }

    .settings-select:focus {
      border-color: var(--accent-blue);
    }

    /* ===== Mobile Profile Dropdown ===== */
    .profile-mobile-dropdown {
      display: none;
      position: fixed;
      top: 0;
      right: -300px;
      width: 300px;
      height: 100%;
      background: var(--bg-header);
      border-left: 1px solid var(--border-color);
      z-index: 999;
      transition: right 0.3s;
      flex-direction: column;
      padding: 0;
    }

    .profile-mobile-dropdown.open {
      right: 0;
      display: flex;
    }

    .profile-mobile-header {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 20px 16px;
      border-bottom: 1px solid var(--border-color);
    }

    .profile-mobile-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1a4a3a, #0d2a1f);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .profile-mobile-avatar i {
      font-size: 20px;
      color: var(--accent-blue);
    }

    .profile-mobile-user {
      flex: 1;
    }

    .profile-mobile-user-name {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .profile-mobile-user-email {
      font-size: 12px;
      color: var(--text-muted);
    }

    .profile-mobile-close {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: var(--text-secondary);
      border-radius: 6px;
      cursor: pointer;
      background: none;
      border: none;
      font-family: var(--font);
    }

    .profile-mobile-close:hover {
      background: var(--bg-card);
    }

    .profile-mobile-nav {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 2px;
      padding: 12px;
      overflow-y: auto;
    }

    .profile-mobile-nav a,
    .profile-mobile-nav button {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px;
      font-size: 15px;
      font-weight: 500;
      color: var(--text-secondary);
      border-radius: 6px;
      transition: all 0.2s;
      text-decoration: none;
      background: none;
      border: none;
      font-family: var(--font);
      cursor: pointer;
      width: 100%;
      text-align: left;
    }

    .profile-mobile-nav a i,
    .profile-mobile-nav button i {
      width: 20px;
      text-align: center;
      color: var(--accent-blue);
      font-size: 15px;
    }

    .profile-mobile-nav a:hover,
    .profile-mobile-nav button:hover {
      color: var(--text-primary);
      background: var(--bg-card);
    }

    .profile-mobile-nav .logout-item {
      color: var(--accent-red);
      margin-top: auto;
      border-top: 1px solid var(--border-color);
      padding-top: 12px;
    }

    .profile-mobile-nav .logout-item i {
      color: var(--accent-red);
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
    }

    .empty-state i {
      font-size: 48px;
      margin-bottom: 16px;
      display: block;
    }

    .empty-state p {
      font-size: 14px;
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
      .profile-banner {
        padding: 24px 0 20px;
      }

      .profile-avatar {
        width: 72px;
        height: 72px;
      }

      .profile-avatar i.avatar-icon {
        font-size: 28px;
      }

      .profile-username {
        font-size: 18px;
      }

      .profile-logout-btn {
        display: none;
      }

      .profile-tab .tab-text {
        display: none;
      }

      .profile-tab {
        padding: 14px 16px;
        font-size: 18px;
      }

      .profile-tabs-inner {
        justify-content: space-around;
      }

      .bookmarks-table {
        display: none;
      }

      .bookmarks-cards {
        display: block;
      }

      .bookmarks-table-header {
        display: none;
      }

      .profile-form {
        max-width: 100%;
      }
    }

    @media (max-width: 480px) {
      .profile-banner-inner {
        gap: 14px;
      }

      .profile-avatar {
        width: 64px;
        height: 64px;
      }

      .profile-avatar i.avatar-icon {
        font-size: 24px;
      }

      .profile-avatar-edit {
        width: 24px;
        height: 24px;
        font-size: 10px;
      }

      .profile-username {
        font-size: 16px;
      }

      .profile-tab {
        padding: 12px 12px;
        font-size: 16px;
      }

      .bookmarks-toolbar {
        flex-direction: column;
        align-items: stretch;
      }

      .bookmarks-search {
        min-width: unset;
      }
    }

    @media (min-width: 769px) {
      .profile-mobile-trigger {
        display: none;
      }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$u = $user ?? $currentUser ?? [];
$username = $u['username'] ?? $u['name'] ?? '';
?>

<!-- Profile Banner -->
<section class="profile-banner">
  <div class="profile-banner-inner container">
    <div class="profile-avatar">
      <i class="fas fa-bolt avatar-icon"></i>
      <div class="profile-avatar-edit" title="Change avatar">
        <i class="fas fa-camera"></i>
      </div>
    </div>
    <div class="profile-user-info">
      <div class="profile-username"><?= esc($username) ?></div>
      <div class="profile-handle"><?= esc($username) ?></div>
    </div>
    <a href="<?= base_url('logout') ?>" class="profile-logout-btn">SALIR</a>
  </div>
</section>

<!-- Profile Tabs -->
<div class="profile-tabs">
  <div class="profile-tabs-inner container">
    <a href="<?= base_url('profile') ?>" class="profile-tab">
      <i class="fas fa-user-pen"></i>
      <span class="tab-text">Editar Perfil</span>
    </a>
    <a href="<?= base_url('notifications') ?>" class="profile-tab active">
      <i class="fas fa-bell"></i>
      <span class="tab-text">Notificaciones</span>
    </a>
    <a href="<?= base_url('history') ?>" class="profile-tab">
      <i class="fas fa-clock-rotate-left"></i>
      <span class="tab-text">Historial</span>
    </a>
    <a href="<?= base_url('bookmarks') ?>" class="profile-tab">
      <i class="fas fa-bookmark"></i>
      <span class="tab-text">Marcadores</span>
    </a>
    <a href="<?= base_url('profile/settings') ?>" class="profile-tab">
      <i class="fas fa-gear"></i>
      <span class="tab-text">Configuración</span>
    </a>
  </div>
</div>

<!-- Profile Content -->
<div class="container profile-content">
  <?php if (!empty($unread) && $unread > 0): ?>
    <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between">
      <span style="font-size:13px;color:var(--text-muted)"><?= esc($unread) ?> unread</span>
      <form action="<?= base_url('notifications/mark-all-read') ?>" method="post" style="display:inline">
        <?= csrf_field() ?>
        <button type="submit" style="background:none;border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--accent-blue);cursor:pointer">Mark All as Read</button>
      </form>
    </div>
  <?php endif; ?>

  <?php if (empty($notifications)): ?>
    <div class="empty-state">
      <i class="fas fa-bell"></i>
      <p>Sin notificaciones</p>
    </div>
  <?php else: ?>
    <div class="notification-list">
      <?php foreach ($notifications as $noti): ?>
        <?php
          $isUnread = empty($noti['is_read']);
          $icon = 'fas fa-bell';
          if (($noti['type'] ?? '') === 'new_chapter') $icon = 'fas fa-book-open';
          elseif (($noti['type'] ?? '') === 'reply') $icon = 'fas fa-comment';
          elseif (($noti['type'] ?? '') === 'mention') $icon = 'fas fa-star';
          elseif (($noti['type'] ?? '') === 'system') $icon = 'fas fa-bullhorn';

          $link = '#';
          if (!empty($noti['manga_slug']) && !empty($noti['chapter_slug'])) {
              $link = base_url('manga/' . esc($noti['manga_slug']) . '/' . esc($noti['chapter_slug']));
          } elseif (!empty($noti['manga_slug'])) {
              $link = base_url('manga/' . esc($noti['manga_slug']));
          }

          $timeAgo = '';
          if (!empty($noti['created_at'])) {
              $createdTime = strtotime($noti['created_at']);
              $diff = time() - $createdTime;
              if ($diff < 60) $timeAgo = 'Ahora';
              elseif ($diff < 3600) $timeAgo = floor($diff / 60) . ' min';
              elseif ($diff < 86400) $timeAgo = floor($diff / 3600) . ' horas';
              elseif ($diff < 604800) $timeAgo = floor($diff / 86400) . ' días';
              else $timeAgo = date('M d, Y', $createdTime);
          }
        ?>
        <a href="<?= $link ?>" class="notification-item<?= $isUnread ? ' unread' : '' ?>" style="text-decoration:none">
          <div class="notification-icon"><i class="<?= $icon ?>"></i></div>
          <div class="notification-content">
            <div class="notification-text">
              <?php if (!empty($noti['preview'])): ?>
                <?= esc($noti['preview']) ?>
              <?php elseif (!empty($noti['manga_name'])): ?>
                <?php if (($noti['type'] ?? '') === 'new_chapter'): ?>
                  <strong><?= esc($noti['manga_name']) ?></strong> has a new chapter<?= !empty($noti['chapter_slug']) ? ': ' . esc($noti['chapter_slug']) : '' ?>
                <?php else: ?>
                  <?php if (!empty($noti['actor_name'])): ?><strong><?= esc($noti['actor_name']) ?></strong> <?php endif; ?>
                  <?= esc($noti['manga_name']) ?>
                <?php endif; ?>
              <?php else: ?>
                <?= esc($noti['message'] ?? 'Notification') ?>
              <?php endif; ?>
            </div>
            <div class="notification-time"><?= esc($timeAgo) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
