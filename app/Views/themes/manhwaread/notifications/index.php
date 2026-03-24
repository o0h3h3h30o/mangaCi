<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('head_extra') ?>
<style>
    .container {
      max-width: 1368px;
      margin: 0 auto;
      padding: 0 24px;
      box-sizing: border-box;
    }

    /* ===== Profile Banner ===== */
    .profile-banner {
      position: relative;
      background: linear-gradient(135deg, #0f0a1f 0%, #1a0f2e 30%, #1e1245 60%, #0f0a1f 100%);
      padding: 40px 0 24px;
      overflow: hidden;
    }

    .profile-banner::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        repeating-conic-gradient(from 0deg at 50% 50%, transparent 0deg 80deg, rgba(168, 85, 247, 0.015) 80deg 90deg),
        radial-gradient(ellipse at 20% 80%, rgba(168, 85, 247, 0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(168, 85, 247, 0.04) 0%, transparent 60%);
      pointer-events: none;
    }

    .profile-banner::after {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        url("data:image/svg+xml,%3Csvg width='400' height='200' viewBox='0 0 400 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 80 Q100 40 200 80 T400 80' fill='none' stroke='rgba(168,85,247,0.04)' stroke-width='1'/%3E%3Cpath d='M0 120 Q100 80 200 120 T400 120' fill='none' stroke='rgba(168,85,247,0.03)' stroke-width='1'/%3E%3Cpath d='M0 160 Q100 120 200 160 T400 160' fill='none' stroke='rgba(168,85,247,0.025)' stroke-width='1'/%3E%3C/svg%3E");
      background-size: 400px 200px;
      opacity: 0.8;
      pointer-events: none;
    }

    .profile-banner-inner {
      position: relative; z-index: 1; display: flex; align-items: center; gap: 20px;
    }
    .profile-avatar {
      position: relative; width: 96px; height: 96px; border-radius: 50%;
      background: linear-gradient(135deg, #2d1b4e, #1a0f2e);
      border: 3px solid var(--border-color);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .profile-avatar i.avatar-icon { font-size: 38px; color: var(--accent); }
    .profile-avatar-edit {
      position: absolute; bottom: 2px; right: 2px; width: 28px; height: 28px;
      background: var(--accent); border-radius: 50%; display: flex; align-items: center;
      justify-content: center; font-size: 12px; color: white; cursor: pointer;
      border: 2px solid var(--bg-primary);
    }
    .profile-avatar-edit:hover { background: var(--accent-hover); }
    .profile-user-info { flex: 1; }
    .profile-username { font-size: 22px; font-weight: 700; color: var(--text-primary); }
    .profile-handle { font-size: 14px; color: var(--text-muted); margin-top: 2px; }
    .profile-logout-btn {
      background: var(--bg-card); border: 1px solid var(--border-color); padding: 8px 20px;
      border-radius: 6px; font-size: 14px; font-weight: 700; color: var(--text-secondary);
      letter-spacing: 0.5px; transition: all 0.2s; flex-shrink: 0; text-decoration: none;
    }
    .profile-logout-btn:hover { background: #e84057; border-color: #e84057; color: white; }

    /* ===== Profile Tabs ===== */
    .profile-tabs {
      background: var(--bg-card); border-bottom: 1px solid var(--border-color);
      position: sticky; top: 52px; z-index: 50;
    }
    .profile-tabs-inner { display: flex; gap: 0; }
    .profile-tab {
      display: flex; align-items: center; gap: 8px; padding: 14px 20px; font-size: 14px;
      font-weight: 600; color: var(--text-muted); border-bottom: 2px solid transparent;
      transition: all 0.2s; cursor: pointer; white-space: nowrap; background: none;
      border-top: none; border-left: none; border-right: none; font-family: var(--font);
      text-decoration: none;
    }
    .profile-tab:hover { color: var(--text-secondary); }
    .profile-tab.active { color: var(--accent); border-bottom-color: var(--accent); }
    .profile-tab .tab-text { display: inline; }
    .profile-content { padding-top: 24px; padding-bottom: 40px; }

    /* ===== Notifications ===== */
    .notification-list { display: flex; flex-direction: column; gap: 6px; }
    .notification-item {
      display: flex; gap: 12px; padding: 14px; background: var(--bg-card);
      border: 1px solid var(--border-color); border-radius: 8px;
      transition: background 0.2s; text-decoration: none;
    }
    .notification-item:hover { background: var(--bg-input); }
    .notification-item.unread { border-left: 3px solid var(--accent); }
    .notification-icon {
      width: 40px; height: 40px; border-radius: 50%; background: var(--bg-input);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
      font-size: 16px; color: var(--accent);
    }
    .notification-content { flex: 1; }
    .notification-text {
      font-size: 14px; color: var(--text-primary); margin-bottom: 4px; line-height: 1.5;
    }
    .notification-text strong { color: var(--accent); }
    .notification-time { font-size: 12px; color: var(--text-muted); }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
    .empty-state p { font-size: 14px; }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
      .profile-banner { padding: 24px 0 20px; }
      .profile-avatar { width: 72px; height: 72px; }
      .profile-avatar i.avatar-icon { font-size: 28px; }
      .profile-username { font-size: 18px; }
      .profile-logout-btn { display: none; }
      .profile-tab .tab-text { display: none; }
      .profile-tab { padding: 14px 16px; font-size: 18px; }
      .profile-tabs-inner { justify-content: space-around; }
    }

    @media (max-width: 480px) {
      .profile-banner-inner { gap: 14px; }
      .profile-avatar { width: 64px; height: 64px; }
      .profile-avatar i.avatar-icon { font-size: 24px; }
      .profile-avatar-edit { width: 24px; height: 24px; font-size: 10px; }
      .profile-username { font-size: 16px; }
      .profile-tab { padding: 12px 12px; font-size: 16px; }
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
    <a href="<?= base_url('logout') ?>" class="profile-logout-btn"><?= lang('Comixx.logout') ?></a>
  </div>
</section>

<!-- Profile Tabs -->
<div class="profile-tabs">
  <div class="profile-tabs-inner container">
    <a href="<?= base_url('profile') ?>" class="profile-tab">
      <i class="fas fa-user-pen"></i>
      <span class="tab-text"><?= lang('ComixxProfile.edit_profile') ?></span>
    </a>
    <a href="<?= base_url('notifications') ?>" class="profile-tab active">
      <i class="fas fa-bell"></i>
      <span class="tab-text"><?= lang('ComixxProfile.notifications') ?></span>
    </a>
    <a href="<?= base_url('history') ?>" class="profile-tab">
      <i class="fas fa-clock-rotate-left"></i>
      <span class="tab-text"><?= lang('ComixxProfile.history') ?></span>
    </a>
    <a href="<?= base_url('bookmarks') ?>" class="profile-tab">
      <i class="fas fa-bookmark"></i>
      <span class="tab-text"><?= lang('ComixxProfile.bookmarks') ?></span>
    </a>
    <a href="<?= base_url('profile/settings') ?>" class="profile-tab">
      <i class="fas fa-gear"></i>
      <span class="tab-text"><?= lang('ComixxProfile.settings') ?></span>
    </a>
  </div>
</div>

<!-- Profile Content -->
<div class="container profile-content">
  <?php if (!empty($unread) && $unread > 0): ?>
    <div style="margin-bottom:16px;display:flex;align-items:center;justify-content:space-between">
      <span style="font-size:13px;color:var(--text-muted)"><?= str_replace('{n}', esc($unread), lang('ComixxProfile.unread_count')) ?></span>
      <form action="<?= base_url('notifications/mark-all-read') ?>" method="post" style="display:inline">
        <?= csrf_field() ?>
        <button type="submit" style="background:none;border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--accent);cursor:pointer"><?= lang('ComixxProfile.mark_all_read') ?></button>
      </form>
    </div>
  <?php endif; ?>

  <?php if (empty($notifications)): ?>
    <div class="empty-state">
      <i class="fas fa-bell"></i>
      <p><?= lang('ComixxProfile.no_notifications') ?></p>
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
              if ($diff < 60) $timeAgo = lang('ComixxTime.now');
              elseif ($diff < 3600) $timeAgo = str_replace('{n}', floor($diff / 60), lang('ComixxTime.minutes_ago'));
              elseif ($diff < 86400) $timeAgo = str_replace('{n}', floor($diff / 3600), lang('ComixxTime.hours_ago'));
              elseif ($diff < 604800) $timeAgo = str_replace('{n}', floor($diff / 86400), lang('ComixxTime.days_ago'));
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
