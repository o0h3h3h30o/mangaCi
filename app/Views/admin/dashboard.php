<?php
$cards = [
    ['label' => 'Total Users',    'value' => $stats['users'],    'color' => 'blue',   'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
    ['label' => 'Total Manga',    'value' => $stats['manga'],    'color' => 'purple', 'icon' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
    ['label' => 'Total Comments', 'value' => $stats['comments'], 'color' => 'green',  'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>'],
    ['label' => 'Groups',         'value' => $stats['groups'],   'color' => 'orange', 'icon' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'],
];
?>

<!-- Stats cards -->
<div class="a-stats">
  <?php foreach ($cards as $c): ?>
  <div class="a-stat">
    <div class="a-stat-icon <?= $c['color'] ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <?= $c['icon'] ?>
      </svg>
    </div>
    <div>
      <div class="a-stat-value"><?= number_format($c['value']) ?></div>
      <div class="a-stat-label"><?= $c['label'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Quick links -->
<div class="a-quick-links">
  <a href="/admin/users" class="a-btn">Manage Users</a>
  <a href="/admin/groups" class="a-btn-sec">Manage Groups</a>
</div>

<!-- Recent users -->
<div class="a-panel">
  <div class="a-panel-head">
    <span>Recent Users</span>
    <a href="/admin/users" class="a-link a-text-xs">View all →</a>
  </div>
  <div class="a-overflow-x">
    <table class="a-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Email</th>
          <th>Status</th>
          <th>Joined</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentUsers as $u): ?>
        <?php
          $name = $u['name'] ?: $u['username'];
          $ch   = strtoupper(mb_substr($name, 0, 1));
          $colors = ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'];
          $bg = $colors[$u['id'] % 6];
          $date = !empty($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—';
        ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:12px">
              <div class="a-avatar a-avatar-sm" style="background:<?= $bg ?>">
                <?= esc($ch) ?>
              </div>
              <div>
                <div class="a-font-medium a-txt2"><?= esc($name) ?></div>
                <div class="a-text-xs a-txt5">@<?= esc($u['username']) ?></div>
              </div>
            </div>
          </td>
          <td class="a-txt4 a-text-xs"><?= esc($u['email']) ?></td>
          <td>
            <?php if ($u['active']): ?>
              <span class="a-badge a-badge-green">Active</span>
            <?php else: ?>
              <span class="a-badge a-badge-gray">Inactive</span>
            <?php endif; ?>
          </td>
          <td class="a-txt5 a-text-xs"><?= $date ?></td>
          <td class="a-text-right">
            <a href="/admin/users/<?= $u['id'] ?>/edit" class="a-link a-text-xs">Edit</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentUsers)): ?>
        <tr><td colspan="5" class="a-empty">No users found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
