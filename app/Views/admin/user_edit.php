<?php
$colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];
$name   = $user['name'] ?: $user['username'];
$ch     = strtoupper(mb_substr($name, 0, 1));
$bg     = $colors[$user['id'] % 6];
?>

<!-- Back link -->
<div class="a-mb-5">
  <a href="/admin/users" class="a-link-back">&larr; Back to Users</a>
</div>

<!-- Flash -->
<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<form method="post" action="/admin/users/<?= $user['id'] ?>/edit">
  <?= csrf_field() ?>
  <div class="a-grid-form">

    <!-- Left: info -->
    <div class="a-space-y-5">

      <!-- Profile header card -->
      <div class="a-panel" style="padding:20px">
        <div style="display:flex;align-items:center;gap:16px">
          <div class="a-avatar a-avatar-md" style="background:<?= $bg ?>">
            <?= esc($ch) ?>
          </div>
          <div>
            <div class="a-font-semibold a-txt2"><?= esc($name) ?></div>
            <div class="a-text-sm a-txt5">@<?= esc($user['username']) ?> &middot; ID <?= $user['id'] ?></div>
            <?php if (!empty($user['created_at'])): ?>
            <div class="a-text-xs a-txt6" style="margin-top:2px">Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Basic info -->
      <div class="a-panel">
        <div class="a-panel-head">Basic Info</div>
        <div class="a-panel-body">
          <div>
            <label class="a-label">Display Name</label>
            <input type="text" name="name" value="<?= esc($user['name']) ?>" required class="a-input">
          </div>
          <div>
            <label class="a-label">Username</label>
            <input type="text" name="username" value="<?= esc($user['username']) ?>" required class="a-input">
          </div>
          <div>
            <label class="a-label">Email</label>
            <input type="email" name="email" value="<?= esc($user['email']) ?>" required class="a-input">
          </div>
          <div>
            <label class="a-label">New Password <span class="hint">(leave blank to keep current)</span></label>
            <input type="password" name="password" autocomplete="new-password" class="a-input" placeholder="Min. 6 characters">
          </div>
        </div>
      </div>

    </div>

    <!-- Right: status + groups -->
    <div class="a-space-y-5">

      <!-- Status -->
      <div class="a-panel">
        <div class="a-panel-head">Status</div>
        <div class="a-panel-body compact">
          <label class="a-radio">
            <input type="radio" name="active" value="1" <?= $user['active'] ? 'checked' : '' ?>>
            <div>
              <div class="a-font-medium a-txt2">Active</div>
              <div class="a-text-xs a-txt6">User can log in and comment</div>
            </div>
          </label>
          <label class="a-radio">
            <input type="radio" name="active" value="0" <?= !$user['active'] ? 'checked' : '' ?>>
            <div>
              <div class="a-font-medium a-txt2">Inactive</div>
              <div class="a-text-xs a-txt6">User cannot log in</div>
            </div>
          </label>
        </div>
      </div>

      <!-- Groups -->
      <div class="a-panel">
        <div class="a-panel-head">Groups</div>
        <div class="a-panel-body compact">
          <?php if (empty($allGroups)): ?>
          <p class="a-text-xs a-txt6">No groups defined. <a href="/admin/groups/new" class="a-link">Create one</a></p>
          <?php else: ?>
          <?php foreach ($allGroups as $g): ?>
          <label class="a-checkbox">
            <input type="checkbox" name="groups[]" value="<?= $g['id'] ?>"
                   <?= in_array((int)$g['id'], $userGroups, true) ? 'checked' : '' ?>>
            <span><?= esc($g['name']) ?></span>
            <?php if ($g['description']): ?>
            <span class="a-text-xs a-txt6 a-truncate"><?= esc($g['description']) ?></span>
            <?php endif; ?>
          </label>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Save -->
      <button type="submit" class="a-btn a-btn-block a-btn-xl">
        Save Changes
      </button>
    </div>

  </div>
</form>
