<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('head_extra') ?>
<style>
    .profile-page {
      max-width: 600px;
      margin: 0 auto;
      padding: 32px 16px 48px;
    }

    .profile-banner {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 32px;
      padding: 24px;
      background: linear-gradient(135deg, #0f0a1f, #1a0f2e);
      border: 1px solid var(--border-color);
      border-radius: 12px;
    }

    .profile-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      overflow: hidden;
      flex-shrink: 0;
    }

    .profile-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-info h1 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0;
    }

    .profile-info p {
      font-size: 13px;
      color: var(--text-muted);
      margin: 4px 0 0;
    }

    .profile-tab-content {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 24px;
    }

    .profile-form-header {
      margin-bottom: 24px;
    }

    .profile-form-header h2 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-primary);
      margin: 12px 0 0;
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      font-weight: 600;
      color: var(--accent);
      text-decoration: none;
      transition: color 0.2s;
    }

    .btn-back:hover {
      color: var(--accent-hover);
    }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 13px;
    }

    .alert-success {
      background: rgba(168, 85, 247, .1);
      border: 1px solid rgba(168, 85, 247, .3);
      color: var(--accent);
    }

    .alert-error {
      background: rgba(232, 64, 87, .1);
      border: 1px solid rgba(232, 64, 87, .3);
      color: #e84057;
    }

    .alert-error ul { margin: 0; padding-left: 16px; }

    .profile-form .form-group {
      margin-bottom: 20px;
    }

    .profile-form label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 6px;
    }

    .form-control {
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

    .form-control:focus {
      border-color: var(--accent);
    }

    .btn-primary {
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

    .btn-primary:hover {
      background: var(--accent-hover);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="profile-page">
    <div class="profile-banner">
        <div class="profile-avatar">
            <img src="https://ui-avatars.com/api/?name=<?= esc(urlencode($currentUser['username'])) ?>&size=120&background=7c3aed&color=fff" alt="<?= esc($currentUser['username']) ?>">
        </div>
        <div class="profile-info">
            <h1><?= esc($currentUser['username']) ?></h1>
            <p><?= lang('ComixxProfile.change_password') ?></p>
        </div>
    </div>

    <div class="profile-tab-content">
        <div class="profile-form-header">
            <a href="/profile" class="btn-back">&larr; <?= lang('ComixxProfile.back_to_profile') ?></a>
            <h2><?= lang('ComixxProfile.change_password') ?></h2>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/profile/change-password" method="post" class="profile-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="current_password"><?= lang('ComixxProfile.current_password') ?></label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password"><?= lang('ComixxProfile.new_password') ?></label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password"><?= lang('ComixxProfile.confirm_new_pass') ?></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn-primary"><?= lang('ComixxProfile.update_password') ?></button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
