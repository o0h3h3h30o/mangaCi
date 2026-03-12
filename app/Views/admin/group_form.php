<?php
$isEdit = !empty($group);
$action = $isEdit ? "/admin/groups/{$group['id']}/edit" : '/admin/groups/new';
?>

<div class="a-mb-5">
  <a href="/admin/groups" class="a-link-back">← Back to Groups</a>
</div>

<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<div class="a-max-w-md">
  <div class="a-panel">
    <div class="a-panel-head">
      <?= $isEdit ? 'Edit Group' : 'Create New Group' ?>
    </div>
    <form method="post" action="<?= $action ?>">
      <?= csrf_field() ?>
      <div class="a-panel-body">
        <label class="a-label">Group Name <span class="req">*</span></label>
        <input type="text" name="name" value="<?= esc($group['name'] ?? '') ?>" required
               placeholder="e.g. admin, moderator, vip"
               class="a-input">
      </div>
      <div style="padding:0 20px 20px;display:flex;gap:12px">
        <button type="submit" class="a-btn"><?= $isEdit ? 'Save Changes' : 'Create Group' ?></button>
        <a href="/admin/groups" class="a-btn-sec">Cancel</a>
      </div>
    </form>
  </div>
</div>
