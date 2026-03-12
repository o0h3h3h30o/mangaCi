<?php if ($flash = ($flash ?? null)): ?>
<div class="a-flash <?= $flash['type']==='success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="a-toolbar a-items-center a-justify-between">
  <p class="a-count"><?= count($groups) ?> group<?= count($groups) !== 1 ? 's' : '' ?></p>
  <a href="/admin/groups/new" class="a-btn a-btn-sm">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    New Group
  </a>
</div>

<div class="a-panel">
  <table class="a-table">
    <thead>
      <tr>
        <th style="width:40px">#</th>
        <th>Name</th>
        <th>Members</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($groups as $g): ?>
      <tr>
        <td class="a-txt6 a-text-xs"><?= $g['id'] ?></td>
        <td>
          <span class="a-font-medium a-txt2"><?= esc($g['name']) ?></span>
          <?php if ($g['name'] === 'admin'): ?>
          <span class="a-badge a-badge-indigo" style="margin-left:8px">admin</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="/admin/users?group=<?= $g['id'] ?>" class="a-link-muted">
            <?= number_format((int)$g['member_count']) ?>
            <span class="a-txt6 a-text-xs">user<?= (int)$g['member_count'] !== 1 ? 's' : '' ?></span>
          </a>
        </td>
        <td>
          <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
            <a href="/admin/groups/<?= $g['id'] ?>/edit" class="a-btn-sec a-btn-sm">Edit</a>
            <form method="post" action="/admin/groups/<?= $g['id'] ?>/delete"
                  onsubmit="return confirm('Delete group &quot;<?= esc($g['name']) ?>&quot;? This will remove all users from this group.')">
              <?= csrf_field() ?>
              <button type="submit" class="a-btn-danger">Delete</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($groups)): ?>
      <tr><td colspan="4" class="a-empty">No groups yet. <a href="/admin/groups/new" class="a-link">Create one</a></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
