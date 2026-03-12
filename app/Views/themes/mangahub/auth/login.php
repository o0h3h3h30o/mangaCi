<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.auth-page {
  display: flex; align-items: center; justify-content: center;
  min-height: calc(100vh - 200px); padding: 40px 0;
}
.auth-wrapper { width: 100%; max-width: 420px; }
.auth-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); box-shadow: var(--shadow); padding: 32px;
}
.auth-icon {
  width: 48px; height: 48px; border-radius: 12px;
  background: var(--accent); color: #fff;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
}
.auth-title {
  font-size: 20px; font-weight: 700; text-align: center;
  margin-bottom: 4px; color: var(--txt);
}
.auth-subtitle {
  font-size: 13px; color: var(--txt3); text-align: center;
  margin-bottom: 24px;
}
.auth-error {
  background: rgba(232,25,44,.08); border: 1px solid rgba(232,25,44,.25);
  color: var(--accent); border-radius: var(--radius-sm);
  padding: 10px 14px; font-size: 13px; margin-bottom: 18px; line-height: 1.5;
}
.auth-success {
  background: rgba(34,197,94,.08); border: 1px solid rgba(34,197,94,.25);
  color: #22c55e; border-radius: var(--radius-sm);
  padding: 10px 14px; font-size: 13px; margin-bottom: 18px; line-height: 1.5;
}
.form-group { margin-bottom: 16px; }
.form-label {
  display: block; font-size: 12px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .5px;
  color: var(--txt2); margin-bottom: 6px;
}
.form-input {
  width: 100%; padding: 10px 14px; font-size: 14px;
  color: var(--txt); background: var(--surface);
  border: 1px solid var(--border); border-radius: var(--radius-sm);
  outline: none; transition: border-color .2s; font-family: inherit;
  box-sizing: border-box;
}
.form-input:focus { border-color: var(--accent); }
.form-input::placeholder { color: var(--txt3); }
.remember-row {
  display: flex; align-items: center; gap: 8px;
  margin-top: 4px; margin-bottom: 20px;
}
.remember-row input[type="checkbox"] {
  width: 16px; height: 16px; accent-color: var(--accent); cursor: pointer;
}
.remember-row label {
  font-size: 13px; color: var(--txt2); cursor: pointer; user-select: none;
}
.btn-submit {
  width: 100%; padding: 12px; font-size: 14px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .5px;
  color: #fff; background: var(--accent); border: none;
  border-radius: var(--radius-sm); cursor: pointer;
  transition: background .2s; font-family: inherit;
}
.btn-submit:hover { background: var(--accent2); }
.auth-footer {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: 20px; padding-top: 16px;
  border-top: 1px solid var(--border);
}
.auth-footer a {
  font-size: 13px; color: var(--accent); text-decoration: none;
}
.auth-footer a:hover { text-decoration: underline; }
</style>

<div class="auth-page">
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-icon">
        <svg width="22" height="22" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
          <polyline points="10 17 15 12 10 7"/>
          <line x1="15" y1="12" x2="3" y2="12"/>
        </svg>
      </div>
      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-subtitle">Sign in to your account to continue</p>

      <?php if (session()->getFlashdata('error')): ?>
      <div class="auth-error"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
      <div class="auth-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>

      <form method="post" action="/login">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Email or Username</label>
          <input type="text" name="login" value="<?= esc(old('login')) ?>"
                 class="form-input" placeholder="Email or Username" autocomplete="username">
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password"
                 class="form-input" placeholder="Password" autocomplete="current-password">
        </div>

        <div class="remember-row">
          <input type="checkbox" name="remember" id="remember" value="1">
          <label for="remember">Remember me for 7 days</label>
        </div>

        <button type="submit" class="btn-submit">Login</button>
      </form>

      <div class="auth-footer">
        <a href="/forgot-password">Forgot password?</a>
        <a href="/register">Create new account</a>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
