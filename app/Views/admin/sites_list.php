<?php $flash = session()->getFlashdata('flash'); ?>

<?php if ($flash): ?>
<div class="a-flash <?= $flash['type'] === 'success' ? 'a-flash-ok' : 'a-flash-err' ?>">
  <?= esc($flash['msg']) ?>
</div>
<?php endif; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
  <p style="color:#9ca3af;font-size:13px"><?= count($sites) ?> site(s)</p>
  <a href="/admin/sites/new" class="a-btn a-btn-sm">+ New Site</a>
</div>

<div class="a-panel" style="overflow-x:auto">
  <table class="a-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Domain</th>
        <th>Name</th>
        <th>Active</th>
        <th style="width:120px">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($sites as $site): ?>
      <tr>
        <td><?= $site['id'] ?></td>
        <td><code style="font-size:12px"><?= esc($site['domain']) ?></code></td>
        <td><?= esc($site['name']) ?></td>
        <td>
          <?php if ($site['is_active']): ?>
            <span style="color:#34d399;font-size:12px;font-weight:600">Active</span>
          <?php else: ?>
            <span style="color:#f87171;font-size:12px;font-weight:600">Inactive</span>
          <?php endif; ?>
        </td>
        <td style="display:flex;gap:0.5rem">
          <a href="/admin/sites/<?= $site['id'] ?>/edit" class="a-btn-sec a-btn-sm">Edit</a>
          <?php if ((int) $site['id'] !== 1): ?>
          <form method="post" action="/admin/sites/<?= $site['id'] ?>/delete" onsubmit="return confirm('Delete this site?')">
            <?= csrf_field() ?>
            <button type="submit" class="a-btn-danger a-btn-sm">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
