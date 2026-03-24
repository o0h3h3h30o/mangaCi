<?= $this->extend('themes/manhwaread/layouts/main') ?>

<?= $this->section('head_extra') ?>
<style>
    .auth-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: calc(100vh - 52px - 200px);
      padding: 40px 20px;
    }

    .auth-card {
      width: 100%;
      max-width: 420px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      overflow: hidden;
    }

    .auth-tabs {
      display: flex;
      border-bottom: 1px solid var(--border-color);
    }

    .auth-tab {
      flex: 1;
      padding: 14px;
      text-align: center;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      background: none;
      border: none;
      border-bottom: 2px solid transparent;
      cursor: pointer;
      font-family: var(--font);
      transition: all 0.2s;
      text-decoration: none;
    }

    .auth-tab:hover {
      color: var(--text-secondary);
    }

    .auth-tab.active {
      color: var(--accent);
      border-bottom-color: var(--accent);
    }

    .auth-body {
      padding: 28px 24px;
    }

    .auth-panel {
      display: none;
    }

    .auth-panel.active {
      display: block;
    }

    .auth-form-group {
      margin-bottom: 18px;
    }

    .auth-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 6px;
      letter-spacing: 0.3px;
    }

    .auth-input-wrapper {
      position: relative;
    }

    .auth-input-wrapper i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 14px;
    }

    .auth-input {
      width: 100%;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      padding: 11px 14px 11px 40px;
      font-family: var(--font);
      font-size: 14px;
      color: var(--text-primary);
      outline: none;
      transition: border-color 0.2s;
    }

    .auth-input:focus {
      border-color: var(--accent);
    }

    .auth-input::placeholder {
      color: var(--text-muted);
    }

    .auth-password-toggle {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      cursor: pointer;
      font-size: 14px;
      background: none;
      border: none;
      padding: 0;
    }

    .auth-password-toggle:hover {
      color: var(--text-secondary);
    }

    .auth-submit {
      width: 100%;
      background: var(--accent);
      color: white;
      padding: 12px;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.5px;
      cursor: pointer;
      border: none;
      font-family: var(--font);
      transition: background 0.2s;
    }

    .auth-submit:hover {
      background: var(--accent-hover);
    }

    .auth-divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 24px 0;
      font-size: 12px;
      color: var(--text-muted);
    }

    .auth-divider::before,
    .auth-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border-color);
    }

    .auth-social-buttons {
      display: flex;
      gap: 10px;
    }

    .auth-social-btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 6px;
      font-size: 14px;
      color: var(--text-secondary);
      cursor: pointer;
      transition: all 0.2s;
      font-family: var(--font);
      font-weight: 600;
    }

    .auth-social-btn:hover {
      border-color: var(--accent);
      color: var(--text-primary);
    }

    .auth-footer-text {
      text-align: center;
      margin-top: 20px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .auth-footer-text a {
      color: var(--accent);
      font-weight: 600;
    }

    .auth-footer-text a:hover {
      color: var(--accent-hover);
    }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 13px;
    }

    .alert-danger {
      background: rgba(232, 64, 87, .1);
      border: 1px solid rgba(232, 64, 87, .3);
      color: #e84057;
    }

    .alert-danger ul { margin: 0; padding-left: 16px; }

    .alert-success {
      background: rgba(168, 85, 247, .1);
      border: 1px solid rgba(168, 85, 247, .3);
      color: var(--accent);
    }

    @media (max-width: 480px) {
      .auth-wrapper {
        padding: 20px 12px;
      }

      .auth-body {
        padding: 20px 16px;
      }

      .auth-social-buttons {
        flex-direction: column;
      }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-tabs">
      <a href="<?= base_url('login') ?>" class="auth-tab" data-panel="login"><?= lang('ComixxAuth.login_tab') ?></a>
      <a href="<?= base_url('register') ?>" class="auth-tab active" data-panel="register"><?= lang('ComixxAuth.register_tab') ?></a>
    </div>

    <div class="auth-body">
      <div class="auth-panel active" id="panel-register">
        <?php if (session('error')): ?>
        <div class="alert alert-danger">
          <?= esc(session('error')) ?>
        </div>
        <?php endif; ?>

        <?php if (session('errors')): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach (session('errors') as $err): ?>
            <li><?= esc($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <?php if (session('success')): ?>
        <div class="alert alert-success">
          <?= esc(session('success')) ?>
        </div>
        <?php endif; ?>

        <form action="<?= base_url('register') ?>" method="post">
          <?= csrf_field() ?>

          <div class="auth-form-group">
            <label class="auth-label"><?= lang('ComixxAuth.display_name') ?></label>
            <div class="auth-input-wrapper">
              <i class="fas fa-id-card"></i>
              <input type="text" name="name" class="auth-input" placeholder="<?= lang('ComixxAuth.your_display_name') ?>" value="<?= old('name') ?>" required>
            </div>
          </div>

          <div class="auth-form-group">
            <label class="auth-label"><?= lang('ComixxAuth.username') ?></label>
            <div class="auth-input-wrapper">
              <i class="fas fa-user"></i>
              <input type="text" name="username" class="auth-input" placeholder="<?= lang('ComixxAuth.choose_username') ?>" value="<?= old('username') ?>" required>
            </div>
          </div>

          <div class="auth-form-group">
            <label class="auth-label"><?= lang('ComixxAuth.email_address') ?></label>
            <div class="auth-input-wrapper">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" class="auth-input" placeholder="<?= lang('ComixxAuth.enter_email') ?>" value="<?= old('email') ?>" required>
            </div>
          </div>

          <div class="auth-form-group">
            <label class="auth-label"><?= lang('ComixxAuth.password') ?></label>
            <div class="auth-input-wrapper">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" class="auth-input auth-password" placeholder="<?= lang('ComixxAuth.create_password') ?>" required>
              <button type="button" class="auth-password-toggle" title="<?= lang('ComixxAuth.toggle_password') ?>"><i class="far fa-eye"></i></button>
            </div>
          </div>

          <div class="auth-form-group">
            <label class="auth-label"><?= lang('ComixxAuth.confirm_password') ?></label>
            <div class="auth-input-wrapper">
              <i class="fas fa-lock"></i>
              <input type="password" name="confirm_password" class="auth-input auth-password" placeholder="<?= lang('ComixxAuth.confirm_your_pass') ?>" required>
              <button type="button" class="auth-password-toggle" title="<?= lang('ComixxAuth.toggle_password') ?>"><i class="far fa-eye"></i></button>
            </div>
          </div>

          <button type="submit" class="auth-submit"><?= lang('ComixxAuth.register_tab') ?></button>
        </form>

        <div class="auth-divider"><?= lang('ComixxAuth.or_continue_with') ?></div>
        <div class="auth-social-buttons">
          <button class="auth-social-btn"><i class="fab fa-google"></i> Google</button>
          <button class="auth-social-btn"><i class="fab fa-discord"></i> Discord</button>
        </div>

        <div class="auth-footer-text">
          <?= lang('ComixxAuth.has_account') ?> <a href="<?= base_url('login') ?>"><?= lang('ComixxAuth.login_link') ?></a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.auth-password-toggle').forEach(function(toggle) {
    toggle.addEventListener('click', function() {
      var input = toggle.parentElement.querySelector('.auth-password');
      var icon = toggle.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  });
});
</script>

<?= $this->endSection() ?>
