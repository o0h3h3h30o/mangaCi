<?= $this->extend('themes/mangahub/layouts/main') ?>

<?= $this->section('content') ?>
<style>
.cp-page { max-width: 1200px; margin: 0 auto; padding: 24px 12px; }
.cp-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden; }
.cp-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
.cp-header-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; }
.cp-header h2 { font-size: 18px; font-weight: 700; color: var(--txt); margin: 0; }
.cp-back { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: var(--radius-sm); color: var(--txt3); text-decoration: none; transition: background 0.2s, color 0.2s; }
.cp-back:hover { background: var(--border); color: var(--txt); }
.cp-body { padding: 24px; max-width: 420px; }
.cp-alert { margin-bottom: 16px; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; }
.cp-alert-error { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; }
.cp-alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #22c55e; }
.cp-field { margin-bottom: 16px; }
.cp-field label { display: block; font-size: 13px; font-weight: 500; color: var(--txt2); margin-bottom: 6px; }
.cp-field input { width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s; }
.cp-field input:focus { border-color: var(--accent); }
.cp-actions { display: flex; align-items: center; gap: 12px; margin-top: 20px; }
.cp-submit { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: var(--radius-sm); background: var(--accent); color: #fff; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: background 0.2s; }
.cp-submit:hover { background: var(--accent2); }
.cp-cancel { font-size: 13px; color: var(--txt3); text-decoration: none; transition: color 0.2s; }
.cp-cancel:hover { color: var(--txt); }
</style>

<main>
    <div class="cp-page">
        <div class="cp-panel">

            <div class="cp-header">
                <a href="/profile" class="cp-back">
                    <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="cp-header-icon">
                    <svg style="width:14px;height:14px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h2>Change Password</h2>
            </div>

            <div class="cp-body">

                <?php if (session()->getFlashdata('error')): ?>
                <div class="cp-alert cp-alert-error">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                <div class="cp-alert cp-alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
                <?php endif; ?>

                <form action="/profile/change-password" method="POST">

                    <div class="cp-field">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>

                    <div class="cp-field">
                        <label>New Password</label>
                        <input type="password" name="new_password" required minlength="6">
                    </div>

                    <div class="cp-field">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="6">
                    </div>

                    <div class="cp-actions">
                        <button type="submit" class="cp-submit">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Save
                        </button>
                        <a href="/profile" class="cp-cancel">Cancel</a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</main>
<?= $this->endSection() ?>
