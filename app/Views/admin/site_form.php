<?php
$flash = session()->getFlashdata('flash');
$isEdit = !empty($site);
$action = $isEdit ? "/admin/sites/{$site['id']}/edit" : '/admin/sites/new';
?>

<?php if ($flash): ?>
<div class="a-flash <?= $flash['type'] === 'success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="<?= $action ?>" class="a-max-w-2xl a-space-y-6">
  <?= csrf_field() ?>

  <div class="a-panel" style="padding:1.25rem">
    <div class="a-space-y-4">
      <div>
        <label class="a-label">Domain</label>
        <input type="text" name="domain" value="<?= esc($site['domain'] ?? '') ?>"
               placeholder="example.com"
               class="a-input" required>
        <p class="a-hint">Domain without protocol (e.g. manhwas.me, not https://manhwas.me)</p>
      </div>

      <div>
        <label class="a-label">Site Name</label>
        <input type="text" name="name" value="<?= esc($site['name'] ?? '') ?>"
               placeholder="My Manga Site"
               class="a-input">
        <p class="a-hint">Display name for admin panel. Leave empty to use domain.</p>
      </div>

      <div>
        <label class="a-label">Status</label>
        <select name="is_active" class="a-select">
          <option value="1" <?= ($site['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= ($site['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
    </div>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center">
    <a href="/admin/sites" class="a-btn-sec">Cancel</a>
    <button type="submit" class="a-btn"><?= $isEdit ? 'Update Site' : 'Create Site' ?></button>
  </div>
</form>
