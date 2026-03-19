<?= $this->extend('themes/comixx/layouts/main') ?>

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
      background: var(--bg-secondary);
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
    }

    .auth-tab:hover {
      color: var(--text-secondary);
    }

    .auth-tab.active {
      color: var(--accent-blue);
      border-bottom-color: var(--accent-blue);
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
      border-color: var(--accent-blue);
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

    .auth-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      font-size: 13px;
    }

    .auth-remember {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-secondary);
      cursor: pointer;
    }

    .auth-remember input[type="checkbox"] {
      appearance: none;
      width: 16px;
      height: 16px;
      background: var(--bg-input);
      border: 1px solid var(--border-color);
      border-radius: 3px;
      cursor: pointer;
      position: relative;
    }

    .auth-remember input[type="checkbox"]:checked {
      background: var(--accent-blue);
      border-color: var(--accent-blue);
    }

    .auth-remember input[type="checkbox"]:checked::after {
      content: '\f00c';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      font-size: 10px;
      color: white;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .auth-forgot {
      color: var(--accent-blue);
      font-weight: 600;
      transition: color 0.2s;
    }

    .auth-forgot:hover {
      color: #059669;
    }

    .auth-submit {
      width: 100%;
      background: var(--accent-blue);
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
      background: #059669;
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
      background: var(--bg-card);
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
      background: var(--bg-card-hover);
      color: var(--text-primary);
    }

    .auth-footer-text {
      text-align: center;
      margin-top: 20px;
      font-size: 13px;
      color: var(--text-muted);
    }

    .auth-footer-text a {
      color: var(--accent-blue);
      font-weight: 600;
    }

    .auth-footer-text a:hover {
      color: #059669;
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
      <a href="<?= base_url('login') ?>" class="auth-tab active" data-panel="login">INICIAR SESIÓN</a>
      <a href="<?= base_url('register') ?>" class="auth-tab" data-panel="register">REGISTRARSE</a>
    </div>

    <div class="auth-body">
      <div class="auth-panel active" id="panel-login">
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

        <form action="<?= base_url('login') ?>" method="post">
          <?= csrf_field() ?>

          <div class="auth-form-group">
            <label class="auth-label">Usuario o Correo</label>
            <div class="auth-input-wrapper">
              <i class="fas fa-user"></i>
              <input type="text" name="login" class="auth-input" placeholder="Ingresa tu usuario o correo" value="<?= old('login') ?>" required>
            </div>
          </div>

          <div class="auth-form-group">
            <label class="auth-label">Contraseña</label>
            <div class="auth-input-wrapper">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" class="auth-input auth-password" placeholder="Ingresa tu contraseña" required>
              <button type="button" class="auth-password-toggle" title="Mostrar contraseña"><i class="far fa-eye"></i></button>
            </div>
          </div>

          <div class="auth-options">
            <label class="auth-remember">
              <input type="checkbox" name="remember"> Recuérdame
            </label>
            <a href="<?= base_url('forgot-password') ?>" class="auth-forgot">¿Olvidaste tu contraseña?</a>
          </div>

          <button type="submit" class="auth-submit">INICIAR SESIÓN</button>
        </form>

        <div class="auth-divider">o continuar con</div>
        <div class="auth-social-buttons">
          <button class="auth-social-btn"><i class="fab fa-google"></i> Google</button>
          <button class="auth-social-btn"><i class="fab fa-discord"></i> Discord</button>
        </div>

        <div class="auth-footer-text">
          ¿No tienes una cuenta? <a href="<?= base_url('register') ?>">Regístrate</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password toggle
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
