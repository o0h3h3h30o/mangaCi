<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('head_extra') ?>
<style>
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

    .container {
      max-width: 1368px;
      margin: 0 auto;
      padding: 0 24px;
      box-sizing: border-box;
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
      background: linear-gradient(135deg, #2d1b4e, #1a0f2e);
      border: 3px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .profile-avatar i.avatar-icon {
      font-size: 38px;
      color: var(--accent);
    }

    .profile-avatar-edit {
      position: absolute;
      bottom: 2px;
      right: 2px;
      width: 28px;
      height: 28px;
      background: var(--accent);
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
      background: var(--accent-hover);
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
      text-decoration: none;
    }

    .profile-logout-btn:hover {
      background: #e84057;
      border-color: #e84057;
      color: white;
    }

    /* ===== Profile Tabs ===== */
    .profile-tabs {
      background: var(--bg-card);
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
      color: var(--accent);
      border-bottom-color: var(--accent);
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
      border-color: var(--accent);
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
      color: var(--accent);
      margin-bottom: 24px;
      cursor: pointer;
      transition: color 0.2s;
      text-decoration: none;
    }

    .change-password-link:hover {
      color: var(--accent-hover);
    }

    .save-btn {
      background: var(--accent);
      color: white;
      padding: 10px 28px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.5px;
      transition: background 0.2s;
      border: none;
      cursor: pointer;
      font-family: var(--font);
    }

    .save-btn:hover {
      background: var(--accent-hover);
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
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$u = $user ?? $currentUser ?? [];
$name = $u['name'] ?? '';
$username = $u['username'] ?? '';
$email = $u['email'] ?? '';
$uid = $u['id'] ?? '';
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
      <div class="profile-username"><?= esc($name ?: $username) ?></div>
      <div class="profile-handle"><?= esc($username) ?></div>
    </div>
    <a href="<?= base_url('logout') ?>" class="profile-logout-btn"><?= lang('Comixx.logout') ?></a>
  </div>
</section>

<!-- Profile Tabs -->
<div class="profile-tabs">
  <div class="profile-tabs-inner container">
    <a href="<?= base_url('profile') ?>" class="profile-tab active">
      <i class="fas fa-user-pen"></i>
      <span class="tab-text"><?= lang('ComixxProfile.edit_profile') ?></span>
    </a>
    <a href="<?= base_url('notifications') ?>" class="profile-tab">
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
  <?php if ($msg = session()->getFlashdata('success')): ?>
  <div style="padding:12px 16px;margin-bottom:16px;background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.3);border-radius:8px;font-size:13px;color:var(--accent)"><?= esc($msg) ?></div>
  <?php endif; ?>
  <?php if ($msg = session()->getFlashdata('error')): ?>
  <div style="padding:12px 16px;margin-bottom:16px;background:rgba(232,64,87,.1);border:1px solid rgba(232,64,87,.3);border-radius:8px;font-size:13px;color:#e84057"><?= esc($msg) ?></div>
  <?php endif; ?>

  <form action="<?= base_url('profile') ?>" method="post" class="profile-form">
    <?= csrf_field() ?>
    <div class="form-group">
      <label class="form-label">User ID</label>
      <input type="text" class="form-input readonly" value="<?= esc($uid) ?>" readonly>
    </div>
    <div class="form-group">
      <label class="form-label">Username</label>
      <input type="text" class="form-input" name="username" value="<?= esc($username) ?>" placeholder="<?= lang('ComixxProfile.enter_username') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Email Address</label>
      <input type="email" class="form-input" name="email" value="<?= esc($email) ?>" placeholder="<?= lang('ComixxProfile.enter_email') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Display Name</label>
      <input type="text" class="form-input" name="name" value="<?= esc($name) ?>" placeholder="<?= lang('ComixxProfile.enter_name') ?>">
    </div>
    <a href="<?= base_url('profile/change-password') ?>" class="change-password-link">
      <span>&#128273;</span> <?= lang('ComixxProfile.change_password') ?>
    </a>
    <br><br>
    <button type="submit" class="save-btn"><?= lang('ComixxProfile.save_changes') ?></button>
  </form>
</div>

<?= $this->endSection() ?>
