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

    /* ===== History ===== */
    .history-list { display: flex; flex-direction: column; gap: 6px; }
    .history-item {
      display: flex; gap: 12px; padding: 12px; background: var(--bg-card);
      border: 1px solid var(--border-color); border-radius: 8px;
      transition: background 0.2s; text-decoration: none;
    }
    .history-item:hover { background: var(--bg-input); }
    .history-thumb {
      width: 48px; height: 64px; border-radius: 4px; overflow: hidden;
      flex-shrink: 0; background: var(--bg-input);
    }
    .history-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .history-info { flex: 1; min-width: 0; }
    .history-title { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
    .history-chapter { font-size: 13px; color: var(--accent); font-weight: 500; margin-bottom: 4px; }
    .history-time { font-size: 12px; color: var(--text-muted); }

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
    <a href="<?= base_url('notifications') ?>" class="profile-tab">
      <i class="fas fa-bell"></i>
      <span class="tab-text"><?= lang('ComixxProfile.notifications') ?></span>
    </a>
    <a href="<?= base_url('history') ?>" class="profile-tab active">
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
  <?php if (empty($history)): ?>
    <div class="empty-state">
      <i class="fas fa-clock-rotate-left"></i>
      <p><?= lang('ComixxProfile.no_history_desc') ?></p>
    </div>
  <?php else: ?>
    <div class="history-list">
      <?php foreach ($history as $h): ?>
        <?php
          $diff = time() - ($h['time'] ?? 0);
          if ($diff < 60)         $ago = lang('ComixxTime.now');
          elseif ($diff < 3600)   $ago = str_replace('{time}', str_replace('{n}', floor($diff / 60), lang('ComixxTime.minutes_ago')), lang('ComixxTime.last_read_time'));
          elseif ($diff < 86400)  $ago = str_replace('{time}', str_replace('{n}', floor($diff / 3600), lang('ComixxTime.hours_ago')), lang('ComixxTime.last_read_time'));
          elseif ($diff < 604800) $ago = str_replace('{time}', str_replace('{n}', floor($diff / 86400), lang('ComixxTime.days_ago')), lang('ComixxTime.last_read_time'));
          else                    $ago = str_replace('{time}', date('M d, Y', $h['time'] ?? 0), lang('ComixxTime.last_read_time'));

          $chapUrl = base_url('manga/' . esc($h['manga_slug']) . '/' . esc($h['chap_slug']));
          $mangaUrl = base_url('manga/' . esc($h['manga_slug']));
        ?>
        <a href="<?= $chapUrl ?>" class="history-item" style="text-decoration:none">
          <div class="history-thumb"><img src="<?= esc($h['cover'] ?? '') ?>" alt="<?= esc($h['manga_name']) ?>" loading="lazy"></div>
          <div class="history-info">
            <div class="history-title"><?= esc($h['manga_name']) ?></div>
            <div class="history-chapter"><?= esc($h['chap_slug']) ?></div>
            <div class="history-time"><?= esc($ago) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
function clearHistory() {
  if (!confirm('<?= lang('ComixxProfile.clear_confirm') ?>')) return;
  document.cookie = '_history=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
  window.location.reload();
}
</script>

<?= $this->endSection() ?>
