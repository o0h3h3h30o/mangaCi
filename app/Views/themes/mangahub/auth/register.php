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
.form-hint { font-size: 11px; color: var(--txt3); margin-top: 4px; }
.form-input {
  width: 100%; padding: 10px 14px; font-size: 14px;
  color: var(--txt); background: var(--surface);
  border: 1px solid var(--border); border-radius: var(--radius-sm);
  outline: none; transition: border-color .2s; font-family: inherit;
  box-sizing: border-box;
}
.form-input:focus { border-color: var(--accent); }
.form-input::placeholder { color: var(--txt3); }
.btn-submit {
  width: 100%; padding: 12px; font-size: 14px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .5px;
  color: #fff; background: var(--accent); border: none;
  border-radius: var(--radius-sm); cursor: pointer;
  transition: background .2s; margin-top: 8px; font-family: inherit;
}
.btn-submit:hover { background: var(--accent2); }
.auth-footer {
  margin-top: 20px; padding-top: 16px;
  border-top: 1px solid var(--border); text-align: center;
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
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="8.5" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
      </div>
      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Join us and start reading your favorite manga</p>

      <?php if (session()->getFlashdata('error')): ?>
      <div class="auth-error"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
      <div class="auth-success"><?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>

      <form method="post" action="/register">
        <?= csrf_field() ?>

        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" value="<?= esc(old('name')) ?>"
                 class="form-input" placeholder="Your name" autocomplete="name">
        </div>

        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" value="<?= esc(old('username')) ?>"
                 class="form-input" placeholder="e.g. cool_user123" autocomplete="username">
          <p class="form-hint">3-30 characters, letters, numbers, underscore</p>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="<?= esc(old('email')) ?>"
                 class="form-input" placeholder="Email" autocomplete="email">
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password"
                 class="form-input" placeholder="Min 6 characters" autocomplete="new-password">
        </div>

        <div class="form-group">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password"
                 class="form-input" placeholder="Repeat password" autocomplete="new-password">
        </div>

        <button type="submit" class="btn-submit">Register</button>
      </form>

      <div class="auth-footer">
        <a href="/login">Already have an account? Login</a>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
